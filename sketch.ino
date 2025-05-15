#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>
#include <PZEM004Tv30.h>
#include <HardwareSerial.h>

// ==== WiFi Setup ====
const char* ssid = "Playmedia2";
const char* password = "123456899";
const char* serverUrl = "http://192.168.18.207/post_data.php";

// ==== DHT22 Setup ====
#define DHTPIN 15
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

// ==== MQ-2 Setup ====
#define MQ2_PIN 34 // Analog pin

// ==== Flame Sensor ====
#define FLAME_PIN 33 // Digital pin

// ==== PZEM Setup (Serial2) ====
HardwareSerial pzemSerial(2); // UART2
PZEM004Tv30 pzem(pzemSerial, 16, 17); // RX, TX

void setup() {
  Serial.begin(115200);
  dht.begin();

  pinMode(FLAME_PIN, INPUT);

  // WiFi Connect
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500); Serial.print(".");
  }
  Serial.println("\nConnected to WiFi");
}

void loop() {
  // === Baca Sensor ===
  float temperature = dht.readTemperature();
  float humidity = dht.readHumidity();
  int gasLevel = analogRead(MQ2_PIN);
  int flameDetected = digitalRead(FLAME_PIN);

  float voltage = pzem.voltage();
  float current = pzem.current();
  float power   = pzem.power();
  float energy  = pzem.energy();

  // Tangani NaN jika listrik loss
  if (isnan(voltage)) voltage = 0.0;
  if (isnan(current)) current = 0.0;
  if (isnan(power))   power   = 0.0;
  if (isnan(energy))  energy  = 0.0;

  Serial.println("Data:");
  Serial.printf("Temp: %.2f °C, Hum: %.2f %%, Gas: %d, Flame: %d\n", temperature, humidity, gasLevel, flameDetected);
  Serial.printf("Volt: %.2f V, Amp: %.2f A, Power: %.2f W, Energy: %.2f Wh\n", voltage, current, power, energy);

  // === Kirim ke Server via HTTP POST ===
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/json");

    // Buat JSON data
    String postData = "{";
    postData += "\"temperature\":" + String(temperature, 2) + ",";
    postData += "\"humidity\":" + String(humidity, 2) + ",";
    postData += "\"gas_level\":" + String(gasLevel) + ",";
    postData += "\"flame\":" + String(flameDetected) + ",";
    postData += "\"voltage\":" + String(voltage, 2) + ",";
    postData += "\"current\":" + String(current, 2) + ",";
    postData += "\"power\":" + String(power, 2) + ",";
    postData += "\"energy\":" + String(energy, 2);
    postData += "}";

    int httpResponseCode = http.POST(postData);
    if (httpResponseCode > 0) {
      Serial.print("Data sent, response: ");
      Serial.println(http.getString());
    } else {
      Serial.print("Error sending data: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  } else {
    Serial.println("WiFi not connected, retrying...");
  }

  delay(1000); // Kirim data tiap 1 detik
}
