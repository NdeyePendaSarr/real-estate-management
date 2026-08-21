<?php /** @var array $biens */ ?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-text">
            <p class="eyebrow">Location à Dakar</p>
            <h1>Trouvez le bien qui vous <em>ressemble</em>.</h1>
            <p class="lead">Appartements et villas sélectionnés, réservation en ligne, accompagnement de A à Z.</p>
            <form class="hero-search" method="get" action="<?= e(url('biens')) ?>">
                <div class="field">
                    <label for="h-type">Type</label>
                    <select id="h-type" name="type">
                        <option value="">Tous</option>
                        <option value="appartement">Appartement</option>
                        <option value="villa">Villa</option>
                    </select>
                </div>
                <div class="field">
                    <label for="h-ville">Ville</label>
                    <input id="h-ville" name="ville" placeholder="Dakar, Mbour…">
                </div>
                <div class="field">
                    <label for="h-prix">Budget max</label>
                    <input id="h-prix" name="prix_max" type="number" min="0" step="50000" placeholder="FCFA">
                </div>
                <button class="btn btn-primary" type="submit">Rechercher</button>
            </form>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <div>
                <p class="eyebrow">Sélection</p>
                <h2>Nos biens disponibles</h2>
            </div>
            <a class="btn btn-outline" href="<?= e(url('biens')) ?>">Voir tout</a>
        </div>
        <?php if (empty($biens)): ?>
            <p class="muted">Aucun bien disponible pour le moment.</p>
        <?php else: ?>
            <div class="grid-biens stagger">
                <?php foreach ($biens as $b): ?>
                    <?php include __DIR__ . '/partials/bien-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section band">
    <div class="container trio stagger">
        <div class="trio-item">
            <span class="trio-ico">🔒</span>
            <h3>Réservation sécurisée</h3>
            <p>Vos données et vos paiements sont protégés à chaque étape.</p>
        </div>
        <div class="trio-item">
            <span class="trio-ico">🏡</span>
            <h3>Biens vérifiés</h3>
            <p>Chaque annonce est contrôlée par nos commerciaux avant publication.</p>
        </div>
        <div class="trio-item">
            <span class="trio-ico">🤝</span>
            <h3>Accompagnement</h3>
            <p>Une équipe disponible du premier contact à la remise des clés.</p>
        </div>
    </div>
</section>
