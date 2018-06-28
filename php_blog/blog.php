<?php
session_start();
require_once 'includes/connect.php';  ##Verbindung zur Datenbank
$zeilen = 0; ## Anzahl der anzuzeigenden Datensätze
$result = ''; ## beinhaltet des Ergebnis der SQL-Abfrage
$sql = ''; ## beinhaltet die SQL-befehle
$datensatz = ''; ## enthält ein Array mit einem gefundenen Datensatz
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
                    <div class="sidebar_top"></div>
                    <div class="sidebar_item">
                        <?php
                        ## Wenn in der Session eine Fehlermeldung enthalten ist
                        if (isset($_SESSION['keine_rechte'])) {
                            ### Fehlermeldung ausgeben, Hintergrund rot anzeigen
                            echo '<span style="background-color: #ff0000;">' . $_SESSION['keine_rechte'] . '</span>';
                            unset($_SESSION['keine_rechte']); ## Fehlermeldung aus der Session wieder löschen
                        }
                        include 'includes/login_out.php'; ## Die Login-Form oder den Logout-Link anzeigen
                        ?>
                        <br>
                    </div>
                    <div class="sidebar_base"></div>
                </div>

            </div>
            <div id="content" class="item3">
                <h2 class="title">Blog</h2>
                <div id="blog">
                    <?php
                    ## Abfrage aus der DB
                    ## 1. SQL formulieren
                    $sql = "SELECT posts.id, ueberschrift, imgname, text, user_id, vorname, nachname, datum, avatar FROM posts, user WHERE user_id = user.id";

                    ## 2. SQL Abschicken und Resultset entgegennehmen
                    $result = mysqli_query($link, $sql);
                    ## 3. Resultset Auswerten
                    if (!$result) {
                        echo $sql . "<br>";
                        echo mysqli_error($link);
                        exit;
                    }
                    ## Anzahl der Datensätze im resultset
                    $zeilen = mysqli_num_rows($result);

                    ## 4. Datensätze aus dem Resultset herausholen (fetchen)
                    ## Schleife über die Anzahl der Posts
                    $zaehler = 0;
                    while ($zeilen > $zaehler) {
                        $datensatz = mysqli_fetch_array($result, MYSQLI_ASSOC); ## MYSQLI_ASSOC sorgt dafür, dass ein assoziatives Array gebildet wird
                        ## Abfrage aus der DB - Wie viele Kommentare?
                        ## 1. SQL formulieren
                        $statement = "SELECT id FROM comments WHERE post_id = " . $datensatz['id'];
                        ## 2. SQL Abschicken und Resultset entgegennehmen
                        $result_kommentare = mysqli_query($link, $statement);
                        if (!$result_kommentare) {
                            echo $statement . "<br>";
                            echo mysqli_error($link);
                            exit;
                        }
                        ## Anzahl der Kommentare zum jeden Post
                        $anzahl_kommentare = mysqli_num_rows($result_kommentare);

                        echo "<a href ='artikel.php?id=" . $datensatz['id'] . "'><div><img style='float: left; width:350px; margin-right:15px; margin-bottom:10px; padding-top:10px;' alt='' src='images/" . $datensatz['imgname'] . "'/><h2>" . $datensatz['ueberschrift'] . "</h2><p>" . $datensatz['text'] . "</p>Gepostet von: <img style='width:30px; margin-right:5px;' alt='' src='images/" . $datensatz['avatar'] . "'/>" . $datensatz['vorname'] . " " . $datensatz['nachname'] . " am " . $datensatz['datum'] . "  <img style='width:15px; margin-right:2px; margin-left:70px;' alt='' src='images/kommentare2.png'/> " . $anzahl_kommentare . " Kommentare </div></a><br><hr><br>";
                        $zaehler++;
                    }

                    ## 4. Datenbankverbindung schließen
                    mysqli_close($link);
                    ?>
                </div>
            </div>

            <div class="item4"></div>

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