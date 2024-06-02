<?php
session_start();

// Vérifier si l'agent est connecté
if (!isset($_SESSION['isAgent']) || !$_SESSION['isAgent']) {
    header('Location: login.php');
    exit();
}

// Vérifier si l'email de l'utilisateur est passé en paramètre
if (!isset($_GET['user_mail'])) {
    echo "Email de l'utilisateur non spécifié.";
    exit();
}

$user_mail = $_GET['user_mail'];

// Informations de connexion à la base de données
$host = 'localhost';
$dbname = 'projet';
$username = 'elias';
$password = 'douady';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM profil WHERE mail = :email");
$stmt->execute(['email' => $user_mail]);
$user = $stmt->fetch();

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}

// Gestion de l'envoi de message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $agent_id = $_SESSION['agent_id'];

    $stmt = $pdo->prepare("INSERT INTO messages (agent_id, user_mail, message, sender_type, sender_id) VALUES (?, ?, ?, 'agent', ?)");
    $stmt->execute([$agent_id, $user_mail, $message, $agent_id]);

    if ($stmt->rowCount() > 0) {
        echo "Message envoyé avec succès.";
    } else {
        echo "Erreur lors de l'envoi du message.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Répondre à l'utilisateur - Omnes Immobilier</title>
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
            background-color: #2c3e50;
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
    </style>
</head>
<body>
    <header>
        <h1>Répondre à l'utilisateur - Omnes Immobilier</h1>
        <nav>
            <ul>
                <li><a href="Accueil.html">Accueil</a></li>
                <li><a href="ToutParcourir.html">Tout Parcourir</a></li>
                <li><a href="recherche.html">Recherche</a></li>
                <li><a href="rendezvous.html">Rendez-vous</a></li>
                <li><a href="compte.html">Votre Compte</a></li>
                <li><a href="messagerie.html">Messagerie</a></li>
                <a id="userIconLink" href="#"><img id="userIcon" src="images/icone_agent.png" alt="Agent" style="width: 20px; height: 20px; margin-left: 10px;"></a>
            </ul>
        </nav>
    </header>

    <main>
        <section id="repondre-utilisateur">
            <h2>Répondre à <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h2>
            <div class="profile-container">
                <h3>Informations de l'utilisateur</h3>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['mail']); ?></p>
                <p><strong>Nom:</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
                <p><strong>Prénom:</strong> <?php echo htmlspecialchars($user['prenom']); ?></p>

                <!-- Formulaire pour répondre à l'utilisateur -->
                <h3>Envoyer un message</h3>
                <form method="POST" action="reply.php?user_mail=<?php echo htmlspecialchars($user_mail); ?>">
                    <textarea name="message" placeholder="Écrivez votre message ici..." required></textarea>
                    <button type="submit">Envoyer</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Omnes Immobilier. Tous droits réservés.</p>
        <a href="logout.php">Déconnexion</a>
    </footer>
</body>
</html>
