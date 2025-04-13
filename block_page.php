<?php
session_start();
$conn = new mysqli("localhost", "root", "", "login_system");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$ip = $_SERVER['REMOTE_ADDR']; // Obtener la IP del usuario
$stmt = $conn->prepare("SELECT * FROM users WHERE ip = ?");
$stmt->bind_param("s", $ip);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && $user['failed_attempts'] >= 3) {
    $lock_time = new DateTime($user['lock_time']);
    $current_time = new DateTime();

    // Mostrar mensaje con el tiempo restante para desbloqueo
    if ($current_time->diff($lock_time)->h < 24) {
        $remaining_time = 24 - $current_time->diff($lock_time)->h;
        echo "<h2>Cuenta Bloqueada</h2>";
        echo "<p>Tu cuenta ha sido bloqueada por 24 horas. Te quedan {$remaining_time} horas para poder volver a intentar.</p>";
    } else {
        // Si ya pasaron 24 horas, desbloquear al usuario
        $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, lock_time = NULL WHERE ip = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        echo "<script>alert('Tu cuenta ha sido desbloqueada. Puedes intentar iniciar sesión de nuevo.'); window.location.href='index.html';</script>";
    }
} else {
    echo "<script>alert('Tu cuenta no está bloqueada.'); window.location.href='index.html';</script>";
}

$stmt->close();
$conn->close();
?>