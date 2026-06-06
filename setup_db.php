<?php
require "config/database.php";
$sql = file_get_contents(__DIR__ . "/schema.sql");
if ($conn->multi_query($sql)) {
    do {
        if ($res = $conn->store_result()) { $res->free(); }
    } while ($conn->more_results() && $conn->next_result());
    echo "Database setup complete. You can delete this file now.";
} else {
    echo "Error: " . $conn->error;
}
?>
