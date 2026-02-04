<?php
include("db.php");

$action = $_POST['action'] ?? ($_GET['action'] ?? null);
$id = $_POST['id'] ?? ($_GET['id'] ?? null);

error_log("Action: " . $action);
error_log("ID: " . $id);
error_log("POST data: " . print_r($_POST, true));

$taches = lireTaches();

if ($action === "ajouter") {
    $nouvelle_tache = [
        "id" => uniqid(),
        "titre" => $_POST['titre'],
        "description" => $_POST['description'],
        "priorite" => $_POST['priorite'] ?? "moyenne",
        "statut" => "à faire", // statut initial
        "date_creation" => date("Y-m-d"),
        "date_limite" => $_POST['date_limite'],
        "responsable" => $_POST['responsable'] ?? "Non assigné"
    ];

    $taches[] = $nouvelle_tache;
    ecrireTaches($taches);
    header("Location: ../index.php?page=indexTaches");
    exit;
}

if ($action === "modifier" && $id) {
    error_log("Tentative de modification de la tâche ID: " . $id);
    foreach ($taches as &$t) {
        if ($t['id'] == $id) {
            error_log("Tâche trouvée, mise à jour en cours...");
            $t['titre'] = $_POST['titre'];
            $t['description'] = $_POST['description'];
            $t['priorite'] = $_POST['priorite'];
            $t['date_limite'] = $_POST['date_limite'];
            $t['responsable'] = $_POST['responsable'];
            error_log("Tâche mise à jour: " . print_r($t, true));
        }
    }
    ecrireTaches($taches);
    error_log("Redirection vers ../index.php?page=indexTaches");
    header("Location: ../index.php?page=indexTaches");
    exit;
}

if ($action === "supprimer" && $id) {
    $taches = array_filter($taches, fn($t) => $t['id'] != $id);
    ecrireTaches(array_values($taches));
    header("Location: ../index.php?page=indexTaches");
    exit;
}

if ($action === "changer" && $id) {
    foreach ($taches as &$t) {
        if ($t['id'] == $id) {
            if ($t['statut'] === "à faire") $t['statut'] = "en cours";
            elseif ($t['statut'] === "en cours") $t['statut'] = "terminée";
        }
    }
    ecrireTaches($taches);
    header("Location: ../index.php?page=indexTaches");
    exit;
}
?>
