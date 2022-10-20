<?php
session_start();

if ( isset( $_SESSION['username'] ) ) {} 

else {
    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Google Maps Page</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        #map {
            height: 700px;
            width: 100%;
        }
    </style>
</head>
<style>
     body{
        background-color:#42a832;  
    }
    input[type=button] {
      background-color:#42a832;
      color: white;
      padding: 14px 20px;
      margin: 8px 0;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    input[type=button]:hover {
        background-color: #42a832;
    }
</style>
<body>
    <h3>Google Maps Page</h3>
    <?php
       
       echo "Welcome, {$_SESSION['username']} !";
    ?>
    <div id="map"></div>
    <br>
    <a href="logout.php"><input type="button" value="Logout" name = "logout" ></a>
    <?php
    if(isset($_POST['logout'])) {
        session_start();
        session_destroy();
        header('Location: login.php');
    }
    ?>
    <?php

    $conn = mysqli_connect("localhost", "root", "") or die(mysqli_error());
    mysqli_select_db($conn, "users");

    $sql_read = "SELECT * FROM points";

    $result = mysqli_query($conn, $sql_read);
    if (!$result) {
        die('Could not read data: ' . mysqli_error());
    }
    ?>
 
    <script>
        function initMap() {
            var uluru = {
                lat: 23,
                lng: 31
            };
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 4,
                center: uluru
            });

            <?php
            while ($row = mysqli_fetch_array($result)) {

                $id = $row['ID'];
                $lat = $row['lat'];
                $long = $row['long'];
                $description = $row['description'];
                
                for ($i = 0; $i <= $id; $i++) {
                    echo "var marker$i = new google.maps.Marker({position:{lat:$lat,lng:$long}, label:'$description', map: map});";
                };
            }

            ?>
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEl64vITi4s1Vf0t5CpgmA0uSCQR8P0-U&callback=initMap&v=weekly">
    </script>
</body>

</html>