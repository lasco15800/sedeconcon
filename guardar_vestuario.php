<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$nombre = $_POST['nombre'];
$runt = $_POST['runt'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$foto = $_FILES['foto'];
$pdf = $_FILES['pdf'];
$uniforme_institucional = isset($_POST['uniforme_institucional']) ? 1 : 0;
$guerrera = isset($_POST['guerrera']) ? 1 : 0;
$polera_servicio = isset($_POST['polera_servicio']) ? 1 : 0;
$pantalon_servicio = isset($_POST['pantalon_servicio']) ? 1 : 0;
$porta_equipo = isset($_POST['porta_equipo']) ? 1 : 0;
$quipi = isset($_POST['quipi']) ? 1 : 0;
$parca_polar = isset($_POST['parca_polar']) ? 1 : 0;
$corta_viento = isset($_POST['corta_viento']) ? 1 : 0;
$cinturon = isset($_POST['cinturon']) ? 1 : 0;
$cinturon_na = isset($_POST['cinturon_na']) ? 1 : 0;
$botas = isset($_POST['botas']) ? 1 : 0;
$talla_botas = $_POST['talla_botas'];
$polera_instruccion = isset($_POST['polera_instruccion']) ? 1 : 0;
$pantalon_instruccion = isset($_POST['pantalon_instruccion']) ? 1 : 0;

// Directorio de uploads
$upload_dir = 'uploads/';

// Verificar si el directorio de uploads existe, si no, crearlo
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Manejar la carga de la foto
$foto_nombre = basename($foto["name"]);
$foto_ruta = $upload_dir . $foto_nombre;
if (move_uploaded_file($foto["tmp_name"], $foto_ruta)) {
    echo "La foto se ha subido correctamente.<br>";
} else {
    echo "Error al subir la foto.<br>";
}

// Manejar la carga del PDF
$pdf_nombre = basename($pdf["name"]);
$pdf_ruta = $upload_dir . $pdf_nombre;
if (move_uploaded_file($pdf["tmp_name"], $pdf_ruta)) {
    echo "El PDF se ha subido correctamente.<br>";
} else {
    echo "Error al subir el PDF.<br>";
}

// Insertar datos en la base de datos
$sql = "INSERT INTO vestuario (nombre, runt, fecha_nacimiento, foto, pdf, uniforme_institucional, guerrera, polera_servicio, pantalon_servicio, porta_equipo, quipi, parca_polar, corta_viento, cinturon, cinturon_na, botas, talla_botas, polera_instruccion, pantalon_instruccion) VALUES ('$nombre', '$runt', '$fecha_nacimiento', '$foto_ruta', '$pdf_ruta', '$uniforme_institucional', '$guerrera', '$polera_servicio', '$pantalon_servicio', '$porta_equipo', '$quipi', '$parca_polar', '$corta_viento', '$cinturon', '$cinturon_na', '$botas', '$talla_botas', '$polera_instruccion', '$pantalon_instruccion')";

if ($conn->query($sql) === TRUE) {
    echo "Datos guardados exitosamente";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>