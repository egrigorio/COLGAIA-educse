<?php 
include '../include/config.inc.php';

$_SESSION['tipoLog'] = 'Inserção de registo na tabela ' . $_GET['modulo'];

$strCampos = '';
$strValores = '';
$arrCampos = array();
$modulo = $_GET['modulo'];
def_arrCampos($_GET['modulo'], $arrCampos);

foreach($arrCampos as $kCampos => $vCampos) {      
    if(isset($vCampos['inserir']) && $vCampos['inserir']) {
        if(isset($vCampos['unique']) && $vCampos['unique']) {
            $query = "SELECT * FROM $modulo WHERE $kCampos = '$_POST[$kCampos]'";
            $res = my_query($query);
            if(count($res) > 0) {
                $_SESSION['tipoLog'] = 'Erro ao inserir registo na tabela ' . $_GET['modulo'] . ' - Campo ' . $kCampos . ' já existe';
                echo 'Erro ao inserir registo na tabela ' . $_GET['modulo'] . ' - Campo ' . $kCampos . ' já existe';
                /* header('Location: ' . $arrConfig['url_site'] . '/admin/paginas/' . $modulo . '.adm.php'); */
                exit;
            }
        }        
      
        if($vCampos['tipo'] == 'checkbox') {
            // Tratar excepções dos campos (checkbox)
            $strCampos .= $kCampos . ', ';
            $estado = ( isset($_POST[$kCampos]) ? 1 : 0 );
            $strValores .= "'$estado', ";
          } else if($vCampos['tipo'] == 'img') {
            // Tratar excepções dos campos (img)
            $strCampos .= $kCampos . ', ';
            $strValores .= "'{$_FILES[$kCampos]['name']}', ";   
            //validar se a imagem já existe
            if (file_exists($arrConfig['dir_img'] . $_FILES[$kCampos]['name'])) {
              $_SESSION['tipoLog'] = 'Erro ao inserir imagem de ' . $_GET['modulo'] . ' - Imagem já existe';
              header('Location: ' . $arrConfig['url_site'] . '/admin/paginas/' . $modulo . '.adm.php');
              exit;
            }                   
            move_uploaded_file($_FILES[$kCampos]['tmp_name'], $arrConfig['dir_img'] . $_FILES[$kCampos]['name']);
          
        }else {
          // Campos genéricos
          $strCampos .= $kCampos . ', ';
          $strValores .= "'$_POST[$kCampos]', ";
        }

    }
};
$strCampos = substr($strCampos, 0, strlen($strCampos)-2);
$strValores = substr($strValores, 0, strlen($strValores)-2);

$query = "INSERT INTO $modulo ($strCampos) VALUES ($strValores)";

my_query($query);

$redirecionamento = definir_redirecionamento($modulo);

header('Location: ' . $arrConfig['url_site'] . $redirecionamento);



