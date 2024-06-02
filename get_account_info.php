<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$servername = "localhost";
$username = "elias";
$password = "douady";
$dbname = "projet";

$response = array('success' => false);

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    $email = $_SESSION['user_email'];
    $role = $_SESSION['user_role'];

    // Connexion à la base de données
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        die("Connection failed: " . $conn->connect_error);
    }

    if ($role == 'client') {
        $sql = "SELECT * FROM profil WHERE mail = ?";
    } elseif ($role == 'agent') {
        $sql = "SELECT * FROM agent WHERE mail = ?";
    } elseif ($role == 'admin') {
        $sql = "SELECT * FROM profil WHERE mail = ? AND is_admin = 1";
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $response['success'] = true;
        $response['role'] = $role;
        $response = array_merge($response, $user);
    } else {
        $response['error'] = "Aucun utilisateur trouvé";
        error_log("Aucun utilisateur trouvé pour l'email: " . $email);
    }

    $stmt->close();
    $conn->close();
} else {
    $response['error'] = "Utilisateur non connecté";
    error_log("Utilisateur non connecté");
}

header('Content-Type: application/json');
echo json_encode($response);
?>
