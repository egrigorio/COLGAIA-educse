<?php
include '../include/config.inc.php';
include '../header.php'; ?>
<body>
<?php 


include_once 'dashboards/layout.dash.php';
$arr_items = array();
navbar($arr_items);
if(!isset($_SESSION['id'])){
    header('Location: ../pages/auth/login.php');
    exit;
}
$id_dono_insituicao = $_SESSION['id'];

$sql = "SELECT * FROM instituicao WHERE id_dono = $id_dono_insituicao";
$arrResultado = my_query($sql);
$_SESSION['id_instituicao'] = $arrResultado[0]['id'];


instituicao($arrResultado);

?>    
</body>
</html>
