<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['playerName'];
    $score = $_POST['playerScoreHidden'];

    // Validate form data (you can add more validation as needed)
    if (!empty($name) && !empty($score)) {
            // Process the data (for example, save it to a database)
            // Here, we're just displaying it back to the user
           try {
            // Connect to the SQLite database
            $db = new PDO('sqlite:score.db');

            // Set PDO to throw exceptions on errors
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare an SQL statement to insert data into the database
            $stmt = $db->prepare("INSERT INTO score (name, score) VALUES (:name, :score)");

            // Bind parameters to the SQL statement
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':score', $score);

            // Execute the SQL statement
            $stmt->execute();

            // Close the database connection
            $db = null;

            // Respond with success message
            header("Location: index.php");
            die();
        } catch (PDOException $e) {
            // Handle any errors that occur during execution
            echo json_encode(array('success' => false, 'error' => $e->getMessage()));
        }
    } else {
            echo "<h2>Error: All fields are required!</h2>";
    }
} else {
    header('Content-Type: application/json');



    try {
        // Ouvrir la connexion à la base de données SQLite
        $db = new PDO('sqlite:score.db'); // Remplacez 'scores.db' par le chemin vers votre fichier de base de données

        // Préparer et exécuter la requête SQL
        $stmt = $db->query('SELECT name, score FROM score ORDER BY score DESC LIMIT 20');

        // Récupérer tous les résultats
        $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Renvoyer les résultats en JSON
        echo json_encode($scores);

    } catch (PDOException $e) {
        // En cas d'erreur, renvoyer un message d'erreur JSON
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>