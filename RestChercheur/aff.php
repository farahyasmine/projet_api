<?php
 // Connexion à la base de données
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
        $query = "SELECT * FROM aff";
        $stm = $conn->prepare($query);
        $stm->execute();
        $affs = $stm->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($affs);
        break;
    case 'POST':
        if (isset($data->np, $data->nc, $data->annee)) {
            $query = "INSERT INTO aff (NP, NC, ANNEE) VALUES (:np, :nc, :annee)";
            $stm = $conn->prepare($query);
            $stm->bindParam(':np', $data->np);
            $stm->bindParam(':nc', $data->nc, PDO::PARAM_INT);
            $stm->bindParam(':annee', $data->annee, PDO::PARAM_INT);
            if ($stm->execute()) {
                echo json_encode(["message" => "Affectation ajoutée avec succès"]);
            } else {
                echo json_encode(["message" => "Erreur lors de l'ajout de l'affectation"]);
            }
        } else {
            echo json_encode(["message" => "Données manquantes pour l'ajout de l'affectation"]);
        }
        break;
    case 'PUT':
        // La mise à jour d'une affectation peut nécessiter une logique spécifique, notamment si les clés primaires sont impliquées
        echo json_encode(["message" => "Mise à jour non supportée pour les affectations"]);
        break;
    case 'DELETE':
        if (isset($data->np, $data->nc, $data->annee)) {
            $query = "DELETE FROM aff WHERE NP = :np AND NC = :nc AND ANNEE = :annee";
            $stm = $conn->prepare($query);
            $stm->bindParam(':np', $data->np);
            $stm->bindParam(':nc', $data->nc, PDO::PARAM_INT);
            $stm->bindParam(':annee', $data->annee, PDO::PARAM_INT);
            $stm->execute();
            echo json_encode(["message" => "Affectation supprimée avec succès"]);
        } else {
            echo json_encode(["message" => "Données manquantes pour la suppression de l'affectation"]);
        }
        break;
    default:
        echo json_encode(["message" => "Méthode $method non supportée"]);
        break;
}
?>
