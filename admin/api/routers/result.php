<?php

function route($data) {
    
    if ($data['method'] === 'POST') {
        echo json_encode(getResult($data['formData']));
        exit;
    }
}

function getResult($fData) {
    // замена ^ на ** - корректный возведение в степень
    // $funcFormat = str_replace("^", "**", $fData['func']);
    // echo $funcFormat;

    // расчет симплекса
    $n = $fData['countx'];
    $l = $fData['lambda'] ?: 1;
    $delta1 = ((sqrt($n + 1) + $n - 1) / ($n * sqrt(2))) * $l;
    $delta2 = ((sqrt($n + 1) - 1) / ($n * sqrt(2))) * $l;

    foreach($fData['coord'] as $key => $coord) {
        if(!$coord) $fData['coord'][$key] = 0;
    }

    $simplex = [];

    for($i = 0; $i <= $n; ++$i) {
        foreach($fData['coord'] as $key => $coord) {
            if($i == 0) {
                $simplex[$i]["x" . ($key + 1)] = $coord;
            }
            elseif(($key + 1) == $i)
                $simplex[$i]["x" . ($key + 1)] = $coord + $delta2;
            else
                $simplex[$i]["x" . ($key + 1)] = $coord + $delta1;
        }
    }

    print_r($simplex);

    $flag = true;
    $iteration = 0;
    // $maxIteration = 1000;
    $maxIteration = 1;

    

    while($flag && $iteration < $maxIteration) {
        $fResult = [];

        $xl = $simplex[0];        
        $xg = [];
        $xh = $simplex[0];

        $resXl = 0;
        $resXh = 0;

        foreach($simplex as $key => $t) {
            var_dump($t);
            try {
                $compiler = new FormulaInterpreter\Compiler();
                $executable = $compiler->compile($fData['func']);
                $result = $executable->run($t);
            } catch (\Exception $e) {
                echo $e->getMessage(), "\n";
            }
            $fResult[] = $result;
            // try {
            //     $parser = new FormulaParser($fData['func'], 3);
            //     $parser->setVariables($t);
            //     $result = $parser->getResult(); // [0 => 'done', 1 => 16.38]
            // } catch (\Exception $e) {
            //     echo $e->getMessage(), "\n";
            // }
            // $fResult[] = $result;

            // $funcFormatLocal = $funcFormat;

            // // подготовка формулы для вычисления
            // for($i = $n; $i > 0; $i--) {
            //     $funcFormatLocal = str_replace("x$i", $t[$i - 1], $funcFormatLocal);
            // }

            // print_r($funcFormatLocal);

            // $fResult[] = (int) $funcFormatLocal;
        }

        $iteration++;
    }
    
    print_r($fResult);
    
}