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
                    <?php $estArchive = (int) ($b['archive'] ?? 0) === 1; ?>
                    <tr<?= $estArchive ? ' class="row-archived"' : '' ?>>
                        <td><?= e($b['titre']) ?>
                            <?php if ($estArchive): ?><span class="pill pill-annulee">Archivé</span><?php endif; ?>
                        </td>
                        <td><?= e(ucfirst($b['type'])) ?></td>
                        <td><?= e($b['ville']) ?></td>
                        <td><?= e(format_prix($b['prix'])) ?></td>
                        <td><span class="pill pill-<?= $b['statut'] === 'disponible' ? 'confirmee' : 'annulee' ?>"><?= e(statut_label($b['statut'])) ?></span></td>
                        <td><?= (int) ($b['nb_images'] ?? 0) ?></td>
                        <td class="row-actions">
                            <a class="btn-mini" href="<?= e(url('admin/biens/' . $b['id'] . '/modifier')) ?>">Modifier</a>
                            <?php if ($estArchive): ?>
                                <form method="post" action="<?= e(url('admin/biens/' . $b['id'] . '/restaurer')) ?>">
                                    <?= csrf_field() ?>
                                    <button class="btn-mini" type="submit">Restaurer</button>
                                </form>
                            <?php else: ?>
                                <form method="post" action="<?= e(url('admin/biens/' . $b['id'] . '/archiver')) ?>"
                                      onsubmit="return confirm('Archiver ce bien ? Il sera retiré du site public (historique conservé).');">
                                    <?= csrf_field() ?>
                                    <button class="btn-mini btn-mini-danger" type="submit">Archiver</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
        <?php endif; ?>
    </div>
</div>
