<?php
require_once 'includes/funktionen.php';
if (!isset($_SESSION['aut_user'])) { ## Wenn nicht eingeloggt: Login-Form anzeigen
    ?>
    <h3>Login</h3>
    <form method="POST" action="auth.php" id="login_form">
        <p>
            <label for="email">Email</label><br>
            <input size="22" id="email" class="login" type="text" name="email" value="" /><br><br>

            <label for="passwort">Passwort</label><br>
            <input size="22" id="passwort" class="login" type="password" name="passwort" value="" /><br><br>

            <input class="submit" type="submit" value="anmelden" />
        </p>
    </form>
    <?php
} else { ## Wenn eingeloggt: Logout-Link anzeigen. 
    echo "<img class='avatar' src='images/" . $_SESSION['avatarname'] . "'/> <br>" . $_SESSION['aut_user'] . "<br><br>";
    //echo "<img class='avatar' src='images/defaultavatar.png'/><br><br>";
    ##Links sind nur für angeloggte User sichtbar
    echo "<a href='posts.php'><img style='width:15px; margin-right:5px;' src='images/post-verfassen.png'/>Post verfassen</a><br>";
    echo "<a href='postadmin.php'><img style='width:15px; margin-right:5px;' src='images/posts-verwalten.png'/>Posts verwalten</a><br><br>";
    $_SESSION['user_id'] = getUserIdFromUserEmail($_SESSION['aut_user'], $link);
    echo "<a href='profileinstellungen.php?user_id=" . $_SESSION['user_id'] . "'><img style='width:15px; margin-right:5px;' src='images/profil-einstellungen.png'/>Profileinstellungen</a><br>";

    echo "<br><a href ='logout.php'><img style='width:15px; margin-right:5px;' src='images/logout.png'/>Logout</a>";
}
?>