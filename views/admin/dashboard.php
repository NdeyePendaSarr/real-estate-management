<?php /** @var int $nbBiens @var int $nbClients @var int $nbEnAttente @var int $nbConfirmees */ ?>
<div class="admin-wrap container">
    <?php include __DIR__ . '/../partials/admin-nav.php'; ?>
    <div class="admin-main">
        <h1>Tableau de bord</h1>
        <div class="kpi-grid stagger">
            <div class="kpi"><span class="kpi-num"><?= (int) $nbBiens ?></span><span class="kpi-lab">Biens</span></div>
            <div class="kpi"><span class="kpi-num"><?= (int) $nbClients ?></span><span class="kpi-lab">Clients</span></div>
            <div class="kpi"><span class="kpi-num"><?= (int) $nbEnAttente ?></span><span class="kpi-lab">Réservations en attente</span></div>
            <div class="kpi"><span class="kpi-num"><?= (int) $nbConfirmees ?></span><span class="kpi-lab">Réservations confirmées</span></div>
        </div>
        <div class="admin-quick reveal">
            <a class="btn btn-primary" href="<?= e(url('admin/biens/nouveau')) ?>">+ Ajouter un bien</a>
            <a class="btn btn-outline" href="<?= e(url('admin/reservations')) ?>">Gérer les réservations</a>
        </div>
    </div>
</div>
