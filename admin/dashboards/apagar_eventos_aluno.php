<?php
include '../../include/config.inc.php';
$sql = "DELETE FROM eventos_alunos_calendario WHERE id_user = {$_SESSION['id']}";
$res = my_query($sql);