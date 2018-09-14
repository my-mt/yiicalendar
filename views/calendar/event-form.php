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
    <h3>Событие календаря: "<?= $calendarSummary ?>"</h3>
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
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
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
                <div class="col-xs-6">
                    <div class="form-group">
                        <label class="control-label">Окончание</label>
                        <?php
                        echo DatePicker::widget([
                            'name' => 'dateEnd',
                            'value' => $dateEnd,
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
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
            <div class="form-group">
                <label class="control-label">Описание</label>
                <textarea class="form-control" name="description"><?= @$event->description ?></textarea>
            </div>
            <?php
            if(is_object($calendarFields)):
                foreach($calendarFields as $field => $typeField) {
                $type = 'text';
                $data = @json_decode($event->description);
                switch ($typeField) {
                    case 'int':
                    case 'float':
                        $type = "number";
                        break;
                    case 'time':
                        $type = "time";
                        break;
                } 
            ?>
            
            <div class="form-group description">
                <label class="control-label description-filed"><?= $field ?></label>
                <input class="form-control description-value" type="<?= $type ?>" name="<?= $field ?>" value="<?= @$data->$field ?>">
            </div>
            
            <?php } endif; ?>
            <div class="row">
                <div class="col-xs-6">
                </div>
            </div>
            <div class="form-group">
            <button form="w0" type="submit" class="btn btn-primary">Сохранить</button>
            </div>
            <?php ActiveForm::end(); ?>     
            
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function(){

    // Добавлнеие обработчика изменения изменения input
    $(".description-value").change(function() {
        makeDescriptionStr();
    });

    
    // Функция формирует строку json для description
    function makeDescriptionStr() {
        var data = {}
        $('div.form-group.description').each(function() {
            data[$(this).children('.description-filed').text()] = $(this).children('.description-value').val();

        });
        console.log(data);
        $('textarea[name="description"]').val(JSON.stringify(data));
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