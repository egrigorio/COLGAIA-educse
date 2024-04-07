<?php
include '../include/config.inc.php';
if(isset($_POST['theme'])) {
    $_SESSION['theme'] = $_POST['theme'];
}