<?php
if (getenv("JAWSDB_URL")) {
    $url = parse_url(getenv("JAWSDB_URL"));
    $conn = new mysqli($url["host"], $url["user"], $url["pass"], ltrim($url["path"], "/"), $url["port"]);
} else {
    $conn = new mysqli("localhost", "root", "", "regiscore");
}
if ($conn->connect_error) { die("Connection Failed"); }
?>
