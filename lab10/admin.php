<?php
// ========================
// Inicjalizacja sesji i konfiguracja
// ========================
session_start();
include('cfg.php'); // Plik konfiguracyjny z danymi logowania i połączeniem z bazą danych

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
echo "<h2>Panel administracyjny</h2>";
echo '<a href="?action=list">Lista podstron</a> | ';
echo '<a href="?action=add">Dodaj nową podstronę</a> | ';
echo '<a href="?action=logout">Wyloguj</a>';
echo '<a href="?action=list_categories">Lista kategorii</a> | ';
echo '<a href="?action=add_category">Dodaj nową kategorię</a> | ';

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
?>