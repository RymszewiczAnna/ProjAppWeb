<?php
session_start(); // Rozpocznij sesję

$login = 'admin'; 
$pass = 'haslo123';

$host = 'localhost'; 
$user = 'root'; 
$password = ''; 
$dbname = 'moja_strona';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>