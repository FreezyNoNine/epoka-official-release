<?php
// Inclure le fichier de connexion à la base de données
include 'db.php';

// Vérifier si un formulaire a été soumis et s'il est complet
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'valider_parametres') {
    if (!empty($_POST['remboursement_km']) && !empty($_POST['indemnite_hebergement'])) {
        // Récupérer les valeurs des inputs
        $remboursement_km = $_POST['remboursement_km'];
        $indemnite_hebergement = $_POST['indemnite_hebergement'];

        // Préparer la requête SQL pour mettre à jour les valeurs dans la table "parametre"
        $sql = "UPDATE parametre SET kilometrage = ?, indemnite = ?";


        // Préparer et exécuter la requête avec gestion des erreurs
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }

        // Liaison des paramètres et exécution de la requête
        $result = $stmt->bind_param("dd", $remboursement_km, $indemnite_hebergement); // Les valeurs sont de type double
        if (!$result) {
            die("Erreur lors de la liaison des paramètres : " . $stmt->error);
        }

        $result = $stmt->execute();
        if (!$result) {
            die("Erreur lors de l'exécution de la requête : " . $stmt->error);
        }

        // Fermeture du statement
        $stmt->close();

        // Rediriger vers la page de paramétrage avec un message de succès
        header("Location: parametrage.php?success=1");
        exit();
    } else {
        // Rediriger vers la page de paramétrage avec un message d'erreur si des champs sont vides
        header("Location: parametrage.php?error=1");
        exit();
    }
} else {
    // Rediriger vers la page de paramétrage si le formulaire n'a pas été soumis correctement
    header("Location: parametrage.php");
    exit();
}
?>