<?php
/** @var array|null $bien @var string $action */
$images = $images ?? [];
$val = static fn(string $k, string $d = '') => e($bien[$k] ?? ($_SESSION['_old'][$k] ?? $d));
$sel = static fn(string $k, string $v) => (($bien[$k] ?? ($_SESSION['_old'][$k] ?? '')) === $v) ? 'selected' : '';
?>
<div class="admin-wrap container">
    <?php include __DIR__ . '/../../partials/admin-nav.php'; ?>
    <div class="admin-main">
        <a class="back-link" href="<?= e(url('admin/biens')) ?>">← Retour aux biens</a>
        <h1><?= $bien ? 'Modifier le bien' : 'Ajouter un bien' ?></h1>

        <form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="form-card reveal">
            <?= csrf_field() ?>
            <p class="form-legend"><span class="req">*</span> Champs obligatoires</p>
            <div class="field-row">
                <div class="field">
                    <label for="type">Type <span class="req">*</span></label>
                    <select id="type" name="type" required>
                        <option value="appartement" <?= $sel('type', 'appartement') ?>>Appartement</option>
                        <option value="villa" <?= $sel('type', 'villa') ?>>Villa</option>
                    </select>
                </div>
                <div class="field">
                    <label for="statut">Statut <span class="req">*</span></label>
                    <select id="statut" name="statut" required>
                        <option value="disponible" <?= $sel('statut', 'disponible') ?>>Disponible</option>
                        <option value="loue" <?= $sel('statut', 'loue') ?>>Loué</option>
                    </select>
                </div>
            </div>
            <div class="field">
                <label for="titre">Titre <span class="req">*</span></label>
                <input id="titre" name="titre" value="<?= $val('titre') ?>" required>
            </div>
            <div class="field">
                <label for="description">Description <span class="req">*</span></label>
                <textarea id="description" name="description" rows="5" required><?= $val('description') ?></textarea>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="prix">Prix (FCFA / mois) <span class="req">*</span></label>
                    <input id="prix" name="prix" type="number" min="0" step="1000" value="<?= $val('prix') ?>" required>
                </div>
                <div class="field">
                    <label for="ville">Ville <span class="req">*</span></label>
                    <input id="ville" name="ville" value="<?= $val('ville', 'Dakar') ?>" required>
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="chambres">Chambres <span class="opt">(optionnel)</span></label>
                    <input id="chambres" name="chambres" type="number" min="0" value="<?= $val('chambres', '1') ?>">
                </div>
                <div class="field">
                    <label for="surface">Surface (m²) <span class="opt">(optionnel)</span></label>
                    <input id="surface" name="surface" type="number" min="0" value="<?= $val('surface') ?>">
                </div>
            </div>

            <div class="field">
                <label for="images">Photos <span class="opt">(optionnel)</span> <span class="muted small">(JPG, PNG ou WebP — 4 Mo max)</span></label>
                <input id="images" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
            </div>

            <?php if (!empty($images)): ?>
                <div class="current-images">
                    <?php foreach ($images as $img): ?>
                        <img src="<?= e(upload_url($img['fichier'])) ?>" alt="" width="90" height="70">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <button class="btn btn-primary" type="submit"><?= $bien ? 'Enregistrer' : 'Ajouter le bien' ?></button>
        </form>
    </div>
</div>
