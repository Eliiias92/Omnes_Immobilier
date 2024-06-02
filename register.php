<?php
$host = 'localhost';
$dbname = 'projet';
$username = 'elias';
$password = 'douady';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si la table "profil" a besoin d'être modifiée
    $result = $pdo->query("SHOW COLUMNS FROM profil LIKE 'is_admin'");
    if ($result->rowCount() === 0) {
        // Ajouter la colonne "is_admin" à la table "profil"
        $pdo->exec("ALTER TABLE profil ADD COLUMN is_admin INT(11) NOT NULL DEFAULT 0");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adresse1 = $_POST['adresse1'];
        $adresse2 = $_POST['adresse2'];
        $ville = $_POST['ville'];
        $code_postal = $_POST['code_postal'];
        $type_payement = $_POST['type_payement'];
        $numero_carte = $_POST['numero_carte'];
        $nom_affiche = $_POST['nom_affiche'];
        $date_expiration = $_POST['date_expiration'];
        $code_securite = $_POST['code_securite'];
        $pays = $_POST['pays'];
        $mail = $_POST['mail'];
        $mot_de_passe = $_POST['mot_de_passe'];
        $role = $_POST['role'];

        // Vérifier le rôle de l'utilisateur et définir la valeur de "is_admin"
        $is_admin = ($role == '1') ? 1 : (($role == '2') ? 2 : 3);

        $mot_de_passe = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO profil (id,nom, prenom, adresse_1, adresse_2, ville, code_postal, type_de_payement, numero_de_carte, nom_affiche, date_d_expiration, code_de_securite, pays, mail, mot_de_passe, is_admin) VALUES (:id,:nom, :prenom, :adresse1, :adresse2, :ville, :code_postal, :type_payement, :numero_carte, :nom_affiche, :date_expiration, :code_securite, :pays, :mail, :mot_de_passe, :is_admin)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':adresse1', $adresse1);
        $stmt->bindParam(':adresse2', $adresse2);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':code_postal', $code_postal);
        $stmt->bindParam(':type_payement', $type_payement);
        $stmt->bindParam(':numero_carte', $numero_carte);
        $stmt->bindParam(':nom_affiche', $nom_affiche);
        $stmt->bindParam(':date_expiration', $date_expiration);
        $stmt->bindParam(':code_securite', $code_securite);
        $stmt->bindParam(':pays', $pays);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        $stmt->bindParam(':is_admin', $is_admin);

        $stmt->execute();

        header('Location: compte.html');
        exit;
    }
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}
?>