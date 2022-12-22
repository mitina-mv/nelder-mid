<?php

// Роутинг, основная функция
function route($data) {
    // POST /table  method delete
    if ($data['method'] === 'POST' && $data['formData']['doc'] == 'report1') {
        echo json_encode(madeReportByTestlog($data['formData']));
        exit;
    }
}

function madeReportByTestlog($fData) {
    try{
        $pdo = \Helpers\query\connectDB();
    } catch (PDOException $e) {
        \Helpers\query\throwHttpError('error connect db', $e->getMessage());
    }
    
    try{
        $query = 'SELECT * FROM testlog WHERE user_id=:student AND testlog_date=:dateTest AND test_id=:test';
        $data = $pdo->prepare($query);
        $data->execute([
            'student' => $fData['student'],
            'dateTest' => $fData['dateTest'],
            'test' => $fData['test'],
        ]);
        $testlog = $data->fetchAll();

        if(count($testlog) > 1){
            throw new \Exception('Записей в журнале тестирования на этот день больше одной!');
        }
        if(count($testlog) == 0){
            throw new \Exception('Записи с такими параметрами не найдена.');
        }
    } catch (\Exception $e){
        \Helpers\query\throwHttpError('data not found', $e->getMessage());
        exit;
    } catch (PDOException $e) {
        \Helpers\query\throwHttpError('data not found', $e->getMessage());
        exit;
    }

    if($testlog) {
        $testlog = $testlog[0];

        $mpdf = new \Mpdf\Mpdf();

        $mpdf->WriteHTML('<h1 style="text-align: center;">Отчет</h1>');
        $mpdf->WriteHTML('<h3 style="text-align: center;">о прохождении тестирования</h3>');

        try{
            // запрос данных о студенте
            $query = 'SELECT u.user_lastname, u.user_firstname, u.user_patronymic, st.studgroup_name 
                FROM "user" as u
                JOIN studgroup as st
                ON u.studgroup_id = st.studgroup_id
                WHERE u.user_id = ?';

            $data = $pdo->prepare($query);
            $data->execute([$fData['student']]);
            $student = $data->fetchAll(PDO::FETCH_ASSOC)[0];
        } catch (PDOException $e) {
            \Helpers\query\throwHttpError('data not found', $e->getMessage());
            exit;
        }

        try{
            // запрос данных о теста
            $query = 'SELECT te.test_name, d.discipline_name, te.test_settings
                FROM test as te
                JOIN discipline as d
                ON d.discipline_id = te.discipline_id
                AND te.test_id = ?';

            $data = $pdo->prepare($query);
            $data->execute([$fData['test']]);
            $test = $data->fetchAll(PDO::FETCH_ASSOC)[0];
    
        } catch (PDOException $e) {
            \Helpers\query\throwHttpError('data not found', $e->getMessage());
            exit;
        }

        $studentName = $student['user_lastname'] . " " . $student['user_firstname'] . " " . $student['user_patronymic'];

        $statusTest = $testlog['testlog_mark'] ? "пройден, ".$testlog['testlog_mark']."/100" : "не пройден";
        $testSettings = json_decode($test['test_settings'], true);

        $mpdf->WriteHTML('<span><b>Студент:</b> ' . $studentName . '</span>');
        $mpdf->WriteHTML('<span><b>Тест:</b> ' . $test['test_name'] . '</span>');
        $mpdf->WriteHTML('<span><b>Группа:</b> ' . $student['studgroup_name'] . '</span>');
        $mpdf->WriteHTML('<span><b>Дата сдачи:</b> ' . $testlog['testlog_date'] . '</span>');
        $mpdf->WriteHTML('<span><b>Статус прохождения:</b> ' . $statusTest . '</span>');
        $mpdf->WriteHTML('<span><b>Количество вопросов:</b> ' . $testSettings['question_count'] . '</span>');
        $mpdf->WriteHTML('<span><b>Дисциплина:</b> ' . $test['discipline_name'] . '</span>');

        try{
            // запрос данных о теста
            $query = 'SELECT q.question_text, al.answerlog_mark, an.answer_text as answerok, anget.answer_text as answerget
                FROM answerlog as al
                JOIN question as q
                ON q.question_id = al.question_id
                and al.testlog_id = ?
                JOIN answer as an
                ON an.question_id = q.question_id
                AND an.answer_status = true
                LEFT JOIN (
                    SELECT an2.answer_text, fix2.answerlog_id
                    FROM answer as an2
                    JOIN fixanswer as fix2
                    ON fix2.answer_id = an2.answer_id
                ) as anget
                ON anget.answerlog_id = al.answerlog_id';

            $data = $pdo->prepare($query);
            $data->execute([$testlog['testlog_id']]);
            $questions = $data->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            \Helpers\query\throwHttpError('data not found', $e->getMessage());
            exit;
        }

        $num = 1;

        foreach($questions as $question) {
            $mpdf->WriteHTML('<div style="margin: 20px 0 10px;"><b>Вопрос '.$num.': </b> ' . $question['question_text'] . '</div>');

            $answer = $question['answerget'] ?: '--';
            $mpdf->WriteHTML('<span>Выбранный ответ: ' . $answer . '</span>');
            $mpdf->WriteHTML('<span>Правильный ответ: ' . $question['answerok'] . '</span>');
            $mpdf->WriteHTML('<span>Оценка ответа: ' . $question['answerlog_mark'] . '</span>');

            $num++;
        }
    }

        $mpdf->Output($_SERVER['DOCUMENT_ROOT'] . "/upload/reports/user".$fData['curUser']."_report1_student".$fData['student'].".pdf", \Mpdf\Output\Destination::FILE);

    return [
        'file' => "/upload/reports/user".$fData['curUser']."_report1_student".$fData['student'].".pdf"
    ];
}
