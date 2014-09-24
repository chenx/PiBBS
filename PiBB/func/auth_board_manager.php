<?php

// require this after auth.php, so session_start() is not necessary.
//session_start(); 

if (!isset($_SESSION['bbs_role']) || $_SESSION['bbs_role'] == '') {
    header('Location: ../');
}
//print $_SESSION['bbs_role'];
?>
