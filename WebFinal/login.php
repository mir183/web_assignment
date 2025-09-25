<?php
include "connect.php";
session_start();


$email="";
$password="";
$err=null;

if (isset($_SESSION["email"])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $email=$_POST["email"];
    $password=$_POST["password"];

    $stmt = $conn->prepare("SELECT id,name,email,pass FROM userinfo WHERE email = ? AND pass = ?");
    $stmt->bind_param("ss",$email,$password);
    $stmt->execute();
    $result=$stmt->get_result();

    if($result->num_rows>0){
        $user=$result->fetch_assoc();
        $_SESSION["username"]=$user["name"];
        $_SESSION["email"]=$user["email"];
        
        // Regenerate session ID for security and uniqueness
        session_regenerate_id(true);
        $session_id=session_id();
        $current_time=date('Y-m-d H:i:s');
        
        // Get existing session IDs
        $get_stmt=$conn->prepare("SELECT sessionids FROM userinfo WHERE email = ?");
        $get_stmt->bind_param("s", $email);
        $get_stmt->execute();
        $get_result=$get_stmt->get_result();
        $existing_data=$get_result->fetch_assoc();
        $get_stmt->close();
        
        // Parse existing session IDs or create new array
        $session_array=[];
        if (!empty($existing_data['sessionids'])) {
            $session_array=json_decode($existing_data['sessionids'],true)?:[];
        }
        
        // Check if this session ID already exists (avoid duplicates)
        $session_exists=false;
        foreach ($session_array as $existing_session) {
            if ($existing_session['session_id']===$session_id) {
                $session_exists=true;
                break;
            }
        }
        
        // Add new session only if it doesn't exist
        if(!$session_exists){
            $session_array[]=[
                'session_id'=>$session_id,
                'login_time'=>$current_time,
                'ip_address'=>$_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ];
        }
        
        // Keep only last 10 sessions to prevent unlimited growth
        if (count($session_array)>10) {
            $session_array=array_slice($session_array,-10);
        }
        
        // Update database with JSON array
        $update_stmt=$conn->prepare("UPDATE userinfo SET sessionids = ? WHERE email = ?");
        $json_sessions=json_encode($session_array);
        $update_stmt->bind_param("ss",$json_sessions,$email);
        $update_stmt->execute();
        $update_stmt->close();
        
        header("Location: dashboard.php");
        exit();
    }else{
        $err="The user does not exist or invalid credentials!";
    }

    $stmt->close();
}

?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
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

        <div class="form-section" style="max-width: 500px; margin: 100px auto 0;">
            <form method="post">
                <h2 class="text-center mb-4">Login to Your Account</h2>
                
                <div class="form-group">
                    <label for="email" class="mb-3">Email address</label>
                    <input type="text" class="form-control" id="email" placeholder="Enter email" name="email">
                    <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                </div>
                <div class="form-group mt-3">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                </div>
                <!-- <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Check me out</label>
                </div> -->
                <?php
            if($err!==null){
                echo "<p class='text-danger mt-3'>$err</p>";
            }
            ?>  
            <p class="mt-3">Don't have an account? <a href="reg.php">Register here</a></p>

                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </form>
        </div>
        



        <script src="dark-theme.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>



