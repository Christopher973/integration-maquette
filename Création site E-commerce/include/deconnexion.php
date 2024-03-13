<?php session_start();

session_unset();
$_SESSION['access'] = 'non';

header("Location: ../index.php?message=Déconnexion réussite");
