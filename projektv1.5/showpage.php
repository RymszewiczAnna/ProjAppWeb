<?php
include('cfg.php');

$sql = "SELECT * FROM page_list WHERE status = 1"; // przykład z limitem
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<h2>" . $row["page_title"] . "</h2>";
        echo "<p>" . $row["page_content"] . "</p>";
    }
} else {
    echo "0 results";
}
?>