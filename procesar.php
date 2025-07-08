<?php
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDeDatos = "bdapps";

// Crear conexión
$conn = new mysqli($servidor, $usuario, $contraseña, $baseDeDatos);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener datos del formulario
$nickname = $conn->real_escape_string($_POST['nickname']);
$nombre = $conn->real_escape_string($_POST['nombre']);
$apellido = $conn->real_escape_string($_POST['apellido']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$fecha_nac = $conn->real_escape_string($_POST['fecha_nac']);
$email = $conn->real_escape_string($_POST['email']);
$telefono = $conn->real_escape_string($_POST['telefono']);
$avatar = null;

// Procesar avatar si se subió
if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] === UPLOAD_ERR_OK) {
    // Configuración de tamaño máximo (5MB en bytes)
    $maxFileSize = 5 * 1024 * 1024;
    
    // Verificar tamaño del archivo
    if ($_FILES["avatar"]["size"] > $maxFileSize) {
        die("El avatar excede el tamaño máximo permitido de 5MB");
    }
    
    // Verificar tipo de archivo
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $nombreArchivo = $_FILES["avatar"]["name"];
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedExtensions)) {
        die("Solo se permiten archivos JPG, JPEG, PNG o GIF para el avatar");
    }
    
    // Generar nombre único para el archivo
    $avatar = "avatar_".date("Y_m_d_His").".".$extension;
    $temporal = $_FILES["avatar"]["tmp_name"];
    $destination = "imagenes/".$avatar;
    
    if (!move_uploaded_file($temporal, $destination)) {
        die("Error al mover el archivo subido");
    }
}

// Validación adicional del servidor
$errors = [];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "El formato del email no es válido";
}

if (strlen($telefono) < 9 || !is_numeric($telefono)) {
    $errors[] = "El teléfono debe tener al menos 9 dígitos numéricos";
}

if (!empty($errors)) {
    echo "<h1>Error en el formulario</h1>";
    foreach ($errors as $error) {
        echo "<p class='error'>$error</p>";
    }
    echo "<a href='index.html'>Volver al formulario</a>";
    exit();
}

// Preparar la consulta SQL
$sql = "INSERT INTO usuarios (nickname, nombre, apellido, contraseña, fecha_nac, email, telefono, avatar) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $nickname, $nombre, $apellido, $password, $fecha_nac, $email, $telefono, $avatar);

if ($stmt->execute()) {
    echo "<h1>Registro exitoso</h1>";
    echo "<p class='success'>Los datos se han guardado correctamente.</p>";
    echo "<a href='index.html'>Regresar al formulario</a>";
    echo "<br><a href='viewAvatar.php'>Ver registros</a>";
} else {
    echo "<h1>Error</h1>";
    echo "<p class='error'>Error al guardar los datos: " . $conn->error . "</p>";
    echo "<a href='index.html'>Volver al formulario</a>";
}

$stmt->close();
$conn->close();
?>