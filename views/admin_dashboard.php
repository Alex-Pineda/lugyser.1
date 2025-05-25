<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array('administrador', array_column($_SESSION['roles'], 'nombre_rol'))) {
    header("Location: ../index.php"); // Redirigir al nuevo index.php si no hay sesión o no tiene el rol de administrador
    exit;
}

require_once '../config/database.php';
require_once '../models/RolModel.php';

$db = new Database();
$conn = $db->getConnection();

$usuarioNombre = $_SESSION['usuario']['nombre'];
$rolModel = new RolModel($conn);

// Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = $_POST['usuario_id'];
    $nombreRol = $_POST['rol'];

    // Validar que el usuario y rol existan
    $usuarioExiste = $rolModel->verificarUsuarioExiste($idUsuario);
    $rolExiste = $rolModel->verificarRolExiste($nombreRol);

if ($usuarioExiste && $rolExiste) {
    $idRol = $rolExiste['idrol'];

    // ✅ Esta es la función correcta
    $rolModel->asignarRolUnico($idUsuario, $idRol);

    header("Location: admin_dashboard.php?mensaje=rol_asignado");
    exit;
    }else {
        // Mostrar un error si el usuario o rol no existen
        echo "<div class='alert alert-danger text-center'>Error: Usuario o rol inválido.</div>";
    }
}

// Obtener la lista de usuarios
$queryUsuarios = "SELECT idusuario, nombre, apellido, nombre_usuario FROM usuario";
$stmtUsuarios = $conn->prepare($queryUsuarios);
$stmtUsuarios->execute();
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        
         html, body {
            margin-right: 8px;
            padding: 0;
            height: auto;
            overflow-x: hidden;
            padding-bottom: 80px; /* Aumenta este valor según necesites */
        }

        .navbar {
            background-color: #007bff;
            margin-top: 30px;
        }
        .navbar a {
            color: white;
            font-weight: bold;
        }
        .navbar .ml-auto a {
            margin-left: 10px;
        }
        .container h1 {
            color: #007bff;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            margin-bottom: 15px; /* espacio entre botones */
        }
        .btn-primary:hover {
            background-color:rgb(28, 204, 192);
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="views/admin_dashboard.php">Bienvenido <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></a>
            <div class="ml-auto">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="../controllers/logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="../views/login.php" class="btn btn-light btn-sm">Iniciar Sesión</a>
                    <a href="../views/register.php" class="btn btn-light btn-sm">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center">Panel de Administrador</h1>
        <div class="row text-center mt-4">
            <div class="col-md-3">
                <a href="publicar_finca.php" class="btn btn-primary btn-block">Publicar Finca</a>
            </div>
            <div class="col-md-3">
                <a href="reservar_finca.php" class="btn btn-primary btn-block">Reservar Finca</a>
            </div>
            <div class="col-md-3">
                <a href="listar_fincas.php" class="btn btn-primary btn-block">Listar Fincas</a>
            </div>
            <div class="col-md-3">
                <a href="listar_reservas.php" class="btn btn-primary btn-block">Listar Reservas</a>
            </div>
        </div>
        <h1 class="text-center mt-5">Asignar Roles</h1>
        <form method="POST" action="admin_dashboard.php" class="mt-4">
            <div class="form-group">
                <label for="usuario_id">Seleccionar Usuario</label>
                <select name="usuario_id" id="usuario_id" class="form-control" required>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo $usuario['idusuario']; ?>">
                            <?php echo $usuario['nombre'] . ' ' . $usuario['apellido'] . ' (' . $usuario['nombre_usuario'] . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mt-3">
                <label for="rol">Seleccionar Rol</label>
                <select name="rol" id="rol" class="form-control" required>
                    <option value="administrador">Administrador</option>
                    <option value="proveedor">Proveedor</option>
                    <option value="cliente">Cliente</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-4">Asignar Rol</button>
        </form>
    </div>
        <div>
            <h1 class="text-center mt-5">Usuarios Registrados</h1>
            <div class="row mb-3">
            <div class="col-md-6">
                <style>
                #usuariosPorRolChart {
                    max-width: 300px;
                    max-height: 200px;
                    margin: 0 auto;
                    display: block;
                }
                </style>
                <input type="text" id="busquedaUsuario" class="form-control" placeholder="Buscar usuario...">
            </div>
            <div class="col-md-6 text-right">
                <div class="btn-group" role="group">
                <button id="exportExcel" class="btn btn-success">Exportar a Excel</button>
                <button id="exportPDF" class="btn btn-danger">Exportar a PDF</button>
                <button id="exportCSV" class="btn btn-info">Exportar a CSV</button>
                <button id="exportPrint" class="btn btn-secondary">Imprimir</button>
                <button id="exportCopy" class="btn btn-primary">Copiar</button>
                </div>
            </div>
            </div>
            <div style="max-height: 300px; overflow-y: auto;">
            <table class="table table-sm table-bordered mt-2" id="usuariosTable">
                <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                    <td><?php echo $usuario['idusuario']; ?></td>
                    <td><?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?></td>
                    <td><?php echo $usuario['nombre_usuario']; ?></td>
                    <td><?php echo implode(', ', array_column($rolModel->obtenerRolesPorUsuario($usuario['idusuario']), 'nombre_rol')); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <!-- Gráfica de usuarios por rol -->
            <div class="mt-5">
            <h2 class="text-center">Distribución de Usuarios por Rol</h2>
            <canvas id="usuariosPorRolChart" height="100"></canvas>
            </div>
            <?php
            // Calcular conteo de usuarios por rol
            $roles = ['administrador', 'proveedor', 'cliente'];
            $conteoRoles = array_fill_keys($roles, 0);
            foreach ($usuarios as $usuario) {
            $rolesUsuario = array_column($rolModel->obtenerRolesPorUsuario($usuario['idusuario']), 'nombre_rol');
            foreach ($rolesUsuario as $rol) {
                if (isset($conteoRoles[$rol])) {
                $conteoRoles[$rol]++;
                }
            }
            }
            ?>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
            const ctx = document.getElementById('usuariosPorRolChart').getContext('2d');
            const usuariosPorRolChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Administrador', 'Proveedor', 'Cliente'],
                datasets: [{
                data: [
                    <?php echo $conteoRoles['administrador']; ?>,
                    <?php echo $conteoRoles['proveedor']; ?>,
                    <?php echo $conteoRoles['cliente']; ?>
                ],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.7)',
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)'
                ],
                borderColor: [
                    'rgba(0, 123, 255, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)'
                ],
                borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                    font: {
                        size: 16
                    }
                    }
                },
                title: {
                    display: false
                }
                }
            }
            });

            // Exportar a CSV
            document.getElementById('exportCSV').addEventListener('click', function () {
            let table = document.getElementById('usuariosTable');
            let rows = Array.from(table.querySelectorAll('tr'));
            let csv = rows.map(row => {
                return Array.from(row.querySelectorAll('th,td'))
                .map(cell => `"${cell.innerText.replace(/"/g, '""')}"`).join(',');
            }).join('\n');
            let blob = new Blob([csv], { type: 'text/csv' });
            let link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'usuarios.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            });

            // Imprimir tabla
            document.getElementById('exportPrint').addEventListener('click', function () {
            let printContents = document.getElementById('usuariosTable').outerHTML;
            let win = window.open('', '', 'height=600,width=800');
            win.document.write('<html><head><title>Imprimir Usuarios</title>');
            win.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
            win.document.write('</head><body>');
            win.document.write(printContents);
            win.document.write('</body></html>');
            win.document.close();
            win.print();
            });

            // Copiar tabla al portapapeles
            document.getElementById('exportCopy').addEventListener('click', function () {
            let table = document.getElementById('usuariosTable');
            let range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            try {
                document.execCommand('copy');
                alert('Tabla copiada al portapapeles');
            } catch (err) {
                alert('No se pudo copiar');
            }
            window.getSelection().removeAllRanges();
            });
            </script>
        </div>
        <script>
        document.getElementById('busquedaUsuario').addEventListener('keyup', function() {
            var filtro = this.value.toLowerCase();
            var filas = document.querySelectorAll('#usuariosTable tbody tr');
            filas.forEach(function(fila) {
                var texto = fila.textContent.toLowerCase();
                fila.style.display = texto.indexOf(filtro) > -1 ? '' : 'none';
            });
        });
        </script>

        <!-- Scripts para exportar -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
        <script>
        document.getElementById('exportExcel').addEventListener('click', function () {
            var wb = XLSX.utils.table_to_book(document.getElementById('usuariosTable'), {sheet:"Usuarios"});
            XLSX.writeFile(wb, 'usuarios.xlsx');
        });

        document.getElementById('exportPDF').addEventListener('click', function () {
            var { jsPDF } = window.jspdf;
            var doc = new jsPDF();
            doc.autoTable({ html: '#usuariosTable' });
            doc.save('usuarios.pdf');
        });
        </script>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'rol_asignado'): ?>
    <div class="alert alert-success text-center mt-3">
        Rol asignado correctamente.
    </div>
<?php endif; ?>
</body>
</html>
<?php
// Incluir pie de página
include '../includes/footer.php';
?>
