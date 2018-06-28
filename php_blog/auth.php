<?php

session_start();
require_once 'includes/funktionen.php';
require_once 'includes/connect.php'; ##Verbindung zur Datenbank
## Status des Users
$authentifiziert = false; ## Status des Users
$email = sauber($_POST['email'], 255); ##Email des eingeloggten User
$passwort = sauber($_POST['passwort'], 255); ##Passwort des eingeloggten User
## Aufruf der Funktion zum Überprüfen des Logins
$authentifiziert = authentifiziere_user($email, $passwort, $link);

### Weiterleitung, wenn Benutzer erkannt, dann blog.php und Users-Info anzeigen; wenn nicht, dann register.php
if ($authentifiziert) {
    $_SESSION['aut_user'] = $email;

    ### ID des eingeloggten Users ermitteln per Abfrage aus DB
    ## 1. SQL formulieren
    $sql = "SELECT avatar FROM user WHERE email = '" . $email . "'";
    ## 2. SQL Abschicken & Resultset entgegennehmen
    $result = mysqli_query($link, $sql);
    ## 3. Resultset Auswerten
    if (!$result) {
        echo $sql . '<br>';
        echo mysqli_error($link);
    }
    $daten = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $avatar = $daten['avatar'];
    $_SESSION['avatarname'] = $avatar;

    header("Location: blog.php");
} else {
    header("Location: register.php");
}
## 4. Datenbankverbindung schließen
mysqli_close($link);
?>