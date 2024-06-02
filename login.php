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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Préparer la requête pour vérifier l'utilisateur
    $stmt = $conn->prepare("SELECT id, mail, mot_de_passe, is_admin FROM profil WHERE mail = ?");
    if (!$stmt) {
        die("Preparation failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $email_db, $hashed_password, $is_admin);
        $stmt->fetch();

        // Vérification du mot de passe
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_email'] = $email;
            $_SESSION['is_admin'] = $is_admin;
            $_SESSION['user_id'] = $id;
            
            if ($is_admin == 3) { // Agent
                $_SESSION['agent_id'] = $id;
                $_SESSION['isAgent'] = true;
                header('Location: agent.php');
            } elseif ($is_admin == 1) { // Administrateur
                $_SESSION['isAdmin'] = true;
                header('Location: admin.php');
            } else { // Utilisateur
                $_SESSION['isUser'] = true;
                header('Location: user.php');
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with this email.";
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <title>Omnes Immobilier - Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 10px;
            text-align: center;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            text-align: center;
            margin: 0;
        }
        nav ul li {
            display: inline-block;
            margin: 0 15px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s;
        }
        nav ul li a:hover {
            color: #f39c12;
        }
        section {
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
        }
        h2 {
            color: #2c3e50;
        }
        footer {
            background-color: #2c3e50;
            color: white;
            padding: 20px 10px;
            text-align: center;
        }
        .error-message {
            color: red;
        }
        form {
            background-color: #ecf0f1;
            padding: 20px;
            margin: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        form:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="password"], input[type="email"], button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
        }
        button {
            background-color: #2c3e50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #f39c12;
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
                <a id="userIconLink" href="#"><img id="userIcon" src="icone_non_connecte.png" alt="Non connecté" style="width: 20px; height: 20px; margin-left: 10px;"></a>

            </ul>
        </nav>
    </header>
    <main>
        <section id="connexion">
            <h2>Connexion</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <label for="email">Adresse e-mail :</label>
                <input type="email" id="email" name="email" required>
                <br>
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Se connecter</button>
            </form>
            <button onclick="window.location.href='register.php'">Créer un compte</button>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Omnes Immobilier. Tous droits réservés.</p>
    </footer>
</body>
</html>
