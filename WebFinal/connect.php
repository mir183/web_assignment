<?php

$conn=new mysqli("localhost","root","","wf");


if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}


?>