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
    $message_id = $data['message_id'];

    if (isset($_SESSION['isUser']) && $_SESSION['isUser'] && isset($_SESSION['user_email'])) {
        $stmt = $conn->prepare("DELETE FROM messages WHERE id = ? AND user_mail = ?");
        if (!$stmt) {
            $response['error'] = "Preparation failed: (" . $conn->errno . ") " . $conn->error;
            echo json_encode($response);
            exit();
        }
        $stmt->bind_param("is", $message_id, $_SESSION['user_email']);
    } elseif (isset($_SESSION['isAgent']) && $_SESSION['isAgent'] && isset($_SESSION['agent_id'])) {
        $stmt = $conn->prepare("DELETE FROM messages WHERE id = ? AND agent_id = ?");
        if (!$stmt) {
            $response['error'] = "Preparation failed: (" . $conn->errno . ") " . $conn->error;
            echo json_encode($response);
            exit();
        }
        $stmt->bind_param("ii", $message_id, $_SESSION['agent_id']);
    } else {
        $response['error'] = "Données invalides ou utilisateur non connecté.";
        echo json_encode($response);
        exit();
    }

    if ($stmt->execute()) {
        $response['success'] = "Message supprimé avec succès.";
    } else {
        $response['error'] = "Erreur lors de la suppression du message.";
    }

    $stmt->close();
} else {
    $response['error'] = "Méthode de requête non valide.";
}

$conn->close();
echo json_encode($response);
?>
