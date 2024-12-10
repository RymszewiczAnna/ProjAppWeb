<?php
// ========================
// Dołączenie pliku konfiguracyjnego
// ========================
include('cfg.php'); // Plik zawierający konfigurację bazy danych i inne ustawienia

// ========================
// Funkcja: Wyświetlenie formularza kontaktowego
// ========================
function PokazKontakt() {
    echo '<form method="POST" action="contact.php">';
    echo '<label for="name">Imię:</label>';
    echo '<input type="text" id="name" name="name" required><br>';
    echo '<label for="email">Email:</label>';
    echo '<input type="email" id="email" name="email" required><br>';
    echo '<label for="message">Wiadomość:</label>';
    echo '<textarea id="message" name="message" required></textarea><br>';
    echo '<button type="submit" name="send">Wyślij</button>';
    echo '</form>';
}

// ========================
// Funkcja: Wysłanie wiadomości e-mail
// ========================
function WyslijMailKontakt() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {
        // Pobranie danych z formularza i zabezpieczenie przed wstrzyknięciem kodu
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $messageContent = htmlspecialchars($_POST['message']);

        // Adres e-mail odbiorcy
        $to = "admin@example.com"; 
        $subject = "Nowa wiadomość z formularza kontaktowego";

        // Treść wiadomości
        $message = "Imię: $name\n";
        $message .= "Email: $email\n";
        $message .= "Wiadomość: $messageContent\n";

        // Nagłówki wiadomości
        $headers = "From: $email";

        // Wysłanie wiadomości e-mail
        if (mail($to, $subject, $message, $headers)) {
            echo "Wiadomość została wysłana.";
        } else {
            echo "Wystąpił problem z wysłaniem wiadomości.";
        }
    }
}

// ========================
// Obsługa żądania POST lub GET
// ========================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {
    // Jeśli formularz został wysłany, wywołaj funkcję wysyłającą e-mail
    WyslijMailKontakt();
} else {
    // Jeśli formularz nie został wysłany, wyświetl formularz kontaktowy
    PokazKontakt();
}
?>