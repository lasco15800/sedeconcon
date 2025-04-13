<?php
session_start();
$conn = new mysqli("localhost", "root", "", "login_system");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (!isset($_SESSION["user"])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION["user"];

// Obtener permisos del usuario
$stmt = $conn->prepare("SELECT categories.name FROM categories 
                        JOIN user_permissions ON categories.id = user_permissions.category_id 
                        JOIN users ON users.id = user_permissions.user_id 
                        WHERE users.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);

// Verificar si el usuario es admin
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND username = 'admin'");
$stmt->bind_param("s", $username);
$stmt->execute();
$isAdmin = $stmt->get_result()->num_rows > 0;

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function checkAccess(category) {
            const categories = <?php echo json_encode(array_column($categories, 'name')); ?>;
            const isAdmin = <?php echo json_encode($isAdmin); ?>;
            if (!isAdmin && !categories.includes(category)) {
                alert('No tienes acceso a esta categoría.');
                return false;
            }
            return true;
        }

        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        }

        let currentImageIndex = 0;
        const images = [
            'image1.jpeg',
            'image2.jpeg',
            'image3.jpeg',
            'image4.jpeg'
        ];

        function changeImage() {
            const imgElement = document.getElementById('slideshow');
            currentImageIndex = (currentImageIndex + 1) % images.length;
            imgElement.src = images[currentImageIndex];
        }

        setInterval(changeImage, 2500);
    </script>
</head>
<body>
    <div class="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <h2>Menú</h2>
        <ul>
            <li><a href="dashboard.php">Inicio</a></li>
            <li><a href="emergencias.html" onclick="return checkAccess('Emergencias')">Emergencias</a></li>
            <li><a href="vestuario.html" onclick="return checkAccess('Vestuario')">Vestuario</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
        </ul>
    </div>
    <div class="content">
        <h2>Bienvenido, <?php echo $_SESSION["user"]; ?>!</h2>
        <p>Selecciona una categoría del menú lateral para continuar.</p>
        <div class="slideshow-container">
            <img id="slideshow" src="image1.jpg" alt="Slideshow Image" style="width:100%; max-width:600px; height:auto;">
        </div>
    </div>
</body>
</html>