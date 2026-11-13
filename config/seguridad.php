<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idusuario'])) {
    header('Location: /jet_express/vista/login.php');
    exit;
}