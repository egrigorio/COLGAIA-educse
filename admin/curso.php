<?php
include '../include/config.inc.php';
include_once $arrConfig['dir_include'] . 'auth.inc.php';
include_once 'dashboards/layout.dash.php';

$items = gerar_items_navbar($_SESSION['id']);


?>

<!DOCTYPE html>
<html data-theme="<?php echo isset($_SESSION['theme']) ? $_SESSION['theme'] : 'default'; ?>" class="bg-primary" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>                
    <link rel="stylesheet" href="../public/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="<?php echo $arrConfig['url_site'] . '/public/select2_override.css' ?>">
</head>
<header>
    <?php navbar($items); ?>
</header>
<body>

<?php curso(); ?>

</body>
</html>
