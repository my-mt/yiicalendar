<?php

namespace app\components;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Calendar form';
$fieldType = [
    'int' => 'целые числа',
    'float' => 'числа с точкой',
    'time' => 'время',
    'bool' => 'да или нет',
    'str'=> 'текст',
    'html' => 'html'
        ]
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
                <label class="control-label">Описание</label>
                <textarea class="form-control" name="description"><?= $description ?></textarea>
            </div>
            <?php ActiveForm::end(); ?>
<!--        </div>
        <div class="col-md-6">-->
            <?php
            $descriptionArr = json_decode($description);
            ?>
            <div class="form-group">
                <label class="control-label">Название основного поля</label>
                <input id="settings-summary" class="form-control" type="text" name="" value="<?= @$descriptionArr->settings->summary ?>">
            </div>
            <div class="form-group add-field-sec">
                <label class="control-label" >Дополнительные поля</label>
                <span id="add-field" class="add-element">+</span>
                <?php
                if (is_object(@$descriptionArr->data)):
                    foreach ($descriptionArr->data as $k => $v) {
                        ?>
                        <p>
                            <input class="form-control add-field" type="text" name="" value="<?= $k ?>">
                            <input class="form-control add-field" type="text" name="" value="<?= $v ?>">
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
    // Добавление пары поле, тип
    $( "#add-field" ).click(function() {
        var tpl = $('#tpl-select-type');
        $('.add-field-sec').append(tpl.html());
        // Добавлнеие обработчика изменения изменения select
        $(".add-field-sec select").last().change(function() {
            makeDescriptionStr();
        });
        // Добавлнеие обработчика изменения изменения input
        $(".add-field-sec input").last().change(function() {
            makeDescriptionStr();
        });
    });
    
    // Добавлнеие обработчика изменения изменения input
    $("#settings-summary").last().change(function() {
        makeDescriptionStr();
    });
    
    // Функция формирует строку json для description
    function makeDescriptionStr() {
        var data = {}
        $('.add-field-sec p').each(function() {
            if($(this).children('input').val()) {
                data[$(this).children('input').val()] = $(this).children('select').val();
            }
        });
        console.log(data);
        var settings = {'summary': $('#settings-summary').val()}
        var description = {'data': data, 'settings': settings};
        $('textarea[name="description"]').val(JSON.stringify(description));
    }
</script>