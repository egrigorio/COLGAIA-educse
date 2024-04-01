<?php
include '../include/config.inc.php';
include_once $arrConfig['dir_include'] . 'auth.inc.php';
include_once 'dashboards/layout.dash.php';

$items = gerar_items_navbar($_SESSION['id']);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>                
    <link rel="stylesheet" href="../public/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<header>
    <?php navbar($items); ?>
</header>
<body>

<?php curso(); ?>

</body>
</html>
