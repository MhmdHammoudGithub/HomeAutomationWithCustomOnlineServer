/*
  Rui Santos
  Complete project details at https://RandomNerdTutorials.com/control-esp32-esp8266-gpios-from-anywhere/

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files.

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  Rui Santos
  Complete project details at https://RandomNerdTutorials.com/cloud-weather-station-esp32-esp8266/

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files.

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.
*/

#include <Wire.h>
#include <LiquidCrystal_I2C.h>


#define activeBuzzer 7
#define flameSensorPin A1
#define redLedPin 5
#define greenLedPin 6
#define arduinoPin 3

int Flame = 0;
const int flameThreshold = 75;

LiquidCrystal_I2C lcd(0x27,16,2); // set the LCD address to 0x27 for a 16 chars and 2 line display



void setup() {
  pinMode(activeBuzzer, OUTPUT);
  pinMode(redLedPin, OUTPUT);
  pinMode(greenLedPin, OUTPUT);
  pinMode(flameSensorPin, INPUT);
  pinMode(arduinoPin, OUTPUT);

  lcd.init(); // initialize the lcd
  lcd.clear();

  Serial.begin(9600);
}

void loop() {
  digitalWrite(arduinoPin, LOW);
  bool doneItOnceFlag = false;
  int Flame = analogRead(flameSensorPin); // Read the analog value from the sensor
  delay(100);
  while(Flame < flameThreshold){
    Serial.print("Flame Sensor Value: ");
    Serial.println(Flame); // Print the value to the Serial Monitor
    if(!doneItOnceFlag){
      digitalWrite(arduinoPin, HIGH);
      doneItOnceFlag = true;
    }
    digitalWrite(activeBuzzer, LOW);
    digitalWrite(redLedPin, HIGH);
    digitalWrite(greenLedPin, LOW);
    lcd.backlight();
    lcd.setCursor(0,0);
    lcd.print("Fire");
    lcd.setCursor(0,1);
    lcd.print("Detected!");
    Flame = analogRead(flameSensorPin);
    delay(100);
  }
  
  digitalWrite(arduinoPin, LOW);
  lcd.noBacklight();
  lcd.clear();
  digitalWrite(activeBuzzer, HIGH);
  digitalWrite(greenLedPin, HIGH);
  digitalWrite(redLedPin, LOW);
  delay(100);
  
}


