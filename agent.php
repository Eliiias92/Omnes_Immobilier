<?php
session_start();

// Connexion à la base de données
$host = 'localhost';
$dbname = 'projet';
$username = 'elias';
$password = 'douady';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_email'])) {
    // Si la session n'est pas définie, vérifier le cookie
    if (isset($_COOKIE['user_email'])) {
        $_SESSION['user_email'] = $_COOKIE['user_email'];
    } else {
        header('Location: login.php');
        exit;
    }
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Récupérer les informations de l'utilisateur connecté
$user_email = $_SESSION['user_email'];
$stmt = $pdo->prepare("SELECT * FROM agent WHERE mail = :email");
$stmt->execute(['email' => $user_email]);
$user = $stmt->fetch();

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit;
}

$agent_id = $user['id'];
$_SESSION['agent_id'] = $agent_id;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Profil Agent - Omnes Immobilier</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css">
<style>
    section {
        padding: 40px 20px;
        max-width: 1200px;
        margin: auto;
    }
    h2 {
        color: #2c3e50;
    }
    .profile-container {
        background-color: #ecf0f1;
        padding: 20px;
        margin: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .profile-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    .profile-container h3 {
        margin-top: 0;
    }
    .profile-container label {
        display: block;
        margin: 10px 0 5px;
    }
    footer {
        background-color: #333;
        color: white;
        padding: 20px 10px;
        text-align: center;
    }
    footer a {
        color: white;
        text-decoration: none;
        font-weight: 700;
        transition: color 0.3s;
        margin-left: 10px;
    }
    footer a:hover {
        color: #f39c12;
    }

    /* Styles pour l'image agrandie */
    .enlarged-image-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .enlarged-image-container img {
        max-width: 80%;
        max-height: 80%;
    }
</style>
</head>
<body>
<header>
    <h1>Omnes Immobilier</h1>
    <nav>
        <ul>
            <li><a href="Accueil.html">Accueil</a></li>
            <li><a href="ToutParcourir.html">Tout Parcourir</a></li>
            <li><a href="Recherche.html">Recherche</a></li>
            <li><a href="rendezvous.html">Rendez-vous</a></li>
            <li><a href="compte.html">Votre Compte</a></li>
            <li><a href="messagerie.html">Messagerie</a></li>
        </ul>
    </nav>
</header>

<main>
<section id="votre-compte">
    <h2>Profil agent</h2>
    <div class="profile-container">
        <h3>Informations du profil</h3>
        <p><strong>Nom:</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
        <p><strong>Prénom:</strong> <?php echo htmlspecialchars($user['prenom']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['mail']); ?></p>
        <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($user['telephone']); ?></p>
        <p><strong>Adresse Bureau:</strong> <?php echo htmlspecialchars($user['adresse_bureau']); ?></p>
        <p><strong>Id Agent:</strong> <?php echo htmlspecialchars($user['id']); ?></p>

        <!-- Ajoutez des identifiants aux images pour les cibler avec JavaScript -->
        <div style="position: relative;">
            <div style="position: absolute; top: -200px; right: 0;">
                <!-- Ajoutez un identifiant à l'image de la photo -->
                <img id="agentPhoto" src="<?php echo htmlspecialchars($user['photo']); ?>" alt="Photo" class="agent-photo" style="width: 100px; height: 100px;">
            </div>
            <!-- Ajoutez un identifiant à l'image du CV -->
            <img id="agentCV" src="<?php echo htmlspecialchars($user['cv']); ?>" alt="CV" class="agent-cv" style="width: 100px; height: 100px;">
        </div>
    </div>
</section>
</main>

<footer>
    <p>&copy; 2024 Omnes Immobilier. Tous droits réservés.</p>
    <a href="logout.php">Déconnexion</a>
</footer>

<!-- Ajoutez une div pour afficher les images agrandies -->
<div class="enlarged-image-container" id="enlargedImageContainer">
    <img id="enlargedImg" src="" alt="Image agrandie">
</div>

<!-- Ajoutez du JavaScript pour afficher les images agrandies -->
<script>
    // Fonction pour afficher l'image agrandie
    function showEnlargedImage(imageSrc) {
        document.getElementById('enlargedImg').src = imageSrc;
        document.getElementById('enlargedImageContainer').style.display = 'flex';
    }

    // Fonction pour masquer l'image agrandie
    function hideEnlargedImage() {
        document.getElementById('enlargedImageContainer').style.display = 'none';
    }

    // Ajoutez des gestionnaires d'événements pour chaque image
    document.getElementById('agentPhoto').addEventListener('click', function() {
        showEnlargedImage('<?php echo htmlspecialchars($user['photo']); ?>');
    });
    document.getElementById('agentCV').addEventListener('click', function() {
        showEnlargedImage('<?php echo htmlspecialchars($user['cv']); ?>');
    });

    // Ajoutez un gestionnaire d'événements pour masquer l'image agrandie lorsque l'utilisateur clique dessus
    document.getElementById('enlargedImageContainer').addEventListener('click', hideEnlargedImage);
</script>
</body>
</html>
