<?php
include 'connect.php';  
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Update the current session to mark logout time
$email=$_SESSION["email"];
$current_session_id = session_id();
$logout_time = date('Y-m-d H:i:s');

// Get existing session data
$stmt=$conn->prepare("SELECT sessionids FROM userinfo WHERE email = ?");
$stmt->bind_param("s",$email);
$stmt->execute();
$result=$stmt->get_result();
$user_data=$result->fetch_assoc();
$stmt->close();

if (!empty($user_data['sessionids'])) {
    $session_array=json_decode($user_data['sessionids'],true)?:[];

    // Find and update the current session with logout time
    for($i=0;$i<count($session_array);$i++){
        if($session_array[$i]['session_id']===$current_session_id){
            $session_array[$i]['logout_time']=$logout_time;
            break;
        }
    }
    
    // Save updated session data
    $update_stmt=$conn->prepare("UPDATE userinfo SET sessionids = ? WHERE email = ?");
    $json_sessions=json_encode($session_array);
    $update_stmt->bind_param("ss",$json_sessions,$email);
    $update_stmt->execute();
    $update_stmt->close();
}

session_unset();
session_destroy();
header("Location: login.php");
exit();
?>  