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

#include <SimpleDHT.h>
#include <ArduinoJson.h>

#define DHT11Pin 5
#define LDRPin A0
#define otherArduino 11

SimpleDHT11 dht11;
String apiKeyValue = "MohammadHammoud6421HusseinChoueib6500";


void setup() {
  pinMode(DHT11Pin, INPUT);
  Serial.begin(9600);
}

void loop() {
  while (Serial.available() <= 0);

  onlineOutputCode();
  onlineInputCode();
}

void onlineOutputCode() {
  String outputsState = Serial.readString();
  
  DynamicJsonDocument doc(1024);
  deserializeJson(doc, outputsState);
  int outpin;
  int state;
  String type;

  for (int i = 0; i < doc.size(); i++) {
    outpin = doc[i]["outpin"].as<int>();
    state = doc[i]["state"].as<int>();
    type = doc[i]["type"].as<String>();
    pinMode(outpin, OUTPUT);
    if (type == "analog") {
      analogWrite(outpin, state);
    } else {
      digitalWrite(outpin, state);
    }
      Serial.print("PIN: ");
      Serial.print(outpin);
      Serial.print(" TYPE: ");
      Serial.print(type);
      Serial.print(" - SET to: ");
      Serial.println(state);
  }
    Serial.println("finished onlineOutputCode");
  delay(1000);
}

void onlineInputCode() {
  DHT11Code();
  LDRCode();
  flameSensorCode();

}

void DHT11Code() {
  byte temperature = 0;
  byte humidity = 0;
  byte data[40] = {0};
  if (dht11.read(DHT11Pin, &temperature, &humidity, data)) {
      Serial.println("Read DHT11 failed");
    delay(1300);
    return;
  }

  String DHT11HttpRequestData = "action=receiveDHT11reading&api_key=" + apiKeyValue + "&temperature=" + String(temperature) + "&humidity=" + String(humidity);

    Serial.println(DHT11HttpRequestData);

  String response = "";
  unsigned long timeoutMillis = millis() + 5000; // 5 seconds timeout
  while (response.indexOf("finished for DHT11Code") == -1 && millis() < timeoutMillis) {
    if (Serial.available() > 0) {
      response += Serial.readStringUntil('\n'); // Read the response until newline
    }
  }
}

void LDRCode() {
  int reading = analogRead(LDRPin);

  String LDRHttpRequestData = "action=receiveLDRreading&api_key=" + apiKeyValue + "&value=" + String(reading);

  Serial.println(LDRHttpRequestData);

  String response = "";
  unsigned long timeoutMillis = millis() + 5000; // 5 seconds timeout
  while (response.indexOf("finished for LDRCode") == -1 && millis() < timeoutMillis) {
    if (Serial.available() > 0) {
      response += Serial.readStringUntil('\n'); // Read the response until newline
    }
  }
}

void flameSensorCode(){
  int flameStatus = digitalRead(otherArduino);
  if(flameStatus == LOW){
    Serial.println("no fire detected");
    delay(500);
  }
  else{
    Serial.println("FIRE DETECTED!");
    delay(500);
  }
  
}

