<?php

## Session löschen und zur Startseite weiterleiten
session_start();
session_destroy();
header('Location: blog.php');
?>