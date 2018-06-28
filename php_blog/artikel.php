<?php
session_start();
require_once 'includes/funktionen.php';
require_once 'includes/connect.php'; ##Verbindung zur Datenbank
$zaehler = 0; ## Zählvariable für die Steuerung der Schleifendurchläufe
$zeilen = 0; ## Anzahl der gefundenen Datensätze
$anzahl = 0; ## Anzahl der gefundenen Datensätze
$meldung = ''; ## enthält die auszugebenden Fehlermeldungen
$id = ''; ## id des zu löschenden Datensatzes
$result = ''; ## beinhaltet das Ergebnis der SQL-Abfrage
$sql = ''; ## beinhaltet die SQL-Befehle
$datensatz = ''; ## enthält ein Array mit einem gefundenen Datensatz
$ueberschrift = ''; ## Überschrift des Posts
$text = ''; ## Text des Posts
$datum = ''; ## Datum des Posts
$user_id = ''; ## Id des eingeloggten Users
$imgname = ''; ## Bild des Posts
$anzahl_kommentare = ''; ## Anzahl der Kommentare
$post_id = ''; ## Id des Posts
$komm_text = ''; ## Text des Kommentars
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
                        ## Wenn in der Session eine Fehlermeldung enthalten ist...
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

                <div id="artikel">
                    <?php
                    if (isset($_SESSION['aut_user'])) {
                        ### ID des eingeloggten Users ermitteln per Abfrage aus DB
                        ## 1. SQL formulieren
                        $sql = "SELECT id, vorname, nachname FROM user WHERE email = '" . $_SESSION['aut_user'] . "'";
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


                        if (!isset($_GET['id'])) {
                            $meldung .= 'Keine Id übergeben! | <a href="blog.php">zurück</a>';
                        }
                    }
                    ## Ab hier nur abarbeiten, wenn eine ID übergeben wurde
                    if (isset($_GET['id'])) { ## wurde ein Parameter id übergeben?
                        $id = $_GET['id']; ## hole die id aus dem Array $_GET
                        if (is_numeric($id) && empty($meldung)) { #steht in id eine Zahl? verhindert (hoffentlich) so etwas wie Sql-injection
                            ## Abfrage aus der DB - Wie viele Kommentare?
                            ## 1. SQL formulieren
                            $sql = "SELECT id FROM comments WHERE post_id = " . $_GET['id'];

                            ## 2. SQL Abschicken und Resultset entgegennehmen
                            $result = mysqli_query($link, $sql);
                            ## 3. Resultset Auswerten
                            if (!$result) {
                                echo $sql . "<br>";
                                echo mysqli_error($link);
                                exit;
                            }
                            ## Anzahl der Datensätze im resultset
                            $anzahl_kommentare = mysqli_num_rows($result);

                            ## Abfrage aus der Datenbank
                            ## Abfrage der Posts   
                            ## 1. SQL formulieren (nur den Post der zu kommentieren ist)
                            $sql = "SELECT posts.id, imgname, ueberschrift, text, datum, user_id, avatar, vorname, nachname FROM user, posts WHERE posts.id = '" . $id . "' AND user_id = user.id ";
                            ## 2. SQL Abschicken & Resultset entgegennehmen
                            $result = mysqli_query($link, $sql);
                            ## 3. Resultset Auswerten
                            if (!$result) {
                                echo $sql . '<br>';
                                echo mysqli_error($link);
                                exit;
                            }
                            ## Anzahl der Datensätze im resultset
                            $anzahl = mysqli_num_rows($result);
                            if (1 == $anzahl) {
                                $datensatz = mysqli_fetch_array($result, MYSQLI_ASSOC); ## MYSQLI_ASSOC sorgt dafür, dass ein assoziatives Array gebildet wird
                                $post_id = $datensatz['id'];
                                echo '<div><img style="float: left; width:350px; margin-right:15px; margin-bottom:10px; padding-top:10px;" alt=" " src="images/' . $datensatz['imgname'] . '"/>
                                <h2>' . $datensatz['ueberschrift'] . '</h2><p>' . $datensatz['text'] . '<br><br> Gepostet von: <img style="width:30px; margin-right:5px;" alt="" src="images/' . $datensatz['avatar'] . '"/>' . $datensatz['vorname'] . ' ' . $datensatz['nachname'] . ' am  ' . $datensatz['datum'] . '<img style="width:15px; margin-right: 2px; margin-left: 70px;" alt="" src="images/kommentare2.png"/> ' . $anzahl_kommentare . ' Kommentare</p>
                                <hr></div>';
                            }


                            //echo showComments($link, $post_id, $id); ## Kommentare zum Post anzeigen (function showComments() in includes/functionen.php )
                            ## Abfrage aus der DB
                            ## 1. SQL formulieren
                            $sql = "SELECT comments.id, text, comments.datum, post_id, user_id, vorname, nachname, email, avatar FROM comments, user WHERE post_id ='" . $post_id . "' AND user_id = user.id";

                            ## 2. SQL Abschicken und Resultset entgegennehmen
                            $result = mysqli_query($link, $sql);
                            ## 3. Resultset Auswerten
                            if (!$result) {
                                printf("Error: %s\n", mysqli_error($link));

                                echo $sql . "<br>";
                                echo mysqli_error($link);
                                exit;
                            }
                            ## Anzahl der Datensätze im resultset
                            $zeilen = mysqli_num_rows($result);

                            ## 4. Datensätze aus dem Resultset herausholen (fetchen)
                            ## Schleife über die Anzahl der Datensätze
                            $zaehler = 0;
                            if ($zeilen > 0) {
                                echo '<h3>Kommentare:</h3><table style="width:100%">';

                                while ($zeilen > $zaehler) {
                                    $daten = mysqli_fetch_array($result, MYSQLI_ASSOC); ## MYSQLI_ASSOC sorgt dafür, dass ein assoziatives Array gebildet wird
                                    echo "<tr><td width=100><img style='width:50px; margin-right:5px;' alt='' src='images/" . $daten['avatar'] . "'/><br>" . $daten['vorname'] . " " . $daten['nachname'] . " <br> " . $daten['datum'] . "</td><td><p>" . $daten['text'] . "</p>";
                                    if (isset($_SESSION['aut_user']) && ($_SESSION['aut_user'] == $daten['email'])) {
                                        echo "<div style='float:left; padding:0;'><a href='artikel.php?loeschen=0&komm_id=" . $daten['id'] . "&id=" . $id . "'>Kommentar löschen</a></div>";
                                        if (isset($_GET['loeschen']) && ($_GET['loeschen'] == 0)) {
                                            if (isset($_GET['komm_id']) && ($_GET['komm_id'] == $daten['id'])) {
                                                $komm_id = $_GET['komm_id'];
                                                echo "<div style='padding:0; float:right; font-size:14px;'><a style='color:red;' href='artikel.php?loeschen=1&komm_id=" . $daten['id'] . "&id=" . $id . "'>Jetzt wirklich löschen!</a></div>";
                                            }
                                        }

                                        if (isset($_GET['loeschen']) && ($_GET['loeschen'] == 1)) {
                                            if (isset($_GET['komm_id']) && ($_GET['komm_id'] == $daten['id'])) {
                                                ## 1. sql Formulieren / Post löschen
                                                $sql = "DELETE FROM comments WHERE id = '" . $daten['id'] . "'";
                                                ## 2. SQL Abschicken & Resultset entgegennehmen
                                                $result = mysqli_query($link, $sql);
                                                ## 3. Resultset Auswerten
                                                if (!$result) {
                                                    echo $sql . '<br>';
                                                    echo mysqli_error($link);
                                                    exit;
                                                } else {

                                                    echo "<script type='text/javascript'> location.reload();</script>";
                                                }
                                            }
                                        }
                                    }
                                    echo "</td><tr>";

                                    $zaehler++;
                                }
                                echo '</table>';
                            }

                            ## Ist das Formular mit dem Kommentar abgeschickt worden?
                            if (isset($_GET['kommentar']) && $_GET['kommentar'] == 1) { // Formular abgeschickt
                                $kommentar = sauber($_POST['kommentar'], 5000);

                                # Sind die Felder alle ausgefüllt worden?
                                if (empty($kommentar)) {
                                    $meldung .= '<span style="color:red;">Bitte Kommentar eingeben! </span><br>';
                                }

                                if (!empty($meldung)) {
                                    echo $meldung;
                                } else {
                                    ## 1. SQL Formulieren
                                    ## Passwort verschlüsseln

                                    $sql = "INSERT INTO comments (text, datum, post_id, user_id) VALUES ('" . $kommentar . "', NOW(), '" . $id . "', '" . $user_id . "')";
                                    ## 2. SQL Abschicken & Resultset entgegennehmen
                                    $result = mysqli_query($link, $sql);
                                    ## 3. Resultset Auswerten
                                    if (!$result) {
                                        echo $sql . '<br>';
                                        echo mysqli_error($link);
                                    } else {

                                        echo '<h2>Vielen Dank, das Kommentar wurde geschrieben!</h2>';
                                    }
                                }
                            }

                            if ($id == $post_id) {## Wenn der übertragene Parameter gleich Post-id is
                                if (isset($_SESSION['aut_user'])) { ## Form zeigen nur für eingeloggte User
                                    ?>
                                    <h3>Schreiben Sie einen Kommentar zum Artikel</h3>
                                    <form id = "submitKomm" method="post" action="artikel.php?kommentar=1&id=<?php echo $id ?>"><!-- Id des Posts und Parameter kommentar beim Abschicken mitgeben --> 
                                        <p><label>Kommentar:<br>
                                                <textarea name="kommentar" cols="80" rows="9"></textarea></label></p>
                                        <input id = "submit_kommentar" type="submit" value="Absenden">
                                    </form>

                <?php
            } else {
                ## Wenn der Besucher nicht eingeloggt ist, weiterleiten zum Anmelden oder Registrieren
                echo "<h3 style='color:#9c0404;'>Bitte melden Sie sich an, um zu kommentieren.</h3> <p style='margin-bottom: 150px;'><a href='artikel.php?id=" . $id . "'>Anmelden</a> | <a href='register.php'>Registrieren</a></p><br><br>";
            }
        } else {
            echo "<h3 style='margin-bottom: 630px;'>Es ist kein Artikel gefunden!</h3>";
        }
    } else {
        echo "<h3 style='margin-bottom: 630px;'>Es ist kein Artikel gefunden!</h3>";
    }
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