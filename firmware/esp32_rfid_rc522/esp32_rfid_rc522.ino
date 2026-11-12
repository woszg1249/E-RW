/*
 =========================================================================================
  E-RW DIGITAL - FIRMWARE ESP32 + RFID RC-522 CARD READER
 =========================================================================================
  Deskripsi:
  Membaca UID dari kartu RFID / e-KTP / Tag 13.56 MHz menggunakan modul MFRC522 dan ESP32,
  kemudian mengirimkan UID tersebut secara HTTP POST JSON ke endpoint server E-RW:
  https://e-rw.unique-monsters.my.id/api/iot/rfid/tap
  
  Wiring ESP32 ke RC-522 (Tegangan Wajib 3.3V! Jangan pakai 5V):
  +------------------+------------------+
  | Pin RC-522       | Pin ESP32        |
  +------------------+------------------+
  | VCC              | 3.3V (Wajib!)    |
  | RST (Reset)      | GPIO 22          |
  | GND              | GND              |
  | MISO             | GPIO 19          |
  | MOSI             | GPIO 23          |
  | SCK              | GPIO 18          |
  | SDA / SS         | GPIO 5           |
  | Buzzer (+)       | GPIO 4  (Opsional)|
  | LED Hijau (+)    | GPIO 2  (Opsional)|
  +------------------+------------------+

  Library yang Dibutuhkan di Arduino IDE:
  1. MFRC522 by GithubCommunity (install via Tools -> Manage Libraries)
  2. ArduinoJson by Benoit Blanchon (opsional untuk parse respons, atau pakai String bawaan)
 =========================================================================================
*/

#include <WiFi.h>
#include <HTTPClient.h>
#include <WiFiClientSecure.h>
#include <SPI.h>
#include <MFRC522.h>

// ========== KONFIGURASI WIFI & SERVER ==========
const char* WIFI_SSID     = "NAMA_WIFI_ANDA";       // Ganti dengan SSID WiFi Anda
const char* WIFI_PASSWORD = "PASSWORD_WIFI_ANDA";   // Ganti dengan Password WiFi Anda

// URL Endpoint Backend E-RW
const char* SERVER_URL    = "https://e-rw.unique-monsters.my.id/api/iot/rfid/tap";
const char* DEVICE_ID     = "ESP32-GERBANG-RW19";    // Identitas perangkat scanner

// ========== PINOUT DEFINITIONS ==========
#define SS_PIN    5   // Pin SDA / SS RC522
#define RST_PIN   22  // Pin Reset RC522
#define BUZZER_PIN 4  // Pin Buzzer (opsional, set -1 jika tidak pakai)
#define LED_PIN    2  // On-board LED ESP32

MFRC522 rfid(SS_PIN, RST_PIN);

// Variabel pencegah multi-tap terlalu cepat (debounce)
String lastUidScanned = "";
unsigned long lastScanTime = 0;
const unsigned long SCAN_COOLDOWN_MS = 2500; // 2.5 detik jeda antar tap kartu yang sama

// Fungsi bunyi buzzer singkat
void beep(int durationMs, int count = 1) {
  if (BUZZER_PIN < 0) return;
  for (int i = 0; i < count; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(durationMs);
    digitalWrite(BUZZER_PIN, LOW);
    if (count > 1 && i < count - 1) delay(80);
  }
}

void setup() {
  Serial.begin(115200);
  delay(1000);
  Serial.println("\n\n========================================");
  Serial.println("  E-RW RFID RC-522 SCANNER - ESP32     ");
  Serial.println("========================================");

  // Inisialisasi Pin LED & Buzzer
  pinMode(LED_PIN, OUTPUT);
  digitalWrite(LED_PIN, LOW);
  if (BUZZER_PIN >= 0) {
    pinMode(BUZZER_PIN, OUTPUT);
    digitalWrite(BUZZER_PIN, LOW);
  }

  // Inisialisasi SPI Bus & MFRC522
  SPI.begin();
  rfid.PCD_Init();
  delay(100);
  rfid.PCD_DumpVersionToSerial();
  Serial.println("[RFID] Sensor RC-522 Siap Digunakan!");

  // Koneksi ke Jaringan WiFi
  Serial.print("[WiFi] Menyambungkan ke: ");
  Serial.println(WIFI_SSID);
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  int retryCount = 0;
  while (WiFi.status() != WL_CONNECTED && retryCount < 40) {
    delay(500);
    Serial.print(".");
    digitalWrite(LED_PIN, !digitalRead(LED_PIN));
    retryCount++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    digitalWrite(LED_PIN, HIGH); // LED Menyala menandakan WiFi online
    Serial.println("\n[WiFi] Terhubung Berhasil!");
    Serial.print("[WiFi] IP Address: ");
    Serial.println(WiFi.localIP());
    beep(100, 2); // 2x beep tanda siap
  } else {
    Serial.println("\n[WiFi] Gagal terhubung ke WiFi! Periksa SSID & Password.");
  }
}

void loop() {
  // Reconnect WiFi otomatis jika terputus
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("[WiFi] Koneksi terputus, mencoba reconnect...");
    WiFi.reconnect();
    delay(2000);
    return;
  }

  // Cek apakah ada kartu baru yang didekatkan
  if (!rfid.PICC_IsNewCardPresent()) {
    delay(50);
    return;
  }

  // Baca serial kartu
  if (!rfid.PICC_ReadCardSerial()) {
    delay(50);
    return;
  }

  // Konversi UID Byte Array ke format HEX String (UPPERCASE tanpa separator)
  String uidString = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) {
      uidString += "0";
    }
    uidString += String(rfid.uid.uidByte[i], HEX);
  }
  uidString.toUpperCase();

  // Debounce: jika kartu yang sama ditempelkan dalam rentang < 2.5 detik, abaikan
  unsigned long now = millis();
  if (uidString == lastUidScanned && (now - lastScanTime < SCAN_COOLDOWN_MS)) {
    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();
    return;
  }

  lastUidScanned = uidString;
  lastScanTime = now;

  Serial.println("\n----------------------------------------");
  Serial.print("[TAP] Kartu Dideteksi! UID: ");
  Serial.println(uidString);

  // Indikasi suara / LED saat kartu ditempel
  beep(80, 1);

  // Kirim data UID ke Server E-RW
  kirimDataKeServer(uidString);

  // Hentikan komunikasi dengan kartu saat ini
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
  delay(200);
}

// Fungsi pengiriman HTTP POST JSON ke server E-RW
void kirimDataKeServer(String uid) {
  WiFiClientSecure client;
  client.setInsecure(); // Mengabaikan verifikasi sertifikat SSL untuk kemudahan ESP32

  HTTPClient http;
  Serial.print("[HTTP] Menghubungi: ");
  Serial.println(SERVER_URL);

  if (http.begin(client, SERVER_URL)) {
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");
    http.setTimeout(10000);

    // Siapkan Payload JSON
    String jsonPayload = "{\"rfid_uid\":\"" + uid + "\",\"device_id\":\"" + DEVICE_ID + "\"}";
    Serial.print("[HTTP] Payload: ");
    Serial.println(jsonPayload);

    int httpCode = http.POST(jsonPayload);
    Serial.print("[HTTP] Response Code: ");
    Serial.println(httpCode);

    if (httpCode > 0) {
      String response = http.getString();
      Serial.print("[HTTP] Response Body: ");
      Serial.println(response);

      if (httpCode == 200) {
        if (response.indexOf("\"registered\":true") >= 0) {
          Serial.println("[BERHASIL] Kartu TERDAFTAR milik warga!");
          beep(60, 2); // 2x beep tanda terdaftar
        } else {
          Serial.println("[INFO] Kartu BELUM TERDAFTAR. Silakan hubungkan di menu Data Warga.");
          beep(200, 1); // 1x beep panjang tanda belum terdaftar
        }
      }
    } else {
      Serial.printf("[HTTP] Gagal request, error: %s\n", http.errorToString(httpCode).c_str());
      beep(400, 1); // Beep error
    }

    http.end();
  } else {
    Serial.println("[HTTP] Tidak dapat menginisialisasi koneksi HTTP");
  }
}
