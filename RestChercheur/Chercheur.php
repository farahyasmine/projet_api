<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

// PROJET FINAL CORRECT
// jai crée databse.php mais sur mon mac jai eu que des erreurs donc je l'ai réecrite dans chaque fichier
// aussi la bdd ne voulait pas s'importer à cause des collations (version) jai du changer

/* jai effectué toutes les requetes necessaires avec postman pour tester et dans index.html 
 y'a le frontend de l appli */
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
        header('Content-Type: application/json');
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'filtrerChercheurs') {
                $query = "SELECT chercheur.NOM, chercheur.PRENOM 
                FROM chercheur 
                INNER JOIN aff ON chercheur.NC = aff.NC 
                INNER JOIN projet ON aff.NP = projet.NP 
                WHERE aff.ANNEE = 2018 
                GROUP BY chercheur.NC 
                HAVING COUNT(DISTINCT projet.NP) > 2 
                AND SUM(projet.BUDGET) > 30000;";
            } else if ($_GET['action'] == 'chercheursAvecVieira') {
                $query = "SELECT DISTINCT c2.NOM, c2.PRENOM
                FROM chercheur c1
                JOIN aff a1 ON c1.NC = a1.NC
                JOIN aff a2 ON a1.NP = a2.NP
                JOIN chercheur c2 ON a2.NC = c2.NC
                WHERE c1.NOM = 'VIEIRA' AND a1.ANNEE = 2018 AND c2.NOM <> 'VIEIRA'";
                
            }
        } else {
            $query = "SELECT * FROM chercheur";
        }
    
        $stm = $conn->prepare($query);
        $stm->execute();
        $resultat = $stm->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($resultat);
        break;
    
    
    case 'POST':
            if (isset($data->nom, $data->prenom, $data->equipe)) {
                $query = "INSERT INTO chercheur (NOM, PRENOM, NE) VALUES (:nom, :prenom, :equipe)";
                $stm = $conn->prepare($query);
                $stm->bindParam(':nom', $data->nom);
                $stm->bindParam(':prenom', $data->prenom);
                $stm->bindParam(':equipe', $data->equipe, PDO::PARAM_INT);
                if ($stm->execute()) {
                    echo json_encode(["message" => "Chercheur ajouté avec succès"]);
                } else {
                    echo json_encode(["message" => "Erreur lors de l'ajout du chercheur"]);
                }
            } else {
                echo json_encode(["message" => "Données manquantes pour l'ajout du chercheur"]);
            }
         break;
        
 case 'PUT':
          
            $query = "UPDATE chercheur SET NOM = :nom, PRENOM = :prenom, NE = :equipe WHERE NC = :nc";
            $stm = $conn->prepare($query);
            $stm->bindParam(':nc', $data->nc, PDO::PARAM_INT);
            $stm->bindParam(':nom', $data->nom, PDO::PARAM_STR);
            $stm->bindParam(':prenom', $data->prenom, PDO::PARAM_STR);
            $stm->bindParam(':equipe', $data->equipe, PDO::PARAM_INT);
            $stm->execute();
            echo json_encode(["message" => "Chercheur mis à jour avec succès"]);
            break;
        
    case 'DELETE':
       
        $query = "DELETE FROM chercheur WHERE NC = :nc";
        $stm = $conn->prepare($query);
        $stm->bindParam(':nc', $data->nc, PDO::PARAM_INT);

        $stm->execute();
        echo json_encode(["message" => "Chercheur supprimé avec succès"]);
        break;
    default:
        echo json_encode(["message" => "Méthode $method non supportée"]);
        break;
}
?>
