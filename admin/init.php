<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/composer/vendor/autoload.php');
require_once('const.php');

$config = [
    'template-name' => 'main',
    'menu' => [
        'top' => 'main'
    ]
];

function getComponent($componentName) {
    if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin/components/' . $componentName)){
        require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/components/' . $componentName . '/template.php');
    } else {
        echo "<span style='color: #f00;>компонент не найден</span>";
    }
}
