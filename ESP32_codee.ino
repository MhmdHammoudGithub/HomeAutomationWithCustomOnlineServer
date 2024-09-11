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


#include <WiFi.h>
#include <HTTPClient.h>
#include <WiFiClientSecure.h>
#include <ArduinoJson.h>
#include <HardwareSerial.h>


#define RXp2 16
#define TXp2 17

const char* ssid = "mhmdhammoud PC";
const char* password = "mhmd4321";
const char* telegramBotToken = "7107025320:AAGTTRbSxlVmfYl-1qZ8idrLBSewwhjuZR8";
const char* chatID = "1688559277";


const char* serverName = "http://16.171.146.109/serverProcessing.php";


const long outputInterval = 1000;
const long DHT11Interval = 1000;
const long LDRInterval = 1000;


unsigned long lastOutputWriteTime = 0;
unsigned long lastDHT11ReadTime = 0; 
unsigned long lastLDRReadTime = 0; 

String outputsState;


void setup() {
  delay(5000);
  Serial.begin(115200);
  Serial2.begin(9600, SERIAL_8N1, RXp2, TXp2);

  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());

}

void loop() {

  unsigned long currentMillis;
  if(WiFi.status() == WL_CONNECTED )
  { 

    currentMillis = millis();
    if(currentMillis - lastOutputWriteTime >= outputInterval)
    {
      outputCode(currentMillis);
    }
    
    currentMillis = millis();
    if(currentMillis - lastDHT11ReadTime >= DHT11Interval)
    {
      String DHT11WhatToDo = "";
      while(Serial2.available()<=0);
      DHT11WhatToDo = Serial2.readString();
      DHT11WhatToDo.trim();
      if(DHT11WhatToDo.indexOf("Read DHT11 failed") == -1)
      {
        // Serial.println(DHT11WhatToDo);
        String DHT11HttpRequestData = DHT11WhatToDo;
        DHT11Code(DHT11HttpRequestData,currentMillis);  
      }
      else{
        // Serial.println("DHT11 reading failed. Skipped the HTTP request");
      }
    }

    currentMillis = millis();
    if(currentMillis - lastLDRReadTime >= LDRInterval)
    {
      String LDRWhatToDo = "";
      while(Serial2.available()<=0);
      LDRWhatToDo = Serial2.readString();
      

      String firstSixChars = LDRWhatToDo.substring(0, 6);

      if (firstSixChars.equals("action")){
        String LDRHttpRequestData = LDRWhatToDo;
        LDRCode(LDRHttpRequestData,currentMillis);   
      }

    }
    flameSensorCode();

  }
  else 
  {
    // Serial.println("WiFi Disconnected");
  }
}


void outputCode(unsigned long currentMillis){
  
  outputsState = httpGETRequest(serverName);

  // Serial.println(outputsState);
  Serial2.println(outputsState);

      
  String response = "";
  long timeout = millis() + 5000;  // 5 seconds timeout
  while (response.indexOf("finished onlineOutputCode") == -1 && millis() < timeout) {
    if (Serial2.available() > 0) {
      response += Serial2.readStringUntil('\n');  // Read the response until newline
    }
  }

  if (response.indexOf("finished onlineOutputCode") != -1) {
    // Serial.println("Got the outputCode response");
  } else {
    // Serial.println("Response timed out for outputCode");
  }

  lastOutputWriteTime = currentMillis;

}


void DHT11Code(String DHT11HttpRequestData, unsigned long currentMillis){

  WiFiClient client;
  HTTPClient http;

  http.begin(client, serverName);

  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  
  // Serial.print("httpRequestData: ");
  // Serial.println(DHT11HttpRequestData);
  
  int httpResponseCode = http.POST(DHT11HttpRequestData);
  
  if (httpResponseCode > 0) {
    // Serial.print("HTTP Response code for DHT11: ");
    // Serial.println(httpResponseCode);
  } else {
    // Serial.print("Error code for DHT11: ");
    // Serial.println(httpResponseCode);
  }

  http.end();

  lastDHT11ReadTime = currentMillis;

  Serial2.println("finished for DHT11Code");

  delay(500);
}


void LDRCode(String LDRHttpRequestData, unsigned long currentMillis){

  WiFiClient client;
  HTTPClient http;

  http.begin(client, serverName);

  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  
  // Serial.print("httpRequestData: ");
  // Serial.println(LDRHttpRequestData);
  
  int httpResponseCode = http.POST(LDRHttpRequestData);
  if (httpResponseCode > 0) {
    // Serial.print("HTTP Response code for LDR: ");
    // Serial.println(httpResponseCode);
  } else {
    // Serial.print("Error code for LDR: ");
    // Serial.println(httpResponseCode);
  }
  
  http.end();

  lastLDRReadTime = currentMillis;
  
  Serial2.println("finished for LDRCode");  

  delay(500);
}


void flameSensorCode() {
  String flameStatus = Serial2.readString();
  flameStatus.trim();  // Remove any leading/trailing whitespace
  // Serial.println(flameStatus);
  if (flameStatus.indexOf("FIRE DETECTED!") != -1) {
    sendTelegramMessage("There is a fire in your home!!!");
    // Serial.println(flameStatus);
  }
}


String httpGETRequest(const char* serverName) {
  const char* action = "?action=outputs_state";

  int len = strlen(serverName) + strlen(action) + 1; // +1 for the null terminator

  char* thisServer = new char[len];

  strcpy(thisServer, serverName);

  strcat(thisServer, action);
  
  WiFiClient client;
  HTTPClient http;

  http.begin(client, thisServer);
  
  int httpResponseCode = http.GET();
  
  String payload = "{}"; 
  
  if (httpResponseCode > 0) {
    // Serial.print("HTTP Response code for output code: ");
    // Serial.println(httpResponseCode);
    payload = http.getString();
  } else {
    // Serial.print("Error code for output code: ");
    // Serial.println(httpResponseCode);
  }
  http.end();

  delete[] thisServer;

  return payload;
}

void sendTelegramMessage(String message) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String serverPath = "https://api.telegram.org/bot" + String(telegramBotToken) + "/sendMessage?chat_id=" + String(chatID) + "&text=" + message;

    http.begin(serverPath.c_str());
    int httpResponseCode = http.GET();

    if (httpResponseCode > 0) {
      String response = http.getString();
     } 

    http.end();
  }
}
