<?php
// Inclure le fichier de connexion à la base de données
include 'db.php';

// Vérifier si la requête est de type POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['mission_id'])) {
    $action = $_POST['action'];
    $mission_id = $_POST['mission_id'];

    // Vérifier si l'action est "valider"
    if ($action === 'valider') {
        // Mettre à jour la mission dans la base de données
        $sql = "UPDATE mission SET valider = 1 WHERE id_mission = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $mission_id);

        if ($stmt->execute()) {
            echo "Mission validée avec succès !";
        } else {
            echo "Erreur lors de la validation de la mission.";
        }

        $stmt->close();
    } elseif ($action === 'rembourser') {
        // Mettre à jour la mission pour le remboursement
        $sql = "UPDATE mission SET payer = 1 WHERE id_mission = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $mission_id);

        if ($stmt->execute()) {
            echo "Mission remboursée avec succès !";
        } else {
            echo "Erreur lors du remboursement de la mission.";
        }

        $stmt->close();
    } else {
        echo "Action non reconnue.";
    }
} else {
    echo "Requête invalide.";
}
?>
