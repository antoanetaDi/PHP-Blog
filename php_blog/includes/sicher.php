<?php

if (!isset($_SESSION['aut_user'])) { ## Wenn der Besucher nicht eingeloggt ist, weiterleiten auf index.php
    $_SESSION['keine_rechte'] = 'Bitte einloggen für den Zugriff auf diese Seite!';
    header('Location: index.html');
    exit; ## Ausführung von PHP beenden, damit nicht noch Teile der 'umgebenden' Datei abgearbeitet werden, bevor die Weiterleitung stattfindet.
}
?>