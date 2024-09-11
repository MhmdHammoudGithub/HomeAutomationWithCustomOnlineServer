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
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set the default timezone to Beirut, Lebanon
date_default_timezone_set('Asia/Beirut');




include_once ('database.php');
if (isset($_GET["readingsCount"])) {
    $data = $_GET["readingsCount"];
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $readings_count = $data;
}
// default readings count set to 10
else {
    $readings_count = 10;
}

$last_DHT11_reading = getLastTemperatureAndHumiditySensorReadings();
$last_reading_temp = $last_DHT11_reading["temperature"];
$last_reading_humi = $last_DHT11_reading["humidity"];
$last_reading_time = $last_DHT11_reading["reading_time"];

// Uncomment to set timezone to - 1 hour (you can change 1 to any number)
//$last_reading_time = date("Y-m-d H:i:s", strtotime("$last_reading_time - 1 hours"));
// Uncomment to set timezone to + 7 hours (you can change 3 to any number)
$last_reading_time = date("Y-m-d H:i:s", strtotime("$last_reading_time + 3 hours"));

$min_temp = minTemperatureAndHumiditySensorReading($readings_count, 'temperature');
$max_temp = maxTemperatureAndHumiditySensorReading($readings_count, 'temperature');
$avg_temp = avgTemperatureAndHumiditySensorReading($readings_count, 'temperature');

$min_humi = minTemperatureAndHumiditySensorReading($readings_count, 'humidity');
$max_humi = maxTemperatureAndHumiditySensorReading($readings_count, 'humidity');
$avg_humi = avgTemperatureAndHumiditySensorReading($readings_count, 'humidity');



$last_LDR_reading = getLastLDRReadings();
$last_LDR_reading_value = $last_LDR_reading["reading_value"];
if ($last_LDR_reading_value < 100)
    $light_state = "The room is dark";
else 
    $light_state = "The room is bright";




$relay = getSpecificOutput(1);
$relay_row = $relay->fetch_assoc();
$relay_outpin = $relay_row["outpin"];
$relay_state = $relay_row["state"];
if ($relay_state == "0")
    $relay_checked = "checked";
else
    $relay_checked = "";



$fan = getSpecificOutput(2);
$fan_row = $fan->fetch_assoc();
$fan_outpin = $fan_row["outpin"];
$fan_state = $fan_row["state"];
$fan_type = $fan_row["type"];


?>

<!DOCTYPE html>
<html lang="en">
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
          <title>Mini-Project</title>
        <link rel="stylesheet" type="text/css" href="home-page.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>


        <script>
        var last_radio_button_value = <?php echo $fan_state ?>;
        
        // Function to check the radio button with the last known value
        function setInitialRadioButton() {
            var radioButtons = document.getElementsByName('switch');
            for (var i = 0; i < radioButtons.length; i++) {
                if (radioButtons[i].value == last_radio_button_value) {
                    radioButtons[i].checked = true;
                    break;
                }
            }
        }

        // Call the function to set the initial radio button with the last known value
        window.onload = setInitialRadioButton;

        
    </script>
    </head>
    
<body>
    <header class="header">
        <h1>🏠 Home</h1>
        <form method="get">
            <input type="number" name="readingsCount" min="1" placeholder="Number of readings (<?php echo $readings_count; ?>)">
            <input type="submit" value="UPDATE">
        </form>
       
   </header>
    <p>Last reading: <?php echo $last_reading_time; ?></p>
    <section class="content">
        <div class="box gauge--1">
        <h3>TEMPERATURE</h3>
              <div class="mask">
              <div class="semi-circle"></div>
              <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="temp">--</p>
            <table cellspacing="5" cellpadding="5">
                <tr>
                    <th colspan="3">Temperature <?php echo $readings_count; ?> readings</th>
                </tr>
                <tr>
                    <td>Min</td>
                    <td>Max</td>
                    <td>Average</td>
                </tr>
                <tr>
                    <td><?php echo $min_temp['min_amount']; ?> &deg;C</td>
                    <td><?php echo $max_temp['max_amount']; ?> &deg;C</td>
                    <td><?php echo round($avg_temp['avg_amount'], 2); ?> &deg;C</td>
                </tr>
            </table>
        </div>
        <div class="box gauge--2">
            <h3>HUMIDITY</h3>
            <div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="humi">--</p>
            <table cellspacing="5" cellpadding="5">
                <tr>
                    <th colspan="3">Humidity <?php echo $readings_count; ?> readings</th>
                </tr>
                <tr>
                    <td>Min</td>
                    <td>Max</td>
                    <td>Average</td>
                </tr>
                <tr>
                    <td><?php echo $min_humi['min_amount']; ?> %</td>
                    <td><?php echo $max_humi['max_amount']; ?> %</td>
                    <td><?php echo round($avg_humi['avg_amount'], 2); ?> %</td>
                </tr>
            </table>
        </div>
        <div class="container">
            <div class="de">
                <div class="den">
                    <hr class="line">
                    <hr class="line">
                    <hr class="line">
                    <div class="switch">
                        <label for="switch_off"><span>OFF</span></label>
                        <label for="switch_1"><span>1</span></label>
                        <label for="switch_2"><span>2</span></label>
                        <label for="switch_3"><span>3</span></label>
                        <label for="switch_4"><span>4</span></label>
                        <label for="switch_5"><span>5</span></label>
                        <input type="radio" checked="" name="switch" id="switch_off" value="0" onclick="updateRadioButtonOutput()">
                        <input type="radio" name="switch" id="switch_1" value="52" onclick="updateRadioButtonOutput()">
                        <input type="radio" name="switch" id="switch_2" value="103" onclick="updateRadioButtonOutput()">
                        <input type="radio" name="switch" id="switch_3" value="154" onclick="updateRadioButtonOutput()">
                        <input type="radio" name="switch" id="switch_4" value="205" onclick="updateRadioButtonOutput()">
                        <input type="radio" name="switch" id="switch_5" value="255" onclick="updateRadioButtonOutput()">
                        <div class="light"><span></span></div>
                        <div class="dot"><span></span></div>
                        <div class="dene">
                            <div class="denem">
                                <div class="deneme">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        

    </section>

       <h2> View Latest <?php echo $readings_count ?> Readings</h2>
    <section class="tables">
        <?php
        echo ' <div>  <table cellspacing="5" cellpadding="5" id="tableReadings">
                        <tr>
                            <th>Temperature (&deg;C)</th>
                            <th>Humidity (%)</th>
                            <th>Timestamp</th>
                        </tr>';

        $result = getAllTemperatureAndHumiditySensorReadings($readings_count);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row_temp = $row["temperature"];
                $row_humi = $row["humidity"];
                $row_reading_time = $row["reading_time"];
                $row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time + 3 hours"));

                echo '<tr>
                            <td>' . $row_temp . '</td>
                            <td>' . $row_humi . '</td>
                            <td>' . $row_reading_time . '</td>
                          </tr>';
            }
    
            echo '</table>';
            echo '</div>';
            $result->free();
        }

            echo  '<div>  <table cellspacing="5" cellpadding="5" id="tableReadings">
                        <tr>
                            <th>Brightness</th>
                            <th>Timestamp</th>
                        </tr>';
        $result = getAllLDRReadings($readings_count);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row_temp = $row["reading_value"];
                $row_reading_time = $row["reading_time"];
                $row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time + 3 hours"));

                echo '<tr>
                            <td>' . $row_temp . '</td>
                            <td>' . $row_reading_time . '</td>
                          </tr>';
            }

            echo '</table>';
            echo '</div>';
            $result->free();
        }
        ?>
    </section>

    <section>
        <div>
            <h3>Lamp</h3>
            <label class="switch"><input type="checkbox" onchange="updateRelayOutput(this)" id="1" <?php echo $relay_checked ?>> <span class="slider"></span></label>
        </div>
                <h3> <?php echo $light_state ?></h3>
        <div>

        </div>

    </section>
    

<script>



    var lastTemp = <?php echo $last_reading_temp; ?>;
    var lastHumi = <?php echo $last_reading_humi; ?>;
    setTemperature(lastTemp);
    setHumidity(lastHumi);



    function setTemperature(curVal){
        //set range for Temperature in Celsius -5 Celsius to 38 Celsius
        var minTemp = -5.0;
        var maxTemp = 50;

        var newVal = scaleValue(curVal, [minTemp, maxTemp], [0, 180]);
        $('.gauge--1 .semi-circle--mask').attr({
            style: '-webkit-transform: rotate(' + newVal + 'deg);' +
            '-moz-transform: rotate(' + newVal + 'deg);' +
            'transform: rotate(' + newVal + 'deg);'
        });
        $("#temp").text(curVal + ' ºC');
    }

    function setHumidity(curVal){
        //set range for Humidity percentage 0 % to 100 %
        var minHumi = 0;
        var maxHumi = 100;

        var newVal = scaleValue(curVal, [minHumi, maxHumi], [0, 180]);
        $('.gauge--2 .semi-circle--mask').attr({
            style: '-webkit-transform: rotate(' + newVal + 'deg);' +
            '-moz-transform: rotate(' + newVal + 'deg);' +
            'transform: rotate(' + newVal + 'deg);'
        });
        $("#humi").text(curVal + ' %');
    }

    function scaleValue(value, from, to) {
        var scale = (to[1] - to[0]) / (from[1] - from[0]);
        var capped = Math.min(from[1], Math.max(from[0], value)) - from[0];
        return ~~(capped * scale + to[0]);
    }


    function updateRelayOutput(element) {
        var xhr = new XMLHttpRequest();
        var url = "http://16.171.146.109/serverProcessing.php"; 

        if (element.checked) {
            xhr.open("GET", url + "?action=output_update&id=" + element.id + "&state=0", true);
        } else {
            xhr.open("GET", url + "?action=output_update&id=" + element.id + "&state=1", true);
        }

        xhr.send();
    }


    function updateRadioButtonOutput() {
        var xhr = new XMLHttpRequest();
        const selectedOption = document.querySelector('input[name="switch"]:checked').value;
        var url = "http://16.171.146.109/serverProcessing.php"; 
        xhr.open("GET", url + "?action=output_update&id=2&state=" + selectedOption, true);
        xhr.send();
    }


</script>
</body>
</html>


