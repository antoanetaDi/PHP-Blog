<?php
session_start();
require_once 'includes/sicher.php'; ## nur eingeloggte User dürfen auf posts.php zugreifen
require_once 'includes/funktionen.php';
require_once 'includes/connect.php'; ##Verbindung zur Datenbank
$ueberschrift = ''; ## Überschrift des Posts
$text = ''; ## Text des Posts
$meldung = ''; ## enthält die auszugebenden Fehlermeldungen
$imgname = ''; ## Bild des Posts
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
                <h2 class="title">Post verfassen</h2>
                <div id="posts">

                    <?php
                    ## Ist das Formular abgeschickt worden?
                    if (isset($_POST['text'])) { // Formular abgeschickt
                        $ueberschrift = sauber($_POST['ueberschrift'], 255);
                        $text = sauber($_POST['text'], 5000);
                        $imgname = $_FILES["fileToUpload"]["name"];

                        # Sind die Felder alle ausgefüllt worden?
                        if (empty($ueberschrift)) {
                            $meldung .= 'Bitte Überschrift eingeben <br>';
                        }
                        if (empty($text)) {
                            $meldung .= 'Bitte Posttext eingeben <br>';
                        }
                        if (empty($imgname)) {
                            $meldung .= 'Bitte ein Bild eingeben <br>';
                        }

                        if (!empty($meldung)) {
                            echo $meldung;
                        } else {
                            ### ID des eingeloggten Users ermitteln per Abfrage aus DB
                            ## 1. SQL formulieren
                            $sql = "SELECT id FROM user WHERE email = '" . $_SESSION['aut_user'] . "'";
                            ## 2. SQL Abschicken & Resultset entgegennehmen
                            $result = mysqli_query($link, $sql);
                            ## 3. Resultset Auswerten
                            if (!$result) {
                                echo $sql . '<br>';
                                echo mysqli_error($link);
                            }
                            $daten = mysqli_fetch_array($result, MYSQLI_ASSOC);
                            $user_id = $daten['id'];
                            ### Schreiben des Posts in die Datenbank
                            ## 1. SQL Formulieren
                            $sql = "INSERT INTO posts (ueberschrift, imgname, text, datum, user_id) VALUES ('" . $ueberschrift . "', '" . $imgname . "', '" . $text . "', NOW(), '" . $user_id . "')";
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
                                    echo "Das Bild wurde NICHT hochgeladen! Bitte versuchen Sie noch einmal!";
                                }
                                echo '<h2>Post wurde in die Datenbank geschrieben</h2><h3 style="margin-bottom: 500px;" ><a href="blog.php">zurück</a></h3>';
                            }
                        }
                    }

                    if (!empty($meldung) || !isset($_POST['text'])) { // wenn ein Fehler passiert ist oder das Formular noch nicht abgeschickt wurde: Formular anzeigen
                        ?>
                        <!-- 
                        HTML-Formular mit den Eingabemöglichkeiten für Überschrift und Posttext.
                        Alle Formularfelder müssen ausgefüllt sein.
                        -->
                        <div class="form_settings"><br><br>
                            <form action="posts.php" method="POST" enctype="multipart/form-data">
                                <label for="fileToUpload">Foto*:</label>
                                <input type="file" name="fileToUpload" id="fileToUpload"><br>
                                <div id="thumb-output">
                                </div><br><br>


                                <label for="ueberschrift">Überschrift*</label><br>
                                <input size="60" class="contact" id="ueberschrift" type="text" value="<?php echo $ueberschrift; ?>" name="ueberschrift"><br><br>

                                <label for="text">Posttext*</label><br>
                                <textarea id='text' name='text' rows="15" cols="61"><?php echo $text; ?></textarea><br><br> 

                                <input class="submit" type="submit" value="Absenden" /><br><br><br><br><br><br><br><br><br>
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