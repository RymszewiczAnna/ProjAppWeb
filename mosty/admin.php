<?php
// ========================
// Inicjalizacja sesji i konfiguracja
// ========================
session_start();
include('cfg.php'); // Plik konfiguracyjny z danymi logowania i połączeniem z bazą danych
// ========================
// Dane logowania do panelu administracyjnego
// ========================
$login = 'admin'; // Login administratora
$pass = 'haslo123'; // Hasło administratora

// ========================
// Funkcja: Formularz Logowania
// ========================
function FormularzLogowania() {
    echo '<form method="POST" action="">';
    echo 'Login: <input type="text" name="login" required><br>';
    echo 'Hasło: <input type="password" name="pass" required><br>';
    echo '<input type="submit" value="Zaloguj">';
    echo '</form>';
}

// ========================
// Obsługa logowania użytkownika
// ========================
if (!isset($_SESSION['loggedin'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sprawdzenie poprawności loginu i hasła z pliku konfiguracyjnego
        if (isset($login, $pass) && $_POST['login'] === $login && $_POST['pass'] === $pass) {
            $_SESSION['loggedin'] = true; 
            header("Location: admin.php"); // Przekierowanie do panelu administracyjnego
            exit();
        } else {
            echo 'Błędny login lub hasło.<br>';
            FormularzLogowania(); 
            exit();
        }
    } else {
        FormularzLogowania(); 
        exit(); 
    }
}

// ========================
// Panel administracyjny
// ========================
echo '<a href="?action=list">Lista podstron</a> | ';
echo '<a href="?action=add">Dodaj nową podstronę</a> | ';
echo '<a href="?action=list_categories">Lista kategorii</a> | ';
echo '<a href="?action=add_category">Dodaj nową kategorię</a> | ';
echo '<a href="?action=list_products">Lista produktów</a> | ';
echo '<a href="?action=add_product">Dodaj nowy produkt</a> | ';
echo '<a href="?action=logout">Wyloguj</a>';

// ========================
// Obsługa akcji w panelu
// ========================
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'list':
            ListaPodstron($conn); // Wywołanie funkcji listującej podstrony
            break;
        case 'edit':
            if (isset($_GET['id'])) {
                EdytujPodstrone((int)$_GET['id'], $conn); // Edycja podstrony
            }
            break;
        case 'add':
            DodajNowaPodstrone($conn); // Dodanie nowej podstrony
            break;
        case 'delete':
            if (isset($_GET['id'])) {
                UsunPodstrone((int)$_GET['id'], $conn); // Usunięcie podstrony
            }
            break;
        case 'logout':
            session_destroy(); // Wylogowanie użytkownika
            header("Location: admin.php");
            exit();
		case 'list_categories':
			ListaKategorii($conn); // Wywołanie funkcji listującej kategorie
			break;
		case 'add_category':
			DodajNowaKategorie($conn); // Dodanie nowej kategorii
			break;
		case 'edit_category':
			if (isset($_GET['id'])) {
				EdytujKategorie((int)$_GET['id'], $conn); // Edycja kategorii
			}
			break;
		case 'delete_category':
			if (isset($_GET['id'])) {
				UsunKategorie((int)$_GET['id'], $conn); // Usunięcie kategorii
			}
			break;
		case 'list_products':
            ListaProduktow($conn);
            break;
        case 'add_product':
            DodajProdukt($conn);
            break;
        case 'edit_product':
            if (isset($_GET['id'])) {
                EdytujProdukt((int)$_GET['id'], $conn);
            }
            break;
        case 'delete_product':
            if (isset($_GET['id'])) {
                UsunProdukt((int)$_GET['id'], $conn);
            }
            break;
		default:
            echo "Nieznana akcja.";
    }
} else {
    echo "<p>Wybierz akcję z menu.</p>";
}

// ========================
// Funkcja: Lista Podstron
// ========================
function ListaPodstron($conn) {
    // Pobranie listy podstron z bazy danych
    $sql = "SELECT * FROM page_list LIMIT 10"; 
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table border="1">';
        echo '<tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["page_title"]) . "</td>";
            echo '<td>';
            echo ' <a href="?action=edit&id=' . (int)$row["id"] . '">Edytuj</a>';
            echo ' | ';
            echo ' <a href="?action=delete&id=' . (int)$row["id"] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\')">Usuń</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo "Brak podstron.";
    }
}

// ========================
// Funkcja: Edytuj Podstronę
// ========================
function EdytujPodstrone($id, $conn) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Pobranie danych z formularza
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $status = isset($_POST['active']) ? 1 : 0;

        // Aktualizacja danych w bazie
        $sql = "UPDATE page_list SET page_title='$title', page_content='$content', status='$status' WHERE id = $id LIMIT 1";
        if ($conn->query($sql) === TRUE) {
            echo "Podstrona zaktualizowana pomyślnie.";
        } else {
            echo "Błąd: " . $conn->error;
        }
    }

    // Pobranie danych podstrony do edycji
    $sql = "SELECT * FROM page_list WHERE id = $id LIMIT 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    // Formularz edycji
    echo '<form method="POST" action="">';
    echo 'Tytuł: <input type="text" name="title" value="' . htmlspecialchars($row["page_title"]) . '" required><br>';
    echo 'Treść: <textarea name="content" required>' . htmlspecialchars($row["page_content"]) . '</textarea><br>';
    echo 'Aktywna: <input type="checkbox" name="active" ' . ($row["status"] == 1 ? 'checked' : '') . '><br>';
    echo '<input type="submit" value="Zapisz">';
    echo '</form>';
}

// ========================
// Funkcja: Dodaj Nową Podstronę
// ========================
function DodajNowaPodstrone($conn) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Pobranie danych z formularza
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $status = isset($_POST['active']) ? 1 : 0;

        // Dodanie nowej podstrony do bazy
        $sql = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$title', '$content', '$status')";
        if ($conn->query($sql) === TRUE) {
            echo "Nowa podstrona dodana pomyślnie.";
        } else {
            echo "Błąd: " . $conn->error;
        }
    }

    // Formularz dodawania nowej podstrony
    echo '<form method="POST" action="">';
    echo 'Tytuł: <input type="text" name="title" required><br>';
    echo 'Treść: <textarea name="content" required></textarea><br>';
    echo 'Aktywna: <input type="checkbox" name="active"><br>';
    echo '<input type="submit" value="Dodaj">';
    echo '</form>';
}

// ========================
// Funkcja: Usuń Podstronę
// ========================
function UsunPodstrone($id, $conn) {
    // Usunięcie podstrony z bazy danych
    $sql = "DELETE FROM page_list WHERE id = $id LIMIT 1";
    if ($conn->query($sql) === TRUE) {
        echo "Podstrona usunięta pomyślnie.";
    } else {
        echo "Błąd: " . $conn->error;
    }
}
function ListaKategorii($conn) {
    // Pobranie listy kategorii głównych z bazy danych
    $sql = "SELECT * FROM kategorie WHERE matka = 0 LIMIT 100";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<h3>Kategorie główne</h3>';
        echo '<ul>';
        while ($row = $result->fetch_assoc()) {
            echo '<li>';
            echo htmlspecialchars($row["nazwa"]);
            echo ' [<a href="?action=edit_category&id=' . (int)$row["id"] . '">Edytuj</a>]';
            echo ' [<a href="?action=delete_category&id=' . (int)$row["id"] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć tę kategorię wraz z podkategoriami?\')">Usuń</a>]';

            // Wyświetlanie podkategorii
            $sql2 = "SELECT * FROM kategorie WHERE matka = " . (int)$row["id"];
            $result2 = $conn->query($sql2);
            if ($result2->num_rows > 0) {
                echo '<ul>';
                while ($row2 = $result2->fetch_assoc()) {
                    echo '<li>';
                    echo htmlspecialchars($row2["nazwa"]);
                    echo ' [<a href="?action=edit_category&id=' . (int)$row2["id"] . '">Edytuj</a>]';
                    echo ' [<a href="?action=delete_category&id=' . (int)$row2["id"] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podkategorię?\')">Usuń</a>]';
                    echo '</li>';
                }
                echo '</ul>';
            }

            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo "Brak kategorii.";
    }
}
function DodajNowaKategorie($conn) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Pobranie danych z formularza
        $nazwa = $conn->real_escape_string($_POST['nazwa']);
        $matka = (int)$_POST['matka'];

        // Dodanie nowej kategorii do bazy
        $sql = "INSERT INTO kategorie (nazwa, matka) VALUES ('$nazwa', $matka)";
        if ($conn->query($sql) === TRUE) {
            echo "Nowa kategoria dodana pomyślnie.";
        } else {
            echo "Błąd: " . $conn->error;
        }
    }

    // Pobranie kategorii głównych do wyboru jako matka
    $sql = "SELECT * FROM kategorie WHERE matka = 0";
    $result = $conn->query($sql);

    // Formularz dodawania nowej kategorii
    echo '<form method="POST" action="">';
    echo 'Nazwa kategorii: <input type="text" name="nazwa" required><br>';
    echo 'Kategoria nadrzędna: ';
    echo '<select name="matka">';
    echo '<option value="0">Brak (kategoria główna)</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . (int)$row['id'] . '">' . htmlspecialchars($row['nazwa']) . '</option>';
    }
    echo '</select><br>';
    echo '<input type="submit" value="Dodaj kategorię">';
    echo '</form>';
}
function EdytujKategorie($id, $conn) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Pobranie danych z formularza
        $nazwa = $conn->real_escape_string($_POST['nazwa']);
        $matka = (int)$_POST['matka'];

        // Aktualizacja danych w bazie
        $sql = "UPDATE kategorie SET nazwa='$nazwa', matka=$matka WHERE id = $id LIMIT 1";
        if ($conn->query($sql) === TRUE) {
            echo "Kategoria zaktualizowana pomyślnie.";
        } else {
            echo "Błąd: " . $conn->error;
        }
    }

    // Pobranie danych kategorii do edycji
    $sql = "SELECT * FROM kategorie WHERE id = $id LIMIT 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    // Pobranie kategorii głównych do wyboru jako matka
    $sql2 = "SELECT * FROM kategorie WHERE matka = 0";
    $result2 = $conn->query($sql2);

    // Formularz edycji kategorii
    echo '<form method="POST" action="">';
    echo 'Nazwa kategorii: <input type="text" name="nazwa" value="' . htmlspecialchars($row["nazwa"]) . '" required><br>';
    echo 'Kategoria nadrzędna: ';
    echo '<select name="matka">';
    echo '<option value="0">Brak (kategoria główna)</option>';
    while ($row2 = $result2->fetch_assoc()) {
        $selected = ($row2['id'] == $row['matka']) ? 'selected' : '';
        echo '<option value="' . (int)$row2['id'] . '" ' . $selected . '>' . htmlspecialchars($row2['nazwa']) . '</option>';
    }
    echo '</select><br>';
    echo '<input type="submit" value="Zapisz">';
    echo '</form>';
}
function UsunKategorie($id, $conn) {
    // Usunięcie kategorii i jej podkategorii z bazy danych
    // Usunięcie podkategorii
    $sql = "DELETE FROM kategorie WHERE matka = $id";
    $conn->query($sql);

    // Usunięcie kategorii
    $sql = "DELETE FROM kategorie WHERE id = $id LIMIT 1";
    if ($conn->query($sql) === TRUE) {
        echo "Kategoria i jej podkategorie zostały usunięte.";
    } else {
        echo "Błąd: " . $conn->error;
    }
}
// ========================
// Funkcja: Lista Produktów
// ========================
function ListaProduktow($conn) {
    // Pobranie listy produktów z bazy danych
    $sql = "SELECT p.*, k.nazwa AS category_name FROM products p LEFT JOIN kategorie k ON p.category_id = k.id LIMIT 100";
    $result = $conn->query($sql);

    echo '<h3>Lista Produktów</h3>';
    echo '<a href="?action=add_product">Dodaj nowy produkt</a><br><br>';

    if ($result->num_rows > 0) {
        echo '<table border="1">';
        echo '<tr>
                <th>ID</th>
                <th>Tytuł</th>
                <th>Cena netto</th>
                <th>VAT</th>
                <th>Ilość w magazynie</th>
                <th>Status dostępności</th>
                <th>Kategoria</th>
                <th>Akcje</th>
              </tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["title"]) . "</td>";
            echo "<td>" . number_format($row["price_net"], 2) . " PLN</td>";
            echo "<td>" . $row["vat_tax"] . "%</td>";
            echo "<td>" . $row["stock_quantity"] . "</td>";
            echo "<td>" . ($row["availability_status"] == 1 ? 'Dostępny' : 'Niedostępny') . "</td>";
            echo "<td>" . htmlspecialchars($row["category_name"]) . "</td>";
            echo '<td>';
            echo ' <a href="?action=edit_product&id=' . (int)$row["id"] . '">Edytuj</a>';
            echo ' | ';
            echo ' <a href="?action=delete_product&id=' . (int)$row["id"] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć ten produkt?\')">Usuń</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo "Brak produktów.";
    }
}
// ========================
// Funkcja: Pobierz Kategorie
// ========================
function PobierzKategorie($conn) {
    $categories = [];
    $sql = "SELECT id, nazwa FROM kategorie";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $categories[$row['id']] = $row['nazwa'];
    }
    return $categories;
}
// ========================
// Funkcja: Dodaj Produkt
// ========================
function DodajProdukt($conn) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Pobranie danych z formularza
        $title = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        $date_expiration = !empty($_POST['date_expiration']) ? $conn->real_escape_string($_POST['date_expiration']) : NULL;
        $price_net = (float)$_POST['price_net'];
        $vat_tax = (float)$_POST['vat_tax'];
        $stock_quantity = (int)$_POST['stock_quantity'];
        $availability_status = isset($_POST['availability_status']) ? 1 : 0;
        $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : NULL;
        $dimensions = $conn->real_escape_string($_POST['dimensions']);
        $image_path = $conn->real_escape_string($_POST['image_path']);

        // Dodanie nowego produktu do bazy
        $sql = "INSERT INTO products (title, description, date_expiration, price_net, vat_tax, stock_quantity, availability_status, category_id, dimensions, image_path) 
                VALUES ('$title', '$description', " . ($date_expiration ? "'$date_expiration'" : "NULL") . ", $price_net, $vat_tax, $stock_quantity, $availability_status, " . ($category_id ? $category_id : "NULL") . ", '$dimensions', '$image_path')";
        if ($conn->query($sql) === TRUE) {
            echo "Nowy produkt dodany pomyślnie.";
        } else {
            echo "Błąd: " . $conn->error;
        }
    }

    // Pobranie kategorii do wyboru
    $categories = PobierzKategorie($conn);

    // Formularz dodawania nowego produktu
    echo '<h3>Dodaj Produkt</h3>';
    echo '<form method="POST" action="">';
    echo 'Tytuł: <input type="text" name="title" required><br>';
    echo 'Opis: <textarea name="description" required></textarea><br>';
    echo 'Data wygaśnięcia: <input type="date" name="date_expiration"><br>';
    echo 'Cena netto: <input type="number" step="0.01" name="price_net" required> PLN<br>';
    echo 'Podatek VAT: <input type="number" step="0.01" name="vat_tax" value="23.00" required> %<br>';
    echo 'Ilość w magazynie: <input type="number" name="stock_quantity" required><br>';
    echo 'Dostępny: <input type="checkbox" name="availability_status" checked><br>';
    echo 'Kategoria: <select name="category_id">';
    echo '<option value="">Brak</option>';
    foreach ($categories as $cat_id => $cat_name) {
        echo '<option value="' . $cat_id . '">' . htmlspecialchars($cat_name) . '</option>';
    }
    echo '</select><br>';
    echo 'Gabaryt produktu: <input type="text" name="dimensions"><br>';
    echo 'Ścieżka do zdjęcia: <input type="text" name="image_path"><br>';
    echo '<input type="submit" value="Dodaj produkt">';
    echo '</form>';
}
// ========================
// Funkcja: Edytuj Produkt
// ========================
function EdytujProdukt($id, $conn) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Pobranie danych z formularza
        $title = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        $date_expiration = !empty($_POST['date_expiration']) ? $conn->real_escape_string($_POST['date_expiration']) : NULL;
        $price_net = (float)$_POST['price_net'];
        $vat_tax = (float)$_POST['vat_tax'];
        $stock_quantity = (int)$_POST['stock_quantity'];
        $availability_status = isset($_POST['availability_status']) ? 1 : 0;
        $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : NULL;
        $dimensions = $conn->real_escape_string($_POST['dimensions']);
        $image_path = $conn->real_escape_string($_POST['image_path']);

        // Aktualizacja danych w bazie
        $sql = "UPDATE products SET 
                title='$title', 
                description='$description', 
                date_expiration=" . ($date_expiration ? "'$date_expiration'" : "NULL") . ", 
                price_net=$price_net, 
                vat_tax=$vat_tax, 
                stock_quantity=$stock_quantity, 
                availability_status=$availability_status, 
                category_id=" . ($category_id ? $category_id : "NULL") . ", 
                dimensions='$dimensions', 
                image_path='$image_path',
                date_modified=NOW()
                WHERE id = $id LIMIT 1";
        if ($conn->query($sql) === TRUE) {
            echo "Produkt zaktualizowany pomyślnie.";
        } else {
            echo "Błąd: " . $conn->error;
        }
    }

    // Pobranie danych produktu do edycji
    $sql = "SELECT * FROM products WHERE id = $id LIMIT 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if (!$row) {
        echo "Produkt nie został znaleziony.";
        return;
    }

    // Pobranie kategorii do wyboru
    $categories = PobierzKategorie($conn);

    // Formularz edycji produktu
    echo '<h3>Edytuj Produkt</h3>';
    echo '<form method="POST" action="">';
    echo 'Tytuł: <input type="text" name="title" value="' . htmlspecialchars($row["title"]) . '" required><br>';
    echo 'Opis: <textarea name="description" required>' . htmlspecialchars($row["description"]) . '</textarea><br>';
    echo 'Data wygaśnięcia: <input type="date" name="date_expiration" value="' . ($row["date_expiration"] ? htmlspecialchars(date('Y-m-d', strtotime($row["date_expiration"]))) : '') . '"><br>';
    echo 'Cena netto: <input type="number" step="0.01" name="price_net" value="' . $row["price_net"] . '" required> PLN<br>';
    echo 'Podatek VAT: <input type="number" step="0.01" name="vat_tax" value="' . $row["vat_tax"] . '" required> %<br>';
    echo 'Ilość w magazynie: <input type="number" name="stock_quantity" value="' . $row["stock_quantity"] . '" required><br>';
    echo 'Dostępny: <input type="checkbox" name="availability_status" ' . ($row["availability_status"] == 1 ? 'checked' : '') . '><br>';
    echo 'Kategoria: <select name="category_id">';
    echo '<option value="">Brak</option>';
    foreach ($categories as $cat_id => $cat_name) {
        $selected = ($cat_id == $row["category_id"]) ? 'selected' : '';
        echo '<option value="' . $cat_id . '" ' . $selected . '>' . htmlspecialchars($cat_name) . '</option>';
    }
    echo '</select><br>';
    echo 'Gabaryt produktu: <input type="text" name="dimensions" value="' . htmlspecialchars($row["dimensions"]) . '"><br>';
    echo 'Ścieżka do zdjęcia: <input type="text" name="image_path" value="' . htmlspecialchars($row["image_path"]) . '"><br>';
    echo '<input type="submit" value="Zapisz">';
    echo '</form>';
}
// ========================
// Funkcja: Usuń Produkt
// ========================
function UsunProdukt($id, $conn) {
    // Usunięcie produktu z bazy danych
    $sql = "DELETE FROM products WHERE id = $id LIMIT 1";
    if ($conn->query($sql) === TRUE) {
        echo "Produkt usunięty pomyślnie.";
    } else {
        echo "Błąd: " . $conn->error;
    }
}
?>