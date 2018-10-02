<?php

namespace app\components;

use yii\helpers\Html;
//use yii\base\Widget;

/* @var $this yii\web\View */

$this->title = 'Calendar-events';

//echo '<pre>';
//print_r($calendarDescription->data);
//echo '</pre>';
//exit;

//[calendar_description] => {"data":{"время":"time","калорий":"int"},"settings":{"summary":"дистанция"}}
//[calendar_summary] => Бег
//[calendar_backgroundColor] => 
//[calendar_id] => i1ui96kcj6hnc4q8ib8td7blmc@group.calendar.google.com
//[id] => _95hm2r3165i6cphh6kom8dr3c9ijcor3c4q64dhg70q3edhi65i3ep9l6g
//[start] => 2017-06-07T11:00:00+03:00
//[end] => 2017-06-07T11:00:00+03:00
//[summary] => 2
//[description] => 
//?>

<div class="body-content">

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-responsive table-calendar-events">
                <thead class="thead-inverse">
                    <tr>
                        <th>№</th>
                        <th>Начало</th>
                        <th>Конец</th>
                        <?php if (is_array($calendarDescription)) { ?>
                        <th><?= $calendarDescription['settings']['summary'] ?></th>
                        <?php
                        foreach($calendarDescription['data'] as $k => $v) {
                            echo "<th>$k</th>";
                        }
                        ?>
                        <?php } else { ?>
                        <th>Заголовок</th>
                        <?php } ?>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = count($dataEvents) + 1; foreach($dataEvents as $event) { $i--; ?>
                    <tr>
                        <td><?= Html::a($i, ['calendar/update-event', 'calendarId' => $event['calendar_id'], 'eventId' => $event['id']], ['class' => '']); ?></td>
                        <td><p class="date-str"><?= substr($event['start'], 0, 10) ?></p><?= substr($event['start'], 11, 5) ?> </td>
                        <td><p class="date-str"><?= substr($event['end'], 0, 10) ?></p><?= substr($event['end'], 11, 5) ?> </td>
                        <td><?= $event['summary']?> </td>
                        <?php 
                        if (is_array($calendarDescription)):
                            $dataDescription = json_decode($event['description'], true);
                            if (is_array($dataDescription)) { // если в поле description данные json то обрабатываем как надо
                                foreach($calendarDescription['data'] as $k => $v) {
                                    echo "<td>" . @$dataDescription[$k] . "</td>";
                                }
                                echo "<td></td>";
                            } else {  // если нет, товыводим пустые столбцы
                                foreach($calendarDescription['data'] as $v) { //calendarDescription->data
                                    echo "<td>---</td>";
                                }
                                echo "<td>" . $event['description'] . "</td>";
                            }
                        else:
                            echo "<td>" . $event['description'] . "</td>";                       
                        endIf;
                        ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
       
           
        </div>
    </div>

</div>
