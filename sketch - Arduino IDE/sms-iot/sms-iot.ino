  /*
  * ============================================================
  * PROYECTO: Sistema Inteligente de Monitoreo del Sueño IoT
  * VERSIÓN: 1.0.1 - CORREGIDO
  * ============================================================
  */

  #include <WiFi.h>
  #include <HTTPClient.h>
  #include <ArduinoJson.h>
  #include <Wire.h>
  #include <Adafruit_GFX.h>
  #include <Adafruit_SSD1306.h>
  #include "DHT.h"

  // ============================================================
  //  CONFIGURACIÓN DE PINES (SEGÚN PROYECTO)
  // ============================================================

  // OLED SSD1306 I2C
  #define OLED_SDA 21
  #define OLED_SCL 22
  #define SCREEN_WIDTH 128
  #define SCREEN_HEIGHT 64
  #define OLED_ADDRESS 0x3C

  // DHT11
  #define PIN_DHT 4

  // KY-037 (Sonido) - AO (Salida Analógica)
  #define SONIDO_PIN 34

  // SW-420 (Vibración/Movimiento)
  #define SW420_PIN 27

  // LED RGB
  #define LED_R 25
  #define LED_G 26
  #define LED_B 33

  // ============================================================
  //  CONFIGURACIÓN DHT11
  // ============================================================

  #define DHTTYPE DHT11
  DHT dht(PIN_DHT, DHTTYPE);

  // ============================================================
  //  CONFIGURACIÓN OLED
  // ============================================================

  Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);

  // ============================================================
  //  CONFIGURACIÓN WIFI
  // ============================================================

  const char* ssid = "INFINITUM1329_2.4_EXT";
  const char* password = "FrZfvJJkKg";

  String serverName = "http://192.168.1.101:8080/api/lecturas";

  unsigned long lastTime = 0;
  const unsigned long timerDelay = 15000;  // 15 segundos

  // ============================================================
  //  VARIABLES GLOBALES
  // ============================================================

  float temperatura = 0;
  float humedad = 0;
  int ruido = 0;
  int movimiento = 0;
  int indiceSueno = 0;
  bool wifiConectado = false;

  // Contador de intentos fallidos
  int fallosConsecutivos = 0;
  const int maxFallos = 5;

  // ============================================================
  //  FUNCIÓN: CONECTAR WiFi
  // ============================================================

  void conectarWiFi() {
    Serial.println();
    Serial.print("📡 Conectando a WiFi");

    WiFi.mode(WIFI_STA);
    WiFi.begin(ssid, password);

    int intentos = 0;

    while (WiFi.status() != WL_CONNECTED && intentos < 30) {
      delay(500);
      Serial.print(".");
      intentos++;
    }

    if (WiFi.status() == WL_CONNECTED) {
      wifiConectado = true;
      fallosConsecutivos = 0;
      Serial.println();
      Serial.println("=================================");
      Serial.println("✅ WiFi conectado correctamente");
      Serial.print("📡 IP ESP32: ");
      Serial.println(WiFi.localIP());
      Serial.println("=================================");
      Serial.println();
      
      display.clearDisplay();
      display.setCursor(0, 0);
      display.println("✅ WiFi Conectado");
      display.print("IP: ");
      display.println(WiFi.localIP());
      display.display();
      
    } else {
      wifiConectado = false;
      Serial.println();
      Serial.println("❌ Error al conectar WiFi");
      Serial.println("⚠️ Reintentando en 10 segundos...");
      
      display.clearDisplay();
      display.setCursor(0, 0);
      display.println("❌ WiFi Error");
      display.println("Reintentando...");
      display.display();
      
      delay(10000);
      conectarWiFi();
    }
  }

  // ============================================================
  //  FUNCIÓN: TEST DE CONEXIÓN AL SERVIDOR
  // ============================================================

  void testServerConnection() {
    Serial.println("🔍 Probando conexión al servidor...");
    
    WiFiClient client;
    HTTPClient http;
    
    http.begin(client, "http://192.168.1.101:8080/");
    http.setTimeout(3000);
    
    int code = http.GET();
    Serial.print("📥 Código de respuesta GET raíz: ");
    Serial.println(code);
    
    if (code > 0) {
      Serial.println("✅ El servidor responde correctamente");
    } else {
      Serial.print("❌ Error al conectar: ");
      Serial.println(http.errorToString(code));
    }
    
    http.end();
  }

  // ============================================================
  //  FUNCIÓN: LED RGB
  // ============================================================

  void setColorRGB(int r, int g, int b) {
    r = constrain(r, 0, 255);
    g = constrain(g, 0, 255);
    b = constrain(b, 0, 255);
    
    analogWrite(LED_R, r);
    analogWrite(LED_G, g);
    analogWrite(LED_B, b);
  }

  // ============================================================
  //  FUNCIÓN: LEER SENSORES
  // ============================================================

  void leerSensores() {
    // 1. Leer DHT11 (Temperatura y Humedad)
    float temp = dht.readTemperature();
    float hum = dht.readHumidity();
    
    if (!isnan(temp) && temp > -10 && temp < 60) {
      temperatura = temp;
    } else {
      Serial.println("⚠️ Error lectura DHT11 (Temperatura)");
    }
    
    if (!isnan(hum) && hum >= 0 && hum <= 100) {
      humedad = hum;
    } else {
      Serial.println("⚠️ Error lectura DHT11 (Humedad)");
    }
    
    // 2. Leer KY-037 (Sonido) - Valor analógico 0-4095
    ruido = analogRead(SONIDO_PIN);
    
    // 3. Leer SW-420 (Movimiento) - HIGH = movimiento detectado
    int lecturaMov = digitalRead(SW420_PIN);
    movimiento = (lecturaMov == HIGH) ? 1 : 0;
  }

  // ============================================================
  //  FUNCIÓN: CALCULAR ICS
  // ============================================================

  void calcularICS() {
    float puntaje = 0;
    int factores = 0;
    
    // 1. Temperatura (18-22°C ideal)
    if (temperatura >= 18 && temperatura <= 22) {
      puntaje += 25;
    } else if (temperatura >= 16 && temperatura <= 24) {
      puntaje += 15;
    } else {
      puntaje += 5;
    }
    factores++;
    
    // 2. Humedad (40-60% ideal)
    if (humedad >= 40 && humedad <= 60) {
      puntaje += 25;
    } else if (humedad >= 30 && humedad <= 70) {
      puntaje += 15;
    } else {
      puntaje += 5;
    }
    factores++;
    
    // 3. Ruido (< 500 = silencio)
    if (ruido < 500) {
      puntaje += 25;
    } else if (ruido < 1500) {
      puntaje += 15;
    } else {
      puntaje += 5;
    }
    factores++;
    
    // 4. Movimiento (0 = sin movimiento = ideal)
    if (movimiento == 0) {
      puntaje += 25;
    } else {
      puntaje += 10;
    }
    factores++;
    
    if (factores > 0) {
      indiceSueno = (int)((puntaje / factores) * 4);
      indiceSueno = constrain(indiceSueno, 0, 100);
    } else {
      indiceSueno = 50;
    }
  }

  // ============================================================
  //  FUNCIÓN: ACTUALIZAR LED
  // ============================================================

  void actualizarLED() {
    if (indiceSueno >= 80) {
      setColorRGB(0, 255, 0);   // 🟢 Verde - Excelente
    } else if (indiceSueno >= 60) {
      setColorRGB(255, 255, 0); // 🟡 Amarillo - Regular
    } else {
      setColorRGB(255, 0, 0);   // 🔴 Rojo - Deficiente
    }
  }

  // ============================================================
  //  FUNCIÓN: ACTUALIZAR OLED
  // ============================================================

  void actualizarOLED() {
    display.clearDisplay();
    display.setTextSize(1);
    display.setTextColor(SSD1306_WHITE);
    
    display.setCursor(0, 0);
    display.println("🌙 SueñoSmart IoT");
    display.drawLine(0, 10, 128, 10, SSD1306_WHITE);
    
    display.setCursor(0, 14);
    display.print("🌡️ ");
    display.print(temperatura, 1);
    display.println(" °C");
    
    display.print("💧 ");
    display.print(humedad, 1);
    display.println(" %");
    
    display.print("🔊 ");
    display.print(ruido);
    display.println("");
    
    display.print("🌀 ");
    display.println(movimiento ? "SI" : "NO");
    
    display.setTextSize(2);
    display.setCursor(0, 48);
    display.print("ICS: ");
    display.print(indiceSueno);
    display.print("/100");
    
    display.display();
  }

  // ============================================================
  //  FUNCIÓN: MOSTRAR MENSAJE EN OLED
  // ============================================================

  void mostrarMensajeOLED(String linea1, String linea2 = "") {
    display.clearDisplay();
    display.setTextSize(1);
    display.setTextColor(SSD1306_WHITE);
    display.setCursor(0, 0);
    display.println(linea1);
    if (linea2 != "") {
      display.println(linea2);
    }
    display.display();
  }

  // ============================================================
  //  FUNCIÓN: ENVIAR DATOS AL SERVIDOR (CORREGIDA)
  // ============================================================

  void enviarDatosServidor() {
    // Verificar conexión WiFi
    if (WiFi.status() != WL_CONNECTED) {
      Serial.println("⚠️ WiFi desconectado, reconectando...");
      mostrarMensajeOLED("⚠️ WiFi Error", "Reconectando...");
      conectarWiFi();
      return;
    }

    // Crear documento JSON
    StaticJsonDocument<200> doc;
    doc["temperatura"] = temperatura;
    doc["humedad"] = humedad;
    doc["ruido"] = ruido;
    doc["movimiento"] = movimiento;
    doc["indice_sueno"] = indiceSueno;
    
    String payload;
    serializeJson(doc, payload);

    // Crear conexión HTTP
    WiFiClient client;
    HTTPClient http;
    
    http.begin(client, serverName);
    http.addHeader("Content-Type", "application/json");
    
    // ✅ AUMENTAR TIMEOUT a 10 segundos
    http.setTimeout(10000);

    Serial.println();
    Serial.println("========== ENVIANDO DATOS ==========");
    Serial.println("📤 Payload:");
    Serial.println(payload);

    mostrarMensajeOLED("📤 Enviando...", "ICS: " + String(indiceSueno));

    // Medir tiempo de respuesta
    unsigned long startTime = millis();
    int httpResponseCode = http.POST(payload);
    unsigned long endTime = millis();

    Serial.print("📥 Código HTTP: ");
    Serial.println(httpResponseCode);
    Serial.print("⏱️ Tiempo de respuesta: ");
    Serial.print((endTime - startTime) / 1000.0);
    Serial.println(" segundos");

    if (httpResponseCode > 0) {
      String respuesta = http.getString();
      Serial.println("📥 Respuesta del servidor:");
      Serial.println(respuesta);
      
      if (httpResponseCode == 200 || httpResponseCode == 201) {
        Serial.println("✅ Datos enviados correctamente");
        fallosConsecutivos = 0;
        mostrarMensajeOLED("✅ Datos enviados", "ICS: " + String(indiceSueno));
        delay(1000);
      } else {
        Serial.println("⚠️ Error en el servidor");
        fallosConsecutivos++;
        mostrarMensajeOLED("⚠️ Error Servidor", "Código: " + String(httpResponseCode));
        delay(1000);
      }
    } else {
      Serial.print("❌ Error en POST: ");
      Serial.println(http.errorToString(httpResponseCode));
      
      // Manejo específico de errores
      if (httpResponseCode == -11) {
        Serial.println("⚠️ Timeout: El servidor tardó demasiado en responder");
        Serial.println("💡 Sugerencia: Verifica que el servidor esté funcionando");
        mostrarMensajeOLED("⏱️ Timeout", "Servidor lento");
      } else if (httpResponseCode == -1) {
        Serial.println("⚠️ Error de conexión: No se pudo conectar al servidor");
        mostrarMensajeOLED("❌ Conexión", "Servidor no disponible");
      } else {
        mostrarMensajeOLED("❌ Error HTTP", http.errorToString(httpResponseCode));
      }
      
      fallosConsecutivos++;
      delay(2000);
    }

    http.end();
    Serial.println("=====================================");
    Serial.println();
  }

  // ============================================================
  //  FUNCIÓN: MOSTRAR DATOS EN SERIAL
  // ============================================================

  void mostrarDatosSerial() {
    Serial.println("========== LECTURA DE SENSORES ==========");
    Serial.print("🌡️ Temperatura: ");
    Serial.print(temperatura, 1);
    Serial.println(" °C");
    
    Serial.print("💧 Humedad: ");
    Serial.print(humedad, 1);
    Serial.println(" %");
    
    Serial.print("🔊 Ruido: ");
    Serial.print(ruido);
    Serial.println(" (0-4095)");
    
    Serial.print("🌀 Movimiento: ");
    Serial.println(movimiento ? "SI" : "NO");
    
    Serial.print("⭐ ICS: ");
    Serial.print(indiceSueno);
    Serial.println("/100");
    
    Serial.print("📊 Calidad: ");
    if (indiceSueno >= 80) {
      Serial.println("Excelente 🟢");
    } else if (indiceSueno >= 60) {
      Serial.println("Regular 🟡");
    } else {
      Serial.println("Deficiente 🔴");
    }
    Serial.println("=========================================");
    Serial.println();
  }

  // ============================================================
  //  FUNCIÓN: COMANDOS POR SERIAL
  // ============================================================

  void procesarComandosSerial() {
    if (!Serial.available()) return;
    
    String comando = Serial.readStringUntil('\n');
    comando.trim();
    comando.toLowerCase();
    
    if (comando == "info") {
      mostrarDatosSerial();
    } else if (comando == "test") {
      testServerConnection();
    } else if (comando == "led off") {
      setColorRGB(0, 0, 0);
      Serial.println("🔴 LED apagado");
      mostrarMensajeOLED("🔴 LED OFF", "");
    } else if (comando == "led green") {
      setColorRGB(0, 255, 0);
      Serial.println("🟢 LED verde");
      mostrarMensajeOLED("🟢 LED Verde", "");
    } else if (comando == "led yellow") {
      setColorRGB(255, 255, 0);
      Serial.println("🟡 LED amarillo");
      mostrarMensajeOLED("🟡 LED Amarillo", "");
    } else if (comando == "led red") {
      setColorRGB(255, 0, 0);
      Serial.println("🔴 LED rojo");
      mostrarMensajeOLED("🔴 LED Rojo", "");
    } else if (comando == "help") {
      Serial.println();
      Serial.println("========== COMANDOS DISPONIBLES ==========");
      Serial.println("  info        - Mostrar datos de sensores");
      Serial.println("  test        - Probar conexión al servidor");
      Serial.println("  led off     - Apagar LED");
      Serial.println("  led green   - LED verde");
      Serial.println("  led yellow  - LED amarillo");
      Serial.println("  led red     - LED rojo");
      Serial.println("  reset       - Reiniciar ESP32");
      Serial.println("  help        - Mostrar esta ayuda");
      Serial.println("==========================================");
      Serial.println();
      mostrarMensajeOLED("📋 HELP", "Comandos en Serial");
      delay(1500);
    } else if (comando == "reset") {
      Serial.println("🔄 Reiniciando ESP32...");
      mostrarMensajeOLED("🔄 Reiniciando...", "");
      delay(1000);
      ESP.restart();
    }
  }

  // ============================================================
  //  SETUP
  // ============================================================

  void setup() {
    Serial.begin(115200);
    delay(1000);

    Serial.println();
    Serial.println("========================================");
    Serial.println("  🌙 SISTEMA DE MONITOREO DEL SUEÑO");
    Serial.println("  Versión 1.0.1 - CORREGIDO");
    Serial.println("========================================");
    Serial.println();

    // ============================================================
    //  INICIALIZAR OLED
    // ============================================================
    
    Wire.begin(OLED_SDA, OLED_SCL);
    
    if (!display.begin(SSD1306_SWITCHCAPVCC, OLED_ADDRESS)) {
      Serial.println("❌ Error: OLED no encontrado");
      Serial.println("⚠️ Verifica las conexiones del OLED");
      for (;;);
    }
    
    display.clearDisplay();
    display.setTextSize(1);
    display.setTextColor(SSD1306_WHITE);
    display.setCursor(0, 0);
    display.println("🌙 SueñoSmart IoT");
    display.println("Iniciando...");
    display.display();
    
    Serial.println("✅ OLED inicializado");

    // ============================================================
    //  CONFIGURAR PINES
    // ============================================================
    
    pinMode(SW420_PIN, INPUT);
    pinMode(LED_R, OUTPUT);
    pinMode(LED_G, OUTPUT);
    pinMode(LED_B, OUTPUT);

    dht.begin();

    // Secuencia de inicio
    Serial.println("🔵 Iniciando sistema...");
    setColorRGB(0, 0, 255);
    mostrarMensajeOLED("🔵 Iniciando...", "SueñoSmart IoT");
    delay(1000);
    
    // Conectar WiFi
    conectarWiFi();

    // Probar conexión al servidor
    if (wifiConectado) {
      testServerConnection();
    }

    // Secuencia completada
    setColorRGB(0, 255, 0);
    mostrarMensajeOLED("✅ Sistema Listo", "ICS: --/100");
    delay(500);
    
    Serial.println();
    Serial.println("✅ Sistema listo");
    Serial.println("========================================");
    Serial.println();
    Serial.println("📋 Escribe 'help' para ver los comandos disponibles");
    Serial.println("📋 Escribe 'test' para probar la conexión al servidor");
    Serial.println();
  }

  // ============================================================
  //  LOOP PRINCIPAL
  // ============================================================

  void loop() {
    // 1. Procesar comandos por Serial
    procesarComandosSerial();

    // 2. Verificar y reconectar WiFi si es necesario
    if (WiFi.status() != WL_CONNECTED) {
      Serial.println("⚠️ WiFi desconectado, reconectando...");
      mostrarMensajeOLED("⚠️ WiFi Error", "Reconectando...");
      conectarWiFi();
    }

    // 3. Si hay demasiados fallos, aumentar el delay
    if (fallosConsecutivos >= maxFallos) {
      Serial.println("⚠️ Demasiados fallos consecutivos");
      Serial.println("⏳ Esperando 30 segundos antes de reintentar...");
      mostrarMensajeOLED("⏳ Esperando...", "Reintento en 30s");
      delay(30000);
      fallosConsecutivos = 0;
    }

    // 4. Lectura de sensores y envío cada 15 segundos
    if ((millis() - lastTime) > timerDelay) {
      leerSensores();
      calcularICS();
      actualizarLED();
      actualizarOLED();
      mostrarDatosSerial();
      enviarDatosServidor();
      lastTime = millis();
    }

    delay(100);
  }