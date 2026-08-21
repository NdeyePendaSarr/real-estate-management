<?php /** @var array $utilisateur @var array $reservations */ ?>
<section class="page-head"><div class="container">
    <p class="eyebrow">Espace client</p>
    <h1>Bonjour, <?= e($utilisateur['prenom']) ?></h1>
</div></section>
<section class="section"><div class="container account-grid stagger">
    <div class="card">
        <h2>Mes informations</h2>
        <dl class="info-list">
            <div><dt>Nom</dt><dd><?= e($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></dd></div>
            <div><dt>Email</dt><dd><?= e($utilisateur['email']) ?></dd></div>
            <div><dt>Téléphone</dt><dd><?= e($utilisateur['telephone'] ?: '—') ?></dd></div>
        </dl>
        <div class="account-actions">
            <a class="btn btn-outline" href="<?= e(url('favoris')) ?>">Mes favoris</a>
            <a class="btn btn-outline" href="<?= e(url('mes-reservations')) ?>">Mes réservations</a>
        </div>
        <form method="post" action="<?= e(url('compte/desactiver')) ?>" class="danger-form"
              onsubmit="return confirm('Désactiver votre compte ? Vous serez déconnecté.');">
            <?= csrf_field() ?>
            <button class="btn btn-danger" type="submit">Désactiver mon compte</button>
        </form>
    </div>
    <div class="card">
        <h2>Dernières réservations</h2>
        <?php if (empty($reservations)): ?>
            <p class="muted">Aucune réservation pour le moment.</p>
            <a class="btn btn-primary" href="<?= e(url('biens')) ?>">Trouver un bien</a>
        <?php else: ?>
            <ul class="resa-list">
                <?php foreach (array_slice($reservations, 0, 4) as $r): ?>
                    <li>
                        <span><?= e($r['titre']) ?></span>
                        <span class="pill pill-<?= e($r['statut']) ?>"><?= e(statut_label($r['statut'])) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div></section>
