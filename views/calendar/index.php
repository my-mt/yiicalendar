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
        $year = '2018';
        $month = '9';

        echo CalendareventsWidget::widget(['year' => $year, 'month' => $month, 'data_events' => $listEvents]);
        ?>
        </div>
        <div class="col-md-3">
            <table class="table">
                <tr>
                    <th>#</th>
                    <th>События</th>
                </tr>
                <?php foreach ($calendarList as $k => $calendar) { ?>
                <tr>
                    <td><?= ++$k ?></td>
                    <td>
                        <?php
                        echo $calendar->summary;
                        echo Html::a(" edit", ['calendar/update-calendar', 'id' => $calendar->id], ['class' => 'profile-link']);
                        echo Html::a(" add", ['calendar/insert-event', 'calendarId' => $calendar->id], ['class' => 'profile-link']);
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </table> 
        </div>

    </div>

</div>
