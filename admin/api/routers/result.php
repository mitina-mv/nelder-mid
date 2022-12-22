<?php

function route($data) {
    
    if ($data['method'] === 'POST') {
        echo json_encode(getResult($data['formData']));
        exit;
    }
}

function getResult($fData) {
    // замена ^ на ** - корректный возведение в степень
    $funcFormat = str_replace("^", "**", $fData['func']);

    // шаг 1  -  расчет симплекса
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
    $maxIteration = 1000;

    while($flag && $iteration < $maxIteration) {
        $iteration++;

        // шаг 2   -  просчет значений всех функций
        $fResult = [];

        foreach($simplex as $key => $t) {
            try {
                $compiler = new FormulaInterpreter\Compiler();
                $executable = $compiler->compile($fData['func']);
                $result = $executable->run($t);
            } catch (\Exception $e) {
                echo $e->getMessage(), "\n";
            }
            $fResult[] = $result;
        }

        // переопредление точек
        $indexMin = array_keys($fResult, min($fResult))[0];
        $indexMax = array_keys($fResult, max($fResult))[0];

        $xl = $simplex[$indexMin];
        $xh = $simplex[$indexMax];
        $xg = $simplex;

        unset($xg[$indexMin]);
        unset($xg[$indexMax]);

        // шаг 3  - расчет центра тяжести - без xh
        $xc = [];

        foreach($xl as $key => $coord) {
            $xc[$key] = $coord / $n;
        }

        foreach($xg as $xi) {
            foreach($xi as $key => $coord) {
                $xc[$key] += $coord / $n;
            }
        }
        // расчет значения fc
        $fc = $executable->run($xc);

        // шаг 4 - поиск пробной точки xr
        $xr = [];

        foreach($xc as $key => $coord) {
            $xr[$key] = (1 + $fData['alpha']) * $coord - $fData['alpha'] * $xh[$key];
        }
        $fr = $executable->run($xr);

        // шаг 5  -  сравнение fr и fl
        $tmpfResult = $fResult;
        unset($tmpFResult[$indexMin]); 
        unset($tmpFResult[$indexMax]); 

        if($fr < $fResult[$indexMin]) {
            // шаг 5.1  - расчет xe
            $xe = [];

            foreach($xr as $key => $coord) {
                $xe[$key] = ($fData['gamma'] * $coord) - ((1 - $fData['gamma']) * $xc[$key]);
            }
            $fe = $executable->run($xe);

            if($fe < $fr) {
                $simplex[$indexMax] = $xe;
            } else {
                $simplex[$indexMax] = $xr;
            }

        } elseif($fr >= $fResult[$indexMin] || $fr <= max($tmpfResult)) {
            // шаг 5.2  - замена xh на xr
            $simplex[$indexMax] = $xr;
        } else {
            // шаг 6 - сжатие многогранника
            $xs = [];

            if($fResult[$indexMax] <= $fr) {
                // вычисляем xs от xc и xh
                foreach($xh as $key => $coord) {
                    $xs[$key] = ($fData['betta'] * $coord) - ((1 - $fData['betta']) * $xc[$key]);
                }
            } else {
                // вычисляем xs от xc и xr
                foreach($xr as $key => $coord) {
                    $xs[$key] = ($fData['betta'] * $coord) - ((1 - $fData['betta']) * $xc[$key]);
                }
            }
            
            $fs = $executable->run($xs);

            // шаг 6 - сравнение fs и fr, fh
            if($fs < min($fr, $fResult[$indexMax])) {
                $simplex[$indexMax] = $xs;
            } elseif($fs >= $fResult[$indexMax]) {
                // шаг 8 - редукция симплекса
                foreach($simplex as $i => $xi) {
                    if($i == $indexMin) continue;

                    foreach($xi as $key => $coord) {
                        $xi[$key] = $xl[$key] + 0.5 * ($xi[$key] - $xl[$key]);
                    }

                    $simplex[$i] = $xi;
                }
            }
        }

        // проверка критерия останова
        // print_r($fResult);
        // print_r($fc);
        $fx = $executable->run($simplex[$indexMax]);
        print_r($fx);
        // $sigma = 0;
        // foreach($simplex as $i => $xi) {
        //     $sigma += pow(($fResult[$i] - $fc), 2);
        //     echo "   ".($fResult[$i] - $fc)."<br>";
        // }
        // $sigma = sqrt((1 / ($n+1)) * $sigma);
        // echo "sigma = ".$sigma."  ";

        if($fx <= (float)$fData['epx']){
            $flag = false;
        }

        echo "iteration " . ($iteration - 1);
        print_r($simplex);
    }
    
    
    
}