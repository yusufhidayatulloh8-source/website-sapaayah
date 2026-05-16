<?php if (!empty($title) || !empty($subtitle)): ?>
<div class="section-head reveal">
    <?php if (!empty($title)): ?><h2><?= e($title) ?></h2><?php endif; ?>
    <?php if (!empty($subtitle)): ?><p><?= e($subtitle) ?></p><?php endif; ?>
</div>
<?php endif; ?>
