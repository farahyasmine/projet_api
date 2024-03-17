<?php
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
        $query = "SELECT * FROM equipe";
        $stm = $conn->prepare($query);
        $stm->execute();
        $equipes = $stm->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($equipes);
        break;
    case 'POST':
        if (isset($data->ne, $data->nom)) {
            $query = "INSERT INTO equipe (NE, NOM) VALUES (:ne, :nom)";
            $stm = $conn->prepare($query);
            $stm->bindParam(':ne', $data->ne);
            $stm->bindParam(':nom', $data->nom);
            if ($stm->execute()) {
                echo json_encode(["message" => "Équipe ajoutée avec succès"]);
            } else {
                echo json_encode(["message" => "Erreur lors de l'ajout de l'équipe"]);
            }
        } else {
            echo json_encode(["message" => "Données manquantes pour l'ajout de l'équipe"]);
        }
        break;
    case 'PUT':
        if (isset($data->ne, $data->nom)) {
            $query = "UPDATE equipe SET NOM = :nom WHERE NE = :ne";
            $stm = $conn->prepare($query);
            $stm->bindParam(':ne', $data->ne, PDO::PARAM_INT);
            $stm->bindParam(':nom', $data->nom);
            $stm->execute();
            echo json_encode(["message" => "Équipe mise à jour avec succès"]);
        } else {
            echo json_encode(["message" => "Données manquantes pour la mise à jour de l'équipe"]);
        }
        break;
    case 'DELETE':
        if (isset($data->ne)) {
            $query = "DELETE FROM equipe WHERE NE = :ne";
            $stm = $conn->prepare($query);
            $stm->bindParam(':ne', $data->ne, PDO::PARAM_INT);
            $stm->execute();
            echo json_encode(["message" => "Équipe supprimée avec succès"]);
        } else {
            echo json_encode(["message" => "Données manquantes pour la suppression de l'équipe"]);
        }
        break;
    default:
        echo json_encode(["message" => "Méthode $method non supportée"]);
        break;
}
?>
