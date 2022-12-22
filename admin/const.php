<?php
define('TABLE_FIELDS_MANUAL', [
    'test' => [
        'test_id' => 'ID теста',
        'user_id' => 'ID пользователя',
        'org_id' => 'ID огранизации',
        'discipline_id' => 'ID дисциплины',
        'test_description' => 'Описание теста',
        'test_settings' => 'Настройки теста',
        'test_name' => 'Название теста',
    ],
    'user' => [
        'org_id' => 'ID огранизации',
        'studgroup_id' => 'ID группы студентов',
        'role_id' => 'ID группы прав',
        'user_id' => 'ID пользователя',
        'user_firstname' => 'Имя',
        'user_lastname' => 'Фамилия',
        'user_patronymic' => 'Отчество',
        'user_login' => 'Логин',
        'user_password' => 'Пароль',
        'user_email' => 'Почта',
    ],
    'testlog' => [
        'testlog_id' => 'ID записи тестир.',
        'user_id' => 'ID пользователя',
        'test_id' => 'ID теста',
        'testlog_date' => 'Дата тестирования',
        'testlog_mark' => 'Итоговая оценка',
        'testlog_time' => 'Затрачееное время',
    ],
    'answer' => [
        'answer_id' => 'ID ответа',
        'question_id' => 'ID вопроса',
        'answer_name' => 'Текст ответа',
        'answer_status' => 'Статус правильности',
        'answer_mark' => 'Стоимость',
    ],
    'answerlog' => [
        'answerlog_id' => 'ID записи ответа',
        'testlog_id' => 'ID записи тестир.',
        'question_id' => 'ID вопроса',
        'answerlog_mark' => 'Оценка ответа',
    ],
    'studgroup' => [
        'studgroup_id' => 'ID группы студентов',
        'org_id' => 'ID огранизации',
        'studgroup_name' => 'Название группы студентов'
    ],
    'orgs' => [
        'org_id' => 'ID огранизации',
        'org_name' => 'Название огранизации',
        'org_info' => 'Информация',
        'org_address' => 'Адрес',
    ],
    'question' => [
        'question_id' => 'ID вопроса',
        'user_id' => 'ID пользователя',
        'org_id' => 'ID огранизации',
        'discipline_id' => 'ID дисциплины',
        'question_private' => 'Приватность',
        'question_text' => 'Текст вопроса',
        'question_settings' => 'Настройки вопроса',
    ],
    'discipline' => [
        'discipline_id' => 'ID дисциплины',
        'discipline_name' => 'Название дисциплины'
    ],
    'roles' => [
        'role_id' => 'ID группы прав',
        'role_name' => 'Название группы прав'
    ],
    'fixanswer' => [
        'answerlog_id' => 'ID записи ответа',
        'answer_id' => 'ID  ответа',
    ],
    'profess' => [
        'discipline_id' => 'ID дисциплины',
        'user_id' => 'ID пользователя',
    ],
    'teaches' => [
        'studgroup_id' => 'ID группы студентов',
        'user_id' => 'ID пользователя',
    ],
]);