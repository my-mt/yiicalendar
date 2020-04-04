<?php

namespace app\components;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

// https://repl.it/@__Antares__/Struktura-svoistva-kaliendaria
// json структура {"data":{},"settings":{"summary":"Количество","summaryProp":"1","descriptionHide":"1"}}

/* @var $this yii\web\View */

// echo '<pre>';
// print_r($calendar);
// echo '</pre>';


$this->title = 'Calendar form';
$fieldType = [
    'int' => 'целые числа',
    'float' => 'числа с точкой',
    'time' => 'время',
    'bool' => 'да или нет',
    'str'=> 'текст',
    'html' => 'html',
    'url_image' => 'ссылки на изображения'
];

$descriptionArr = json_decode($description);

// echo '<pre>';
// print_r($descriptionArr);
// echo '</pre>';
// exit;

if (!isset($descriptionArr->formatVersion) || $descriptionArr->formatVersion != "02") {
    // надо переформатировать в формат 02
    $data_02 = [];
    if (isset($descriptionArr->data)) {
        foreach ($descriptionArr->data as $k => $type) {
            $data_02[$k] = (object) [
                'name' => $k,
                'type' => $type
            ];

        }

        $descriptionArr->data = (object) $data_02;
        $descriptionArr->formatVersion = "02";
        $description = json_encode($descriptionArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        echo '<h2>Произведено переформатирование в формат версии 02</h2>';
    }

}

?>
<div class='row hidden'>
    <div id="tpl-select-type">
        <p>
        <input class="form-control add-field" type="text" name="" value="">
        <select class="form-control add-field" name="">
            <?php foreach ($fieldType as $k => $v) { ?>
                <option value="<?= $k ?>"><?= $v ?></option>
            <?php } ?>
        </select>
        </p>
    </div>
</div>

<div class="content">
    <h3>Календарь: "<?= $summary ?>"</h3>
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-group">
                <label class="control-label">Название</label>
                <input class="form-control" type="text" name="summary" value="<?= $summary ?>">
            </div>
            <div class="form-group">
                <label class="control-label">Родитель</label>
                <p>
                    <select class="form-control"  id="parent-calendar" name="">
                        <?php foreach ($calendarList as $k => $v) {
                            $selected = ($k == $descriptionArr->parent) ? 'selected ' : '';
                            ?>
                            <option <?=$selected ?>value="<?= $k ?>"><?= $v ?></option>
                        <?php } ?>
                    </select>
                </p>
            </div>
            <div class="form-group">
                <label class="control-label">Описание</label>
                <textarea class="form-control" name="description"><?= $description ?></textarea>
            </div>
            <?php ActiveForm::end(); ?>
<!--        </div>
        <div class="col-md-6">-->
            <?php
            
            $checkedCalc_1 = in_array(1, $descriptionArr->settings->summaryCalc) ? 'checked' : '';
            $checkedSimpleMode = in_array(1, $descriptionArr->settings->simpleMode) ? 'checked' : '';
            ?>
            <div class="form-group">
                <label class="control-label">Название основного поля</label>
                <input id="settings-summary" class="form-control" type="text" name="" value="<?= @$descriptionArr->settings->summary ?>">
                <div class="checkbox">
                    <label>
                        <input data-calc="1" class="calc-summary" type="checkbox" <?= $checkedCalc_1 ?>> Суммировать данные основного поля
                    </label>
<!--                    <label>
                        <input data-calc="2" class="calc-summary" type="checkbox"> Считать среднее значение основного поля
                    </label>-->
                </div>
                <div class="checkbox">
                    <label>
                        <input data-mode="1" class="simple-mode" type="checkbox" <?= $checkedSimpleMode ?>> Простой режим (при записи)
                    </label>
                </div>
            </div>
            <div class="form-group add-field-sec">
                <label class="control-label" >Дополнительные поля</label>
                <span id="add-field" class="add-element">+</span>
                <?php
                // echo '<pre>';
                // print_r($descriptionArr->data);
                // echo '</pre>';
                if (is_object(@$descriptionArr->data)):
                    foreach ($descriptionArr->data as $key => $dataField) {
                        ?>
                        <p>
                            <span name="key" class="hide"><?= $key  ?></span>
                            <input class="form-control add-field" type="text" name="" value="<?= $dataField->name ?>">
                            <select class="form-control add-field" name="">
                                <?php foreach ($fieldType as $k => $v) {
                                    $selected = ($k == $dataField->type) ? 'selected ' : '';
                                    ?>
                                    <option <?=$selected ?>value="<?= $k ?>"><?= $v ?></option>
                                <?php } ?>
                            </select>
                        </p>
                        <?php
                    }
                endif
                ?>
            </div>
            <div class="form-group">
            <button form="w0" type="submit" class="btn btn-primary">Сохранить</button></div>
        </div>
    </div>

</div>

<script>
    // Добавлнеие обработчика изменения уже имеющихся select
    $(".add-field-sec select, input.add-field, #parent-calendar").change(function() {
        makeDescriptionStr();
    });
        
    // Добавление пары поле, тип
    $( "#add-field" ).click(function() {
        var tpl = $('#tpl-select-type');
        $('.add-field-sec').append(tpl.html());
        // Добавлнеие обработчика изменения select
        $(".add-field-sec select").last().change(function() {
            makeDescriptionStr();
        });
        // Добавлнеие обработчика изменения input
        $(".add-field-sec input").last().change(function() {
            makeDescriptionStr();
        });
    });
    
    // Добавлнеие обработчика изменения input
    $("#settings-summary").last().change(function() {
        makeDescriptionStr();
    });
    
    // обработчик чекбосов
    $( '.calc-summary[type="checkbox"]' ).on( "click", function() {
        makeDescriptionStr();
    })

    $( '.simple-mode[type="checkbox"]' ).on( "click", function() {
        makeDescriptionStr();
    }) 
    
    // Функция формирует строку json для description
    function makeDescriptionStr() {
        var data = {};
        var dateForKey = new Date().getTime(); // используем время для ключа поля
        $('.add-field-sec p').each(function() {
            if ($(this).children('input').val()) {
            // при этом условии поле не будет удаляться
            // if ($(this).children('input').val() || $(this).children('span[name="key"]').text()) {

                var keyField = $(this).children('span[name="key"]').text();

                if (!keyField) {
                    dateForKey += 1;
                    keyField = 'key_' + dateForKey; // используем время для ключа поля
                }
                
                data[keyField] = {
                    'name': $(this).children('input').val(),
                    'type': $(this).children('select').val()
                }
                
            }
        });

        console.log('data --- ', data);

        // родительский календарь
        var parent = $('#parent-calendar').val();
        
        // проверка чекбоксов основного поля
        var summaryCalc = [];
        $('.calc-summary[type="checkbox"]').each(function() {
            if($(this).is(":checked")) {
                summaryCalc.push($(this).data('calc'));
            }
        });

        var simpleMode = [];
        $('.simple-mode[type="checkbox"]').each(function() {
            if($(this).is(":checked")) {
                simpleMode.push($(this).data('mode'));
            }
        });
        
        var settings = {
            'summary': $('#settings-summary').val(),
            'summaryCalc': summaryCalc,
            'simpleMode': simpleMode,
        }
        var description = {
            'data': data,
            'settings': settings,
            'parent': parent,
            'formatVersion': '02'
        };
        console.log(description);
        $('textarea[name="description"]').val(JSON.stringify(description));
    }

    // Функция формирует строку json для description
    // function makeDescriptionStr0() {
    //     var data = {};
    //     $('.add-field-sec p').each(function() {
    //         if($(this).children('input').val()) {
    //             data[$(this).children('input').val()] = $(this).children('select').val();
    //         }
    //     });

    //     // родительский календарь
    //     var parent = $('#parent-calendar').val();
        
    //     // проверка чекбоксов основного поля
    //     var summaryCalc = [];
    //     $('.calc-summary[type="checkbox"]').each(function() {
    //         if($(this).is(":checked")) {
    //             summaryCalc.push($(this).data('calc'));
    //         }
    //     });

    //     var simpleMode = [];
    //     $('.simple-mode[type="checkbox"]').each(function() {
    //         if($(this).is(":checked")) {
    //             simpleMode.push($(this).data('mode'));
    //         }
    //     });
        
    //     var settings = {
    //         'summary': $('#settings-summary').val(),
    //         'summaryCalc': summaryCalc,
    //         'simpleMode': simpleMode,
    //     }
    //     var description = {
    //         'data': data,
    //         'settings': settings,
    //         'parent': parent,
    //         'formatVersion': '02'
    //     };
    //     console.log('new');
    //     console.log(description);
    //     $('textarea[name="description"]').val(JSON.stringify(description));
    // }
</script>