/*
 * ============================================================
 * PROYECTO: Sistema de Monitoreo del Sueño IoT
 * VERSIÓN: 1.0.0
 * ============================================================
 * 
 * SENSORES:
 * - DHT11 (Temperatura y Humedad) - GPIO4
 * - KY-038 (Sonido) - GPIO34 (AO)
 * - SW-420 (Vibración/Movimiento) - GPIO14
========================================================
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

// Sensores
#define PIN_DHT 4
#define KY038_PIN 34      // Sensor de sonido (AO)
#define SW420_PIN 14      // Sensor de vibración/movimiento

// LED RGB
#define LED_R 2
#define LED_G 15
#define LED_B 12

// OLED SSD1306 I2C
#define OLED_SDA 21
#define OLED_SCL 22
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define OLED_ADDRESS 0x3C

// ============================================================
//  CONFIGURACIÓN DE RED
// ============================================================

// WiFi
const char* WIFI_SSID = "TU_WIFI_SSID";
const char* WIFI_PASS = "TU_WIFI_PASSWORD";

// Servidor API
const char* API_URL = "http://192.168.1.100:8080/api/lecturas";
// CAMBIA LA IP POR LA DE TU SERVIDOR

// ============================================================
//  CONFIGURACIÓN DE SENSORES
// ============================================================

#define DHTTYPE DHT11
DHT dht(PIN_DHT, DHTTYPE);

// OLED
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);

// ============================================================
//  VARIABLES GLOBALES
// ============================================================

// Variables de sensores
float temperatura = 0;
float humedad = 0;
int ruido = 0;
int movimiento = 0;

// Índice de Calidad del Sueño (ICS)
int indiceSueno = 0;

// Estado del sistema
unsigned long lastSendTime = 0;
const unsigned long SEND_INTERVAL = 5000; // Enviar cada 5 segundos
bool wifiConnected = false;

// Variables para cálculos
float tempMin = 99, tempMax = -99;
float humMin = 99, humMax = -99;
int ruidoMax = 0;
int movimientosTotales = 0;
int contadorLecturas = 0;

// ============================================================
//  SETUP
// ============================================================

void setup() {
  Serial.begin(115200);
  
  // Inicializar OLED
  if (!display.begin(SSD1306_SWITCHCAPVCC, OLED_ADDRESS)) {
    Serial.println("❌ Error: OLED no encontrado");
    for (;;);
  }
  
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  display.setCursor(0, 0);
  display.println("SueñoSmart IoT");
  display.println("Iniciando...");
  display.display();
  delay(2000);
  
  // Configurar pines
  pinMode(SW420_PIN, INPUT);
  pinMode(LED_R, OUTPUT);
  pinMode(LED_G, OUTPUT);
  pinMode(LED_B, OUTPUT);
  
  // Inicializar DHT
  dht.begin();
  
  // Inicializar LED RGB
  setColorRGB(0, 0, 255); // Azul durante inicio
  delay(1000);
  
  // Conectar a WiFi
  conectarWiFi();
  
  // Mostrar estado inicial en OLED
  mostrarOLED("Inicializado", "Conectando...");
  
  Serial.println("========================================");
  Serial.println("  🌙 SISTEMA DE MONITOREO DEL SUEÑO");
  Serial.println("========================================");
  Serial.println("✅ Sistema iniciado");
  
  delay(500);
  setColorRGB(0, 255, 0); // Verde = listo
}

// ============================================================
//  LOOP PRINCIPAL
// ============================================================

void loop() {
  // 1. Leer sensores
  leerSensores();
  
  // 2. Calcular ICS
  calcularICS();
  
  // 3. Actualizar LED RGB
  actualizarLED();
  
  // 4. Actualizar OLED
  actualizarOLED();
  
  // 5. Enviar datos al servidor
  if (millis() - lastSendTime >= SEND_INTERVAL) {
    enviarDatosServidor();
    lastSendTime = millis();
  }
  
  delay(100);
}

// ============================================================
//  FUNCIONES DE SENSORES
// ============================================================

void leerSensores() {
  // Leer DHT11
  float temp = dht.readTemperature();
  float hum = dht.readHumidity();
  
  if (!isnan(temp) && temp > -10 && temp < 60) {
    temperatura = temp;
    
    // Actualizar estadísticas
    if (temp < tempMin) tempMin = temp;
    if (temp > tempMax) tempMax = temp;
  }
  
  if (!isnan(hum) && hum >= 0 && hum <= 100) {
    humedad = hum;
    
    if (hum < humMin) humMin = hum;
    if (hum > humMax) humMax = hum;
  }
  
  // Leer KY-038 (Sonido)
  // SYM-213 tiene salida analógica de 0-4095 (12 bits)
  ruido = analogRead(KY038_PIN);
  if (ruido > ruidoMax) ruidoMax = ruido;
  
  // Leer SW-420 (Vibración/Movimiento)
  // SW-420 entrega HIGH cuando detecta vibración
  int lecturaMov = digitalRead(SW420_PIN);
  if (lecturaMov == HIGH) {
    movimiento = 1;
    movimientosTotales++;
  } else {
    movimiento = 0;
  }
  
  contadorLecturas++;
  
  // Serial para depuración
  Serial.print("🌡️ Temp: "); Serial.print(temperatura);
  Serial.print("°C | 💧 Hum: "); Serial.print(humedad);
  Serial.print("% | 🔊 Ruido: "); Serial.print(ruido);
  Serial.print(" | 🌀 Mov: "); Serial.println(movimiento ? "SI" : "NO");
}

// ============================================================
//  CÁLCULO DEL ÍNDICE DE CALIDAD DEL SUEÑO (ICS)
// ============================================================

void calcularICS() {
  /*
   * Fórmula del ICS basada en el PSQI (Pittsburgh Sleep Quality Index)
   * Adaptada para datos objetivos del ESP32
   */
  
  float puntaje = 0;
  int factores = 0;
  
  // 1. Temperatura (18-22°C ideal)
  if (temperatura >= 18 && temperatura <= 22) {
    puntaje += 25;  // Excelente
  } else if (temperatura >= 16 && temperatura <= 24) {
    puntaje += 15;  // Aceptable
  } else {
    puntaje += 5;   // Deficiente
  }
  factores++;
  
  // 2. Humedad (40-60% ideal)
  if (humedad >= 40 && humedad <= 60) {
    puntaje += 25;  // Excelente
  } else if (humedad >= 30 && humedad <= 70) {
    puntaje += 15;  // Aceptable
  } else {
    puntaje += 5;   // Deficiente
  }
  factores++;
  
  // 3. Ruido (<30 dB ideal, usando valores analógicos)
  // Se asume que ruido < 800 es bajo (ajustable)
  if (ruido < 800) {
    puntaje += 25;  // Bajo ruido
  } else if (ruido < 1500) {
    puntaje += 15;  // Ruido moderado
  } else {
    puntaje += 5;   // Ruido alto
  }
  factores++;
  
  // 4. Movimiento (0 = sin movimiento, ideal)
  if (movimiento == 0) {
    puntaje += 25;  // Sin movimiento
  } else {
    puntaje += 10;  // Con movimiento
  }
  factores++;
  
  // Calcular promedio y escalar a 0-100
  if (factores > 0) {
    indiceSueno = (int)((puntaje / factores) * 4);
    indiceSueno = constrain(indiceSueno, 0, 100);
  } else {
    indiceSueno = 50;
  }
}

// ============================================================
//  FUNCIONES LED RGB
// ============================================================

void setColorRGB(int r, int g, int b) {
  r = constrain(r, 0, 255);
  g = constrain(g, 0, 255);
  b = constrain(b, 0, 255);
  
  analogWrite(LED_R, r);
  analogWrite(LED_G, g);
  analogWrite(LED_B, b);
}

void actualizarLED() {
  /*
   * Verde  → Sueño excelente (ICS >= 80)
   * Amarillo → Sueño regular (ICS 60-79)
   * Rojo   → Sueño deficiente (ICS < 60)
   */
  
  if (indiceSueno >= 80) {
    setColorRGB(0, 255, 0);   // Verde
  } else if (indiceSueno >= 60) {
    setColorRGB(255, 255, 0); // Amarillo
  } else {
    setColorRGB(255, 0, 0);   // Rojo
  }
}

// ============================================================
//  FUNCIONES OLED
// ============================================================

void mostrarOLED(String linea1, String linea2) {
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  display.setCursor(0, 0);
  display.println("🌙 SueñoSmart IoT");
  display.println("---------------");
  display.println(linea1);
  display.println(linea2);
  display.display();
}

void actualizarOLED() {
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  
  // Título
  display.setCursor(0, 0);
  display.println("🌙 SueñoSmart IoT");
  
  // Línea separadora
  display.drawLine(0, 10, 128, 10, SSD1306_WHITE);
  
  // Datos
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
  
  // ICS
  display.setTextSize(2);
  display.setCursor(0, 48);
  display.print("ICS: ");
  display.print(indiceSueno);
  display.print("/100");
  
  display.display();
}

// ============================================================
//  FUNCIONES WIFI
// ============================================================

void conectarWiFi() {
  Serial.print("📡 Conectando a WiFi...");
  
  // Mostrar en OLED
  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("📡 Conectando...");
  display.println(WIFI_SSID);
  display.display();
  
  WiFi.begin(WIFI_SSID, WIFI_PASS);
  
  int intentos = 0;
  while (WiFi.status() != WL_CONNECTED && intentos < 20) {
    delay(500);
    Serial.print(".");
    intentos++;
    
    // Animación en OLED
    display.setCursor(0, 30);
    display.print(".");
    for (int i = 0; i < intentos % 10; i++) display.print(".");
    display.display();
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    wifiConnected = true;
    Serial.println();
    Serial.println("✅ Conectado a WiFi");
    Serial.print("📡 IP: ");
    Serial.println(WiFi.localIP());
    
    mostrarOLED("✅ Conectado", "IP: " + WiFi.localIP().toString());
    delay(1000);
  } else {
    wifiConnected = false;
    Serial.println();
    Serial.println("❌ Error: No se pudo conectar a WiFi");
    mostrarOLED("❌ Error WiFi", "Reiniciando...");
    delay(3000);
    ESP.restart();
  }
}

// ============================================================
//  FUNCIONES DE ENVÍO DE DATOS (API REST)
// ============================================================

void enviarDatosServidor() {
  if (!wifiConnected || WiFi.status() != WL_CONNECTED) {
    Serial.println("⚠️ WiFi no disponible, reconectando...");
    conectarWiFi();
    return;
  }
  
  HTTPClient http;
  http.begin(API_URL);
  http.addHeader("Content-Type", "application/json");
  
  // Crear payload JSON
  StaticJsonDocument<200> doc;
  doc["temperatura"] = temperatura;
  doc["humedad"] = humedad;
  doc["ruido"] = ruido;
  doc["movimiento"] = movimiento;
  doc["indice_sueno"] = indiceSueno;
  
  String payload;
  serializeJson(doc, payload);
  
  Serial.println("📤 Enviando datos al servidor...");
  Serial.println(payload);
  
  int httpResponseCode = http.POST(payload);
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.print("📥 Respuesta: ");
    Serial.println(response);
    
    if (httpResponseCode == 200 || httpResponseCode == 201) {
      Serial.println("✅ Datos enviados correctamente");
    } else {
      Serial.println("⚠️ Error en el servidor: " + String(httpResponseCode));
    }
  } else {
    Serial.println("❌ Error HTTP: " + String(httpResponseCode));
  }
  
  http.end();
}

// ============================================================
//  FUNCIONES DE REPORTE
// ============================================================

void generarReporte() {
  Serial.println("========================================");
  Serial.println("  📊 REPORTE DE LA NOCHE");
  Serial.println("========================================");
  Serial.print("🌡️ Temperatura: ");
  Serial.print(tempMin); Serial.print("°C - ");
  Serial.print(tempMax); Serial.println("°C");
  
  Serial.print("💧 Humedad: ");
  Serial.print(humMin); Serial.print("% - ");
  Serial.print(humMax); Serial.println("%");
  
  Serial.print("🔊 Ruido máximo: ");
  Serial.println(ruidoMax);
  
  Serial.print("🌀 Movimientos totales: ");
  Serial.println(movimientosTotales);
  
  Serial.print("📊 ICS promedio: ");
  Serial.println(indiceSueno);
  
  Serial.print("📈 Total de lecturas: ");
  Serial.println(contadorLecturas);
  Serial.println("========================================");
}

// ============================================================
//  COMANDOS POR SERIAL
// ============================================================

void procesarComandoSerial() {
  if (!Serial.available()) return;
  
  String comando = Serial.readStringUntil('\n');
  comando.trim();
  comando.toLowerCase();
  
  if (comando == "reporte" || comando == "report") {
    generarReporte();
  } else if (comando == "led off") {
    setColorRGB(0, 0, 0);
  } else if (comando == "led green") {
    setColorRGB(0, 255, 0);
  } else if (comando == "led yellow") {
    setColorRGB(255, 255, 0);
  } else if (comando == "led red") {
    setColorRGB(255, 0, 0);
  } else if (comando == "help") {
    Serial.println("Comandos disponibles:");
    Serial.println("  reporte     - Mostrar reporte");
    Serial.println("  led off     - Apagar LED");
    Serial.println("  led green   - LED verde");
    Serial.println("  led yellow  - LED amarillo");
    Serial.println("  led red     - LED rojo");
    Serial.println("  reset       - Reiniciar estadísticas");
  } else if (comando == "reset") {
    tempMin = 99; tempMax = -99;
    humMin = 99; humMax = -99;
    ruidoMax = 0;
    movimientosTotales = 0;
    contadorLecturas = 0;
    Serial.println("✅ Estadísticas reiniciadas");
  }
}