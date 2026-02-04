<?php
$base_path = dirname(__DIR__) . '/';
include_once($base_path . "traitements/db.php");
include_once($base_path . "traitements/requets.php");

$taches = lireTaches();
$date_actuelle = date("Y-m-d");

$total = count($taches);
$terminees = count(array_filter($taches, fn($t) => $t['statut'] === "terminée"));
$en_cours = count(array_filter($taches, fn($t) => $t['statut'] === "en cours"));
$a_faire = count(array_filter($taches, fn($t) => $t['statut'] === "à faire"));

$retard = count(array_filter($taches, fn($t) => 
    $t['statut'] !== 'terminée' && 
    $t['date_limite'] && 
    $t['date_limite'] < $date_actuelle
));

$pourcentage_terminees = $total > 0 ? round(($terminees / $total) * 100, 1) : 0;
$pourcentage_en_cours = $total > 0 ? round(($en_cours / $total) * 100, 1) : 0;
$pourcentage_a_faire = $total > 0 ? round(($a_faire / $total) * 100, 1) : 0;

$haute_priorite = count(array_filter($taches, fn($t) => $t['priorite'] === 'haute'));
$moyenne_priorite = count(array_filter($taches, fn($t) => $t['priorite'] === 'moyenne'));
$basse_priorite = count(array_filter($taches, fn($t) => $t['priorite'] === 'basse'));

$taches_recentes = count(array_filter($taches, fn($t) => 
    isset($t['date_creation']) && 
    (strtotime($date_actuelle) - strtotime($t['date_creation'])) <= 7 * 24 * 3600
));

?>

<div class="row">
    <div class="col-12">
        <h1>Tableau de bord</h1>
        <p class="text-muted">Vue d'ensemble de toutes vos tâches</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-primary mb-2"><?= $total ?></h3>
                <p class="text-muted mb-1">Total tâches</p>
                <small class="text-muted"><?= $taches_recentes ?> cette semaine</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-success mb-2"><?= $terminees ?></h3>
                <p class="text-muted mb-1">Terminées</p>
                <small class="text-muted"><?= $pourcentage_terminees ?>%</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-warning mb-2"><?= $en_cours ?></h3>
                <p class="text-muted mb-1">En cours</p>
                <small class="text-muted"><?= $pourcentage_en_cours ?>%</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-danger mb-2"><?= $retard ?></h3>
                <p class="text-muted mb-1">En retard</p>
                <small class="text-muted"><?= $total > 0 ? round(($retard / $total) * 100, 1) : 0 ?>%</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Répartition par statut</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>À faire</span>
                        <span><?= $a_faire ?> (<?= $pourcentage_a_faire ?>%)</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-secondary" style="width: <?= $pourcentage_a_faire ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>En cours</span>
                        <span><?= $en_cours ?> (<?= $pourcentage_en_cours ?>%)</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-warning" style="width: <?= $pourcentage_en_cours ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Terminées</span>
                        <span><?= $terminees ?> (<?= $pourcentage_terminees ?>%)</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" style="width: <?= $pourcentage_terminees ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Répartition par priorité</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h4><?= $haute_priorite ?></h4>
                                <small>Haute</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h4><?= $moyenne_priorite ?></h4>
                                <small>Moyenne</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h4><?= $basse_priorite ?></h4>
                                <small>Basse</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Informations supplémentaires</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Tâches récentes (7 jours)</span>
                        <span class="badge bg-info"><?= $taches_recentes ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Taux de complétion</span>
                        <span class="badge bg-success"><?= $pourcentage_terminees ?>%</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Tâches à faire</span>
                        <span class="badge bg-secondary"><?= $a_faire ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="?page=indexTaches" class="btn btn-primary">
                        <i class="fas fa-list"></i> Voir toutes les tâches
                    </a>
                    <?php if ($retard > 0): ?>
                        <a href="?page=indexTaches&filtre_statut=en cours" class="btn btn-warning">
                            <i class="fas fa-exclamation-triangle"></i> Voir les tâches en retard
                        </a>
                    <?php endif; ?>
                    <a href="?page=indexTaches" class="btn btn-success">
                        <i class="fas fa-plus"></i> Ajouter une tâche
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>