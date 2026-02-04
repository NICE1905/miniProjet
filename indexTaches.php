<?php
$base_path = dirname(__DIR__) . '/';
include_once($base_path . "traitements/db.php");
include_once($base_path . "traitements/requets.php");

$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;
$recherche = $_GET['recherche'] ?? '';
$filtre_statut = $_GET['filtre_statut'] ?? 'tous';
$filtre_priorite = $_GET['filtre_priorite'] ?? 'toutes';

if (isset($_POST['action']) && $_POST['action'] === "ajouter") {
    $taches = lireTaches();
    $nouvelle_tache = [
        "id" => uniqid(),
        "titre" => $_POST['titre'] ?? '',
        "description" => $_POST['description'] ?? '',
        "priorite" => $_POST['priorite'] ?? 'moyenne',
        "statut" => "à faire",
        "date_creation" => date("Y-m-d"),
        "date_limite" => $_POST['date_limite'] ?? '',
        "responsable" => $_POST['responsable'] ?? 'Non assigné'
    ];
    $taches[] = $nouvelle_tache;
    ecrireTaches($taches);
    header("Location: index.php?page=indexTaches");
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === "modifier" && isset($_POST['id']) && $_POST['id']) {
    $taches = lireTaches();
    foreach ($taches as &$t) {
        if ($t['id'] == $_POST['id']) {
            $t['titre'] = $_POST['titre'] ?? '';
            $t['description'] = $_POST['description'] ?? '';
            $t['priorite'] = $_POST['priorite'] ?? 'moyenne';
            $t['date_limite'] = $_POST['date_limite'] ?? '';
            $t['responsable'] = $_POST['responsable'] ?? 'Non assigné';
            ecrireTaches($taches);
            header("Location: index.php?page=indexTaches");
            exit;
        }
    }
}

if ($action === "modifier" && $id) {
    $taches = lireTaches();
    foreach ($taches as $tache) {
        if ($tache['id'] == $id) {
            $tache_a_modifier = $tache;
            break;
        }
    }
}

if ($action === "supprimer" && $id) {
    $taches = lireTaches();
    $taches = array_filter($taches, fn($t) => $t['id'] != $id);
    ecrireTaches(array_values($taches));
    header("Location: index.php?page=indexTaches");
    exit;
}

if ($action === "changer" && $id) {
    $taches = lireTaches();
    foreach ($taches as &$t) {
        if ($t['id'] == $id) {
            if ($t['statut'] === "à faire") $t['statut'] = "en cours";
            elseif ($t['statut'] === "en cours") $t['statut'] = "terminée";
        }
    }
    ecrireTaches($taches);
    header("Location: index.php?page=indexTaches");
    exit;
}

$taches = lireTaches();
$taches_filtrees = $taches;

if ($recherche) {
    $taches_filtrees = array_filter($taches_filtrees, function($tache) use ($recherche) {
        return stripos($tache['titre'], $recherche) !== false || 
               stripos($tache['description'], $recherche) !== false;
    });
}

if ($filtre_statut !== 'tous') {
    $taches_filtrees = array_filter($taches_filtrees, function($tache) use ($filtre_statut) {
        return $tache['statut'] === $filtre_statut;
    });
}

if ($filtre_priorite !== 'toutes') {
    $taches_filtrees = array_filter($taches_filtrees, function($tache) use ($filtre_priorite) {
        return $tache['priorite'] === $filtre_priorite;
    });
}

$date_actuelle = date("Y-m-d");
$taches_en_retard = array_filter($taches, function($tache) use ($date_actuelle) {
    return $tache['statut'] !== 'terminée' && 
           $tache['date_limite'] && 
           $tache['date_limite'] < $date_actuelle;
});
?>

<?php if ($tache_a_modifier): ?>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3>Modifier la tâche</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="modifier">
                        <input type="hidden" name="id" value="<?= $tache_a_modifier['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control" name="titre" value="<?= htmlspecialchars($tache_a_modifier['titre']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($tache_a_modifier['description']) ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Priorité</label>
                            <select class="form-select" name="priorite">
                                <option value="basse" <?= $tache_a_modifier['priorite'] == 'basse' ? 'selected' : '' ?>>Basse</option>
                                <option value="moyenne" <?= $tache_a_modifier['priorite'] == 'moyenne' ? 'selected' : '' ?>>Moyenne</option>
                                <option value="haute" <?= $tache_a_modifier['priorite'] == 'haute' ? 'selected' : '' ?>>Haute</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Date limite</label>
                            <input type="date" class="form-control" name="date_limite" value="<?= $tache_a_modifier['date_limite'] ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Responsable</label>
                            <input type="text" class="form-control" name="responsable" value="<?= htmlspecialchars($tache_a_modifier['responsable']) ?>">
                        </div>
                        
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                        <a href="?page=indexTaches" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php if (!empty($taches_en_retard)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>⚠️ Alertes !</strong> <?= count($taches_en_retard) ?> tâche(s) en retard détectée(s) :
            <ul class="mb-0 mt-2">
                <?php foreach ($taches_en_retard as $tache): ?>
                    <li><?= htmlspecialchars($tache['titre']) ?> (Date limite: <?= $tache['date_limite'] ?>)</li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Recherche et Filtres</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="indexTaches">
                
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <input type="text" class="form-control" name="recherche" placeholder="Titre ou description..." value="<?= htmlspecialchars($recherche) ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select" name="filtre_statut">
                        <option value="tous" <?= $filtre_statut === 'tous' ? 'selected' : '' ?>>Tous</option>
                        <option value="à faire" <?= $filtre_statut === 'à faire' ? 'selected' : '' ?>>À faire</option>
                        <option value="en cours" <?= $filtre_statut === 'en cours' ? 'selected' : '' ?>>En cours</option>
                        <option value="terminée" <?= $filtre_statut === 'terminée' ? 'selected' : '' ?>>Terminée</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Priorité</label>
                    <select class="form-select" name="filtre_priorite">
                        <option value="toutes" <?= $filtre_priorite === 'toutes' ? 'selected' : '' ?>>Toutes</option>
                        <option value="basse" <?= $filtre_priorite === 'basse' ? 'selected' : '' ?>>Basse</option>
                        <option value="moyenne" <?= $filtre_priorite === 'moyenne' ? 'selected' : '' ?>>Moyenne</option>
                        <option value="haute" <?= $filtre_priorite === 'haute' ? 'selected' : '' ?>>Haute</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                        <a href="?page=indexTaches" class="btn btn-secondary">Réinitialiser</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <h1>Liste des tâches 
                <small class="text-muted">(<?= count($taches_filtrees) ?> résultat(s))</small>
            </h1>
            <?php if (empty($taches_filtrees)): ?>
                <div class="alert alert-info">
                    Aucune tâche trouvée pour les critères sélectionnés.
                </div>
            <?php else: ?>
                <?php foreach ($taches_filtrees as $tache): ?>
                    <?php 
                    $est_en_retard = $tache['statut'] !== 'terminée' && 
                                   $tache['date_limite'] && 
                                   $tache['date_limite'] < $date_actuelle;
                    ?>
                    <div class="card mb-3 <?= $est_en_retard ? 'border-danger' : '' ?> shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <h5 class="card-title mb-0 me-3">
                                            <?= htmlspecialchars($tache['titre']) ?>
                                        </h5>
                                        <?php if ($est_en_retard): ?>
                                            <span class="badge bg-danger animate-pulse">
                                                <i class="fas fa-exclamation-triangle"></i> EN RETARD
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="card-text text-muted mb-3">
                                        <?= htmlspecialchars($tache['description']) ?>
                                    </p>
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="badge bg-<?= $tache['statut'] == 'terminée' ? 'success' : ($tache['statut'] == 'en cours' ? 'warning' : 'secondary') ?>">
                                            <?= $tache['statut'] ?>
                                        </span>
                                        <span class="badge bg-info text-dark">
                                            <?= $tache['priorite'] ?>
                                        </span>
                                        <?php if ($tache['date_limite']): ?>
                                            <span class="badge bg-light text-dark">
                                                <?= $tache['date_limite'] ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="badge bg-secondary">
                                            <?= $tache['responsable'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2 ms-3">
                                    <a href="?page=indexTaches&action=changer&id=<?= $tache['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        Statut
                                    </a>
                                    <?php if ($tache['statut'] !== 'terminée'): ?>
                                        <a href="?page=indexTaches&action=modifier&id=<?= $tache['id'] ?>" 
                                           class="btn btn-sm btn-outline-warning">
                                            Modifier
                                        </a>
                                    <?php endif; ?>
                                    <a href="?page=indexTaches&action=supprimer&id=<?= $tache['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Supprimer cette tâche ?')">
                                        Supprimer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ajouter une tâche</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="ajouter">
                        
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control" name="titre" required 
                                   placeholder="Titre de la tâche">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" 
                                      placeholder="Description"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Priorité</label>
                            <select class="form-select" name="priorite">
                                <option value="basse">Basse</option>
                                <option value="moyenne" selected>Moyenne</option>
                                <option value="haute">Haute</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Date limite</label>
                            <input type="date" class="form-control" name="date_limite">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Responsable</label>
                            <input type="text" class="form-control" name="responsable" 
                                   placeholder="Responsable">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            Ajouter la tâche
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
