<?php
// Paramètres de connexion à la base de données MySQL
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$database = "epoka_missions";

// Créer une connexion à la base de données
$conn = new mysqli($servername, $dbUsername, $dbPassword, $database);
$query = $conn->prepare("SELECT matricule as username, mot_de_passe as password, nom, prenom FROM salarie");
$query->execute();


$query->bind_result($username, $password, $nom, $prenom);
$contents=array();
while($query->fetch()){
    $data=array();
    $data['username'] = $username;
    $data['password'] = $password;
    $data['nom'] = $nom;
    $data['prenom'] = $prenom;

    array_push($contents, $data);
}

echo json_encode($contents);