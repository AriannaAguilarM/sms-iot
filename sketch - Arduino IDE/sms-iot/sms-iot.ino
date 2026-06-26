/*
 * ============================================================
 * PROYECTO: Sistema Inteligente de Monitoreo del Sueño IoT
 * VERSIÓN: 1.0.2 - CORREGIDO PARA CZN15E
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
//  CONFIGURACIÓN DE PINES
// ============================================================

#define OLED_SDA 21
#define OLED_SCL 22
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define OLED_ADDRESS 0x3C

#define PIN_DHT 4
#define SONIDO_PIN 34      // CZN15E (AO)
#define SW420_PIN 27

#define LED_R 25
#define LED_G 26
#define LED_B 33

#define DHTTYPE DHT11
DHT dht(PIN_DHT, DHTTYPE);
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);

// ============================================================
//  CONFIGURACIÓN WIFI
// ============================================================

const char* ssid = "INFINITUM1329_2.4_EXT";
const char* password = "FrZfvJJkKg";
String serverName = "http://192.168.1.101:8080/api/lecturas";

unsigned long lastTime = 0;
const unsigned long timerDelay = 15000;

// ============================================================
//  VARIABLES GLOBALES
// ============================================================

float temperatura = 0;
float humedad = 0;
int ruido = 0;           // En dB (0-100)
int movimiento = 0;
int indiceSueno = 0;
bool wifiConectado = false;
int fallosConsecutivos = 0;
const int maxFallos = 5;

// ============================================================
//  FUNCIONES
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
    display.clearDisplay();
    display.setCursor(0, 0);
    display.println("❌ WiFi Error");
    display.println("Reintentando...");
    display.display();
    delay(10000);
    conectarWiFi();
  }
}

void setColorRGB(int r, int g, int b) {
  r = constrain(r, 0, 255);
  g = constrain(g, 0, 255);
  b = constrain(b, 0, 255);
  analogWrite(LED_R, r);
  analogWrite(LED_G, g);
  analogWrite(LED_B, b);
}

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
//  FUNCIÓN: LEER SENSORES (CORREGIDA PARA CZN15E)
// ============================================================

void leerSensores() {
  // 1. Leer DHT11
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
  
  // ============================================================
  // 2. Leer CZN15E (SONIDO) - CON MAPEO MEJORADO
  // ============================================================
  
  int ruidoRaw = analogRead(SONIDO_PIN);
  
  // ✅ MAPEO PARA CZN15E
  // El CZN15E tiene menos sensibilidad, usamos mapeo exponencial
  float ruidoPorcentaje = (ruidoRaw / 4095.0) * 100.0;
  
  // Aplicar curva de sensibilidad (exponencial)
  // Para que ruidos bajos se detecten mejor
  ruido = (int)(ruidoPorcentaje * ruidoPorcentaje / 100.0);
  
  // Limitar a 0-100
  ruido = constrain(ruido, 0, 100);
  
  // Depuración
  Serial.print("🔊 Raw: ");
  Serial.print(ruidoRaw);
  Serial.print(" → ");
  Serial.print(ruido);
  Serial.println(" dB");
  
  // 3. Leer SW-420 (Movimiento)
  int lecturaMov = digitalRead(SW420_PIN);
  movimiento = (lecturaMov == HIGH) ? 1 : 0;
}

// ============================================================
//  FUNCIÓN: CALCULAR ICS (CORREGIDA)
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
  
  // 3. Ruido (AJUSTADO PARA CZN15E)
  // Ahora ruido está en dB (0-100) con mejor sensibilidad
  if (ruido < 10) {
    puntaje += 25;  // Silencio
  } else if (ruido < 30) {
    puntaje += 20;  // Muy bajo
  } else if (ruido < 50) {
    puntaje += 15;  // Moderado
  } else if (ruido < 70) {
    puntaje += 8;   // Alto
  } else {
    puntaje += 3;   // Muy alto
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

void actualizarLED() {
  if (indiceSueno >= 80) {
    setColorRGB(0, 255, 0);
  } else if (indiceSueno >= 60) {
    setColorRGB(255, 255, 0);
  } else {
    setColorRGB(255, 0, 0);
  }
}

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
  display.println(" dB");
  
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
//  FUNCIÓN: ENVIAR DATOS AL SERVIDOR
// ============================================================

void enviarDatosServidor() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("⚠️ WiFi desconectado, reconectando...");
    mostrarMensajeOLED("⚠️ WiFi Error", "Reconectando...");
    conectarWiFi();
    return;
  }

  StaticJsonDocument<200> doc;
  doc["temperatura"] = temperatura;
  doc["humedad"] = humedad;
  doc["ruido"] = ruido;        // ✅ Ahora en dB (0-100)
  doc["movimiento"] = movimiento;
  doc["indice_sueno"] = indiceSueno;
  
  String payload;
  serializeJson(doc, payload);

  WiFiClient client;
  HTTPClient http;
  
  http.begin(client, serverName);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(10000);

  Serial.println();
  Serial.println("========== ENVIANDO DATOS ==========");
  Serial.println("📤 Payload:");
  Serial.println(payload);

  mostrarMensajeOLED("📤 Enviando...", "ICS: " + String(indiceSueno));

  int httpResponseCode = http.POST(payload);

  Serial.print("📥 Código HTTP: ");
  Serial.println(httpResponseCode);

  if (httpResponseCode > 0) {
    String respuesta = http.getString();
    Serial.println("📥 Respuesta del servidor:");
    Serial.println(respuesta);
    
    if (httpResponseCode == 200 || httpResponseCode == 201) {
      Serial.println("✅ Datos enviados correctamente");
      fallosConsecutivos = 0;
      mostrarMensajeOLED("✅ Datos enviados", "ICS: " + String(indiceSueno));
    } else {
      Serial.println("⚠️ Error en el servidor");
      fallosConsecutivos++;
    }
  } else {
    Serial.print("❌ Error en POST: ");
    Serial.println(http.errorToString(httpResponseCode));
    fallosConsecutivos++;
    mostrarMensajeOLED("❌ Error HTTP", String(httpResponseCode));
  }

  http.end();
  Serial.println("=====================================");
  Serial.println();
}

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
  Serial.println(" dB");
  Serial.print("🌀 Movimiento: ");
  Serial.println(movimiento ? "SI" : "NO");
  Serial.print("⭐ ICS: ");
  Serial.print(indiceSueno);
  Serial.println("/100");
  Serial.println("=========================================");
  Serial.println();
}

void procesarComandosSerial() {
  if (!Serial.available()) return;
  
  String comando = Serial.readStringUntil('\n');
  comando.trim();
  comando.toLowerCase();
  
  if (comando == "info") {
    mostrarDatosSerial();
  } else if (comando == "test") {
    enviarDatosServidor();
  } else if (comando == "help") {
    Serial.println("Comandos: info, test, reset, help");
  } else if (comando == "reset") {
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
  Serial.println("  Versión 1.0.2 - CZN15E");
  Serial.println("========================================");

  Wire.begin(OLED_SDA, OLED_SCL);
  
  if (!display.begin(SSD1306_SWITCHCAPVCC, OLED_ADDRESS)) {
    Serial.println("❌ Error: OLED no encontrado");
    for (;;);
  }
  
  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("🌙 SueñoSmart IoT");
  display.println("Iniciando...");
  display.display();
  
  Serial.println("✅ OLED inicializado");

  pinMode(SW420_PIN, INPUT);
  pinMode(LED_R, OUTPUT);
  pinMode(LED_G, OUTPUT);
  pinMode(LED_B, OUTPUT);

  dht.begin();

  setColorRGB(0, 0, 255);
  mostrarMensajeOLED("🔵 Iniciando...", "SueñoSmart IoT");
  delay(1000);
  
  conectarWiFi();

  setColorRGB(0, 255, 0);
  mostrarMensajeOLED("✅ Sistema Listo", "ICS: --/100");
  delay(500);
  
  Serial.println();
  Serial.println("✅ Sistema listo");
  Serial.println("========================================");
  Serial.println("📋 Comandos: info, test, help");
  Serial.println();
}

// ============================================================
//  LOOP
// ============================================================

void loop() {
  procesarComandosSerial();

  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("⚠️ WiFi desconectado, reconectando...");
    conectarWiFi();
  }

  if (fallosConsecutivos >= maxFallos) {
    Serial.println("⚠️ Demasiados fallos. Esperando 30s...");
    delay(30000);
    fallosConsecutivos = 0;
  }

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