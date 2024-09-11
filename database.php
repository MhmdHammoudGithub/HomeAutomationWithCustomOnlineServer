<!--
  Rui Santos
  Complete project details at https://RandomNerdTutorials.com/control-esp32-esp8266-gpios-from-anywhere/

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files.

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.
-->

<!--
  Rui Santos
  Complete project details at https://RandomNerdTutorials.com/cloud-weather-station-esp32-esp8266/

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files.

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.
-->

<?php
use UI\Area;

$servername = "localhost";
$dbname = "hammoud6421_choueib6500";
$username = "hammoud6421";
$password = "MiniProject2024!";


// temperature and humidity sensor
function insertTemperatureAndHumiditySensorReading($temperature, $humidity)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO DHT11 (temperature, humidity)
    VALUES ('" . $temperature . "', '" . $humidity . "')";

    if ($conn->query($sql) === TRUE) {
        $result = "New record created successfully";
    } else {
        $result = "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
    return $result;

}
function getAllTemperatureAndHumiditySensorReadings($limit)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, temperature, humidity, reading_time FROM DHT11 order by reading_time desc limit " . $limit;
    if ($result = $conn->query($sql)) {
        $conn->close();
        return $result;
    } else {
        $conn->close();
        return false;
    }

}
function getLastTemperatureAndHumiditySensorReadings()
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, temperature, humidity, reading_time FROM DHT11 order by reading_time desc limit 1";
    if ($result = $conn->query($sql)) {

        $result = $result->fetch_assoc();
    } else {
        $result = false;
    }
    $conn->close();
    return $result;
}
function minTemperatureAndHumiditySensorReading($limit, $value)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT MIN(" . $value . ") AS min_amount FROM (SELECT " . $value . " FROM DHT11 order by reading_time desc limit " . $limit . ") AS min";
    if ($result = $conn->query($sql)) {
        $result = $result->fetch_assoc();
    } else {
        $result = false;
    }
    $conn->close();
    return $result;
}
function maxTemperatureAndHumiditySensorReading($limit, $value)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT MAX(" . $value . ") AS max_amount FROM (SELECT " . $value . " FROM DHT11 order by reading_time desc limit " . $limit . ") AS max";
    if ($result = $conn->query($sql)) {
        $result = $result->fetch_assoc();
    } else {
        $result = false;
    }
    $conn->close();
    return $result;
}
function avgTemperatureAndHumiditySensorReading($limit, $value)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT AVG(" . $value . ") AS avg_amount FROM (SELECT " . $value . " FROM DHT11 order by reading_time desc limit " . $limit . ") AS avg";
    if ($result = $conn->query($sql)) {
        $result = $result->fetch_assoc();
    } else {
        $result = false;
    }
    $conn->close();
    return $result;
}

// LDR
function insertLDRReading($value)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO LDR (reading_value)
    VALUES ('" . $value . "')";

    if ($conn->query($sql) === TRUE) {
        $result = "New record created successfully";
    } else {
        $result = "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
    return $result;

}
function getAllLDRReadings($limit)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, reading_value, reading_time FROM LDR order by reading_time desc limit " . $limit;
    if ($result = $conn->query($sql)) {
        $conn->close();
        return $result;
    } else {
        $conn->close();
        return false;
    }

}
function getLastLDRReadings()
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, reading_value, reading_time FROM LDR order by reading_time desc limit 1";
    if ($result = $conn->query($sql)) {

        $result = $result->fetch_assoc();
    } else {
        $result = false;
    }
    $conn->close();
    return $result;
}


// outputs
function updateOutput($id, $state)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE Outputs SET state='" . $state . "' WHERE id='" . $id . "'";

    if ($conn->query($sql) === TRUE) {
        $result = "Output state updated successfully";
    } else {
        $result = "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
    return $result;
}
function getSpecificOutput($id)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, name, outpin, state, type FROM Outputs WHERE id = '" . $id . "' ;";
    if ($result = $conn->query($sql)) {
        $conn->close();
        return $result;
    } else {
        $conn->close();
        return false;
    }
}
function getAllOutputStatesAndTypes()
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT outpin, state, type FROM Outputs ";
    if ($result = $conn->query($sql)) {
        $conn->close();
        return $result;
    } else {
        $conn->close();
        return false;
    }
}

?>