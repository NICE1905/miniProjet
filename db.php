<?php

function lireTaches() {
    $fichier = __DIR__ . "/taches.json";
    if (!file_exists($fichier)) {
        return [];
    }

    $contenu = file_get_contents($fichier);
    $taches = json_decode($contenu, true);

    return is_array($taches) ? $taches : []; 
}
function ecrireTaches($taches) {
    $fichier = __DIR__ . "/taches.json";
    file_put_contents($fichier, json_encode($taches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}



?>
