<?php

if (isset($_SESSION['aut_user'])) { ## Link ist nur für angeloggte User sichtbar
    echo "<li><a href='posts.php'>Post verfassen</a></li>";
    echo "<li><a href='postadmin.php'>Posts verwalten</a></li>";
}
?>