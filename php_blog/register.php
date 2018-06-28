<?php
session_start();
require_once 'includes/funktionen.php';
require_once 'includes/connect.php'; ##Verbindung zur Datenbank
$vorname = ''; ## Vorname des Users
$nachname = ''; ## Nachname des Users
$email = ''; ## Email des Users
$passwort = ''; ## Passwort des Users
$avatar = ''; ## Avatar des Users
$meldung = ''; ## enthält die auszugebenden Fehlermeldungen
$anzahl = ''; ## Anzahl der gefundenen Datensätze
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
                            echo '<span style="background-color: #ff0000;">' . $_SESSION['keine_rechte'];
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
                <div id="registerform">

                    <?php
                    ## Ist das Formular abgeschickt worden?
                    if (isset($_POST['passwort'])) { // Formular abgeschickt
                        $vorname = sauber($_POST['vorname'], 255);
                        $nachname = sauber($_POST['nachname'], 255);
                        $email = sauber($_POST['email'], 255);
                        $passwort = sauber($_POST['passwort'], 255);
                        $avatar = $_FILES["fileToUpload"]["name"];
                        # Sind die Felder alle ausgefüllt worden?
                        if (empty($vorname)) {
                            $meldung = 'Bitte Vornamen eingeben! <br>';
                        }
                        if (empty($nachname)) {
                            $meldung .= 'Bitte Nachnamen eingeben! <br>';
                        }

                        if (empty($email)) {
                            $meldung .= 'Bitte Email eingeben! <br>';
                        } else {
                            if (!is_mail($email)) {## Wenn email ausgefüllt ist, schaue genauer hin
                                $meldung = $meldung . 'Das scheint keine gültige Mailadresse zu sein <br>';
                            } else {
                                ## Schaue nach, ob diese Mail bereits registriert wurde
                                ## 1. SQL Formulieren
                                $sql = "SELECT id FROM user WHERE email = '" . $email . "'";
                                ## 2. SQL Abschicken & Resultset entgegennehmen
                                $result = mysqli_query($link, $sql);
                                ## 3. Resultset Auswerten
                                if (!$result) {
                                    echo $sql . '<br>';
                                    echo mysqli_error($link);
                                } else {
                                    ## Schaue nach, wieviele Datensätze gefunden worden sind
                                    $anzahl = mysqli_num_rows($result); // ..num_rows liefert die Anzahl gefundener Datensätze
                                    if ($anzahl != 0) { ## wenn ein oder mehrere Datensätze gefunden wurden
                                        $meldung = $meldung . 'Diese Mailadresse wurde schon registriert<br>';
                                    }
                                }
                            }
                        }
                        if (empty($passwort)) {
                            $meldung .= 'Bitte Passwort eingeben! <br>';
                        }

                        if (empty($avatar)) {
                            $avatar = 'defaultavatar.png';
                        }

                        if (!empty($meldung)) {
                            echo $meldung;
                        } else {
                            ## 1. SQL Formulieren
                            ## Passwort verschlüsseln
                            ## 
                            $passwort = password_hash($passwort, PASSWORD_DEFAULT);
                            $sql = "INSERT INTO user (vorname, nachname, email, passwort, avatar) VALUES ('" . $vorname . "', '" . $nachname . "', '" . $email . "', '" . $passwort . "', '" . $avatar . "')";
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
                                    echo "<br>";
                                } else {
                                    $avatar = 'defaultavatar.png';
                                }
                                echo '<h2 style="margin-bottom: 600px;">Vielen Dank, Sie haben sich registriert</h2>';
                            }
                        }
                    }
                    if (!empty($meldung) || !isset($_POST['passwort'])) { // wenn ein Fehler passiert ist oder das Formular noch nicht abgeschickt wurde: Formular anzeigen
                        ?>
                        <h1 class="title">Bitte registrieren Sie sich</h1>
                        <div class="form_settings">
                            <form action="register.php" method="POST" enctype="multipart/form-data">
                                <label for="vorname">Vorname*</label><br>
                                <input size="30" class="contact" id="vorname" type="text" value="<?php echo $vorname; ?>" name="vorname"><br><br>
                                <label for="nachname">Nachname*</label><br>
                                <input size="30" class="contact" id="nachname"  type="text" value="<?php echo $nachname; ?>" name="nachname"><br><br>
                                <label for="email">Email*</label><br>
                                <input size="30" class="contact" id="email"  type="text" value="<?php echo $email; ?>" name="email"><br><br>
                                <label for="passwort">Passwort*</label><br>
                                <input size="30" class="contact" id="passwort"  type="password" value="" name="passwort"><br><br>
                                <label for="avatarToUpload">Avatar:</label>
                                <input type="file" name="fileToUpload" id="avatarToUpload"><br><br>
                                <div id="avatar-output"><img  class='thumb' alt="" src='images/defaultavatar.png'/></div><br><br>
                                <input class="submit" type="submit" value="Absenden"><br>
                            </form>
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