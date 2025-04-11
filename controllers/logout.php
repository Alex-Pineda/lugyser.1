<?php
session_start();
session_unset();
session_destroy();
header("Location: ../index.php"); // Redirigir al nuevo index.php después de cerrar sesión
exit;
?>
