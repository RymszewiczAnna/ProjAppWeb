<?php
include('cfg.php'); 


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


function WyslijMailKontakt() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {
        $to = "admin@example.com"; 
        $subject = "Nowa wiadomość z formularza kontaktowego";
        $message = "Imię: " . htmlspecialchars($_POST['name']) . "\n";
        $message .= "Email: " . htmlspecialchars($_POST['email']) . "\n";
        $message .= "Wiadomość: " . htmlspecialchars($_POST['message']) . "\n";
        $headers = "From: " . htmlspecialchars($_POST['email']);

        if (mail($to, $subject, $message, $headers)) {
            echo "Wiadomość została wysłana.";
        } else {
            echo "Wystąpił problem z wysłaniem wiadomości.";
        }
    }
}


function PrzypomnijHaslo() {
    $to = "admin@example.com"; 
    $subject = "Przypomnienie hasła";
    $message = "Twoje hasło do panelu administracyjnego to: haslo123"; 
    $headers = "From: no-reply@example.com";

    if (mail($to, $subject, $message, $headers)) {
        echo "Hasło zostało wysłane na adres e-mail administratora.";
    } else {
        echo "Wystąpił problem z wysłaniem wiadomości.";
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {
    WyslijMailKontakt();
} elseif (isset($_GET['action']) && $_GET['action'] == 'remind') {
    PrzypomnijHaslo();
} else {
    PokazKontakt();
}
?>
