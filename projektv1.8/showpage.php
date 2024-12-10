<?php
// ========================
// Dołączenie pliku konfiguracyjnego
// ========================
include('cfg.php'); // Plik zawierający konfigurację bazy danych i inne ustawienia

// ========================
// Pobranie listy aktywnych podstron z bazy danych
// ========================
$sql = "SELECT * FROM page_list WHERE status = 1"; // Pobranie tylko aktywnych podstron
$result = $conn->query($sql); // Wykonanie zapytania SQL

// ========================
// Wyświetlenie podstron
// ========================
if ($result->num_rows > 0) {
    // Iteracja przez wyniki zapytania
    while ($row = $result->fetch_assoc()) {
        // Wyświetlenie tytułu podstrony
        echo "<h2>" . htmlspecialchars($row["page_title"]) . "</h2>";
        // Wyświetlenie treści podstrony
        echo "<p>" . htmlspecialchars($row["page_content"]) . "</p>";
    }
} else {
    // Komunikat, gdy brak wyników
    echo "<p>Brak dostępnych podstron do wyświetlenia.</p>";
}
?>