<?php
session_start();
require_once 'includes/sicher.php'; ## nur eingeloggte User dürfen auf loschen.php zugreifen
require_once 'includes/funktionen.php';
require_once 'includes/connect.php'; ##Verbindung zur Datenbank
$zaehler = 0; ## Zählvariable für die Steuerung der Schleifendurchläufe
$zeilen = 0; ## Anzahl der gefundenen Datensätze
$meldung = ''; ## enthält die auszugebenden Fehlermeldungen
$id = ''; ## id des zu löschenden Datensatzes
$result = ''; ## beinhaltet das Ergebnis der SQL-Abfrage
$sql = ''; ## beinhaltet die SQL-Befehle
$datensatz = ''; ## enthält ein Array mit einem gefundenen Datensatz
$sicher = ''; ## Parameter, der vom Bestätigungslink mit versendet wird
?>
<!doctype html>
<html lang="de">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale = 1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <title>Blog</title>
        <link href="external/jquery-ui/jquery-ui.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" href="leaflet/leaflet.css" />
        <link rel="stylesheet" href="leaflet/Control.FullScreen.css" />

        <script src="leaflet/leaflet.js"></script>
        <script src="leaflet/Control.FullScreen.js"></script>
        <script src="leaflet/leaflet.ajax.min.js"></script>
    </head>

    <body>
        <div id="wrap">
            <header id="header" class="item1">
                <div>
                    <a href="index.html">
                        <img id="logo" src="css/img/cimdata_logo2.gif" alt="cimdata" height="40">
                    </a>
                </div>

            </header>

            <div id="menu" class="item2">
                <div class="topnav" id="myTopnav">
                    <a href="#home" id="homeID" class="active">Home</a>
                    <a href="#details" id="detailsID">Kursdetails</a>
                    <div class="dropdown">
                        <button class="dropbtn">QUIZ
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <div class="dropdown-content">
                            <a href="#" id="quiz-html">HTML 5</a>
                            <a href="#" id="quiz-jquery">jQuery</a>
                            <a href="#" id="quiz-css">CSS 3</a>
                        </div>
                    </div>
                    <a href="blog.php" id="blogID">Blog</a>
                    <a href="#dokumentation" id="dokumentID">Dokumentation</a>
                    <a href="register.php" id="registerID">Registrieren</a>
                    <a href="#contact" id="contactID">Contact</a>
                    <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
                </div>
            </div>

            <div class="item6">
                <div class="sidebar">
                    <div class="sidebar_top">
                    </div>
                    <div class="sidebar_item">
                        <?php
                        ## Wenn in der Session eine Fehlermeldung enthalten ist
                        if (isset($_SESSION['keine_rechte'])) {
                            ### Fehlermeldung ausgeben, Hintergrund rot anzeigen
                            echo '<span style="background-color: #ff0000;">' . $_SESSION['keine_rechte'];
                            unset($_SESSION['keine_rechte']); ## Fehlermeldung aus der Session wieder löschen
                        }
                        include 'includes/login_out.php'; ## Die Login-Form oder den Logout-Link anzeigen
                        ?>
                        <br>
                    </div>
                    <div class="sidebar_base">
                    </div>
                </div>

            </div>
            <div id="content" class="item3">
                <h2 class="title">Post löschen</h2>
                <div id="loeschen"> 
                    <?php
                    ### ID des eingeloggten Users ermitteln per Abfrage aus DB
                    ## 1. SQL formulieren
                    $sql = "SELECT id, vorname, nachname, avatar FROM user WHERE email = '" . $_SESSION['aut_user'] . "'";
                    ## 2. SQL Abschicken & Resultset entgegennehmen
                    $result = mysqli_query($link, $sql);
                    ## 3. Resultset Auswerten
                    if (!$result) {
                        echo $sql . '<br>';
                        echo mysqli_error($link);
                    }
                    $daten = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    $user_id = $daten['id'];
                    $vorname = $daten['vorname'];
                    $nachname = $daten['nachname'];
                    $avatar = $daten['avatar'];

                    if (isset($_GET['id']) && !isset($_GET['sicher'])) { ## wurde ein Parameter id übergeben?
                        $id = $_GET['id']; ## hole die id aus dem Array $_GET
                        if (is_numeric($id)) { #steht in id eine Zahl? 
                            ## Abfrage aus der Datenbank
                            ## Abfrage der Posts   
                            ## 1. SQL formulieren (nur den Post der zu löschen ist)
                            $sql = "SELECT id, ueberschrift, imgname, text, user_id, datum FROM posts WHERE id = '" . $id . "' AND user_id = '" . $user_id . "'";
                            ## 2. SQL Abschicken & Resultset entgegennehmen
                            $result = mysqli_query($link, $sql);
                            ## 3. Resultset Auswerten
                            if (!$result) {
                                echo $sql . '<br>';
                                echo mysqli_error($link);
                                exit;
                            }
                            ## Anzahl der Datensätze im resultset
                            $zeilen = mysqli_num_rows($result);
                            if (1 == $zeilen) {
                                $datensatz = mysqli_fetch_array($result, MYSQLI_ASSOC); ## MYSQLI_ASSOC sorgt dafür, dass ein assoziatives Array gebildet wird
                                echo '<div><img style="float: left; width:350px; margin-right:15px; margin-bottom:10px; padding-top:10px;" alt="" src="images/' . $datensatz['imgname'] . '"/>
                                <h2>' . $datensatz['ueberschrift'] . '</h2><p>' . $datensatz['text'] . '<br><br> Gepostet von: <img style="width:30px; margin-right:5px;" alt="" src="images/' . $avatar . '"/>' . $vorname . ' ' . $nachname . ' am  ' . $datensatz['datum'] . '</p>
                                </div><a href="loeschen.php?id=' . $id . '&sicher=1">Jetzt wirklich löschen</a> | <a href="postadmin.php">zurück</a>';
                            } else {
                                $meldung .= 'Keine Daten gefunden | <a style="margin-bottom: 500px;" href="postadmin.php">zurück</a>';
                            }
                        } else {
                            $meldung .= 'Id muss eine ganze Zahl sein | <a style="margin-bottom: 500px;" href="postadmin.php">zurück</a>';
                        }
                    }
                    if (!isset($_GET['id'])) {
                        $meldung .= 'Keine Id übergeben! | <a style="margin-bottom: 500px;" href="postadmin.php">zurück</a>';
                    }
                    if (!empty($meldung)) {
                        echo $meldung;
                    } else {
                        ## Wurde der Bestätigungslink angeklickt?
                        if (isset($_GET['id']) && isset($_GET['sicher']) && 1 == $_GET['sicher']) {
                            $id = $_GET['id']; ## hole die id aus dem Array $_GET
                            if (is_numeric($id)) { ## wieder: ist id eine Zahl?
                                ## 1. sql Formulieren / Post löschen
                                $sql = "DELETE FROM posts WHERE id = '" . $id . "' AND user_id = '" . $user_id . "'";
                                ## 2. SQL Abschicken & Resultset entgegennehmen
                                $result = mysqli_query($link, $sql);
                                ## 3. Resultset Auswerten
                                if (!$result) {
                                    echo $sql . '<br>';
                                    echo mysqli_error($link);
                                    exit;
                                } else {
                                    echo '<h2>Der Post wurde gelöscht!</h2><br> <h3 style="margin-bottom: 500px;"><a href="blog.php">zurück</a></h3>';
                                }
                            }
                        }
                    }

                    ## Verbindung zum Datenbankserver schließen
                    mysqli_close($link);
                    ?>
                </div>
            </div>
            <div class="item4">
            </div>

            <footer class="item5">
                Copyright &copy; Antoaneta Dishlieva | cimdata Bildungsakademie GmbH
            </footer>

        </div>

        <script src="external/jquery/jquery.js"></script>
        <script src="js/jquery-3.1.1.min.js"></script>
        <script src="external/jquery-ui/jquery-ui.js"></script>
        <script src="js/main.js"></script>


    </body>

</html>