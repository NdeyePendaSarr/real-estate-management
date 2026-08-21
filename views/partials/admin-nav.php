<?php use App\Core\Auth; $u = Auth::user(); ?>
<aside class="admin-sidebar">
    <div class="admin-brand">Espace pro</div>
    <nav>
        <a href="<?= e(url('admin')) ?>">Tableau de bord</a>
        <a href="<?= e(url('admin/biens')) ?>">Biens</a>
        <a href="<?= e(url('admin/reservations')) ?>">Réservations</a>
    </nav>
    <div class="admin-user"><?= e($u['prenom'] ?? '') ?> · <?= e($u['role'] ?? '') ?></div>
</aside>
