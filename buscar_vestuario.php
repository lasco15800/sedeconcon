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

// Obtener el término de búsqueda desde el parámetro GET
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Consulta para buscar registros en la tabla vestuario por nombre o RUT
$sql = "SELECT * FROM vestuario WHERE nombre LIKE ? OR runt LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = '%' . $query . '%';
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
// Recuperar los resultados y almacenarlos en un array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Cerrar la conexión a la base de datos
$stmt->close();
$conn->close();

// Devolver los resultados en formato JSON
header('Content-Type: application/json');
echo json_encode($data);
?>