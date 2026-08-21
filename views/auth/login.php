<section class="section">
    <div class="container auth-card reveal">
        <p class="eyebrow">Connexion</p>
        <h1>Se connecter</h1>
        <form method="post" action="<?= e(url('connexion')) ?>">
            <?= csrf_field() ?>
            <p class="form-legend"><span class="req">*</span> Champs obligatoires</p>
            <div class="field">
                <label for="email">Email <span class="req">*</span></label>
                <input id="email" type="email" name="email" value="<?= old('email') ?>" required autofocus>
            </div>
            <div class="field">
                <label for="password">Mot de passe <span class="req">*</span></label>
                <input id="password" type="password" name="password" required>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Connexion</button>
        </form>
        <p class="auth-alt">Pas encore de compte ? <a href="<?= e(url('inscription')) ?>">Créer un compte</a></p>
    </div>
</section>
