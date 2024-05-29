<?php

include 'include/config.inc.php';

$csv = array();

// check if the file was uploaded without errors
if(isset($_FILES['csv']) && $_FILES['csv']['error'] == 0){
    $name = $_FILES['csv']['name'];
    $type = $_FILES['csv']['type'];
    $tmpName = $_FILES['csv']['tmp_name'];

    // get file extension using pathinfo
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    // check the file is a csv
    if($ext === 'csv'){
        if(($handle = fopen($tmpName, 'r')) !== FALSE) {
            // necessary if a large csv file
            set_time_limit(0);

            $row = 0;

            // Read the header row
            $header = fgetcsv($handle, 1000, ';');

            while(($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                // check if row is not empty and has at least 2 columns
                if (is_array($data) && count($data) >= 2) {
                    // get the values from the csv and trim whitespace
                    $csv[$row]['email'] = trim($data[0]);
                    $csv[$row]['cargo'] = trim($data[1]);

                    // inc the row
                    $row++;
                }
            }
            fclose($handle);
        }
    } else {
        echo "O arquivo deve ser um CSV.";
    }
} else {
    echo "Erro ao enviar o arquivo.";
}

// Print the CSV data

foreach($csv as $key => $value){
    if(validaEmail($key['email'])) {
        

    } 
}

pr($csv);

function validaEmail($email) {
    $conta = "/^[a-zA-Z0-9\._-]+@";
    $domino = "[a-zA-Z0-9\._-]+.";
    $extensao = "([a-zA-Z]{2,4})$/";
    $pattern = $conta.$domino.$extensao;
    if (preg_match($pattern, $email, $check))
      return true;
    else
      return false;
}

die;

