
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
?>