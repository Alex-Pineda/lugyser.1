<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: ../index.php"); // Redirigir al nuevo index.php si ya hay sesión
    exit;
}

require_once '../config/database.php';
require_once '../models/UsuarioModel.php';
require_once '../controllers/AuthController.php';

$db = new Database();
$usuarioModel = new UsuarioModel($db->getConnection());
$authController = new AuthController($usuarioModel, null);

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => trim($_POST['nombre']),
        'apellido' => trim($_POST['apellido']),
        'nombre_usuario' => trim($_POST['nombre_usuario']),
        'contrasena' => trim($_POST['contrasena']),
        'tipo_documento' => trim($_POST['tipo_documento']),
        'documento_identidad' => trim($_POST['documento_identidad']),
        'email' => trim($_POST['email']),
        'telefono' => trim($_POST['telefono'])
    ];

    if ($authController->register($datos)) {
        $mensaje = '<div class="alert alert-success">Registro exitoso. Ahora puedes iniciar sesión.</div>';
    } else {
        $mensaje = '<div class="alert alert-danger">Error al registrar el usuario. Por favor, inténtelo de nuevo.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro -  FincAntioquia</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="/lugyser/favicon-rounded.ico" type="image/x-icon">
    
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow-x: hidden;
            padding-bottom: 80px; /* Aumenta este valor según necesites */
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color:rgba(221, 246, 221, 0.77);
            color: #333;
        }
        .register-container {
            max-width: 600px;
            margin: 3rem auto;
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .register-container h1 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #28a745;
        }
        .form-control {
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            padding: 0.5rem 1rem;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .text-center a {
            color: #007bff;
            text-decoration: none;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <h1>Registrarse</h1>
            <?php echo $mensaje; ?>
            <form action="register.php" method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ingrese su nombre" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" class="form-control" placeholder="Ingrese su apellido" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nombre_usuario">Nombre de Usuario</label>
                        <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" placeholder="Ingrese un nombre de usuario" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="contrasena">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" class="form-control" placeholder="Ingrese una contraseña" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tipo_documento">Tipo de Documento</label>
                        <select id="tipo_documento" name="tipo_documento" class="form-control" required>
                            <option value="CC">Cédula de Ciudadanía</option>
                            <option value="TI">Tarjeta de Identidad</option>
                            <option value="CE">Cédula de Extranjería</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="documento_identidad">Número de Documento</label>
                        <input type="text" id="documento_identidad" name="documento_identidad" class="form-control" placeholder="Ingrese su número de documento" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Ingrese su correo electrónico" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Ingrese su número de teléfono" required>
                    </div>
                </div>
                <div class="form-group form-check mt-3">
                    <input type="checkbox" class="form-check-input" id="aceptar_terminos" name="aceptar_terminos" required>
                    <label class="form-check-label" for="aceptar_terminos">
                        Acepto los <a href="#" data-toggle="modal" data-target="#terminosModal">términos y condiciones</a> para el tratamiento de la información personal
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
            </form>
            <p class="text-center mt-3">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </div>

    <!-- Modal de Términos y Condiciones -->
    <div class="modal fade" id="terminosModal" tabindex="-1" role="dialog" aria-labelledby="terminosModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="terminosModalLabel">Términos y Condiciones para el Tratamiento de datos Personales</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>
                📄 TÉRMINOS Y CONDICIONES PARA EL TRATAMIENTO DE DATOS PERSONALES <br><br>
                1. Responsable del Tratamiento de Datos<br><br>
                FincAntioquia, como responsable del tratamiento de datos personales, informa que los datos suministrados por los usuarios al momento del registro serán tratados conforme a los principios de legalidad, 
                finalidad, libertad, veracidad, transparencia, seguridad y confidencialidad, en cumplimiento de la legislación vigente en materia de protección de datos personales.
                <br><br><br>
                2. Finalidad del Tratamiento<br><br>
                Los datos personales recolectados tienen como finalidad:
                <br><br>
                - Gestionar el acceso y uso de la plataforma web.
                <br><br>
                - Permitir la correcta identificación de los usuarios y autenticación dentro del sistema.
                <br><br>
                - Gestionar las reservas, solicitudes y publicaciones de servicios.
                <br><br>
                - Enviar notificaciones y comunicaciones relacionadas con el uso de la plataforma.
                <br><br>
                - Administrar el perfil y los bienes inmuebles publicados por los proveedores.
                <br><br>
                - Realizar análisis estadísticos y de uso de la plataforma para mejorar los servicios ofrecidos.
                <br><br>
                - Contactar al usuario en relación con el uso de los servicios, notificaciones de la plataforma, actualizaciones y asuntos administrativos.
                <br><br>
                - Enviar información promocional, publicitaria o comercial sobre servicios propios o de terceros, por cualquier canal (email, SMS, mensajería instantánea, entre otros).
                <br><br>
                - Realizar encuestas de satisfacción y estudios de mercado.
                <br><br>
                - Cumplir con las obligaciones legales y contractuales que correspondan.
                <br><br><br>

                3. Datos Recolectados<br><br>
                Durante el proceso de registro y uso de la plataforma, se podrán recolectar los siguientes datos:
                <br><br>
                Para todos los usuarios:
                <br><br>
                - Nombre completo
                <br><br>
                - Apellido completo
                <br><br>
                - Documento de identidad
                <br><br>
                - Dirección de correo electrónico
                <br><br>
                - Número de teléfono
                <br><br>
                - Dirección de residencia
                <br><br>
                - Información de navegación y uso del sitio
                <br><br>
                Para proveedores:
                <br><br>
                - Información de bienes inmuebles (dirección, descripción, fotografías, tarifas, condiciones del inmueble, atracciones turisticas, horarios, entre otros relacionados con el inmueble)
                <br><br>
                Documentación soporte de la propiedad y autorización para publicar
                <br><br><br>
                4. Tratamiento de Bienes Inmuebles de Proveedores
                <br><br>
                El proveedor autoriza expresamente a Lugyser para:
                <br><br>
                - Publicar en la plataforma web y otros medios electrónicos la información relacionada con los bienes inmuebles registrados.
                <br><br>
                - Utilizar las imágenes, descripciones y detalles de los inmuebles para la promoción y comercialización de los mismos
                <br><br>
                - Usar las imágenes, descripciones y detalles de los inmuebles con fines promocionales y comerciales.
                <br><br>
                - Conservar un registro de los inmuebles para fines estadísticos, legales y de trazabilidad.
                <br><br><br>
                5. Autorización para Envío de Mensajería y Comunicaciones
                <br><br>
                El usuario autoriza expresamente a Lugyser para el envío de:
                <br><br>
                - Mensajes de texto (SMS)
                <br><br>
                - Correos electrónicos
                <br><br>
                - Notificaciones dentro de la plataforma
                <br><br>
                - Comunicaciones a través de aplicaciones de mensajería como WhatsApp
                <br><br>
                Estas comunicaciones podrán estar relacionadas con actualizaciones de servicios, confirmación de actividades,
                 promociones, encuestas, campañas comerciales y demás información relevante del servicio.
                <br><br><br>
                6. Derechos del Titular de los Datos
                <br><br>
                Los usuarios podrán ejercer los siguientes derechos:
                <br><br>
                - Conocer, actualizar y rectificar sus datos personales.
                <br><br>
                - Solicitar la supresión de los datos cuando considere que no están siendo tratados conforme a los principios y normas aplicables.
                <br><br>
                - Revocar la autorización otorgada para el tratamiento de sus datos.
                <br><br>
                - Solicitar prueba de la autorización otorgada.
                <br><br>
                - Estas solicitudes podrán enviarse al correo electrónico de contacto de Lugyser o a través del formulario de contacto dispuesto en la plataforma.
                <br><br><br>
                7. Conservación de la Información
                <br><br>
                - Los datos serán conservados únicamente durante el tiempo que sea necesario para cumplir con los fines del tratamiento, 
                o mientras exista una relación activa con el usuario, y conforme a los términos legales y contractuales aplicables.
                <br><br><br>
                8. Modificaciones a los Términos
                <br><br>
                FincAntioquia se reserva el derecho de modificar en cualquier momento estos Términos y Condiciones. 
                Cualquier cambio será informado oportunamente a través del sitio web.
                <br><br><br>
                9. Aceptación
                <br><br>
                Al registrarse y utilizar la plataforma Lugyser, el usuario declara haber leído, comprendido y aceptado los presentes Términos y Condiciones 
                de tratamiento de datos personales, así como autorizar expresamente el uso de su información en los términos descritos anteriormente.
            </p>
            <!-- Puedes agregar más contenido aquí -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Scripts necesarios para el modal de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

