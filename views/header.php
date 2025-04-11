<?php
session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Lugyser</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (!isset($_SESSION['usuario'])): ?>
                    <li class="nav-item">
                        <a href="../views/login.php" class="btn btn-light btn-sm nav-link">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a href="../views/register.php" class="btn btn-light btn-sm nav-link">Registrarse</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="../controllers/logout.php" class="btn btn-danger btn-sm nav-link">Cerrar Sesión</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
