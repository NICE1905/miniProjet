<?php
include_once("db.php");

function getStats() {
    $taches = lireTaches();
    $total = count($taches);
    $terminees = count(array_filter($taches, fn($t) => $t['statut'] === "terminée"));
    $retard = count(array_filter($taches, fn($t) => $t['statut'] !== "terminée" && $t['date_limite'] < date("Y-m-d")));
    $pourcentage = $total > 0 ? round(($terminees / $total) * 100, 2) : 0;

    return [
        "total" => $total,
        "terminees" => $terminees,
        "retard" => $retard,
        "pourcentage" => $pourcentage
    ];
}
?>
