<?php
include '../include/config.inc.php';
include_once $arrConfig['dir_include'] . 'auth.inc.php';
include_once 'dashboards/layout.dash.php';
$items = gerar_items_navbar($_SESSION['id']);
?>
<?php include '../header.php'; ?>
<header>
    <?php
    navbar($items); ?>
</header>
<body>
<?php curso(); ?>
</body>
</html>
