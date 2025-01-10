<?php
//----------STYL-------------


echo '<style>';
include('css/admin_style.css');
echo '</style>';

// --------FUNKCJE------------


// Wyświetlenie formularza kontaktowego
function PokazKontakt()
{
    echo '<h1>Formularz Kontaktowy</h1>';
    
    // Formularz kontaktowy
    echo '<form method="post" action="?action=wyslij">
            <label>Temat:</label>
            <input type="text" name="temat" required>
            <label>Treść:</label>
            <textarea name="tresc" required></textarea>
            <label>Email:</label>
            <input type="email" name="email" required>
            <input type="submit" name="wyslij_submit" value="Wyślij">
          </form>';

    // Formularz przypomnienia hasła
    echo '<h2>Przypomnienie hasła</h2>';
    echo '<form method="post" action="?action=przypomnij_haslo">
            <label>Email:</label>
            <input type="email" name="email" required>
            <input type="submit" name="przypomnij_haslo_submit" value="Przypomnij hasło">
          </form>';
}

// Wysyłanie maila z formularza kontaktowego
function WyslijMailKontakt()
{
    if (isset($_POST['wyslij_submit'])) {
        if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
            echo '[nie_wypelniles_pola]';
            PokazKontakt();
        } else {
            $mail['subject']   = $_POST['temat'];
            $mail['body']      = $_POST['tresc'];
            $mail['sender']    = $_POST['email'];
            $mail['recipient'] = 'rymszewiczanna@gmail.com';

            $header  = "From: Formularz kontaktowy <" . $mail['sender'] . ">\n";
            $header .= "MIME-Version: 1.0\nCorrect-Type: text/plain; charset=utf8\nContent-Transfer-Encoding: ";
            $header .= "X-Sender: <" . $mail['sender'] . "\n";
            $header .= "X-Mailer: PRapWW mail 1.2\n";
            $header .= "X-Priority: 3\n";
            $header .= "Return-Path: <" . $mail['sender'] . ">\n";

            mail($mail['recipient'], $mail['subject'], $mail['body'], $header);

            echo '[wiadomosc_wyslana]';
        }
    }
}

// Przypomnienie hasła
function PrzypomnijHaslo()
{
    if (isset($_POST['przypomnij_haslo_submit'])) {
        if (empty($_POST['email'])) {
            echo '[nie_wypelniles_pola_email]';
        } else {
            $odbiorca = $_POST['email'];
            $haslo = LosoweHaslo(); // Funkcja do generowania losowego hasła

            $subject = 'Przypomnienie hasła';
            $body = 'Twoje nowe hasło: ' . $haslo;

            $header = "From: Przypomnienie hasła <admin@example.com>\n";
            $header .= "MIME-Version: 1.0\nCorrect-Type: text/plain; charset=utf8\nContent-Transfer-Encoding: ";
            $header .= "X-Sender: <admin@example.com\n";
            $header .= "X-Mailer: PRapWW mail 1.2\n";
            $header .= "X-Priority: 3\n";
            $header .= "Return-Path: <admin@example.com>\n";

            mail($odbiorca, $subject, $body, $header);

            echo '[haslo_przypomniane]';
        }
    }
}

// Funkcja generująca losowe hasło
function LosoweHaslo($dlugosc = 8)
{
    $znaki = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle($znaki), 0, $dlugosc);
}

//------------LOGIKA------------

include("cfg.php");

session_start();

// Obsługa żądań POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'wyslij') {
        WyslijMailKontakt();
    } elseif (isset($_POST['action']) && $_POST['action'] === 'przypomnij_haslo') {
        PrzypomnijHaslo();
    }
}

// Wyświetlenie formularza
PokazKontakt();



?>
