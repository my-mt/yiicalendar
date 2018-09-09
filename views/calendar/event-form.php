<?php

namespace app\components;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Event form';
?>

<div class="content">
    <h3>Событие календаря: "<?= $calendarSummary ?>"</h3>
    <div class="row">
        <div class="col-sm-4">
            <?php $form = ActiveForm::begin(); ?>
            <input type="hidden" name="calendarId" value="<?= $calendarId ?>">
            <input type="hidden" name="eventId" value="<?= $eventId ?>">
            <div class="form-group">
                <label class="control-label"><?= $calendarSetSummary ?></label>
                <input class="form-control" type="text" name="summary" value="<?= $event->summary ?>">
            </div>
            <div class="form-group">
                <label class="control-label">Начало</label>
                <input class="form-control" type="text" name="start[date]" value="<?= $event->start->date ?>">
                <input class="form-control" type="text" name="start[dateTime]" value="<?= $event->start->dateTime ?>">
            </div>
            <div class="form-group">
                <label class="control-label">Окончание</label>
                <input class="form-control" type="text" name="end[date]" value="<?= $event->end->date ?>">
                <input class="form-control" type="text" name="end[dateTime]" value="<?= $event->end->dateTime ?>">
            </div>
            <div class="form-group">
            <button form="w0" type="submit" class="btn btn-primary">Сохранить</button>
            </div>
            <?php ActiveForm::end(); ?>     
            
        </div>
    </div>
</div>