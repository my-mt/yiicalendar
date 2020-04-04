<?php

namespace app\components;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\base\Widget;

/* @var $this yii\web\View */

$this->title = 'Calendar';
$monthStartNav = $monthStart;
$yearStartNav = $yearStart;
$month_arr = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
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
            $calendarHtml = '';
            while ($yearStart != $yearEnd || $monthStart != $monthEnd) {
                $count++;
                if ($count > 100) break;
                $calendarHtml .= CalendareventsWidget::widget(['year' => $yearStart, 'month' => $monthStart, 'data_events' => $listEvents]);
                if ($monthStart > 11) {
                    $monthStart = 0;
                    $yearStart++;
                }
                $monthStart++;
            }

            // Для случая, когда показывается один месяц
            if ($count == 1) {
                if ($monthStartNav == 1) {
                    $monthBack = 12;
                    $yearBack = $yearStartNav -1;
                } else {
                    $monthBack = $monthStartNav - 1;
                    $yearBack = $yearStartNav;
                }

                if ($monthStartNav == 12) {
                    $monthNext = 1;
                    $yearNext = $yearStartNav + 1;
                } else {
                    $monthNext = $monthStartNav + 1;
                    $yearNext = $yearStartNav;
                }
            ?>
            <div class="nav-month">
                <?= Html::a('', ['calendar/index', 'monthStart' => $monthBack, 'yearStart' => $yearBack], ['class' => 'glyphicon glyphicon-chevron-left']) ?>
                <?= $yearStartNav . ' ' . $month_arr[(int)$monthStartNav - 1] ?>
                <?= Html::a('', ['calendar/index', 'monthStart' => $monthNext, 'yearStart' => $yearNext], ['class' => 'glyphicon glyphicon-chevron-right']) ?>
            </div>
            <?php } ?>

            <?= $calendarHtml ?>
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
                <?php
                $count = 1;
                ?>
                <?php foreach ($calendarList as $k => $calendar) {
                    if ($calendar->accessRole != 'owner') {
                        continue;
                    }
                ?>
                <tr>
                    <td><?= $count++ ?></td>
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
                <tr>
                    <td colspan="3"><b>Чужие</b></td>
                </tr>
                <?php foreach ($calendarList as $k => $calendar) {
                    if ($calendar->accessRole != 'reader') {
                        continue;
                    }
                ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td>
                    </td>
                    <td>
                         <?=  Html::a($calendar->summary, ['calendar/calendar-events', 'id' => $calendar->id], ['class' => '']); ?>
                    </td>
                    <td>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

    </div>

</div>
