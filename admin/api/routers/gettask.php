<?php

function route($data) {
    
    if ($data['method'] === 'POST') {
        echo getInfoFromFile();
        exit;
    }
}

function getInfoFromFile() {
    if($_FILES['addTask']['type'] == "application/json"){
        return file_get_contents($_FILES['addTask']['tmp_name']);
    } else {
        \Helpers\query\throwHttpError('file not correct type', 'file not correct type');
    }
}