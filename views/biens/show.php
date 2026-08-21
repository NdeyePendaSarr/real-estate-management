<?php
/** @var array $bien @var array $images @var bool $estFavori */
use App\Core\Auth;
$principale = !empty($images) ? upload_url($images[0]['fichier']) : null;
?>
<section class="section">
    <div class="container">
        <a class="back-link" href="<?= e(url('biens')) ?>">← Retour aux biens</a>

        <div class="bien-detail">
            <div class="bien-gallery reveal from-left">
                <div class="gallery-main">
                    <?php if ($principale): ?>
                        <img id="gallery-main-img" src="<?= e($principale) ?>" alt="<?= e($bien['titre']) ?>">
                    <?php else: ?>
                        <div class="gallery-empty">Pas encore de photo</div>
                    <?php endif; ?>
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="gallery-thumbs">
                        <?php foreach ($images as $img): ?>
                            <button type="button" class="thumb" data-src="<?= e(upload_url($img['fichier'])) ?>">
                                <img src="<?= e(upload_url($img['fichier'])) ?>" alt="" loading="lazy">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bien-info reveal from-right">
                <div class="bien-info-head">
                    <span class="badge badge-<?= e($bien['type']) ?>"><?= e(ucfirst($bien['type'])) ?></span>
                    <span class="badge badge-<?= $bien['statut'] === 'disponible' ? 'dispo' : 'loue' ?>">
                        <?= e(statut_label($bien['statut'])) ?>
                    </span>
                </div>
                <h1><?= e($bien['titre']) ?></h1>
                <p class="bien-loc"><?= e($bien['ville']) ?> · <?= (int) $bien['chambres'] ?> chambre<?= $bien['chambres'] > 1 ? 's' : '' ?>
                    <?= !empty($bien['surface']) ? ' · ' . (int) $bien['surface'] . ' m²' : '' ?></p>
                <p class="bien-prix"><?= e(format_prix($bien['prix'])) ?><small> / mois</small></p>

                <div class="bien-desc"><?= nl2br(e($bien['description'])) ?></div>

                <?php if ($bien['statut'] === 'disponible'): ?>
                    <?php if (Auth::is('client')): ?>
                        <form class="reserve-card" method="post" action="<?= e(url('reservations')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="bien_id" value="<?= (int) $bien['id'] ?>">
                            <h2>Réserver ce bien</h2>
                            <div class="field-row">
                                <div class="field">
                                    <label for="d1">Du <span class="req">*</span></label>
                                    <input id="d1" type="date" name="date_debut" required min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="field">
                                    <label for="d2">Au <span class="req">*</span></label>
                                    <input id="d2" type="date" name="date_fin" required min="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <button class="btn btn-primary btn-block" type="submit">Demander une réservation</button>
                        </form>
                    <?php elseif (Auth::check()): ?>
                        <p class="note">Connecté en tant que professionnel — la réservation est réservée aux clients.</p>
                    <?php else: ?>
                        <div class="reserve-card">
                            <h2>Réserver ce bien</h2>
                            <p class="muted">Connectez-vous pour réserver en ligne.</p>
                            <a class="btn btn-primary btn-block" href="<?= e(url('connexion')) ?>">Se connecter</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="note">Ce bien est actuellement loué.</p>
                <?php endif; ?>

                <?php if (Auth::is('client')): ?>
                    <form method="post" action="<?= e(url('favoris/' . $bien['id'])) ?>" class="inline-form fav-line">
                        <?= csrf_field() ?>
                        <input type="hidden" name="retour" value="biens/<?= (int) $bien['id'] ?>">
                        <button class="btn btn-outline <?= $estFavori ? 'is-fav' : '' ?>" type="submit">
                            <?= $estFavori ? '♥ Retirer des favoris' : '♡ Ajouter aux favoris' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
