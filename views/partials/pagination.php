<?php
/** @var int $page @var int $pages @var array $filtres */
if (($pages ?? 1) < 2) return;
$qs = array_filter($filtres ?? []);
?>
<nav class="pagination" aria-label="Pagination">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
        <?php $qs['page'] = $p; ?>
        <a href="<?= e(url('biens?' . http_build_query($qs))) ?>"
           class="page <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</nav>
