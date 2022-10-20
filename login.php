<?php
session_id("mainID");
session_start();
?>

<html>

<head>
   <title>Login page</title>

   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
   <script type="text/javascript">
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
        var resultDropdown = $(this).siblings(".result");
        if(inputVal.length){
            $.get("backend-search.php", {term: inputVal}).done(function(data){
                // Display the returned data in browser
                resultDropdown.html(data);
            });
        } else{
            resultDropdown.empty();
        }
    });
    
    // Set search input value on click of result item
    $(document).on("click", ".result p", function(){
        $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
        $(this).parent(".result").empty();
    });
});
</script>
</head>

<style>
   body{
      background-color:#42a832;
      margin: 10px;
   }
   div {
      border-radius: 5px;
      background-color: #03ebfc;
      padding: 10px;
      width: 20%;
   }
   input[type=text], select {
      width: 98%;
      padding: 12px 20px;
      margin: 8px 0;
      display: inline-block;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
   }
   input[type=password], select {
      width: 98%;
      padding: 12px 20px;
      margin: 8px 0;
      display: inline-block;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
   }
   button[type=submit] {
      width: 100%;
      background-color: teal;
      color: white;
      padding: 14px 20px;
      margin: 8px 0;
      border: none;
      border-radius: 4px;
      cursor: pointer;
   }
   button[type=submit]:hover {
      background-color: #24fc03;
   }
   .register-form, .login-form{
      margin-left: 100px;
   }

   /* Formatting search box */
   .search-box{
        width: 100%;
        position: relative;
        display: inline-block;
        font-size: 14px;
        background-color: transparent;
    }
   
    .result{
        position: absolute;        
        z-index: 999;
      
        top: 67%;
        left: 0;
    }
    .search-box input[type="text"]{ 
        width: 98%;
        box-sizing: border-box;
        
    }
        .result{
         width: 97%;
        box-sizing: border-box;
         background-color: transparent;
        }
    /* Formatting result items */
    .result p{
        margin: 0;
        padding: 7px 10px;
        border: 1px solid #CCCCCC;
        border-top: none;
        cursor: pointer;
        background: white;
        border-radius: 4px;
        margin-left: -10px;
        box-sizing: border-box;
       
    }
    .result p:hover{
        background: #fc5e03;
        border: 1px solid #ccc;
        border-color: blac;
    }
</style>  

<body>

   <?php

   $servername = "localhost";
   $username = "root";
   $password = "";
   $db_name = "users";

   // Create connection
   $conn = mysqli_connect("localhost", "root", "", "users") or die(mysqli_error());
   
   $sql_read = "SELECT * FROM `users`";

   $result = mysqli_query($conn, $sql_read);
   
   /*
   // Check connection
   if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
   }
   echo "Connected successfully";
   */


   // Check login credentials
   $user = '';
   $password = '';
   if (
      isset($_POST['login']) && !empty($_POST['username'])
      && !empty($_POST['password'])
   ) {
      if(isset($_REQUEST["term"])){
         $readUsers = "SELECT * FROM `users` where `user` LIKE ?";

         if($stmt = mysqli_prepare($conn, $readUsers)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_term);
            
            // Set parameters
            $param_term = $_REQUEST["term"] . '%';
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $autoCompleteResult = mysqli_stmt_get_result($stmt);
                
                // Check number of rows in the result set
                if(mysqli_num_rows($autoCompleteResult) > 0){
                    // Fetch result rows as an associative array
                    while($row = mysqli_fetch_array($autoCompleteResult, MYSQLI_ASSOC)){
                        echo "<p>" . $row["user"] . "</p>";
                    }
                } else{
                    echo "<p>No matches found</p>";
                }
            } else{
                echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
            }
        }
      }
      while ($row = mysqli_fetch_array($result))
      {
          if($row['user'] == $_POST['username']){
             $user = $row['user'];
             $password = $row['password'];
          }
      }
      if (
         $_POST['username'] == $user &&
         $_POST['password'] == $password
      ) {
         $_SESSION['valid'] = true;
         $_SESSION['timeout'] = time();
         $_SESSION['username'] = $user;
         header('Location: maps_page.php');
      } else {
         echo 'Wrong username or password';
      }
   }



   // New user register

   if(isset($_POST['register']) && !empty($_POST['username']) && !empty($_POST['password']) &&
    !empty($_POST['lat']) && !empty($_POST['long']) && !empty($_POST['descriere'])){
      $maxID = mysqli_query($conn, "SELECT max(`ID`) as `max` from `points`");
  
      $result = mysqli_fetch_array($maxID); // face un array de forma 0 0 (ambele valori sunt egale)
      $r = $result[0];
      $next_id = $r + 1; // Incrementarea urmatorului ID
      $sqli_insert = "INSERT INTO `points`(`ID`, `lat`, `long`, `description`) VALUES ('$next_id','$_POST[lat]','$_POST[long]','$_POST[descriere]')";
      $retval = mysqli_query($conn, $sqli_insert);
      $sqli_insert1 = "INSERT INTO `users` (`ID`, `user`, `password`) values ('$next_id', '$_POST[username]', '$_POST[password]')";
      $retval = mysqli_query($conn, $sqli_insert1);
   }
   
   ?>

   <div class="login-form">
     
         <h2>Login Form</h2>
         <form action="" method="POST">
            <label>User</label>
            <div class="search-box">
               <input type="text" style="margin-left: -10px;"  autocomplete = "off" name="username" maxlength = "32" size = "32"> </br>
               <div class="result"></div>  
               
             </div>
             <label>Password</label>
               <input type="password" name="password" maxlength = "32" size = "32" required> <br>

            <button type="submit" name="login">Login</button> 
           
         </form>  
      
   </div>

   <br>

   <div class = "register-form">
   <h2>Register Form</h2>
         <form class="" action="" method="POST">
            <label>User</label>
            <input type="text" autocomplete="off" 
            name="username" maxlength = "32" size = "32"> </br>

            <label>Password</label>
            <input type="password" name="password" maxlength = "32" size = "32" required> <br>

            <label>Latitude</label>
            <input type="text" autocomplete="off" name="lat" maxlength = "32" size = "32"> </br>

            <label>Longitude</label>
            <input type="text" autocomplete="off" name="long" maxlength = "32" size = "32"> </br>

            <label>Description [location name]</label>
            <input type="text" autocomplete="off" name="descriere" maxlength = "10" size = "64"> </br>

            <button type="submit" name="register">Register</button>
         </form>

   </div>

</body>

</html>