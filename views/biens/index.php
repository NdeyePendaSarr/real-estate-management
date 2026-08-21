<?php
/** @var array $biens @var array $filtres @var array $villes @var int $total @var int $page @var int $pages @var array $favoris */
?>
<section class="page-head">
    <div class="container">
        <p class="eyebrow">Catalogue</p>
        <h1>Nos biens à louer</h1>
        <p class="lead"><?= (int) $total ?> bien<?= $total > 1 ? 's' : '' ?> correspondent à votre recherche.</p>
    </div>
</section>

<section class="section">
    <div class="container layout-sidebar">
        <aside class="filters-card reveal from-left">
            <form method="get" action="<?= e(url('biens')) ?>">
                <h2 class="filters-title">Filtrer</h2>
                <div class="field">
                    <label for="f-q">Mot-clé</label>
                    <input id="f-q" name="q" value="<?= e($filtres['q']) ?>" placeholder="Titre, quartier…">
                </div>
                <div class="field">
                    <label for="f-type">Type</label>
                    <select id="f-type" name="type">
                        <option value="">Tous</option>
                        <option value="appartement" <?= $filtres['type'] === 'appartement' ? 'selected' : '' ?>>Appartement</option>
                        <option value="villa" <?= $filtres['type'] === 'villa' ? 'selected' : '' ?>>Villa</option>
                    </select>
                </div>
                <div class="field">
                    <label for="f-ville">Ville</label>
                    <select id="f-ville" name="ville">
                        <option value="">Toutes</option>
                        <?php foreach ($villes as $ville): ?>
                            <option value="<?= e($ville) ?>" <?= $filtres['ville'] === $ville ? 'selected' : '' ?>><?= e($ville) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="f-min">Prix min</label>
                        <input id="f-min" name="prix_min" type="number" min="0" step="50000" value="<?= e($filtres['prix_min']) ?>">
                    </div>
                    <div class="field">
                        <label for="f-max">Prix max</label>
                        <input id="f-max" name="prix_max" type="number" min="0" step="50000" value="<?= e($filtres['prix_max']) ?>">
                    </div>
                </div>
                <div class="field">
                    <label for="f-statut">Disponibilité</label>
                    <select id="f-statut" name="statut">
                        <option value="">Toutes</option>
                        <option value="disponible" <?= $filtres['statut'] === 'disponible' ? 'selected' : '' ?>>Disponible</option>
                        <option value="loue" <?= $filtres['statut'] === 'loue' ? 'selected' : '' ?>>Loué</option>
                    </select>
                </div>
                <button class="btn btn-primary btn-block" type="submit">Appliquer</button>
                <a class="btn btn-ghost btn-block" href="<?= e(url('biens')) ?>">Réinitialiser</a>
            </form>
        </aside>

        <div>
            <?php if (empty($biens)): ?>
                <div class="empty reveal">
                    <p>Aucun bien ne correspond à ces critères.</p>
                    <a class="btn btn-outline" href="<?= e(url('biens')) ?>">Voir tous les biens</a>
                </div>
            <?php else: ?>
                <div class="grid-biens stagger">
                    <?php foreach ($biens as $b): ?>
                        <?php include __DIR__ . '/../partials/bien-card.php'; ?>
                    <?php endforeach; ?>
                </div>
                <?php include __DIR__ . '/../partials/pagination.php'; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
