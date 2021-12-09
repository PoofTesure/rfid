<?php

$database = new mysqli("localhost", "adminrfid", "FaFen542", "rfid");
if ($database->connect_errno) {
    echo "Failed to connect to MySQL: (" . $database->connect_errno . ") " . $database->connect_error;
}
?>