<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="/lugyser/favicon-rounded.ico" type="image/x-icon">
</head>
<body>
    <header class="bg-primary text-white text-center py-2" style="min-height: 64px;">
        <div class="container d-flex align-items-center justify-content-center">
            <a href="../views/admin_dashboard.php" class="mr-4">
                <img src="../uploads/FA.jpeg" alt="Volver" style="height:45px; border-radius: 50%; width:auto; cursor:pointer;">
            </a>
            <h1 class="mb-0 flex-grow-1 text-center" style="font-size: 2rem;">FincAntioquia</h1>
        <!-- Icono flotante de mensajería -->
        <a href="../views/mensajeria.php" 
           class="btn btn-success"
           style="position: static; bottom: 30px; right: 30px; z-index: 9999; border-radius: 50%; width: 54px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.2);"
           title="Enviar mensaje a administradores/proveedores">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="white" class="bi bi-chat-dots" viewBox="0 0 16 16">
              <path d="M2 2a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h2.586A1.5 1.5 0 0 1 6 14.5V15a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-.5a1.5 1.5 0 0 1 1.414-1.5H14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2zm3 7a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
            </svg>
            </a>
        </div>
    </header>
</body>
</html>
