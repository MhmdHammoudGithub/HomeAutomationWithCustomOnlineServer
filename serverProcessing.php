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
include_once ('database.php');

$api_key_value = "MohammadHammoud6421HusseinChoueib6500";

$api_key = "";
$temperature = $humidity = "";
$level = "";
$action = $id = $name = $outpin = $state = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = test_input($_POST["action"]);
    if ($action == "receiveDHT11reading") {
        $api_key = test_input($_POST["api_key"]);
        if ($api_key == $api_key_value) {
            $temperature = test_input($_POST["temperature"]);
            $humidity = test_input($_POST["humidity"]);
            $result = insertTemperatureAndHumiditySensorReading($temperature, $humidity);
            echo $result;
        }
    } else if ($action == "receiveLDRreading") {
        $api_key = test_input($_POST["api_key"]);
        if ($api_key == $api_key_value) {
            $reading_value = test_input($_POST["value"]);
            $result = insertLDRReading($reading_value);
            echo $result;
        }
    } else {
        echo "serverProcessing.php: No data posted with the HTTP request";
    }
}  


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $action = test_input($_GET["action"]);
    if ($action == "outputs_state") {
        $result = getAllOutputStatesAndTypes();

        if ($result) {
            while ($row = $result->fetch_assoc()) {// after the loop, $rows will be an array where the keys are pin numbers 
                                                   //and the values are the states of those pins concatinated with their types
               $data[] = array('outpin' => $row["outpin"], 'state' => $row["state"], 'type' => $row["type"]);
            }
        }
        echo json_encode($data);


    } else if ($action == "output_update") {
        $id = test_input($_GET["id"]);
        $state = test_input($_GET["state"]);
        $result = updateOutput($id, $state);
        echo $result;
    }
    else 
    {
        echo "serverProcessing.php: Invalid HTTP request";
    }
}


function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}