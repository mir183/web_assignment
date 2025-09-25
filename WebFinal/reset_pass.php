<?php

include "connect.php";
session_start();

$email="";
$password="";
$err=null;
if(!isset($_SESSION["email"])){
    header("Location: login.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $email=$_SESSION["email"];
    $password=$_POST["password"];
    $confirm_password=$_POST["confirm_password"];

    if($password!=$confirm_password){
        $err="Password and Confirm Password do not match.";
    }else{
        $stmt = $conn->prepare("UPDATE userinfo SET pass = ? WHERE email = ?");
        $stmt->bind_param("ss",$password,$email);
        
        if($stmt->execute()){
            header("Location: dashboard.php");
            exit();
        }else{
            $err="Error in resetting password. Please try again.";
        }

    $stmt->close();
    }


}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="dark-theme.css" rel="stylesheet">
  </head>
  <body>
    <!-- Dark Theme Toggle Button -->
    <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
        <i class="bi bi-moon-fill" id="themeIcon"></i>
    </button>
    <!-- <h1>Hello, world!</h1> --> 
                    <form method="post" style="max-width: 500px; margin: auto; margin-top: 100px;">

                <div class="form-group mt-3">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                </div>
                <div class="form-group mt-3">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" placeholder="Confirm Password" name="confirm_password">
                </div>

                <?php 
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && $password!=$confirm_password) {
                        echo "<div class='text-danger'>$err</div>";
                    }
                ?>
                <!-- <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Check me out</label>
                </div> -->
                
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
                </form>
            




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="dark-theme.js"></script>
  </body>
</html>