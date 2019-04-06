<?php

namespace app\components;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\field\FieldRange;
use kartik\widgets\DatePicker;
use kartik\widgets\TimePicker;

/* @var $this yii\web\View */

$this->title = 'Event form';
?>

<div class="content">
    <h3>Событие календаря: <?= Html::a($calendarSummary, ['calendar/calendar-events', 'id' => $calendarId], ['class' => '']) ?></h3>
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>
            <input type="hidden" name="calendarId" value="<?= $calendarId ?>">
            <input type="hidden" name="eventId" value="<?= @$eventId ?>">
            <div class="form-group">
                <label class="control-label"><?= $calendarSetSummary ?></label>
                <input class="form-control" type="text" name="summary" value="<?= @$event->summary ?>">
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label class="control-label">Начало</label>
                        <?php
                        echo DatePicker::widget([
                            'name' => 'dateStart',
                            'value' => $dateStart,
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'orientation' => 'bottom left',
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ]
                        ]);
                        ?>
                        <div class="event-time-field">
                        <?php
                        echo TimePicker::widget([
                            'name' => 'timeStart',
                            'id' => 'time-start',
                            'value' => $timeStart,
                            'pluginOptions' => [
                                'showSeconds' => false,
                                'showMeridian' => false,
                                'minuteStep' => 1,
                            ]
                        ]);
                        ?>
                        </div>
                    </div>
                </div>
                <?php if (@$calendarSettings->dateOne) {
                    $timeEndStyle = $descriptionStyle = "style='display: none'";
                } else {
                    $timeEndStyle = $descriptionStyle = "style='display: block'";
                }
                ?>
                <div class="col-xs-6" <?= $timeEndStyle  ?>>
                    <div class="form-group">
                        <label class="control-label">Окончание</label>
                        <?php
                        echo DatePicker::widget([
                            'name' => 'dateEnd',
                            'value' => $dateEnd,
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'orientation' => 'bottom right',
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ]
                        ]);
                        ?>
                        <div class="event-time-field">
                        <?php
                        echo TimePicker::widget([
                            'name' => 'timeEnd',
                            'id' => 'time-end',
                            'value' => $timeEnd,
                            'pluginOptions' => [
                                'showSeconds' => false,
                                'showMeridian' => false,
                                'minuteStep' => 1,
                            ]
                        ]);
                        ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">Весь день</label>
                <input type="checkbox" name="all-day" class="form-check-input" id="all-day" value="1" <?= ($timeStart) ? '' : 'checked' ?> >
            </div>
            <?php
            if(is_object($calendarFields)):
                $showDescription = true;
                foreach($calendarFields as $field => $typeField) {
                $showDescription = false;
                $type = 'text';
                $textarea = 0;
                $data = @json_decode($event->description);
                switch ($typeField) {
                    case 'int':
                    case 'float':
                        $type = "number";
                        break;
                    case 'time':
                        $type = "time";
                    case 'str':
                    case 'url_image':
                        $textarea = 7;
                        break;
                }
            ?>
            
                <div class="form-group description">
                    <label class="control-label description-filed"><?= $field ?></label>
                    <?php if ($textarea) { ?>
                        <textarea class="form-control description-value" rows="<?= $textarea ?>" name="<?= $field ?>"><?= @$data->$field ?></textarea>
                    <?php } else { ?>
                        <input class="form-control description-value" type="<?= $type ?>" name="<?= $field ?>" value="<?= @$data->$field ?>" step="any">
                    <?php } ?>
                    
                </div>
            
            <?php }
                if ($showDescription && !@$calendarSettings->descriptionHide) {
                    $descriptionStyle = "style='display: block'";
                } else {
                    $descriptionStyle = "style='display: none'";
                }
            ?>
                <div class="form-group" <?= $descriptionStyle ?>>
                    <label class="control-label">Описание</label>
                    <textarea class="form-control" rows="4" name="description"><?= @$event->description ?></textarea>
                </div>
            <?php endif; ?>

            <div class="form-group">
            <button form="w0" type="submit" class="btn btn-primary">Сохранить</button>
            
            <?php ActiveForm::end(); ?>
            <?= Html::a('Удалить', ['calendar/delete-event', 'calendarId' => $calendarId, 'eventId' => @$eventId], ['class' => 'btn btn-danger']); ?>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function(){

    // Добавлнеие обработчика изменения изменения input
    $(".description-value").change(function() {
        makeDescriptionStr();
    });

//     Дублирование даты и времени из Начало в Окончание
//     Переделать в связке дата и время
    $('input[name="dateStart"]').change(function() {
        if ($('input[name="dateEnd"]').val().replace(/-/g, '') < $(this).val().replace(/-/g, '')) {
            $('input[name="dateEnd"]').val($(this).val());
        }
    });
    $('input[name="timeStart"]').change(function() {
        if ($('input[name="timeEnd"]').val().replace(':', '') < $(this).val().replace(':', '')) {
            $('input[name="timeEnd"]').val($(this).val());
        }
    });
    
    // Просто дублируем дату (время) начала в дату (время) окончания при изменении даты (времени) начала
//    $('input[name="dateStart"]').change(function() {
//        $('input[name="dateEnd"]').val($(this).val());
//    });
//    $('input[name="timeStart"]').change(function() {
//        $('input[name="timeEnd"]').val($(this).val());
//        console.log('time =========================');
//    });
    
    // Функция формирует строку json для description
    function makeDescriptionStr() {
        var data = {}
        var noData = true;
        $('div.form-group.description').each(function() {
            noData = false;
            data[$(this).children('.description-filed').text()] = $(this).children('.description-value').val().replace( /"/g, "'" );
        });
        // если календарь не содержит разметку полей (занчения data в description)
        if (noData) return;
      
        // Данные, которые уже есть в поле description
        try {
            var curDataDescription = JSON.parse($('textarea[name="description"]').val());
        } catch {
            curDataDescription = {};
        }     
        // Теперь есть два объекта: Текущий curDataDescription и новый data и их надо объеденить
        var result = $.extend(true, curDataDescription,data)
        // Теперь поля, которые были не затираются
        
        $('textarea[name="description"]').val(JSON.stringify(result));
    }
    
    

    function hideTimeField() {
        $('.event-time-field').hide();
    }
    function showTimeField() {
        $('.event-time-field').show();
    }

    $('#all-day').on( "click", function() {
        if($(this).is(":checked")) {
            hideTimeField();
        }
        else {
            showTimeField()
        }
    });
    
    if ($('#all-day').is(':checked')) {
        hideTimeField() 
    }
    
    makeDescriptionStr();

});
</script>