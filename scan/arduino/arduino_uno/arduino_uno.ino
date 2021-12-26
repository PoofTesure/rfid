// LiquidCrystal I2C - Version: Latest
#include <LiquidCrystal_I2C.h>

// MFRC522 - Version: Latest
#include <MFRC522.h>
#include <SPI.h>

const int doorLock = 8;
const int RST_PIN = 9;
const int SS_PIN = 10;
const int transmit = 2;

MFRC522 rfid(SS_PIN, RST_PIN);
int serNum[4];

//Objek LCD
LiquidCrystal_I2C lcd(0x27, 16, 2);

void setup() {
  Serial.begin(38400);
  SPI.begin();
  lcd.init();
  lcd.backlight();
  rfid.PCD_Init();

  pinMode(transmit,OUTPUT);
  digitalWrite(transmit,LOW);

  pinMode(doorLock, OUTPUT);
  digitalWrite(doorLock, HIGH);

  lcd.print("RFID READY");
  lcd.clear();

}

void loop() {
  lcd_idle();
  if(Serial.available() > 0){
    String incomingData = Serial.readString();
    if(incomingData == "readmode"){
      if(readCard() == true){
      }
    }
    else if (incomingData.substring(0,1) == "1"){
      digitalWrite(doorLock, LOW);
      cardSuccess(incomingData.substring(2));
    }
  }
  if (readCard() == true) {
    while (true) {
      digitalWrite(transmit,LOW);
      if (Serial.available() > 0) {
        String incomingData = Serial.readString();
        if (incomingData.substring(0, 1) == "1") {
          digitalWrite(doorLock, LOW);
          digitalWrite(transmit,HIGH);
          Serial.println("Input intact");
          cardSuccess(incomingData.substring(2));
          break;
        }
        else if(incomingData.substring(0, 1) == "0") {
          digitalWrite(transmit,HIGH);
          Serial.println("Input intact");
          cardFailed();
          break;
        }
        else{
          digitalWrite(transmit,HIGH);
          Serial.println("Corrupt input");
          
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
  delay(100);
  for (byte i = 0; i < rfid.uid.size; i++) {
    serNum[i] = rfid.uid.uidByte[i];
    digitalWrite(transmit,HIGH);
    Serial.print(serNum[i]);
  }
  Serial.println("");
  delay(100);
  rfid.PICC_HaltA();
  digitalWrite(transmit,LOW);
  return true;
}

void cardSuccess(String nama) {
  delay(100);
  digitalWrite(doorLock, HIGH);
  lcd.clear();
  lcd.setCursor (0, 0);
  lcd.print(F(" Akses diterima "));
  lcd.setCursor (0, 1);
  lcd.print(nama);
  lcd.setCursor (4, 1);
  delay(1000);
  //digitalWrite(doorLock, HIGH);
  lcd.setCursor (0, 0);
  lcd.print(F(" Silahkan Masuk "));
  lcd.setCursor (0, 1);
  lcd.print(F("KE WILAYAH RW 18"));
  delay(1000);
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