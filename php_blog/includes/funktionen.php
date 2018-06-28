<?php

function sauber($text, $laenge) {
    $text = strip_tags($text);
    $text = trim($text);
    $text = substr($text, 0, $laenge);

    return $text;
}

function is_mail($email) {

    $at_teile = explode('@', $email); // mail in ein Array schreiben, es dürfen nur 2 Teile entstehen
    if (count($at_teile) != 2) {
        return false;
    }
    $leer_teile = explode(' ', $email); // mail in ein Array schreiben, es darf nur 1 Teil entstehen
    if (count($leer_teile) > 1) {
        return false;
    }
    ## Mail muss mindestens 6 Zeichen lang sein
    if (strlen($email) < 6) {
        return false;
    }
    ## Muss mindestens einen Punkt enthalten
    $dot_teile = explode('.', $at_teile[1]); // suche nach Punkten nach dem @; es muss mindestens einen geben
    if (count($dot_teile) < 2) {
        return false;
    }
    ## Zeichen nach dem letzten Punkt: finde den letzten index aus dot_teile
    $last_index = count($dot_teile) - 1;
    if (strlen($dot_teile[$last_index]) < 2) { // wenn der teil kürzer als 2 Zeichen ist
        return false;
    }
    ## @ nicht am Anfang, nicht am Ende
    if (strlen($at_teile[0]) < 1 || strlen($at_teile[1]) < 4) {
        return false;
    }
    return true;
}

function authentifiziere_user($email, $passwort, $link) {
    if (empty($email) || empty($passwort)) {
        return false;
    }

    ## 1. SQL-Statement Formulieren
    $sql = "SELECT email, passwort FROM user WHERE email = '" . $email . "'";
    ## 2. SQL-Statement abschicken und Ergebnis entgegen nehmen
    $result = mysqli_query($link, $sql);
    ## 3. Ergebnis auswerten
    ## Wenn die Abfrage kein Ergebnis liefert, wird der User nicht eingeloggt
    if (!$result) {
        return false;
    }

    ## Ergebnis verarbeiten
    ## wurde nur genau ein Datensatz gefunden?
    if (mysqli_num_rows($result) != 1) { // num_rows liefert die Anzahl gefundener Datensätze
        return false;
    } else { ##hier wieß ich erst einmal nur, dass der username (email) genau ein mal in der DB steht
        ## ich muss noch weiter nachsehen um auch das Passwort zu überprüfen
        $datensatz = mysqli_fetch_array($result, MYSQLI_ASSOC);
        ## verschlüßeltes Passwort aus DB gegen das eingegebene Passwort checken
        $check = password_verify($passwort, $datensatz['passwort']);
        if ($check) {
            return true;
        }
    }
    return false;
}

function getUserIdFromUserEmail($u, $link) {
    $output = 0;

    //hole die spalte 'id' aus der Tabelle 'user' aber nur wenn der inhalt der spalte gleich dem inhalt der variablen '$u'
    $sql = "SELECT id FROM user WHERE email='" . $u . "'";
    $result = mysqli_query($link, $sql);
    //wenn ein Ergebnis der DB-Anfrage zurückgekommen ist und es nicht 0 ist ->
    //wenn ein ergebnis kommt -> ist der user('$u') schon in Tabelle('user') vorhanden
    if (!$result) {
        echo "SQL:" . $sql . "<br>";
        echo mysqli_error($link);
        die('Konnte Datenbank nicht abfragen!');
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        //$row = mysqli_fetch_array($result, MYSQL_ASSOC);    --> Andere Variante
        $output = $row['id'];
    }
    return $output;
}

function showComments($link, $post_id, $id) {
    $comments = '';
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
        $comments .= '<h3>Kommentare:</h3><table style="width:100%">';

        while ($zeilen > $zaehler) {
            $daten = mysqli_fetch_array($result, MYSQLI_ASSOC); ## MYSQLI_ASSOC sorgt dafür, dass ein assoziatives Array gebildet wird
            $comments .= "<tr><td width=100><img style='width:50px; margin-right:5px;' alt='' src='images/" . $daten['avatar'] . "'/><br>" . $daten['vorname'] . " " . $daten['nachname'] . " <br> " . $daten['datum'] . "</td><td><p>" . $daten['text'] . "</p>";
            if (isset($_SESSION['aut_user']) && ($_SESSION['aut_user'] == $daten['email'])) {
                $comments .= "<div style='float:left; padding:0;'><a href='artikel.php?loeschen=0&komm_id=" . $daten['id'] . "&id=" . $id . "'>Kommentar löschen</a></div>";
                if (isset($_GET['loeschen']) && ($_GET['loeschen'] == 0)) {
                    if (isset($_GET['komm_id']) && ($_GET['komm_id'] == $daten['id'])) {
                        $komm_id = $_GET['komm_id'];
                        $comments .= "<div style='padding:0; float:right; font-size:14px;'><a style='color:red;' href='artikel.php?loeschen=1&komm_id=" . $daten['id'] . "&id=" . $id . "'>Jetzt wirklich löschen!</a></div>";
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
            $comments .= "</td><tr>";

            $zaehler++;
        }
        $comments .= '</table>';
    }
    return $comments;
}
?>


