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
            <input type="hidden" name="eventId" value="<?= $eventId ?>">
            <div class="form-group">
                <label class="control-label"><?= $calendarSetSummary ?></label>
                <input class="form-control" type="text" name="summary" value="<?= $event->summary ?>">
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
                        <?php
                        echo TimePicker::widget([
                            'name' => 'timeStart',
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
                        <?php
                        echo TimePicker::widget([
                            'name' => 'timeEnd',
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
            <div class="form-group">
            <button form="w0" type="submit" class="btn btn-primary">Сохранить</button>
            </div>
            <?php ActiveForm::end(); ?>     
            
        </div>
    </div>
</div>