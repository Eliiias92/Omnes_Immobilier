<?php
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "elias", "douady", "projet");

if ($mysqli->connect_error) {
    echo json_encode(['error' => 'Erreur de connexion à la base de données: ' . $mysqli->connect_error]);
    exit();
}

// Récupérer les agents
$agents = [];
$agent_result = $mysqli->query("SELECT id, nom AS name FROM agent");
if ($agent_result) {
    while ($row = $agent_result->fetch_assoc()) {
        $agents[] = $row;
    }
} else {
    echo json_encode(['error' => 'Erreur lors de la récupération des agents: ' . $mysqli->error]);
    exit();
}

// Récupérer les propriétés
$properties = [];
$property_result = $mysqli->query("SELECT id, categorie_id, titre, prix, description, image, chambres, salles_de_bain, adresse, surface, remarques, ville FROM proprietes");
if ($property_result) {
    while ($row = $property_result->fetch_assoc()) {
        $properties[] = $row;
    }
} else {
    echo json_encode(['error' => 'Erreur lors de la récupération des propriétés: ' . $mysqli->error]);
    exit();
}

// Récupérer les villes
$cities = [];
$city_result = $mysqli->query("SELECT DISTINCT ville FROM proprietes");
if ($city_result) {
    while ($row = $city_result->fetch_assoc()) {
        $cities[] = $row;
    }
} else {
    echo json_encode(['error' => 'Erreur lors de la récupération des villes: ' . $mysqli->error]);
    exit();
}

echo json_encode([
    'agents' => $agents,
    'properties' => $properties,
    'cities' => $cities
]);

$mysqli->close();
?>
