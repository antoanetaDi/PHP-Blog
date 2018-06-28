<?php

### Datenbankaktion
$hostname = "localhost";
$username = "root";
$dbpasswort = "";
$dbname = "php_blog";

$link = mysqli_connect($hostname, $username, $dbpasswort, $dbname);
if (!$link) { ## Wenn es keine Verbindung zur DB gibt, PHP abbrechen
    echo mysqli_error($link);
    die("Kann die Datenbank nicht erreichen!");
}
?>