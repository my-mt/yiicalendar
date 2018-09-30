<?php

namespace app\components;

use yii\helpers\Html;
use yii\base\Widget;

/* @var $this yii\web\View */

$this->title = 'Calendar';
?>

<div class="body-content">

    <div class="row">
        <div class="col-md-9">
        <?php

        echo CalendareventsWidget::widget(['year' => $year, 'month' => $month, 'data_events' => $listEvents]);
        ?>
        </div>
        <div class="col-md-3">
            <table class="table">
                <tr>
                    <th>#</th>
                    <th>События</th>
                    <th></th>
                </tr>
                <?php foreach ($calendarList as $k => $calendar) { ?>
                <tr>
                    <td><?= ++$k ?></td>
                    <td>
                        <?php
//                        echo $calendar->summary;
                        echo Html::a($calendar->summary, ['calendar/calendar-events', 'id' => $calendar->id], ['class' => '']);
                        echo '</td><td>';
                        echo Html::a("", ['calendar/update-calendar', 'id' => $calendar->id], ['class' => 'profile-link glyphicon glyphicon-cog']);
                        echo '&nbsp;';
                        echo Html::a("", ['calendar/insert-event', 'calendarId' => $calendar->id], ['class' => 'profile-link glyphicon glyphicon-plus-sign']);
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </table> 
        </div>

    </div>

</div>
