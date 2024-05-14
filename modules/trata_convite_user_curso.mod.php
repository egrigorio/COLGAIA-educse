<?php
include '../include/config.inc.php';



$sql = "SELECT users.id, conf_convite.id_curso, conf_convite.cargo FROM users 
INNER JOIN conf_convite ON users.email = conf_convite.email 
WHERE conf_convite.id = " . $_GET['convite'];



$res = my_query($sql);
if(count($res) == 0) {
    $_SESSION['convite_aceite'] = true;
    header('Location:' . $arrConfig['url_admin'] . 'turma.php');
    exit;
}
pr($res);
$id_user = $res[0]['id'];
$id_curso = $res[0]['id_curso'];
$cargo = $res[0]['cargo'];

$sql = "UPDATE rel_user_curso SET estado = '1' WHERE id_user = $id_user AND id_curso = $id_curso";
$res = my_query($sql);

$sql = "DELETE FROM conf_convite WHERE id = " . $_GET['convite'];
$res = my_query($sql);

$_SESSION['convite_aceite'] = true;
header('Location:' . $arrConfig['url_admin'] . 'turma.php');
