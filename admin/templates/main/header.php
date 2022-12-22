<?php
foreach($config['menu'] as $menu){
    include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/menu/' . $menu . '.php');
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заголовок страницы</title>

    <script src="/admin/assets/hystmodal/hystmodal.min.js"></script>
    <link rel="stylesheet" href="/admin/assets/hystmodal/hystmodal.min.css">

    <link rel="stylesheet" href="/admin/templates/main/style.css">
    <script src="/admin/assets/js/script.js"></script>

</head>
<body>

<header>
    <div class="menu">
        <span class="menu__burger">Меню</span>

        <nav class='main-menu'>
            <?php 
                foreach($arSiteMenu[$config['menu']['top']] as $item):?>
                    <a class='main-menu__item <?if($item['link'] == $_SERVER['REQUEST_URI']) echo 'selected'?>' href="<?=$item['link']?>">
                        <?=$item['title']?>
                    </a>
            <?php endforeach;?>
        </nav>
    </div>
</header>
