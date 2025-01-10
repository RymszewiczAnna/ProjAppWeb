<?php
session_start(); // Inicjalizacja sesji
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


<!-- ========================
     Główna nawigacja strony
     ======================== -->


<!-- ========================
     Zegarek i data
     ======================== -->
<nav>
    <div id="zegarek"></div> <!-- Wyświetlanie zegarka -->
    <div id="data"></div> <!-- Wyświetlanie daty -->
</nav>
<?php
include("cfg.php");
include("showpage.php");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
if ($_GET['idp'] == 'glowna') {
    echo PokazPodstrone(1);
} elseif ($_GET['idp'] == 'chiny') {
    echo PokazPodstrone(2);
} elseif ($_GET['idp'] == 'contact') {
    echo PokazPodstrone(3);
} elseif ($_GET['idp'] == 'filmy') {
    echo PokazPodstrone(4);
} elseif ($_GET['idp'] == 'inne') {
    echo PokazPodstrone(5);
}	elseif ($_GET['idp'] == 'tajwan') {
    echo PokazPodstrone(6);
} 
elseif ($_GET['idp'] == 'usa') {
    echo PokazPodstrone(7);
}else {
    echo PokazPodstrone(1);
}
?>
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
     Sekcja produktów
     ======================== -->
<section>
    <h2>Produkty</h2>
    <?php
    // Pobranie aktualnej daty i czasu
    $today = date('Y-m-d H:i:s');

    // Definiowanie warunków dostępności
    $conditions = "availability_status = 1 AND stock_quantity > 0 AND (date_expiration IS NULL OR date_expiration > '$today')";

    // Pobranie produktów spełniających warunki
    if (isset($_GET['kategoria'])) {
        $sql = "SELECT p.*, k.nazwa AS category_name FROM products p LEFT JOIN kategorie k ON p.category_id = k.id WHERE $conditions AND p.category_id = " . (int)$_GET['kategoria'] . " LIMIT 100";
    } else {
        $sql = "SELECT p.*, k.nazwa AS category_name FROM products p LEFT JOIN kategorie k ON p.category_id = k.id WHERE $conditions LIMIT 100";
    }
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="product-list">';
        while ($row = $result->fetch_assoc()) {
            // Obliczenie ceny brutto
            $price_gross = $row['price_net'] * (1 + $row['vat_tax'] / 100);
            echo '<div class="product-item">';
            echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
            if ($row['image_path']) {
                echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['title']) . '" style="max-width:200px;"><br>';
            }
            echo '<p>' . htmlspecialchars($row['description']) . '</p>';
            echo '<p>Cena netto: ' . number_format($row['price_net'], 2) . ' PLN</p>';
            echo '<p>VAT: ' . $row['vat_tax'] . '%</p>';
            echo '<p>Cena brutto: ' . number_format($price_gross, 2) . ' PLN</p>';
            echo '<p>Ilość dostępnych sztuk: ' . $row['stock_quantity'] . '</p>';
            echo '<p>Kategoria: ' . htmlspecialchars($row['category_name']) . '</p>';
            echo '<form method="post" action="index.php?action=add&product_id=' . $row['id'] . '">';
            echo '<input type="number" name="quantity" value="1" min="1" max="' . $row['stock_quantity'] . '">';
            echo '<button type="submit">Dodaj do koszyka</button>';
            echo '</form>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>Obecnie brak dostępnych produktów.</p>';
    }
    ?>
</section>
<section>
<h2> Koszyk </h2>
<?php


// Funkcja dodawania produktu do koszyka
function addToCart($productId, $quantity) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = []; // Inicjalizacja koszyka, jeśli nie istnieje
    }

    // Jeśli produkt już istnieje w koszyku, zwiększ ilość
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    } else {
        // Pobierz dane produktu z bazy danych
        global $conn;
        $sql = "SELECT * FROM products WHERE id = " . (int)$productId;
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $_SESSION['cart'][$productId] = [
                'title' => $product['title'],
                'price_net' => $product['price_net'],
                'vat_tax' => $product['vat_tax'],
                'quantity' => $quantity
            ];
        }
    }
}

// Funkcja aktualizacji ilości produktu w koszyku
function updateCart($productId, $quantity) {
    if (isset($_SESSION['cart'][$productId])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$productId]); // Usuń produkt, jeśli ilość wynosi 0
        }
    }
}

// Funkcja usuwania produktu z koszyka
function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

// Funkcja wyświetlania koszyka
function showCart() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo '<p>Koszyk jest pusty.</p>';
        return;
    }

    echo '<h2>Twój koszyk</h2>';
    echo '<form method="post" action="index.php?action=update">';
    echo '<table border="1" cellpadding="10">';
    echo '<tr><th>Produkt</th><th>Cena netto</th><th>VAT</th><th>Cena brutto</th><th>Ilość</th><th>Razem</th><th>Akcja</th></tr>';

    $total = 0;
    foreach ($_SESSION['cart'] as $productId => $product) {
        $price_gross = $product['price_net'] * (1 + $product['vat_tax'] / 100);
        $subtotal = $price_gross * $product['quantity'];
        $total += $subtotal;

        echo '<tr>';
        echo '<td>' . htmlspecialchars($product['title']) . '</td>';
        echo '<td>' . number_format($product['price_net'], 2) . ' PLN</td>';
        echo '<td>' . $product['vat_tax'] . '%</td>';
        echo '<td>' . number_format($price_gross, 2) . ' PLN</td>';
        echo '<td><input type="number" name="quantities[' . $productId . ']" value="' . $product['quantity'] . '" min="1"></td>';
        echo '<td>' . number_format($subtotal, 2) . ' PLN</td>';
        echo '<td><a href="index.php?action=remove&product_id=' . $productId . '">Usuń</a></td>';
        echo '</tr>';
    }

    echo '<tr><td colspan="5"><strong>Łączna wartość:</strong></td><td colspan="2"><strong>' . number_format($total, 2) . ' PLN</strong></td></tr>';
    echo '</table>';
    echo '<button type="submit">Zaktualizuj koszyk</button>';
    echo '</form>';
    echo '<p><a href="index.php">Powrót do strony głównej</a></p>'; // Link powrotu do strony głównej
}

// Obsługa akcji (dodawanie, aktualizacja, usuwanie produktów)
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add' && isset($_GET['product_id']) && is_numeric($_GET['product_id']) && isset($_POST['quantity']) && is_numeric($_POST['quantity'])) {
        addToCart((int)$_GET['product_id'], (int)$_POST['quantity']); // Dodaj określoną ilość produktu do koszyka
        header("Location: index.php"); // Przekierowanie na stronę główną po dodaniu do koszyka
        exit();
    } elseif ($_GET['action'] == 'update' && isset($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $productId => $quantity) {
            updateCart((int)$productId, (int)$quantity); // Zaktualizuj ilość produktów w koszyku
        }
        header("Location: index.php"); // Przekierowanie na stronę główną po aktualizacji koszyka
        exit();
    } elseif ($_GET['action'] == 'remove' && isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
        removeFromCart((int)$_GET['product_id']); // Usuń produkt z koszyka
    }
}
showCart(); // Wyświetlenie koszyka
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
     Stopka strony
     ======================== -->
<footer>
    <p>&copy; 2024 Najdłuższe mosty świata. Wszelkie prawa zastrzeżone.</p>
</footer>

</body>
</html>