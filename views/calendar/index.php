<?php

namespace app\components;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\base\Widget;

/* @var $this yii\web\View */

$this->title = 'Calendar';
?>

<div class="body-content">
    <div class="row">
        <div class="col-md-9">
        <?php
        $urlAction = Url::toRoute(['calendar/index']);
        $eventFilterId = '';
        // Для event-filter.php необходимы:
        // $urlAction
        // $yearStart
        // $monthStart
        // $yearEnd
        // $monthEnd
        // $eventFilterId
        require 'event-filter.php';

        $count = 0;
        while ($yearStart != $yearEnd || $monthStart != $monthEnd) {
            $ount++;
            if ($ount > 100) break;
            echo CalendareventsWidget::widget(['year' => $yearStart, 'month' => $monthStart, 'data_events' => $listEvents]);
            if ($monthStart > 11) {
                $monthStart = 0;
                $yearStart++;
            }
            $monthStart++;
        }
        ?>
        </div>
        <div class="col-md-3">
            <table class="table">
                <tr>
                    <th>#</th>
                    <th>Ред.</th>
                    <th>События</th>
                    <th>Доб.</th>
                    <th></th>
                </tr>
                <?php foreach ($calendarList as $k => $calendar) { ?>
                <tr>
                    <td><?= ++$k ?></td>
                    <td>
                        <?= Html::a("", ['calendar/update-calendar', 'id' => $calendar->id], ['class' => 'profile-link glyphicon glyphicon-cog']); ?>
                    </td>
                    <td>
                         <?=  Html::a($calendar->summary, ['calendar/calendar-events', 'id' => $calendar->id], ['class' => '']); ?>
                    </td>
                    <td>
                        <?= Html::a("", ['calendar/insert-event', 'calendarId' => $calendar->id], ['class' => 'profile-link glyphicon glyphicon-plus-sign']); ?>
                    </td>
                </tr>
                <?php } ?>
            </table> 
        </div>

    </div>

</div>
