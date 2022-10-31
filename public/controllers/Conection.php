<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
header("content-type:text/html; charset=utf-8");
$hostname_gb =  "s1.lionfree.net";
$database_gb =  "leaflion_db01";
$username_gb =  "leaflion_forum";
$password_gb =  "Sky940312dna";

// Create connection
$conn = new mysqli($hostname_gb, $username_gb, $password_gb, $database_gb);
$conn->set_charset('utf8mb4');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
?>

