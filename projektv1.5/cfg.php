<?php
$host = 'localhost';
$user = 'root'; // sprawdź swoje dane logowania
$password = 'root'; // sprawdź swoje dane logowania
$dbname = 'moja_strona';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>