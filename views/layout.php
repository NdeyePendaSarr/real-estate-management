<?php
/** @var string $contenu */
/** @var string $titre */
use App\Core\Auth;
use App\Core\Flash;

$flash = Flash::pull();
$u = Auth::user();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titre ?? 'Agence Immobilière') ?></title>
    <meta name="description" content="Agence immobilière à Dakar : location d'appartements et de villas. Réservez votre bien en ligne.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap">
    <script>if(!matchMedia('(prefers-reduced-motion: reduce)').matches)document.documentElement.classList.add('js-anim');</script>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20100%20100'%3E%3Ctext%20y='.9em'%20font-size='90'%3E🏠%3C/text%3E%3C/svg%3E">
</head>
<body>
<a class="skip-link" href="#contenu">Aller au contenu</a>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= e(url('/')) ?>" class="brand">Agence<span>Immo</span></a>
        <nav class="main-nav" aria-label="Navigation principale">
            <a href="<?= e(url('/')) ?>">Accueil</a>
            <a href="<?= e(url('biens')) ?>">Nos biens</a>
            <a href="<?= e(url('services')) ?>">Services</a>
            <a href="<?= e(url('a-propos')) ?>">À propos</a>
            <a href="<?= e(url('contact')) ?>">Contact</a>
        </nav>
        <div class="header-actions">
            <?php if ($u): ?>
                <?php if (in_array($u['role'], ['commercial', 'admin'], true)): ?>
                    <a class="btn btn-ghost" href="<?= e(url('admin')) ?>">Espace pro</a>
                <?php else: ?>
                    <a class="btn btn-ghost" href="<?= e(url('compte')) ?>">Mon compte</a>
                <?php endif; ?>
                <form method="post" action="<?= e(url('deconnexion')) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline">Déconnexion</button>
                </form>
            <?php else: ?>
                <a class="btn btn-ghost" href="<?= e(url('connexion')) ?>">Connexion</a>
                <a class="btn btn-primary" href="<?= e(url('inscription')) ?>">Créer un compte</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if (!empty($flash)): ?>
    <div class="container flash-zone">
        <?php foreach ($flash as $type => $messages): ?>
            <?php foreach ($messages as $m): ?>
                <div class="flash flash-<?= e($type) ?>" role="status"><?= e($m) ?></div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<main id="contenu"><?= $contenu ?></main>

<footer class="site-footer">
    <div class="container footer-inner">
        <div>
            <a href="<?= e(url('/')) ?>" class="brand brand-light">Agence<span>Immo</span></a>
            <p>Location d'appartements et de villas à Dakar. Un accompagnement simple, du premier clic à la remise des clés.</p>
        </div>
        <div>
            <h4>Navigation</h4>
            <a href="<?= e(url('biens')) ?>">Nos biens</a>
            <a href="<?= e(url('services')) ?>">Services</a>
            <a href="<?= e(url('a-propos')) ?>">À propos</a>
        </div>
        <div>
            <h4>Contact</h4>
            <p>Dakar, Sénégal<br>contact@agenceimmo.example<br>+221 33 000 00 00</p>
        </div>
    </div>
    <div class="container footer-bottom">
        © <?= date('Y') ?> AgenceImmo · Développé par Ndeye Penda Sarr
    </div>
</footer>
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
