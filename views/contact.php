<section class="page-head"><div class="container">
    <p class="eyebrow">Contact</p><h1>Nous écrire</h1>
</div></section>
<section class="section"><div class="container auth-card reveal">
    <form method="post" action="<?= e(url('contact')) ?>">
        <?= csrf_field() ?>
            <p class="form-legend"><span class="req">*</span> Champs obligatoires</p>
        <div class="field"><label for="c-nom">Nom <span class="req">*</span></label>
            <input id="c-nom" name="nom" value="<?= old('nom') ?>" required></div>
        <div class="field"><label for="c-email">Email <span class="req">*</span></label>
            <input id="c-email" type="email" name="email" value="<?= old('email') ?>" required></div>
        <div class="field"><label for="c-msg">Message <span class="req">*</span></label>
            <textarea id="c-msg" name="message" rows="5" required><?= old('message') ?></textarea></div>
        <button class="btn btn-primary btn-block" type="submit">Envoyer</button>
    </form>
</div></section>
