/*
 =========================================================================================
  FOLDER 2: ESP32 PORTAL PERUMAHAN OTOMATIS (AKSES & PROTEKSI e-KTP)
 =========================================================================================
  Fungsi:
  - Dipasang di gerbang / portal perumahan (ESP32 + RC-522 + Servo / Relay + LED + Buzzer).
  - Saat warga menempelkan e-KTP:
    1. Mengirim UID ke server E-RW (POST /api/iot/rfid/tap).
    2. JIKA TERDAFTAR (HTTP 200 & "buka_portal":true):
       -> Lampu Hijau ON, Buzzer bunyi 2x singkat, Portal TERBUKA selama 4 detik lalu menutup.
    3. JIKA BELUM TERDAFTAR (HTTP 403 & "buka_portal":false):
       -> AKSES DITOLAK! Lampu Merah ON, Buzzer bunyi alarm 3x panjang, Portal TETAP TERKUNCI.
  - Dilengkapi Captive Portal Web Configurator (WiFi: "Portal Perumahan") untuk atur WiFi.
 =========================================================================================
*/

#include <WiFi.h>
#include <WebServer.h>
#include <DNSServer.h>
#include <Preferences.h>
#include <HTTPClient.h>
#include <WiFiClientSecure.h>
#include <SPI.h>
#include <MFRC522.h>

// ========== PINOUT PORTAL PERUMAHAN ==========
#define SS_PIN        5
#define RST_PIN       22
#define BUZZER_PIN    4
#define LED_BUILTIN_P 2
#define LED_HIJAU_PIN 26  // Indikator Akses Diterima (Portal Terbuka)
#define LED_MERAH_PIN 27  // Indikator Akses Ditolak (KTP Tidak Terdaftar)
#define RELAY_PORTAL  14  // Relay / Sinyal buka palang pintu (HIGH = Buka)
#define SERVO_PWM_PIN 13  // Opsional: Servo PWM (50Hz) untuk palang mini
#define BOOT_BTN      0

MFRC522 rfid(SS_PIN, RST_PIN);
Preferences prefs;
DNSServer dnsServer;
WebServer server(80);

const byte DNS_PORT = 53;
IPAddress apIP(192, 168, 4, 1);
IPAddress netMsk(255, 255, 255, 0);

String configSsid     = "Siti Aniroh-2G";
String configPass     = "ridzigoblokidiotmatiajalu";
String configEndpoint = "https://e-rw.unique-monsters.my.id/api/iot/rfid/tap";
String configDeviceId = "esp32-portal-gerbang-01";

bool inConfigMode = false;
String lastUidScanned = "";
unsigned long lastScanTime = 0;
const unsigned long SCAN_COOLDOWN_MS = 3000;

void beep(int durationMs, int count = 1) {
  if (BUZZER_PIN < 0) return;
  for (int i = 0; i < count; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(durationMs);
    digitalWrite(BUZZER_PIN, LOW);
    if (count > 1 && i < count - 1) delay(90);
  }
}

// Kontrol sudut servo sederhana via LEDC (kompatibel ESP32 Core 2.x & 3.x tanpa library tambahan)
void gerakkanPalangServo(int sudut) {
  int pulseUs = map(sudut, 0, 180, 500, 2400);
  // Software pulse 20x (400ms) agar kompatibel di semua versi ESP32 Arduino Core
  for (int i = 0; i < 20; i++) {
    digitalWrite(SERVO_PWM_PIN, HIGH);
    delayMicroseconds(pulseUs);
    digitalWrite(SERVO_PWM_PIN, LOW);
    delay(20 - (pulseUs / 1000));
  }
}

void bukaPortalPerumahan() {
  Serial.println("[PORTAL] >>> AKSES DITERIMA! Membuka Palang Pintu Perumahan...");
  digitalWrite(LED_MERAH_PIN, LOW);
  digitalWrite(LED_HIJAU_PIN, HIGH);
  digitalWrite(RELAY_PORTAL, HIGH);
  beep(80, 2);
  gerakkanPalangServo(90); // Buka palang 90 derajat

  delay(4000); // Portal terbuka selama 4 detik

  Serial.println("[PORTAL] <<< Menutup kembali Palang Pintu Perumahan.");
  gerakkanPalangServo(0);  // Tutup kembali ke 0 derajat
  digitalWrite(RELAY_PORTAL, LOW);
  digitalWrite(LED_HIJAU_PIN, LOW);
}

void tolakAksesPortal() {
  Serial.println("[PROTEKSI] XXX AKSES DITOLAK! KTP belum terdaftar di sistem E-RW.");
  digitalWrite(LED_HIJAU_PIN, LOW);
  digitalWrite(RELAY_PORTAL, LOW);
  digitalWrite(LED_MERAH_PIN, HIGH);
  beep(300, 3); // Alarm penolakan 3x panjang
  delay(1500);
  digitalWrite(LED_MERAH_PIN, LOW);
}

String getHtmlPage() {
  String html = R"rawliteral(
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Perumahan Config</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
    body { background: #f1f5f9; padding: 20px; display: flex; justify-content: center; }
    .card { background: #fff; border-radius: 18px; padding: 24px; width: 100%; max-width: 420px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
    h2 { color: #0f766e; text-align: center; margin-bottom: 16px; }
    label { display: block; font-size: 0.8rem; font-weight: 700; margin: 10px 0 4px; color: #475569; }
    input { width: 100%; padding: 10px; border: 1.5px solid #cbd5e1; border-radius: 8px; }
    button { width: 100%; padding: 12px; background: #0f766e; color: #fff; border: none; border-radius: 10px; font-weight: 700; margin-top: 16px; cursor: pointer; }
  </style>
</head>
<body>
  <div class="card">
    <h2>&#128679; Portal Perumahan E-RW</h2>
    <form method="POST" action="/save">
      <label>Nama WiFi / Hotspot HP:</label><input type="text" name="ssid" value=")rawliteral" + configSsid + R"rawliteral(" required>
      <label>Password WiFi / Hotspot:</label><input type="password" name="pass" value=")rawliteral" + configPass + R"rawliteral(">
      <label>URL API Verifikasi Portal:</label><input type="text" name="endpoint" value=")rawliteral" + configEndpoint + R"rawliteral(" required>
      <label>ID Gerbang / Device ID:</label><input type="text" name="device_id" value=")rawliteral" + configDeviceId + R"rawliteral(" required>
      <button type="submit">SIMPAN &amp; AKTIFKAN PORTAL</button>
    </form>
  </div>
</body>
</html>
)rawliteral";
  return html;
}

void handleRoot() { server.send(200, "text/html", getHtmlPage()); }
void handleRedirect() { server.sendHeader("Location", "http://192.168.4.1/", true); server.send(302, "text/plain", ""); }

void handleSave() {
  if (server.hasArg("ssid")) configSsid = server.arg("ssid");
  if (server.hasArg("pass")) configPass = server.arg("pass");
  if (server.hasArg("endpoint")) configEndpoint = server.arg("endpoint");
  if (server.hasArg("device_id")) configDeviceId = server.arg("device_id");

  prefs.begin("portal_cfg", false);
  prefs.putString("ssid", configSsid);
  prefs.putString("pass", configPass);
  prefs.putString("endpoint", configEndpoint);
  prefs.putString("devid", configDeviceId);
  prefs.end();

  server.send(200, "text/html", "<h3>Konfigurasi Portal Tersimpan! Rebooting...</h3>");
  delay(1200);
  ESP.restart();
}

void startCaptivePortal() {
  inConfigMode = true;
  WiFi.disconnect();
  WiFi.mode(WIFI_AP);
  WiFi.softAPConfig(apIP, apIP, netMsk);
  WiFi.softAP("Portal Perumahan");
  Serial.println("[PORTAL CONFIG] WiFi: Portal Perumahan -> http://192.168.4.1");

  dnsServer.setErrorReplyCode(DNSReplyCode::NoError);
  dnsServer.start(DNS_PORT, "*", apIP);
  server.on("/", handleRoot);
  server.on("/save", HTTP_POST, handleSave);
  server.on("/generate_204", handleRoot);
  server.on("/hotspot-detect.html", handleRoot);
  server.onNotFound(handleRedirect);
  server.begin();
  beep(80, 2);
}

void cekAksesPortal(String uid) {
  WiFiClientSecure client;
  client.setInsecure();
  HTTPClient http;

  if (http.begin(client, configEndpoint)) {
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");
    http.setTimeout(10000);

    String payload = "{\"rfid_uid\":\"" + uid + "\",\"device_id\":\"" + configDeviceId + "\"}";
    int httpCode = http.POST(payload);
    String response = (httpCode > 0) ? http.getString() : "";

    Serial.printf("[GERBANG] UID: %s | HTTP: %d\n", uid.c_str(), httpCode);
    Serial.println("[RESPON] " + response);

    if (httpCode == 200 && (response.indexOf("\"buka_portal\":true") >= 0 || response.indexOf("\"access_granted\":true") >= 0)) {
      bukaPortalPerumahan();
    } else {
      tolakAksesPortal();
    }
    http.end();
  }
}

void setup() {
  Serial.begin(115200);
  delay(800);
  pinMode(LED_BUILTIN_P, OUTPUT);
  pinMode(LED_HIJAU_PIN, OUTPUT);
  pinMode(LED_MERAH_PIN, OUTPUT);
  pinMode(RELAY_PORTAL, OUTPUT);
  pinMode(SERVO_PWM_PIN, OUTPUT);
  pinMode(BOOT_BTN, INPUT_PULLUP);
  if (BUZZER_PIN >= 0) { pinMode(BUZZER_PIN, OUTPUT); digitalWrite(BUZZER_PIN, LOW); }

  digitalWrite(LED_HIJAU_PIN, LOW);
  digitalWrite(LED_MERAH_PIN, LOW);
  digitalWrite(RELAY_PORTAL, LOW);
  gerakkanPalangServo(0);

  prefs.begin("portal_cfg", true);
  configSsid     = prefs.getString("ssid", configSsid);
  configPass     = prefs.getString("pass", configPass);
  configEndpoint = prefs.getString("endpoint", configEndpoint);
  configDeviceId = prefs.getString("devid", configDeviceId);
  prefs.end();

  SPI.begin();
  rfid.PCD_Init();
  rfid.PCD_DumpVersionToSerial();

  if (digitalRead(BOOT_BTN) == LOW || configSsid == "") {
    startCaptivePortal();
    return;
  }

  WiFi.mode(WIFI_STA);
  WiFi.begin(configSsid.c_str(), configPass.c_str());
  int retry = 0;
  while (WiFi.status() != WL_CONNECTED && retry < 25) {
    delay(500);
    digitalWrite(LED_BUILTIN_P, !digitalRead(LED_BUILTIN_P));
    retry++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    digitalWrite(LED_BUILTIN_P, HIGH);
    Serial.println("\n[PORTAL] Gerbang Online! IP: " + WiFi.localIP().toString());
    beep(100, 2);
  } else {
    startCaptivePortal();
  }
}

void loop() {
  if (inConfigMode) {
    dnsServer.processNextRequest();
    server.handleClient();
    return;
  }
  if (digitalRead(BOOT_BTN) == LOW) {
    delay(50);
    if (digitalRead(BOOT_BTN) == LOW) { startCaptivePortal(); return; }
  }
  if (WiFi.status() != WL_CONNECTED) { WiFi.reconnect(); delay(2000); return; }
  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) { delay(50); return; }

  String uid = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) uid += "0";
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();

  unsigned long now = millis();
  if (uid == lastUidScanned && (now - lastScanTime < SCAN_COOLDOWN_MS)) {
    rfid.PICC_HaltA(); rfid.PCD_StopCrypto1(); return;
  }
  lastUidScanned = uid;
  lastScanTime = now;

  cekAksesPortal(uid);
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
  delay(200);
}
