<?php
// Connexion à la base de données
$servername = "localhost";
$username = "elias";
$password = "douady";
$dbname = "projet";

// Démarrage de la session
session_start();

// Vérification si l'utilisateur est connecté via les cookies
if (!isset($_SESSION['user_email']) && isset($_COOKIE['user_email'])) {
    $_SESSION['user_email'] = $_COOKIE['user_email'];
    $_SESSION['is_admin'] = $_COOKIE['is_admin'];
}

// Initialisation des variables d'état de connexion
$isUserLoggedIn = false;
$isAgent = false;
$isAdmin = false;

// Vérification de la session de l'utilisateur
if (isset($_SESSION['user_email'])) {
    // Mise à jour de l'état de connexion
    $isUserLoggedIn = true;
    
    // Vérification du rôle de l'utilisateur
    switch ($_SESSION['is_admin']) {
        case 1:
            $isAdmin = true;
            break;
        case 2:
            $isUserLoggedIn = true;
            break;
        default:
            break;
    }
}

// Vérifier la connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si une action "like" a été reçue
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action']) && $data['action'] === 'like' && isset($_SESSION['user_email'])) {
        // Récupérer l'ID de la propriété
        $propertyId = $data['propertyId'];
        
        // Récupérer l'adresse email de l'utilisateur
        $userEmail = $_SESSION['user_email'];
        
        // Récupérer la date et l'heure actuelles
        $dateHeure = date('Y-m-d H:i:s');
        
        // Préparer la requête d'insertion pour la table "achat"
        $stmt_achat = $conn->prepare("INSERT INTO hp (mail, id, date_heure) VALUES (?, ?, ?)");
        $stmt_achat->bind_param("sis", $userEmail, $propertyId, $dateHeure);
        
        // Exécuter la requête pour la table "achat"
        if ($stmt_achat->execute()) {
            // Envoi de la réponse JSON indiquant le succès de l'opération
            echo json_encode(array("success" => true));
        } else {
            // Envoi de la réponse JSON indiquant l'échec de l'opération
            echo json_encode(array("success" => false));
        }
        
        // Fermer la requête pour la table "achat"
        $stmt_achat->close();
        exit();
    } elseif (isset($data['action']) && $data['action'] === 'like2' && isset($_SESSION['user_email'])) {
        // Récupérer l'ID de la propriété
        $propertyId = $data['propertyId'];
        
        // Récupérer l'adresse email de l'utilisateur
        $userEmail = $_SESSION['user_email'];
        
        // Récupérer la date et l'heure actuelles
        $dateHeure = date('Y-m-d H:i:s');
        
        // Préparer la requête d'insertion pour la table "hp"
        $stmt_hp = $conn->prepare("INSERT INTO achat (mail, id, date_heure) VALUES (?, ?, ?)");
        $stmt_hp->bind_param("sis", $userEmail, $propertyId, $dateHeure);
        
        // Exécuter la requête pour la table "hp"
        if ($stmt_hp->execute()) {
            // Envoi de la réponse JSON indiquant le succès de l'opération
            echo json_encode(array("success" => true));
        } else {
            // Envoi de la réponse JSON indiquant l'échec de l'opération
            echo json_encode(array("success" => false));
        }
        
        // Fermer la requête pour la table "hp"
        $stmt_hp->close();
        exit();
    }
}

// Fermer la connexion
$conn->close();

// Envoi des données d'état de connexion en JSON
echo json_encode(array(
    "isLoggedIn" => $isUserLoggedIn,
    "isAdmin" => $isAdmin,
    "isAgent" => $isAgent
));
?>