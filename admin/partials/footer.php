<?php
$adminAssetVersion = '20260515-2';
$adminJs = asset('js/admin.js');
if (strpos($adminJs, '?') === false) {
    $adminJs .= '?v=' . $adminAssetVersion;
} else {
    $adminJs .= '&v=' . $adminAssetVersion;
}
?>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= e($adminJs) ?>" defer></script>
</body>
</html>
