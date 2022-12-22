<?php 
    $arBadNames = ['user', 'order'];
    GLOBAL $arParams;

    if(!$arParams['table_name']){
        echo 'нет таблицы!';
        exit;
    }

    // TODO убрать запрос в нормальное располложение (?)
    require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/api/helpers/query.php');

    try{
        $pdo = \Helpers\query\connectDB();
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }

    try {
        $query = "SELECT column_name, data_type FROM information_schema.columns WHERE
        table_name = '" . $arParams['table_name'] . "'";
        $data = $pdo->prepare($query);
        $data->execute();
        $afieldsType = $data->fetchAll();

        foreach($afieldsType as $f) {
            $fieldsType[$f['column_name']] = $f['data_type'];
        }

        if($arParams['fields']){
            $selectFields = implode(', ', array_keys($arParams['fields']));

        } else {
            $selectFields = "*";
            $arParams['fields'] = TABLE_FIELDS_MANUAL[$arParams['table_name']];
        }

        if(in_array($arParams['table_name'], $arBadNames)) {
            $arParams['table_name'] = '"' . $arParams['table_name'] . '"';
        }

        $orderBy = implode(', ', array_map(function($col, $val) {
            return $col . " " . $val;
        }, array_keys($arParams['order_by']), array_values($arParams['order_by'])));

        $query = 'SELECT ' . $selectFields . ' FROM ' . $arParams['table_name'] . ' ORDER BY ' . $orderBy;

    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
?>

<div class="table-control-btns">
    <button class='btn btn_success' data-hystmodal='#update-form'>Добавить</button>
    <button class='btn btn_danger' id='btn-delElements'>Удалить</button>
</div>

<table 
    class="main-table" 
    data-table='<?=$arParams['table_name']?>' 
    data-identity='<?=$arParams['identity']?>'
>

    <thead class="main-table__head">
        <tr>
            <th></th>
            <?foreach($arParams['fields'] as $key => $field):?>
                <th class="main-table__th" data-key='<?=$key?>'>
                    <b><?echo $field ?: $key?></b></br>
                    <span><?echo $field ? $key : ''?></span>
                </th>
            <?endforeach;?>
            <th></th>
        </tr>
    </thead>

    <tbody class="main-table__body">
        <?php
        foreach($pdo->query($query) as $row) {?>            
            <tr class="main-table__item" data-id='<?=$row[$arParams['identity']]?>'>
                <td class="main-table__tr">
                    <input type="checkbox" name="check_row" id="" data-id='<?=$row[$arParams['identity']]?>'>
                </td>

                <?php foreach($arParams['fields'] as $key => $field):?>
                    <td class="main-table__tr" data-field='<?=$key?>'>
                        <?if($row[$key]!== null) {
                            if(is_bool($row[$key])) echo (int) $row[$key];
                            else echo $row[$key];
                        } else {
                            echo 'NULL';
                        }?>
                    </td>
                <?endforeach;?> 

                <td 
                    class="main-table__tr edit-btn" 
                    data-id='<?=$row[$arParams['identity']]?>'
                    data-hystmodal='#update-form'
                >
                    ред.
                </td>           
            </tr>
        <?}?>
    </tbody>
</table>

<div class="hystmodal" id="update-form" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <button data-hystclose class="hystmodal__close">Закрыть</button>
            
            <form action="" method="post" class='table-form'>
                <?php foreach($arParams['fields'] as $key => $field):?>
                    <?if($key == $arParams['identity']) continue;?>

                    <label for="<?=$key?>"><?=$field?></label>
                    
                    <?switch ($fieldsType[$key]) {
                        case 'integer':
                            echo "<input class='form-item' type='number' name='$key'>";
                            break;

                        case 'date':
                            echo "<input class='form-item' type='date' name='$key'>";
                            break;

                        case 'time without time zone':
                            echo "<input class='form-item' type='time' name='$key'>";
                            break;

                        case 'jsonb':
                            echo "<textarea class='form-item' name='$key'></textarea>";
                            break;

                        case 'boolean':
                            echo "<input class='form-item' type='checkbox' name='$key'>";
                            break;

                        case 'character varying':
                            if(strpos($key, '_text') !== false || strpos($key, '_description') !== false) {
                                echo "<textarea class='form-item' name='$key'></textarea>";
                            } else {
                                echo "<input class='form-item' type='text' name='$key'>";
                            }
                            break;
                        
                        default:
                            echo "<input class='form-item' type='text' name='$key'>";
                            break;
                    }?>

                    
                <?endforeach;?>

                <div class='table-control-btns'>
                    <button class='btn btn_success' id='btn-addElement'>Сохранить</button>
                    <button class='btn btn_danger' data-hystclose>Отменить</button>                    
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/admin/components/autotable/script.js"></script>