<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Метод Нелдера-Мида</title>
    <script src="/admin/assets/js/script.js"></script>
    <script src="/script.js"></script>
    <link rel="stylesheet" href="/style.css">
</head>

<body>
    <form action="" method="post" id='taskAction'>
        <input type="file" name="addTask">
        <button id='saveTask'>Сохранить задачу</button>
        <button id='addTask'>Загрузить задачу</button>
    </form>

    <form action="" id='nelderMid' method='get'>
        <h3>Метод Нелдера-Мида</h3>

        <button class='btn btn-settings'> Настройки</button>
        <section class='settings'>
            <h5>Настройки</h5>
            <div class="addition-settings">            
                <div class="form-item">
                    <label for="lambda">Множитель лямбда</label>
                    <input type="number" name="lambda" min='1' value='2'>
                </div>

                <div class="form-item">
                    <label for="alpha">Коэффициент отражения (альфа)</label>
                    <input type="number" name="alpha" min='1' value='2'>
                </div>

                <div class="form-item">
                    <label for="betta">Коэффициент сжатия (бетта)</label>
                    <input type="number" name="betta" min='0.01' value='0.5'>
                </div>

                <div class="form-item">
                    <label for="gamma">Коэффициент растяжения (гамма)</label>
                    <input type="number" name="gamma" min='1' value='2'>
                </div>

                <div class="form-item">
                    <label for="eps">Точность</label>
                    <input type="number" name="eps" min='0.000001' value='0.01'>
                </div>
            </div>
        </section>

        <div class="info">
            <span>Вводите умножение и деление как * и / соответственно. Возведение в степень как ^.</span> 
            <span>Ввод переменной принимается в форме: xi, где i - число от одного до количества переменных</span> 
        </div>

        <div class="grid-2">
            <div class="form-item">
                <label for="countx">Размерность задачи</label>
                <input type="number" name="countx" min='1' required>
            </div>
                    
            <div class="form-item">
                <label for="func">Функция</label>
                <input type="text" name="func" required>
            </div>
        </div>
        
        <div class="form-item" >
            <label>Координаты начальной точки</label>

            <div id='coord-body'></div>
        </div>

        <div class="btn-group">
            <button type="submit" id='submitForm'>Рассчитать</button>
            <button type="reset">Сбросить</button>
        </div>
    </form>

    <h3>Отчет</h3>
    <div>
        <?
            $files1 = scandir($_SERVER['DOCUMENT_ROOT'] . "/upload/reports/");
            unset($files1[0]);
            unset($files1[1]);
            foreach($files1 as $file) {
                echo "<a href='/upload/reports/$file' download>$file</a>";
            }
        ?>

        <div contenteditable="true" id="docs">
            <h5>Ghbf</h5>
        </div>
    </div>

    <button class='btn btn-open-task'>Задачи</button>
    <section class='savetask'>
        <h4>Сохраненные задачи</h4>
        <div id="tasks">
            <?
                $files2 = scandir($_SERVER['DOCUMENT_ROOT'] . "/upload/tasks/");
                unset($files2[0]);
                unset($files2[1]);
                foreach($files2 as $file) {
                    echo "<a href='/upload/tasks/$file' download>$file</a>";
                }
            ?>
        </div>
    </section>

</body>
</html>