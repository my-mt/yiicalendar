<?php

namespace app\components;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\field\FieldRange;
use kartik\widgets\DatePicker;
use kartik\widgets\TimePicker;

/* @var $this yii\web\View */

$this->title = 'Event form';
// echo 'Test mode';
?>
<style>
.content {
    margin: 0 auto;
    width: 267px;
}
h3 {
    margin: -12px 0 -10px 0;
}
.sec-tadev-numeric-keypad {
    margin: -10px 0 13px 0;
}
.row-numeric-keypad {
    display: flex;
}

#result {
    /*border: 2px solid #555;*/
    border-radius: 4px;
    width: 262px;
    margin: 2px;
    font-size: 40px;
    padding: 5px;
    text-align: right;
    /*background-color: #337ab7;*/
    background-color: #4a4a4a;
    color: #fff;
    letter-spacing: 5px;
}
.row-numeric-keypad>div {
    display: inline-block;
    border: 2px solid #555;
    width: 100%;
    height: 50px;
    margin: 2px;
    font-size: 33px;
    font-weight: 500;
    text-align: center;
    border-radius: 5px;
    background: #eee;
    cursor: pointer;
    -moz-user-select: none;
    -khtml-user-select: none;
    user-select: none;
}

.row-numeric-keypad>div:hover {
    background: #ddd;
}

.btn-success {
    width: 221px;
}
.btn-danger {
    width: 39px;
}
</style>


<div class="content">
    <h3><?= Html::a($calendarSummary, ['calendar/calendar-events', 'id' => $calendarId], ['class' => '']) ?></h3>
    <?php $form = ActiveForm::begin(); ?>
    <input type="hidden" name="calendarId" value="<?= $calendarId ?>">
    <input type="hidden" name="eventId" value="<?= @$eventId ?>">
    <div class="form-group">
        <!-- <label class="control-label"><?= $calendarSetSummary ?></label> -->
        <input class="form-control" type="hidden" name="summary" value="<?= @$event->summary ?>">
    </div>
    <div class="row">
        <div class="col-xs-12" style="padding: 0 18px;">
            <div class="form-group start-section" style="display: flex;">
                <div style="width: 141px; margin-right: 15px;">
                <?php
                // https://demos.krajee.com/widget-details/datepicker#markup-input
                echo DatePicker::widget([
                    'name' => 'dateStart',
                    'value' => $dateStart,
                    'readonly' => true,
                    // 'type' => DatePicker::TYPE_INPUT,
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'removeButton' => false,
                    'pluginOptions' => [
                        'orientation' => 'bottom left',
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                    ]
                ]);
                ?>
                </div>
                <div class="event-time-field" style="width: 103px;">
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

        <div class="col-xs-6" style="display: none">
            <div class="form-group end-section">
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
    
    <!-- sec-tadev-numeric-keypad -->
    <div class="sec-tadev-numeric-keypad">
        <div id="result">0</div>
        <div id="tadev-numeric-keypad">
            <div class="row-numeric-keypad">
                <div class="key">1</div>
                <div class="key">2</div>
                <div class="key">3</div>
                <div class="key">.</div>
            </div>
            <div class="row-numeric-keypad">
                <div class="key">4</div>
                <div class="key">5</div>
                <div class="key">6</div>
                <div id="backspace"><</div>
            </div>
            <div class="row-numeric-keypad">
                
                <div class="key">7</div>
                <div class="key">8</div>
                <div class="key">9</div>
                <div class="key">0</div>
            </div>
        </div>
    </div>
    <!-- /sec-tadev-numeric-keypad -->

    <div class="form-group">
    <button form="w0" type="submit" class="btn btn-success">Сохранить</button>
    <?= Html::a('', ['calendar/calendar-events', 'id' => $calendarId], ['class' => 'btn btn-danger glyphicon glyphicon-remove']); ?>
    <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
window.onload=function() {
    // Просто дублируем дату (время) начала в дату (время) окончания при изменении даты (времени) начала
    $('input[name="dateStart"]').change(function() {
        var inputEnd = $('input[name="dateEnd"]');
        inputEnd.val($(this).val());
        inputEnd.css({'color': '#fff'});
    });
    $('input[name="timeStart"]').change(function() {
        var inputEnd = $('input[name="timeEnd"]');
        inputEnd.val($(this).val());
        inputEnd.css({'color': '#fff'});
    });



    // sec-tadev-numeric-keypad
    $('input[name=summary]').val($('#result').html());
    $('#backspace').click(function() {
      var value = $('#result').html();
      value = value.slice(0, -1);
      if (value === '') {
        value = 0;
      }
      $('#result').html(value);
      $('input[name=summary]').val(value);
    })

    $('.key').click(function() {
        var value = $('#result').html();
        var key = $(this).html();

        if (value.indexOf('.') !== -1 && key === '.') {
            return;   
        }

        if (value.length > 6) {
            return; 
        }
        
        if (value === '0' && key !== '.') {
          value = '';
        }
        value = value + key;
        $('#result').html(value);
        $('input[name=summary]').val(value);
    })
}
</script>