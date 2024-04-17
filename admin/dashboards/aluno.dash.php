<?php
include '../include/config.inc.php';
$sql = "SELECT * FROM rel_turma_user WHERE id_user = " . $_SESSION['id']  . " AND ativo = 1";
$res = my_query($sql);
$_SESSION['cargo'] = 'professor';
?>
<ul>
    <?php
    foreach($res as $k => $v){
        $sql = "SELECT * FROM turma WHERE id = " . $v['id_turma'];
        $res2 = my_query($sql);
        foreach($res2 as $k2 => $v2) {
            echo '<li><a href="' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $v['id_turma'] . '">' . $v2['nome_turma'] . '</a></li>';  
        }
    }
    ?>
</ul>


