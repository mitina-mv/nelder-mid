<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Метод Нелдера-Мида</title>
    <script src="/admin/assets/js/script.js"></script>
    <script src="/script.js"></script>
</head>

<?echo sin(3); echo eval('return sin(3);');?>
<body>
    <form action="" id='nelderMid' method='get'>
        <h3>Метод Нелдера-Мида</h3>

        <div class="info">
            <span>Введодите умножение и деление как * и / соответственно.</span> 
            <span>Ввод переменной принимается в форме: xi, где i - число от одного до количества переменных</span> 
        </div>

        <div class="form-item">
            <label for="countx">Количество переменных</label>
            <input type="number" name="countx" min='1' required>
        </div>
        
        <div class="form-item" >
            <label>Координаты начальной точки</label>

            <div id='coord-body'></div>
        </div>

        
        <div class="form-item">
            <label for="func">Функция</label>
            <input type="text" name="func" required>
        </div>

        <div class="addition-settings">
            <h5>Настройки</h5>
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
                <label for="epx">Точность</label>
                <input type="number" name="epx" min='0.00001' value='0.01'>
            </div>
        </div>

        <button type="submit" id='submitForm'>Рассчитать</button>
        <button type="reset">Сбросить</button>
    </form>
</body>
</html>