<?php /** @var array $reservations */ ?>
<section class="page-head"><div class="container">
    <p class="eyebrow">Espace client</p><h1>Mes réservations</h1>
</div></section>
<section class="section"><div class="container">
    <?php if (empty($reservations)): ?>
        <div class="empty reveal"><p>Vous n'avez aucune réservation.</p>
            <a class="btn btn-primary" href="<?= e(url('biens')) ?>">Voir les biens</a></div>
    <?php else: ?>
        <div class="table-wrap reveal"><table class="table">
            <thead><tr><th>Bien</th><th>Période</th><th>Statut</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($reservations as $r): ?>
                <tr>
                    <td><a href="<?= e(url('biens/' . $r['bien_id'])) ?>"><?= e($r['titre']) ?></a>
                        <div class="muted small"><?= e(ucfirst($r['type'])) ?> · <?= e($r['ville']) ?></div></td>
                    <td><?= e($r['date_debut']) ?> → <?= e($r['date_fin']) ?></td>
                    <td><span class="pill pill-<?= e($r['statut']) ?>"><?= e(statut_label($r['statut'])) ?></span></td>
                    <td>
                        <?php if ($r['statut'] !== 'annulee'): ?>
                            <form method="post" action="<?= e(url('reservations/' . $r['id'] . '/annuler')) ?>"
                                  onsubmit="return confirm('Annuler cette réservation ?');">
                                <?= csrf_field() ?>
                                <button class="btn-mini" type="submit">Annuler</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div></section>
