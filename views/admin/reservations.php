<?php /** @var array $reservations */ ?>
<div class="admin-wrap container">
    <?php include __DIR__ . '/../partials/admin-nav.php'; ?>
    <div class="admin-main">
        <h1>Réservations</h1>
        <?php if (empty($reservations)): ?>
            <p class="muted">Aucune réservation.</p>
        <?php else: ?>
            <div class="table-wrap reveal"><table class="table">
                <thead><tr><th>Bien</th><th>Client</th><th>Période</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($reservations as $r): ?>
                    <tr>
                        <td><?= e($r['titre']) ?><div class="muted small"><?= e(ucfirst($r['type'])) ?></div></td>
                        <td><?= e($r['prenom'] . ' ' . $r['nom']) ?><div class="muted small"><?= e($r['email']) ?></div></td>
                        <td><?= e($r['date_debut']) ?> → <?= e($r['date_fin']) ?></td>
                        <td><span class="pill pill-<?= e($r['statut']) ?>"><?= e(statut_label($r['statut'])) ?></span></td>
                        <td>
                            <form method="post" action="<?= e(url('admin/reservations/' . $r['id'] . '/statut')) ?>" class="statut-form">
                                <?= csrf_field() ?>
                                <select name="statut" onchange="this.form.submit()" aria-label="Changer le statut">
                                    <option value="en_attente" <?= $r['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                    <option value="confirmee" <?= $r['statut'] === 'confirmee' ? 'selected' : '' ?>>Confirmée</option>
                                    <option value="annulee" <?= $r['statut'] === 'annulee' ? 'selected' : '' ?>>Annulée</option>
                                </select>
                                <noscript><button type="submit" class="btn-mini">OK</button></noscript>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
        <?php endif; ?>
    </div>
</div>
