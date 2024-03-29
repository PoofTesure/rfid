// LiquidCrystal I2C - Version: Latest
#include <LiquidCrystal_I2C.h>

// MFRC522 - Version: Latest
#include <MFRC522.h>
#include <SPI.h>

const int doorLock = 8;
const int RST_PIN = 5;
const int SS_PIN = 53;

MFRC522 rfid(SS_PIN, RST_PIN);
int serNum[4];

//Objek LCD
LiquidCrystal_I2C lcd(0x27, 16, 2);

void setup() {
  Serial.begin(9600);
  SPI.begin();
  lcd.init();
  lcd.backlight();
  rfid.PCD_Init();

  pinMode(doorLock, OUTPUT);
  digitalWrite(doorLock, LOW);

  lcd.print("RFID READY");
  lcd.clear();

}

void loop() {
  if(Serial.available() > 0){
    String incomingData = Serial.readString();
    if(incomingData == "readmode"){
      if(readCard() == true){
      }
    }
  }
  lcd_idle();
  if (readCard() == true) {
    while (true) {
      if (Serial.available() > 0) {
        String incomingData = Serial.readString();
        if (incomingData.substring(0, 1) == "1") {
          digitalWrite(doorLock, HIGH);
          cardSuccess(incomingData.substring(2));
          break;
        }
        else {
          cardFailed();
          break;
        }
      }
    }
  }
}


void lcd_idle() {
  lcd.setCursor(0, 0);
  lcd.print("    Silahkan");
  lcd.setCursor (0, 1);
  lcd.print("Tempelkan Kartu");
}

bool readCard() {
  rfid.PCD_Init();
  if (! rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
    return false;
  }
  for (byte i = 0; i < rfid.uid.size; i++) {
    serNum[i] = rfid.uid.uidByte[i];
    Serial.print(serNum[i]);
  }
  Serial.println("");
  rfid.PICC_HaltA();
  return true;
}

void cardSuccess(String nama) {
  lcd.clear();
  lcd.setCursor (0, 0);
  lcd.print(F(" Akses diterima "));
  lcd.setCursor (0, 1);
  lcd.print(nama);
  lcd.setCursor (4, 1);
  delay(1000);
  digitalWrite(doorLock, HIGH);
  lcd.setCursor (0, 0);
  lcd.print(F(" Silahkan Masuk "));
  lcd.setCursor (0, 1);
  lcd.print(F("KE WILAYAH RW 18"));
  delay(1000);
  digitalWrite(doorLock, LOW);
  lcd.clear();
}

void cardFailed() {
  lcd.setCursor (0, 0);
  lcd.print(F(" Akses ditolak  "));
  lcd.setCursor (0, 1);
  lcd.print("ID:");
  for (int i = 0; i < 5; i++) {
    lcd.print(serNum[i]);
  }
  delay(2900);
  lcd.clear();
}
