<?php

// require this after auth.php, so session_start() is not necessary.
//session_start(); 

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../');
}

?>
