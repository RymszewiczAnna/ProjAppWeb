<?php
include('cfg.php'); 

function FormularzLogowania() {
    echo '<form method="POST" action="">';
    echo 'Login: <input type="text" name="login" required><br>';
    echo 'Hasło: <input type="password" name="pass" required><br>';
    echo '<input type="submit" value="Zaloguj">';
    echo '</form>';
}

if (!isset($_SESSION['loggedin'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($_POST['login'] == $login && $_POST['pass'] == $pass) {
            $_SESSION['loggedin'] = true; 
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

echo "<h2>Panel administracyjny</h2>";
echo '<a href="?action=list">Lista podstron</a> | ';
echo '<a href="?action=add">Dodaj nową podstronę</a> | ';
echo '<a href="?action=logout">Wyloguj</a>';

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'list':
            ListaPodstron($conn);
            break;
        case 'edit':
            if (isset($_GET['id'])) {
                EdytujPodstrone($_GET['id'], $conn);
            }
            break;
        case 'add':
            DodajNowaPodstrone($conn);
            break;
        case 'delete':
            if (isset($_GET['id'])) {
                UsunPodstrone($_GET['id'], $conn);
            }
            break;
        case 'logout':
            session_destroy();
            header("Location: admin.php");
            break;
        default:
            echo "Nieznana akcja.";
    }
} else {
    echo "<p>Wybierz akcję z menu.</p>";
}

function ListaPodstron($conn) {
    $sql = "SELECT * FROM page_list LIMIT 10"; 
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table border="1">';
        echo '<tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["page_title"] . "</td>";
            echo '<td>';
            echo ' <a href="?action=edit&id=' . $row["id"] . '">Edytuj</a>';
            echo ' | ';
            echo ' <a href="?action=delete&id=' . $row["id"] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\')">Usuń</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo "Brak podstron.";
    }
}

function EdytujPodstrone($id, $conn) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $status = isset($_POST['active']) ? 1 : 0;

        $sql = "UPDATE page_list SET page_title='$title', page_content='$content', status='$status' WHERE id = $id LIMIT 1";
        if ($conn->query($sql) === TRUE) {
            echo "Podstrona zaktualizowana pomyślnie.";
        } else {
            echo "Błąd: " . $conn->error;
        }
    }

    $sql = "SELECT * FROM page_list WHERE id = $id LIMIT 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    echo '<form method="POST" action="">';
    echo 'Tytuł: <input type="text" name="title" value="' . htmlspecialchars($row["page_title"]) . '" required><br>';
    echo 'Treść: <textarea name="content" required>' . htmlspecialchars($row["page_content"]) . '</textarea><br>';
    echo 'Aktywna: <input type="checkbox" name="active" ' . ($row["status"] == 1 ? 'checked' : '') . '><br>';
    echo '<input type="submit" value="Zapisz">';
    echo '</form>';
}

function DodajNowaPodstrone($conn) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $status = isset($_POST['active']) ? 1 : 0;

        $sql = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$title', '$content', '$status')";
        if ($conn->query($sql) === TRUE) {
            echo "Nowa podstrona dodana pomyślnie.";
        } else {
            echo "Błąd: " . $conn->error;
        }
    }

    echo '<form method="POST" action="">';
    echo 'Tytuł: <input type="text" name="title" required><br>';
    echo 'Treść: <textarea name="content" required></textarea><br>';
    echo 'Aktywna: <input type="checkbox" name="active"><br>';
    echo '<input type="submit" value="Dodaj">';
    echo '</form>';
}

function UsunPodstrone($id, $conn) {
    $sql = "DELETE FROM page_list WHERE id = $id LIMIT 1";
    if ($conn->query($sql) === TRUE) {
        echo "Podstrona usunięta pomyślnie.";
    } else {
        echo "Błąd: " . $conn->error;
    }
}
?>