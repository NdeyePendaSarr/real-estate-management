<?php /** @var array $biens */ $favoris = array_map(fn($b) => (int) $b['id'], $biens); ?>
<section class="page-head"><div class="container">
    <p class="eyebrow">Espace client</p><h1>Mes favoris</h1>
</div></section>
<section class="section"><div class="container">
    <?php if (empty($biens)): ?>
        <div class="empty reveal"><p>Aucun favori pour l'instant.</p>
            <a class="btn btn-primary" href="<?= e(url('biens')) ?>">Parcourir les biens</a></div>
    <?php else: ?>
        <div class="grid-biens stagger">
            <?php foreach ($biens as $b): ?>
                <?php include __DIR__ . '/../partials/bien-card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div></section>
