<?php
/** @var array $b  bien avec clé 'image' */
/** @var array $favoris  ids favoris (optionnel) */
$favoris = $favoris ?? [];
$estFav = in_array((int) $b['id'], $favoris, true);
$img = !empty($b['image']) ? upload_url($b['image']) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='267'%3E%3Crect width='400' height='267' fill='%23f0e9db'/%3E%3Ctext x='50%25' y='50%25' fill='%23b8863b' font-family='sans-serif' font-size='16' text-anchor='middle' dominant-baseline='middle'%3EPhoto à venir%3C/text%3E%3C/svg%3E";
?>
<article class="bien-card">
    <a class="bien-card-media" href="<?= e(url('biens/' . $b['id'])) ?>">
        <img src="<?= e($img) ?>" alt="<?= e($b['titre']) ?>" loading="lazy"
             onerror="this.style.background='#e7e2d8';this.removeAttribute('src')">
        <span class="badge badge-<?= e($b['type']) ?>"><?= e(ucfirst($b['type'])) ?></span>
        <?php if (($b['statut'] ?? '') === 'loue'): ?>
            <span class="badge badge-loue">Loué</span>
        <?php endif; ?>
    </a>
    <div class="bien-card-body">
        <h3><a href="<?= e(url('biens/' . $b['id'])) ?>"><?= e($b['titre']) ?></a></h3>
        <p class="bien-meta"><?= e($b['ville']) ?> · <?= (int) $b['chambres'] ?> ch.
            <?= !empty($b['surface']) ? '· ' . (int) $b['surface'] . ' m²' : '' ?></p>
        <div class="bien-card-foot">
            <span class="prix"><?= e(format_prix($b['prix'])) ?><small>/mois</small></span>
            <?php if (\App\Core\Auth::check()): ?>
                <form method="post" action="<?= e(url('favoris/' . $b['id'])) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="retour" value="biens">
                    <button class="fav-btn <?= $estFav ? 'is-fav' : '' ?>" type="submit"
                            aria-label="<?= $estFav ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>">♥</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</article>
