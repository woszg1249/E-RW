/*
 =========================================================================================
  FOLDER 1: ESP32 SCAN & DAFTAR UID e-KTP (USER LOGIN E-RW)
 =========================================================================================
  Fungsi:
  - Membaca UID fisik e-KTP lewat sensor RC-522.
  - Mengirim UID ke server E-RW (POST /api/iot/rfid/tap) agar user yang sedang login
    di web (/profil atau /bank-warga) dapat langsung mendaftarkan e-KTP ke akunnya.
  - Dilengkapi Captive Portal Web Configurator (WiFi: "RFID Reader") untuk atur Hotspot
    HP & Server tanpa perlu colok laptop. Tahan tombol BOOT (GPIO 0) 2 detik.
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

#define SS_PIN     5
#define RST_PIN    22
#define BUZZER_PIN 4
#define LED_PIN    2
#define BOOT_BTN   0

MFRC522 rfid(SS_PIN, RST_PIN);
Preferences prefs;
DNSServer dnsServer;
WebServer server(80);

const byte DNS_PORT = 53;
IPAddress apIP(192, 168, 4, 1);
IPAddress netMsk(255, 255, 255, 0);

String configOwner     = "Reival (E-RW Digital)";
String configSsid      = "Siti Aniroh-2G";
String configPass      = "ridzigoblokidiotmatiajalu";
String configEndpoint  = "https://e-rw.unique-monsters.my.id/api/iot/rfid/tap";
String configDeviceId  = "esp32-daftar-ktp-01";

bool inConfigMode = false;
String lastUidScanned = "";
unsigned long lastScanTime = 0;
const unsigned long SCAN_COOLDOWN_MS = 2500;

void beep(int durationMs, int count = 1) {
  if (BUZZER_PIN < 0) return;
  for (int i = 0; i < count; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(durationMs);
    digitalWrite(BUZZER_PIN, LOW);
    if (count > 1 && i < count - 1) delay(80);
  }
}

String getHtmlPage() {
  String html = R"rawliteral(
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RFID Reader - Daftar e-KTP</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    body { background: #f1f5f9; padding: 20px 14px; color: #1e293b; display: flex; justify-content: center; }
    .card { background: #fff; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); width: 100%; max-width: 440px; padding: 24px 20px; }
    h2 { font-size: 1.25rem; color: #059669; text-align: center; margin-bottom: 14px; }
    .form-group { margin-bottom: 12px; }
    label { display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 5px; }
    input { width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; }
    .btn { background: #059669; color: #fff; border: none; width: 100%; padding: 12px; border-radius: 12px; font-size: 0.95rem; font-weight: 700; cursor: pointer; margin-top: 8px; }
  </style>
</head>
<body>
  <div class="card">
    <h2>&#128179; Scanner Daftar e-KTP</h2>
    <form method="POST" action="/save">
      <div class="form-group"><label>Nama Projek:</label><input type="text" name="owner" value=")rawliteral" + configOwner + R"rawliteral(" required></div>
      <div class="form-group"><label>Nama WiFi / Hotspot HP:</label><input type="text" name="ssid" value=")rawliteral" + configSsid + R"rawliteral(" required></div>
      <div class="form-group"><label>Password WiFi / Hotspot:</label><input type="password" name="pass" value=")rawliteral" + configPass + R"rawliteral("></div>
      <div class="form-group"><label>URL Endpoint Tap:</label><input type="text" name="endpoint" value=")rawliteral" + configEndpoint + R"rawliteral(" required></div>
      <div class="form-group"><label>Device ID:</label><input type="text" name="device_id" value=")rawliteral" + configDeviceId + R"rawliteral(" required></div>
      <button type="submit" class="btn">SIMPAN & REBOOT</button>
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
  if (server.hasArg("owner")) configOwner = server.arg("owner");
  if (server.hasArg("ssid")) configSsid = server.arg("ssid");
  if (server.hasArg("pass")) configPass = server.arg("pass");
  if (server.hasArg("endpoint")) configEndpoint = server.arg("endpoint");
  if (server.hasArg("device_id")) configDeviceId = server.arg("device_id");

  prefs.begin("rfid_cfg", false);
  prefs.putString("owner", configOwner);
  prefs.putString("ssid", configSsid);
  prefs.putString("pass", configPass);
  prefs.putString("endpoint", configEndpoint);
  prefs.putString("devid", configDeviceId);
  prefs.end();

  server.send(200, "text/html", "<h3>Tersimpan! Rebooting...</h3>");
  delay(1200);
  ESP.restart();
}

void startCaptivePortal() {
  inConfigMode = true;
  WiFi.disconnect();
  WiFi.mode(WIFI_AP);
  WiFi.softAPConfig(apIP, apIP, netMsk);
  WiFi.softAP("RFID Reader");
  Serial.println("[PORTAL] WiFi Aktif: RFID Reader -> http://192.168.4.1");

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

void kirimUidKeServer(String uid) {
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

    Serial.printf("[SCAN] UID: %s | HTTP: %d\n", uid.c_str(), httpCode);
    Serial.println("[RESPON] " + response);

    if (httpCode == 200) {
      Serial.println("[STATUS] Kartu SUDAH TERDAFTAR di akun warga.");
      beep(60, 2);
    } else if (httpCode == 403) {
      Serial.println("[STATUS] Kartu BELUM TERDAFTAR. Siap dipairing di halaman Profil Web!");
      beep(200, 1);
    } else {
      Serial.println("[ERROR] Gagal terhubung ke server.");
      beep(400, 1);
    }
    http.end();
  }
}

void setup() {
  Serial.begin(115200);
  delay(800);
  pinMode(LED_PIN, OUTPUT);
  pinMode(BOOT_BTN, INPUT_PULLUP);
  if (BUZZER_PIN >= 0) { pinMode(BUZZER_PIN, OUTPUT); digitalWrite(BUZZER_PIN, LOW); }

  prefs.begin("rfid_cfg", true);
  configOwner    = prefs.getString("owner", configOwner);
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
    digitalWrite(LED_PIN, !digitalRead(LED_PIN));
    retry++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    digitalWrite(LED_PIN, HIGH);
    Serial.println("\n[WIFI] Terhubung! IP: " + WiFi.localIP().toString());
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

  kirimUidKeServer(uid);
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
  delay(200);
}
