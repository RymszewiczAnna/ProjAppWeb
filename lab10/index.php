<?php
// ========================
// Dołączenie pliku konfiguracyjnego
// ========================
include('cfg.php'); // Plik zawierający konfigurację bazy danych i inne ustawienia

// ========================
// Wyłączenie wyświetlania ostrzeżeń i błędów
// ========================
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING); // Ukrycie ostrzeżeń i błędów, które mogą być nieistotne

// ========================
// Pobranie identyfikatora podstrony z parametru GET
// ========================
$idp = isset($_GET['idp']) ? $conn->real_escape_string($_GET['idp']) : ''; // Zabezpieczenie przed SQL Injection

// ========================
// Pobranie danych podstrony z bazy danych
// ========================
$sql = "SELECT * FROM page_list WHERE status = 1 AND (id = '$idp' OR page_title = '$idp') LIMIT 1"; // Pobranie aktywnej podstrony
$result = $conn->query($sql); // Wykonanie zapytania
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- ========================
         Sekcja nagłówka strony
         ======================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Najdłuższe mosty świata</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Dołączenie pliku CSS -->
    <script src="js/kolorujtlo.js" type="text/javascript"></script> <!-- Skrypt zmieniający tło -->
    <script src="js/timedate.js" type="text/javascript"></script> <!-- Skrypt zegarka i daty -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> <!-- Dołączenie jQuery -->
</head>
<body onload="startclock()"> <!-- Uruchomienie zegarka po załadowaniu strony -->

<header>
    <h1>Najdłuższe mosty świata</h1>
</header>

<!-- ========================
     Nawigacja zmiany tła
     ======================== -->
<nav>
    <ul>
        <form method="POST" name="background">
            <li><input type="button" value="żółty" onclick="changeBackground('#FFF000')"></li>
            <li><input type="button" value="czarny" onclick="changeBackground('#000000')"></li>
            <li><input type="button" value="biały" onclick="changeBackground('#FFFFFF')"></li>
            <li><input type="button" value="zielony" onclick="changeBackground('#00FF00')"></li>
            <li><input type="button" value="niebieski" onclick="changeBackground('#0000FF')"></li>
            <li><input type="button" value="pomarańczowy" onclick="changeBackground('#FF8000')"></li>
            <li><input type="button" value="szary" onclick="changeBackground('#c0c0c0')"></li>
            <li><input type="button" value="czerwony" onclick="changeBackground('#FF0000')"></li>
        </form>
    </ul>
</nav>

<!-- ========================
     Główna nawigacja strony
     ======================== -->
<nav>
    <ul>
        <li><a href="index.php?idp=glowna">Główna</a></li>
        <li><a href="index.php?idp=chiny">Chiny</a></li>
        <li><a href="index.php?idp=contact">Kontakt</a></li>
        <li><a href="index.php?idp=filmy">Filmy</a></li>
        <li><a href="index.php?idp=inne">Inne</a></li>
        <li><a href="index.php?idp=tajwan">Tajwan</a></li>
        <li><a href="index.php?idp=usa">USA</a></li>
    </ul>
</nav>

<!-- ========================
     Zegarek i data
     ======================== -->
<nav>
    <div id="zegarek"></div> <!-- Wyświetlanie zegarka -->
    <div id="data"></div> <!-- Wyświetlanie daty -->
</nav>
<!-- ========================
     Wyświetlanie kategorii
     ======================== -->
<section>
    <h2>Kategorie</h2>
    <?php
    // Pobranie kategorii głównych
    $sql = "SELECT * FROM kategorie WHERE matka = 0 LIMIT 100";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<ul>';
        while ($row = $result->fetch_assoc()) {
            echo '<li>';
            echo '<a href="index.php?kategoria=' . (int)$row['id'] . '">' . htmlspecialchars($row['nazwa']) . '</a>';

            // Pobranie podkategorii
            $sql2 = "SELECT * FROM kategorie WHERE matka = " . (int)$row['id'];
            $result2 = $conn->query($sql2);
            if ($result2->num_rows > 0) {
                echo '<ul>';
                while ($row2 = $result2->fetch_assoc()) {
                    echo '<li><a href="index.php?kategoria=' . (int)$row2['id'] . '">' . htmlspecialchars($row2['nazwa']) . '</a></li>';
                }
                echo '</ul>';
            }
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo "Brak kategorii do wyświetlenia.";
    }
    ?>
</section>

<!-- ========================
     Animacje
     ======================== -->
<nav>
    <div id="animacjaTestowa1" class="test-block">Kliknij, a się powiększę</div>
    <script>
        // Animacja powiększania po kliknięciu
        $("#animacjaTestowa1").on("click", function() {
            $(this).animate({
                width: "500px",
                opacity: 0.4,
                fontSize: "3em",
                borderWidth: "10px"
            }, 1500);
        });
    </script>
    <div id="animacjaTestowa2" class="test-block">
        Najedź kursorem, a się powiększę
    </div>
    <script>
        // Animacja powiększania po najechaniu kursorem
        $("#animacjaTestowa2").on({
            "mouseover": function() {
                $(this).animate({
                    width: 300
                }, 800);
            },
            "mouseout": function() {
                $(this).animate({
                    width: 200
                }, 800);
            }
        });
    </script>
    <div id="animacjaTestowa3" class="test-block">
        Kliknij abym urósł
    </div>
    <script>
        // Animacja powiększania po kliknięciu z ograniczeniem
        $("#animacjaTestowa3").on("click", function() {
            if (!$(this).is(":animated")) {
                $(this).animate({
                    width: "+=" + 50,
                    height: "+=" + 10,
                    opacity: "-=" + 0.1
                }, 3000);
            }
        });
    </script>
</nav>

<!-- ========================
     Sekcja treści
     ======================== -->
<section>
    <?php
    // Wyświetlanie treści podstrony
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h2>" . htmlspecialchars($row["page_title"]) . "</h2>"; // Tytuł podstrony
        echo "<div>" . htmlspecialchars($row["page_content"]) . "</div>"; // Treść podstrony
    } else {
        // Komunikat, gdy podstrona nie istnieje
        echo "<h2>Strona nie istnieje</h2>";
        echo "<p>Podana strona nie została znaleziona w bazie danych.</p>";
    }
    ?>
</section>

<!-- ========================
     Stopka strony
     ======================== -->
<footer>
    <p>&copy; 2024 Najdłuższe mosty świata. Wszelkie prawa zastrzeżone.</p>
</footer>

</body>
</html>