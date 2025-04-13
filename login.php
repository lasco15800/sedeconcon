<?php
session_start();
$conn = new mysqli("localhost", "root", "", "login_system");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = hash("sha256", $_POST["password"]); // Encriptar la contraseña usando SHA-256
    $ip = $_SERVER['REMOTE_ADDR']; // Obtener la IP del usuario

    // Verificar si la IP ya está bloqueada en la tabla de usuarios
    $stmt = $conn->prepare("SELECT * FROM users WHERE ip = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verificar si la IP ya está bloqueada en la tabla de IPs bloqueadas
    $stmt_ip_blocked = $conn->prepare("SELECT * FROM blocked_ips WHERE ip = ?");
    $stmt_ip_blocked->bind_param("s", $ip);
    $stmt_ip_blocked->execute();
    $result_ip_blocked = $stmt_ip_blocked->get_result();
    $blocked_ip = $result_ip_blocked->fetch_assoc();

    if (($user && $user['failed_attempts'] >= 3) || ($blocked_ip && $blocked_ip['failed_attempts'] >= 3)) {
        $lock_time = new DateTime($user ? $user['lock_time'] : $blocked_ip['lock_time']);
        $current_time = new DateTime();

        // Mostrar mensaje con el tiempo restante para desbloqueo
        if ($current_time->diff($lock_time)->h < 24) {
            $remaining_time = 24 - $current_time->diff($lock_time)->h;
            echo "<h2>Cuenta Bloqueada</h2>";
            echo "<p>Tu cuenta ha sido bloqueada por 24 horas. Te quedan {$remaining_time} horas para poder volver a intentar.</p>";
            exit();
        } else {
            // Si ya pasaron 24 horas, desbloquear al usuario o IP
            if ($user) {
                $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, lock_time = NULL WHERE ip = ?");
                $stmt->bind_param("s", $ip);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare("DELETE FROM blocked_ips WHERE ip = ?");
                $stmt->bind_param("s", $ip);
                $stmt->execute();
            }
            echo "<script>alert('Tu cuenta ha sido desbloqueada. Puedes intentar iniciar sesión de nuevo.'); window.location.href='index.html';</script>";
        }
    }

    // Verificar si el usuario existe en la base de datos
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error); // Mostrar el error si no se puede preparar la consulta
    }

    $stmt->bind_param("s", $username); // Vincular el parámetro 'username'
    $stmt->execute();
    $result = $stmt->get_result();

    // Comprobar si el usuario existe
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña
        if ($user['password'] === $password) {
            // Si la contraseña es correcta
            $_SESSION["user"] = $username;
            $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, lock_time = NULL, ip = ? WHERE username = ?");
            $stmt->bind_param("ss", $ip, $username);
            $stmt->execute();
            header("Location: dashboard.php");
            exit();
        } else {
            // Incrementar el contador de intentos fallidos
            $stmt = $conn->prepare("UPDATE users SET failed_attempts = failed_attempts + 1, ip = ? WHERE username = ?");
            $stmt->bind_param("ss", $ip, $username);
            $stmt->execute();

            // Bloquear la IP si ha habido 3 intentos fallidos
            if ($user['failed_attempts'] + 1 >= 3) {
                $lock_time = (new DateTime())->format('Y-m-d H:i:s');
                $stmt = $conn->prepare("UPDATE users SET lock_time = ? WHERE username = ?");
                $stmt->bind_param("ss", $lock_time, $username);
                $stmt->execute();
                echo "<h2>Cuenta Bloqueada</h2>";
                echo "<p>Tu cuenta ha sido bloqueada por 24 horas.</p>";
                exit();
            }

            echo "<script>alert('Contraseña incorrecta'); window.location.href='index.html';</script>";
        }
    } else {
        // Incrementar el contador de intentos fallidos si el usuario no existe
        $stmt = $conn->prepare("INSERT INTO blocked_ips (ip, failed_attempts, lock_time) VALUES (?, 1, NULL) ON DUPLICATE KEY UPDATE failed_attempts = failed_attempts + 1");
        $stmt->bind_param("s", $ip);
        $stmt->execute();

        // Bloquear la IP si ha habido 3 intentos fallidos
        $stmt = $conn->prepare("SELECT * FROM blocked_ips WHERE ip = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $result = $stmt->get_result();
        $blocked_ip = $result->fetch_assoc();

        if ($blocked_ip['failed_attempts'] >= 3) {
            $lock_time = (new DateTime())->format('Y-m-d H:i:s');
            $stmt = $conn->prepare("UPDATE blocked_ips SET lock_time = ? WHERE ip = ?");
            $stmt->bind_param("ss", $lock_time, $ip);
            $stmt->execute();
            echo "<h2>Cuenta Bloqueada</h2>";
            echo "<p>Tu cuenta ha sido bloqueada por 24 horas.</p>";
            exit();
        }

        echo "<script>alert('Usuario no encontrado'); window.location.href='index.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>