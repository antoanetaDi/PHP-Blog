<?php
session_start();
require_once 'includes/sicher.php'; ## nur eingeloggte User dürfen auf posts.php zugreifen
require_once 'includes/funktionen.php';
require_once 'includes/connect.php';  ##Verbindung zur Datenbank
$zaehler = 0; ## Zählvariable für die Steuerung der Schleifendurchläufe
$anzahl = 0; ## Anzahl der gefundenen Datensätze
$meldung = ''; ## enthält die auszugebenden Fehlermeldungen
$user_id = ''; ## id des Users
$result = ''; ## beinhaltet das Ergebnis der SQL-Abfrage
$sql = ''; ## beinhaltet die SQL-Befehle
$datensatz = ''; ## enthält ein Array mit einem gefundenen Datensatz

$vorname = ''; ## Vorname des eingeloggten Users
$nachname = ''; ## Nachname des eingeloggten Users
$email = ''; ## Email des eingeloggten Users
$imgname = ''; ## Avatar des eingeloggten Users
$passwort = ''; ## Passwort des eingeloggten Users

$neuer_vorname = ''; ## Der neue Vorname des eingeloggten Users
$neuer_nachname = ''; ## Der neue Nachname des eingeloggten Users
$altes_passwort = ''; ## Das alte Passwort des eingeloggten Users
$neues_passwort = ''; ## Das neue Passwort des eingeloggten Users
$neuer_imgname = ''; ## Der neue Avatar des eingeloggten Users
$daten = ''; ## enthält ein Array mit einem gefundenen Datensatz
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
                <h2 class="title">Profileinstellungen</h2>
                <div id="profileinstellungen">

                    <?php
                    ## Ab hier nur abarbeiten, wenn eine ID übergeben wurde
                    if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) { ## wurde ein Parameter user_id übergeben und steht in id eine Zahl? verhindert (hoffentlich) so etwas wie Sql-injection
                        $user_id = $_GET['user_id'];
                        #steht in id eine Zahl? verhindert (hoffentlich) so etwas wie Sql-injection
                        ## Abfrage aus der Datenbank
                        ## Abfrage der Posts   
                        ## 1. SQL formulieren (nur den User der eingeloggt ist)
                        $sql = "SELECT id, vorname, nachname, email, passwort, avatar FROM user WHERE id ='" . $user_id . "'";
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
                            ## 4. Datensätze aus dem Resultset herausholen (fetchen)
                            $datensatz = mysqli_fetch_array($result, MYSQLI_ASSOC); ## MYSQLI_ASSOC sorgt dafür, dass ein assoziatives Array gebildet wird
                        }
                        $user_id = $datensatz['id'];
                        $vorname = $datensatz['vorname'];
                        $nachname = $datensatz['nachname'];
                        $passwort = $datensatz['passwort'];
                        $imgname = $datensatz['avatar'];


                        #####################################################
                        ## Ist das Formular abgeschickt worden?
                        if (isset($_POST['altes_passwort'])) { // Formular abgeschickt
                            $neuer_vorname = sauber($_POST['neuer_vorname'], 255);
                            $neuer_nachname = sauber($_POST['neuer_nachname'], 255);
                            $altes_passwort = sauber($_POST['altes_passwort'], 255);
                            $neues_passwort = sauber($_POST['neues_passwort'], 255);
                            $neuer_imgname = $_FILES["fileToUpload"]["name"];

                            # Sind die Felder alle ausgefüllt worden?
                            if (empty($altes_passwort)) {
                                $meldung = '<span style = "color:red;">Bitte das Passwort als Bestätigung der Identität
							noch einmal eingeben!</span><br>';
                            } else {
                                ## verschlüßeltes Passwort aus DB gegen das eingegebene Passwort checken
                                $check = password_verify($altes_passwort, $passwort);
                                if (!$check) {
                                    $meldung .= 'Das alte Passwort ist inkorrect!<br>';
                                } else {
                                    if (empty($neuer_vorname)) {
                                        $meldung .= 'Bitte Vorname eingeben! <br>';
                                    }
                                    if (empty($neuer_nachname)) {
                                        $meldung = $meldung . 'Bitte Nachname eingeben! <br>';
                                    }

                                    if (empty($neues_passwort)) {
                                        $neues_passwort = $passwort;
                                    } else {
                                        $neues_passwort = password_hash($neues_passwort, PASSWORD_DEFAULT);
                                    }

                                    if (empty($_SESSION['avatarname']) || !isset($_SESSION['avatarname'])) {
                                        $_SESSION['avatarname'] = $imgname;
                                    }
                                    if (empty($neuer_imgname)) {
                                        $neuer_imgname = $imgname;
                                    }

                                    if (!empty($meldung)) {
                                        echo $meldung;
                                    } else {

                                        ## 1. SQL Formulieren
                                        ## Passwort verschlüsseln
                                        ## 
                                        ### Schreiben der Dateien in die Datenbank
                                        ## 1. SQL Formulieren
                                        $sql = "UPDATE user SET vorname = '" . $neuer_vorname . "', nachname = '" . $neuer_nachname . "', passwort = '" . $neues_passwort . "', avatar = '" . $neuer_imgname . "' WHERE id = '" . $user_id . "'";
                                        ## 2. SQL Abschicken & Resultset entgegennehmen
                                        $result = mysqli_query($link, $sql);
                                        ## 3. Resultset Auswerten
                                        if (!$result) {
                                            echo $sql . '<br>';
                                            echo mysqli_error($link);
                                        } else {
                                            $target_dir = "images/";
                                            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                                            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                                                echo "<h3>Der Avatar wurde geändert!</h3><br>";
                                                $_SESSION['avatarname'] = $_FILES["fileToUpload"]["name"];
                                            } else {
                                                $_SESSION['avatarname'] = $imgname;
                                            }
                                            echo '<h3 style="margin-bottom: 500px;">Ihre Dateien wurden geändert!</h3> Bitte melden Sie wieder an!';
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $meldung .= 'Ungültige Id! | <a href="profileinstellungen.php">zurück</a>';
                    }
                    if (!isset($_GET['user_id'])) {
                        $meldung .= 'Keine Id übergeben! | <a href="profileinstellungen.php">zurück</a>';
                    }

                    ## wenn ein Fehler passiert ist oder das Formular noch nicht abgeschickt wurde: Formular anzeigen
                    if (!empty($meldung) || !isset($_POST['altes_passwort'])) {
                        ?>
                        <div class="form_settings">
                            <div id="avatar2-output"><img  class='thumb' alt="" src='images/<?php echo $imgname; ?>'/>
                            </div><br>
                            <div><form action="profileinstellungen.php?user_id=<?php echo $user_id; ?>" method="POST" enctype="multipart/form-data"> <!-- id des Users beim Abschicken mitgeben --> 
                                    <label for="avatar2ToUpload">Avatar ändern:</label><br>
                                    <input type="file" name="fileToUpload" id="avatar2ToUpload"><br><br>

                                    <label for="vorname">Vorname*</label><br>
                                    <input size = "25" class="contact" id="vorname" type="text" value="<?php echo $vorname; ?>" name="neuer_vorname"><br><br>

                                    <label for="nachname">Nachname*</label><br>
                                    <input size = "25" class="contact" id="nachname"  type="text" value="<?php echo $nachname; ?>" name="neuer_nachname"><br><br>

                                    <label for="passwort2">Passwort*</label><br>
                                    <input size = "25" class="contact" id="passwort2"  type="password" value="" name="altes_passwort"><br><span style = "color:red;">* Bitte das Passwort als Bestätigung der Identität
                                        noch einmal eingeben!</span><br><br>

                                    <label for="neues_passwort">Neues Passwort</label><br>
                                    <input size = "25" class="contact" id="neues_passwort"  type="password" value="" name="neues_passwort"><br><br>

                                    <input class="submit" type="submit" value="Ändern"><br>
                                </form>
                            </div>
                        </div> 
    <?php
}
## 4. Datenbankverbindung schließen
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