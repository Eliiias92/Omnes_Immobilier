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
if ((isset($_SESSION['isUser']) && $_SESSION['isUser'] && isset($_SESSION['user_email'])) || 
    (isset($_SESSION['isAgent']) && $_SESSION['isAgent'] && isset($_SESSION['agent_id']))) {
    
    // Récupérer les données de la requête POST
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['agent_id']) && isset($data['jour']) && isset($data['heure'])) {
        $user_mail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
        $agent_id = $data['agent_id'];
        $jour = $data['jour'];
        $heure = $data['heure'];

        // Vérifier si le créneau est déjà réservé
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM rendezvous WHERE agent_id = ? AND jour = ? AND heure = ?");
        $check_stmt->bind_param("iss", $agent_id, $jour, $heure);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count > 0) {
            $response['success'] = false;
            $response['error'] = "Le créneau est déjà réservé.";
        } else {
            // Préparer et lier
            $stmt = $conn->prepare("INSERT INTO rendezvous (user_mail, agent_id, jour, heure) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $user_mail, $agent_id, $jour, $heure);

            if ($stmt->execute()) {
                $response['success'] = true;
            } else {
                $response['success'] = false;
                $response['error'] = $stmt->error;
            }

            $stmt->close();
        }
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
