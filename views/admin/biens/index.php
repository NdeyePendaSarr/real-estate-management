<?php /** @var array $biens */ ?>
<div class="admin-wrap container">
    <?php include __DIR__ . '/../../partials/admin-nav.php'; ?>
    <div class="admin-main">
        <div class="section-head reveal">
            <h1>Biens</h1>
            <a class="btn btn-primary" href="<?= e(url('admin/biens/nouveau')) ?>">+ Ajouter</a>
        </div>
        <?php if (empty($biens)): ?>
            <p class="muted">Aucun bien enregistré.</p>
        <?php else: ?>
            <div class="table-wrap reveal"><table class="table">
                <thead><tr><th>Titre</th><th>Type</th><th>Ville</th><th>Prix</th><th>Statut</th><th>Photos</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($biens as $b): ?>
                    <tr>
                        <td><?= e($b['titre']) ?></td>
                        <td><?= e(ucfirst($b['type'])) ?></td>
                        <td><?= e($b['ville']) ?></td>
                        <td><?= e(format_prix($b['prix'])) ?></td>
                        <td><span class="pill pill-<?= $b['statut'] === 'disponible' ? 'confirmee' : 'annulee' ?>"><?= e(statut_label($b['statut'])) ?></span></td>
                        <td><?= (int) ($b['nb_images'] ?? 0) ?></td>
                        <td class="row-actions">
                            <a class="btn-mini" href="<?= e(url('admin/biens/' . $b['id'] . '/modifier')) ?>">Modifier</a>
                            <form method="post" action="<?= e(url('admin/biens/' . $b['id'] . '/supprimer')) ?>"
                                  onsubmit="return confirm('Supprimer ce bien ? Cette action est définitive.');">
                                <?= csrf_field() ?>
                                <button class="btn-mini btn-mini-danger" type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
        <?php endif; ?>
    </div>
</div>
