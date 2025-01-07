<?php


// ========================
// Konfiguracja połączenia z bazą danych
// ========================
$host = 'localhost'; // Adres hosta bazy danych (zwykle localhost)
$user = 'root'; // Nazwa użytkownika bazy danych
$password = ''; // Hasło użytkownika bazy danych
$dbname = 'moja_strona'; // Nazwa bazy danych

// ========================
// Połączenie z bazą danych
// ========================
$conn = new mysqli($host, $user, $password, $dbname); // Tworzenie połączenia z bazą danych

// Sprawdzenie, czy połączenie z bazą danych się powiodło
if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error); // Zakończenie działania w przypadku błędu
}

// ========================
// Ustawienia kodowania znaków
// ========================
$conn->set_charset("utf8"); // Ustawienie kodowania znaków na UTF-8, aby obsługiwać polskie znaki
?>