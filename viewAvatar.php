<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bdapps";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el número de página actual
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$registros_por_pagina = 10;
$inicio = ($pagina > 1) ? ($pagina * $registros_por_pagina - $registros_por_pagina) : 0;

// Consulta para obtener los registros con paginación
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM usuarios LIMIT $inicio, $registros_por_pagina";
$resultado = $conn->query($sql);

// Obtener el total de registros para la paginación
$total_registros = $conn->query("SELECT FOUND_ROWS() as total")->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Asegurarse de que la página no sea mayor que el total de páginas
if($pagina > $total_paginas && $total_paginas > 0) {
    header("Location: ?pagina=".$total_paginas);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            position: sticky;
            top: 0;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .paginacion {
            margin-top: 20px;
            text-align: center;
        }
        .paginacion a, .paginacion span {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 4px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
            border-radius: 4px;
        }
        .paginacion a:hover:not(.active) {
            background-color: #ddd;
        }
        .paginacion .active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }
        .avatar-miniatura {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }
        .sin-avatar {
            color: #999;
            font-style: italic;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Lista de Usuarios</h2>
    
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nickname</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha Nacimiento</th>
                    <th>Avatar</th>
                    <th>Última Actualización</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultado->num_rows > 0) {
                    while($fila = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".htmlspecialchars($fila['id_usuarios'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($fila['nickname'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($fila['nombre'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($fila['apellido'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($fila['email'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($fila['telefono'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($fila['fecha_nac'] ?? '')."</td>";
                        
                        // Mostrar avatar como miniatura
                        echo "<td>";
                        if (!empty($fila['avatar'])) {
                            echo '<img src="imagenes/'.htmlspecialchars($fila['avatar']).'" class="avatar-miniatura" alt="Avatar">';
                        } else {
                            echo '<span class="sin-avatar">Sin avatar</span>';
                        }
                        echo "</td>";
                        
                        echo "<td>".htmlspecialchars($fila['horaCambioKey'] ?? '')."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No se encontraron registros</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación simplificada -->
    <div class="paginacion">
        <?php 
        // Mostrar enlace a la primera página
        if($pagina > 1) {
            echo '<a href="?pagina=1">Primera</a>';
            echo '<a href="?pagina='.($pagina-1).'">Anterior</a>';
        }
        
        // Mostrar indicador de página actual
        echo '<span>'.$pagina.' de '.$total_paginas.'</span>';
        
        // Mostrar enlace a la última página
        if($pagina < $total_paginas) {
            echo '<a href="?pagina='.($pagina+1).'">Siguiente</a>';
            echo '<a href="?pagina='.$total_paginas.'">Última</a>';
        }
        ?>
    </div>

    <?php
    // Cerrar conexión
    $conn->close();
    ?>
</body>
</html>