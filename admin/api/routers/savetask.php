<?php

function route($data) {
    
    if ($data['method'] === 'POST') {
        echo json_encode(saveFile($data['formData']));
        exit;
    }
}

function saveFile($fData) {
    unset($fData['fileName']);

    $json = json_encode($fData);
    $fileName = "task_".date('Y-m-d').uniqid().'.json';
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/upload/tasks/".$fileName, $json);

    return [
        'name' => $fileName,
        'file' => "/upload/tasks/".$fileName
    ];
}