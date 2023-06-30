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

    // расчет величины округления
    $round = $fData['eps'] < 1 ? strlen($fData['eps']) : 4;

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
                $simplex[$i]["x" . ($key + 1)] = round($coord,$round);
            }
            elseif(($key + 1) == $i)
                $simplex[$i]["x" . ($key + 1)] = round($coord + $delta2,$round);
            else
                $simplex[$i]["x" . ($key + 1)] = round($coord + $delta1,$round);
        }
    }

    $flag = true;
    $iteration = 0;
    $maxIteration = 1000;
    $min = 0;
    $lastSigma = 0;
    $sigma = 0;

    // создание pdf
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML('<h1 style="text-align: center;">Отчет о расчете методом Нелдера-Мида</h1>');
    $mpdf->WriteHTML('<span><b>Функция: </b>'.$fData['func'].'</span><br/><span>Начальная точка: ['.implode(', ', $fData['coord']).']</span>');

    while($flag && $iteration < $maxIteration) {
        $text = '<h4>Итерация '.$iteration.'</h4><b>Симплекс: </b><br/>';

        $iteration++;

        // шаг 2   -  просчет значений всех функций
        $fResult = [];

        foreach($simplex as $key => $t) {
            $res = $fResult[] = round(expression($t, $funcFormat),$round);
            $text .= "F" . ($key + 1) . "[" . implode(', ', $t) . "]   =   " . $res . "<br/>";
        }
        

        // переопредление точек
        $indexMin = array_keys($fResult, min($fResult))[0];

        $tmpfResult = $fResult;
        unset($tmpfResult[$indexMin]);  
        
        $indexMax = array_keys($tmpfResult, max($tmpfResult))[0];
        unset($tmpfResult[$indexMax]);

        $xl = $simplex[$indexMin];
        $xh = $simplex[$indexMax];
        $xg = $simplex;

        unset($xg[$indexMin]);
        unset($xg[$indexMax]);

        // шаг 3  - расчет центра тяжести - без xh
        $xc = [];

        foreach($xl as $key => $coord) {
            $xc[$key] = round($coord / $n,$round);
        }

        $text .= '<br/>Переодределяем точки:<br/>xl: [' . implode(', ', $xl) . ']<br/>xh: [' . implode(', ', $xh) . ']';

        foreach($xg as $keyxi => $xi) {
            $text .= '<br/>xg' . $keyxi . ': [' . implode(', ', $xi) . ']';
            foreach($xi as $key => $coord) {
                $xc[$key] += round($coord / $n,$round);
            }
        }

        // расчет значения fc
        $fc = round(expression($xc, $funcFormat),$round);
        
        $text .= '<br/><br/>Расчитываем xc: [' . implode(', ', $xc) . ']  =  ' . $fc;
        // шаг 4 - поиск пробной точки xr
        $xr = [];

        foreach($xc as $key => $coord) {
            $xr[$key] = (1 + $fData['alpha']) * $coord - $fData['alpha'] * $xh[$key];
        }
        $fr = round(expression($xr, $funcFormat),$round);

        $text .= '<br/><br/>Расчитываем xr: [' . implode(', ', $xr) . ']   =  ' . $fr;

        // шаг 5  -  сравнение fr и fl
        if($fr < $fResult[$indexMin]) {
            // шаг 5.1  - расчет xe
            $xe = [];

            $text .= '<br/>fr < fl ('.$fr ." < ".$fResult[$indexMin].')   =>   поиск xe';

            foreach($xr as $key => $coord) {
                $xe[$key] = round(($fData['gamma'] * $coord) + ((1 - $fData['gamma']) * $xc[$key]),$round);
            }
            $fe = round(expression($xe, $funcFormat),$round);

            $text .= '<br/><br/>Расчитываем xe: [' . implode(', ', $xe) . ']   =  ' . $fe;

            if($fe < $fr) {
                $text .= '<br/>fe < fr ('.$fe ." < ".$fr.')   =>   замена xh на xe';
                $simplex[$indexMax] = $xe;
            } else {
                $text .= '<br/>fe >= fr ('.$fe ." >= ".$fr.')   =>   замена xh на xr';
                $simplex[$indexMax] = $xr;
            }

        } elseif($fr >= $fResult[$indexMin] && $fr <= max($tmpfResult)) {
            // шаг 5.2  - замена xh на xr
            $text .= '<br/>fr >= fl и fr <= max(fg)   =>   замена xh на xr';
            $simplex[$indexMax] = $xr;
        } else {
            // шаг 6 - сжатие многогранника
            $xs = [];
            $text .= '<br/><u>Шаг сжатия многогранника:</u>';

            if($fResult[$indexMax] <= $fr) {
                $text .= '<br/>fh <= fr   =>   вычисляем xs от xc и xh';
                // вычисляем xs от xc и xh
                foreach($xh as $key => $coord) {
                    $xs[$key] = round(($fData['betta'] * $coord) - ((1 - $fData['betta']) * $xc[$key]),$round);
                }
            } else {
                $text .= '<br/>fh > fr   =>   вычисляем xs от xc и xr';
                // вычисляем xs от xc и xr
                foreach($xr as $key => $coord) {
                    $xs[$key] = round(($fData['betta'] * $coord) - ((1 - $fData['betta']) * $xc[$key]),$round);
                }
            }
            
            $fs = round(expression($xs, $funcFormat),$round);            
            $text .= '<br/>Расчитываем xs: [' . implode(', ', $xs) . ']   =  ' . $fs;

            // шаг 6 - сравнение fs и fr, fh
            if($fs < min($fr, $fResult[$indexMax])) {
                $simplex[$indexMax] = $xs;
            } elseif($fs >= $fResult[$indexMax]) {
                $text .= '<br/>fs >= fh   =>   редукция симплекса<br/>';
                // шаг 8 - редукция симплекса
                foreach($simplex as $i => $xi) {
                    if($i == $indexMin) continue;

                    foreach($xi as $key => $coord) {
                        $xi[$key] = round($xl[$key] + 0.5 * ($xi[$key] - $xl[$key]),$round);
                    }

                    $simplex[$i] = $xi;
                }
                $iteration--;
                continue;
            }
        }

        // проверка критерия останова
        $lastSigma = $sigma;
        $sigma = 0;

        foreach($simplex as $i => $xi) {
            $sigma += pow(($fResult[$i] - ((1 / ($n+1)) * $fc)), 2);
        }
        $sigma = sqrt((1 / ($n+1)) * $sigma);

        if($sigma <= (float)$fData['eps'] || $lastSigma == $sigma){
            $flag = false;
            $min = $simplex[$indexMax];
        }

        $text .= '<br/><b>Проверка критерия останова:</b> sigma = ' . $sigma . " <= " . $fData['eps'].' - ' . ($flag ? 'неверно ' : 'верно, конец расчета');

        $mpdf->WriteHTML($text);
        
    }

    $text = $min ? '<b style="color: #4caf50">' . implode(', ', $min) . "</b>" : '<b style="color: #f00"> НЕ НАЙДЕНА </b>';
    $funcVal = $min ? round(expression($min, $funcFormat),$round) : '--';

    $mpdf->WriteHTML('-----------------------------<br><b>Конец расчета</b><br/>Найденная точка: '. $text . '<br/>Значение функции: ' . $funcVal);

    $fileName = $fData['fileName'] ?: uniqid();
    $mpdf->Output($_SERVER['DOCUMENT_ROOT'] . "/upload/reports/".$fileName.".pdf", \Mpdf\Output\Destination::FILE);
    
    return [
        'file' => "/upload/reports/".$fileName.".pdf",
        'name' => $fileName.".pdf"
    ];
}

function expression($vector, $formula) {
    for($i = count($vector); $i > 0; $i--) {
        $formula = str_replace("x$i", "(".$vector["x$i"].")", $formula);
    }

    return eval('return '.$formula.';');
}
