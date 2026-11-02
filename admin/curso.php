<?php
include __DIR__ . '/../include/config.inc.php';
/* include_once $arrConfig['dir_include'] . 'auth.inc.php'; */
include_once __DIR__ . '/dashboards/layout.dash.php';

if(!isset($_SESSION['id'])){
    header('Location: ../pages/auth/login.php');
    exit;
}
if(isset($_SESSION['cargo'])) {
    if(strtolower($_SESSION['cargo']) != 'professor') {
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: ../pages/auth/login.php');
    exit;

}

$items = gerar_items_navbar($_SESSION['id']);
?>
<?php include __DIR__ . '/../header.php'; ?>
<header>
    <?php
    navbar($items); ?>
</header>
<body>

<?php curso(); ?>
</body>
</html>
