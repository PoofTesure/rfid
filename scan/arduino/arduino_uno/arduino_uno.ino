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
  Serial.begin(38400);  //Start serial begin
  SPI.begin();          //Start SPI
  lcd.init(); //Start lcd lib
  lcd.backlight(); //Init backlight
  rfid.PCD_Init(); //Init scanner

  pinMode(transmit,OUTPUT);  //Set pin transmit as output
  digitalWrite(transmit,LOW); //Set transmit to low, listen to serial

  pinMode(doorLock, OUTPUT);  //Set pin doorLock as output
  digitalWrite(doorLock, LOW);  //Set relay to shut down (off)

  pinMode(transmit,OUTPUT);
  digitalWrite(transmit,LOW);

  lcd.print("RFID READY");
  lcd.clear();

}

void loop() {
  lcd_idle();
  if(Serial.available() > 0){     //Check serial if there's any input, mainly for webserver requesting rfid uid for updating database
    String incomingData = Serial.readString(); //Read incoming data
    if(incomingData == "readmode"){ //If incoming data come as string with values == readmode, give id data to webserver to update database, data got sent through serial
      if(readCard() == true){
      }
    }
    else if (incomingData.substring(0,1) == "1"){ //Read incoming data if first char is 1 then activate relay and lcd routine
      digitalWrite(doorLock, HIGH);
      cardSuccess(incomingData.substring(2));
    }
  }
  if (readCard() == true) { //read rfid uid and send to server
    while (true) {
      digitalWrite(transmit,LOW); //listen to serial
      if (Serial.available() > 0) {
        String incomingData = Serial.readString(); //read serial
        if (incomingData.substring(0, 1) == "1") { ////Read incoming data if first char is 1 then activate relay and lcd routine
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
  if (! rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) { //Check if a new card scanned
    return false;
  }
  delay(10);
  digitalWrite(transmit,HIGH); //Send serial
  for (byte i = 0; i < rfid.uid.size; i++) { //loop for as many as rfid uid size
    serNum[i] = rfid.uid.uidByte[i];
    Serial.print(serNum[i]);
  }
  Serial.println("");
  delay(10);
  rfid.PICC_HaltA();
  digitalWrite(transmit,LOW);//Listen to serial
  return true;
}

void cardSuccess(String nama) {
  //digitalWrite(doorLock, HIGH);
  lcd.clear();
  lcd.setCursor (0, 0);
  lcd.print(F(" Akses diterima "));
  lcd.setCursor (0, 1);
  lcd.print(nama);
  lcd.setCursor (4, 1);
  delayMicroseconds(10000);
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
