<?php 
include '../include/config.inc.php';
echo 'aqui';
pr($_GET);

$id = $_GET['id_user'];

$sql = "DELETE FROM conf_convite WHERE id = $id";
my_query($sql);

header ('Location: ' . $arrConfig['url_admin'] . 'curso.php');

