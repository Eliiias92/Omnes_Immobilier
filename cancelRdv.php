<?php
session_start(); // Assurez-vous que les sessions sont démarrées pour utiliser $_SESSION

// Informations de connexion à la base de données
$servername = "localhost";
$username = "elias";
$password = "douady";
$dbname = "projet";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]));
}

$response = array();

// Vérifier si l'utilisateur ou l'agent est connecté
if ((isset($_SESSION['isUser']) && $_SESSION['isUser']) || (isset($_SESSION['isAgent']) && $_SESSION['isAgent'])) {
    // Récupérer les données de la requête POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['id'])) {
        $rdv_id = $data['id'];
        
        // Préparer et lier
        $stmt = $conn->prepare("DELETE FROM rendezvous WHERE id = ?");
        $stmt->bind_param("i", $rdv_id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['error'] = $stmt->error;
        }

        $stmt->close();
    } else {
        $response['success'] = false;
        $response['error'] = "Missing required parameters";
    }
} else {
    $response['success'] = false;
    $response['error'] = "Utilisateur non connecté ou autorisation insuffisante.";
}

$conn->close();
echo json_encode($response);
?>
