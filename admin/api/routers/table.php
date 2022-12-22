<?php

// Роутинг, основная функция
function route($data) {

    // POST /table  method delete
    if ($data['method'] === 'POST' && $data['formData']['method'] == 'delete') {
        echo json_encode(deleteNodes($data['formData']));
        exit;
    }

    // POST /table  method insert
    if ($data['method'] === 'POST' && $data['formData']['method'] == 'insert') {
        echo json_encode(insertNode($data['formData']));
        exit;
    }

    // POST /table  method update
    if ($data['method'] === 'POST' && $data['formData']['method'] == 'update') {
        echo json_encode(updateNode($data['formData']));
        exit;
    }

    // Если ни один роутер не отработал
    \Helpers\query\throwHttpError('invalid_parameters', 'invalid parameters');

}

function deleteNodes($fData) {
    try{
        $pdo = \Helpers\query\connectDB();
    } catch (PDOException $e) {
        \Helpers\query\throwHttpError('database error connect', $e->getMessage());
        exit;
    }

    $fData['ids'] = explode(',', $fData['ids']);

    foreach($fData['ids'] as $id) {
        if (!\Helpers\query\isExistsNodeById($pdo, $id, $fData['identityField'], $fData['table'])) {
            \Helpers\query\throwHttpError('node not exists', 'Запись с id не найдена', '404 node not exists');
            exit;
        }

        try {
            $query = 'DELETE FROM '.$fData['table'].' WHERE '.$fData['identityField'].'=:id';
    
            $data = $pdo->prepare($query);        
            $data->execute([
                'id' => $id
            ]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            \Helpers\query\throwHttpError('query error', $e->getMessage(), '400 error node delete');
            exit;
        }
    }

    return array(
        'ids' => $fData['ids']
    );
}

function insertNode($fData) {
    try{
        $pdo = \Helpers\query\connectDB();
    } catch (PDOException $e) {
        \Helpers\query\throwHttpError('database error connect', $e->getMessage());
        exit;
    }

    $arTechFields = ['method', 'table', 'identityField'];
    $fields = '';

    foreach($fData as $key => $val){
        if(!in_array($key, $arTechFields)) {
            $vals[$key] = $val;
        }
    }

    if($fData['table'] == '"user"' && !$vals['user_password']){
        $vals['user_password'] = md5(uniqid());
    }

    $fields = implode(', ', array_keys($vals));

    $query = 'INSERT INTO '. $fData['table'] . "(" . implode(', ', array_keys($vals)) . ') VALUES ('. implode(', ', array_map(function($column) {
                        return ':' . $column;
                    }, array_keys($vals))) . ")";
                    
    $data = $pdo->prepare($query);
    $data->execute($vals);

    // Новый айдишник для добавленного поста
    $newId = (int)$pdo->lastInsertId();

    return array(
        'id' => $newId
    );

}

function updateNode($fData) {
    try{
        $pdo = \Helpers\query\connectDB();
    } catch (PDOException $e) {
        \Helpers\query\throwHttpError('database error connect', $e->getMessage());
        exit;
    }

    if (!\Helpers\query\isExistsNodeById($pdo, $fData['nodeId'], $fData['identityField'], $fData['table'])) {
        \Helpers\query\throwHttpError('node not exists', 'Запись с id не найдена', '404 node not exists');
        exit;
    }

    $arTechFields = ['method', 'table', 'identityField', 'nodeId'];
    $fields = '';

    foreach($fData as $key => $val){
        if(!in_array($key, $arTechFields) && $val && $val != "NULL") {
            $vals[$key] = $val;
        }
    }

    $setParams = implode(', ', array_map(function($column) {
        return $column . ' = :' . $column;
    }, array_keys($vals)));


    $query = 'UPDATE ' . $fData['table'] . ' SET ' . $setParams . ' WHERE ' . $fData['identityField'] . " = " . $fData['nodeId'];
    
    $data = $pdo->prepare($query);
    $data->execute($vals);

    return array(
        'id' => $fData['nodeId']
    );
}