<section class="section">
    <div class="container auth-card reveal">
        <p class="eyebrow">Inscription</p>
        <h1>Créer un compte</h1>
        <form method="post" action="<?= e(url('inscription')) ?>">
            <?= csrf_field() ?>
            <p class="form-legend"><span class="req">*</span> Champs obligatoires</p>
            <div class="field-row">
                <div class="field">
                    <label for="prenom">Prénom <span class="req">*</span></label>
                    <input id="prenom" name="prenom" value="<?= old('prenom') ?>" required>
                </div>
                <div class="field">
                    <label for="nom">Nom <span class="req">*</span></label>
                    <input id="nom" name="nom" value="<?= old('nom') ?>" required>
                </div>
            </div>
            <div class="field">
                <label for="email">Email <span class="req">*</span></label>
                <input id="email" type="email" name="email" value="<?= old('email') ?>" required>
            </div>
            <div class="field">
                <label for="telephone">Téléphone <span class="opt">(optionnel)</span></label>
                <input id="telephone" name="telephone" value="<?= old('telephone') ?>">
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="password">Mot de passe <span class="req">*</span></label>
                    <input id="password" type="password" name="password" required minlength="8">
                </div>
                <div class="field">
                    <label for="password_confirm">Confirmation <span class="req">*</span></label>
                    <input id="password_confirm" type="password" name="password_confirm" required>
                </div>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Créer mon compte</button>
        </form>
        <p class="auth-alt">Déjà inscrit ? <a href="<?= e(url('connexion')) ?>">Se connecter</a></p>
    </div>
</section>
