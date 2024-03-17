<?php
header('Content-Type: application/json');
$host = 'localhost';
$port = 8888; // Port MySQL par défaut dans MAMP
$dbname = 'recherche';
$user = 'root';
$password = 'root';
$conn = null;

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST' || $method == 'PUT' || $method == 'DELETE') {
    $data = json_decode(file_get_contents("php://input"));
}

switch ($method) {
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] == 'projetsPourChercheursSpecifiques') {
            $query = "SELECT DISTINCT projet.* FROM projet
                      JOIN aff ON projet.NP = aff.NP
                      JOIN chercheur ON chercheur.NC = aff.NC
                      WHERE chercheur.NOM IN ('BOUGUEROUA', 'WOLSKA')";
            $stm = $conn->prepare($query);
            $stm->execute();
            $projets = $stm->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($projets);
        } elseif (isset($_GET['action']) && $_GET['action'] == 'budgetSuperieur2018') {
            $query = "SELECT * FROM projet WHERE BUDGET > ( SELECT MAX(BUDGET) FROM projet WHERE NP IN ( SELECT NP FROM aff WHERE ANNEE = 2018 ) )";
            $stm = $conn->prepare($query);
            $stm->execute();
            $projets = $stm->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($projets);
        } else {
            $query = "SELECT * FROM projet";
            $stm = $conn->prepare($query);
            $stm->execute();
            $projets = $stm->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($projets);
        }
        break;
    
    
    case 'POST':
        if (isset($data->np, $data->nom, $data->budget, $data->ne)) {
            $query = "INSERT INTO projet (NP, NOM, BUDGET, NE) VALUES (:np, :nom, :budget, :ne)";
            $stm = $conn->prepare($query);
            $stm->bindParam(':np', $data->np);
            $stm->bindParam(':nom', $data->nom);
            $stm->bindParam(':budget', $data->budget);
            $stm->bindParam(':ne', $data->ne, PDO::PARAM_INT);
            if ($stm->execute()) {
                echo json_encode(["message" => "Projet ajouté avec succès"]);
            } else {
                echo json_encode(["message" => "Erreur lors de l'ajout du projet"]);
            }
        } else {
            echo json_encode(["message" => "Données manquantes pour l'ajout du projet"]);
        }
        break;
    case 'PUT':
        if (isset($data->np, $data->nom, $data->budget, $data->ne)) {
            $query = "UPDATE projet SET NOM = :nom, BUDGET = :budget, NE = :ne WHERE NP = :np";
            $stm = $conn->prepare($query);
            $stm->bindParam(':np', $data->np);
            $stm->bindParam(':nom', $data->nom);
            $stm->bindParam(':budget', $data->budget);
            $stm->bindParam(':ne', $data->ne, PDO::PARAM_INT);
            $stm->execute();
            echo json_encode(["message" => "Projet mis à jour avec succès"]);
        } else {
            echo json_encode(["message" => "Données manquantes pour la mise à jour du projet"]);
        }
        break;
    case 'DELETE':
        if (isset($data->np)) {
            $query = "DELETE FROM projet WHERE NP = :np";
            $stm = $conn->prepare($query);
            $stm->bindParam(':np', $data->np);
            $stm->execute();
            echo json_encode(["message" => "Projet supprimé avec succès"]);
        } else {
            echo json_encode(["message" => "Données manquantes pour la suppression du projet"]);
        }
        break;
    default:
        echo json_encode(["message" => "Méthode $method non supportée"]);
        break;
}
?>
