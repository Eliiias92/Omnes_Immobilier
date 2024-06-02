<?php
session_start();

// Informations de connexion à la base de données
$servername = "localhost";
$username = "elias";
$password = "douady";
$dbname = "projet";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $agent_id = $data['agent_id'];
    $message = $data['message'];

    if (isset($_SESSION['isUser']) && $_SESSION['isUser'] && isset($_SESSION['user_email'])) {
        // Insérer le message envoyé par l'utilisateur
        $user_email = $_SESSION['user_email'];
        $stmt = $conn->prepare("INSERT INTO messages (agent_id, user_mail, message, sender_type, sender_id) VALUES (?, ?, ?, 'user', ?)");
        if (!$stmt) {
            $response['error'] = "Preparation failed: (" . $conn->errno . ") " . $conn->error;
            echo json_encode($response);
            exit();
        }
        $stmt->bind_param("issi", $agent_id, $user_email, $message, $_SESSION['user_id']);
    } elseif (isset($_SESSION['isAgent']) && $_SESSION['isAgent'] && isset($_SESSION['agent_id'])) {
        // Insérer le message envoyé par l'agent
        $stmt = $conn->prepare("INSERT INTO messages (agent_id, message, sender_type, sender_id) VALUES (?, ?, 'agent', ?)");
        if (!$stmt) {
            $response['error'] = "Preparation failed: (" . $conn->errno . ") " . $conn->error;
            echo json_encode($response);
            exit();
        }
        $stmt->bind_param("isi", $agent_id, $message, $_SESSION['agent_id']);
    } else {
        $response['error'] = "Utilisateur non connecté.";
        echo json_encode($response);
        exit();
    }

    if ($stmt->execute()) {
        $response['success'] = "Message envoyé avec succès.";
    } else {
        $response['error'] = "Erreur lors de l'envoi du message.";
    }

    $stmt->close();
} else {
    $response['error'] = "Méthode de requête non valide.";
}

$conn->close();
echo json_encode($response);
?>
