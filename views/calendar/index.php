<?php

namespace app\components;

use yii\helpers\Html;
use yii\base\Widget;

/* @var $this yii\web\View */

$this->title = 'Calendar';
?>

<div class="body-content">

    <div class="row">
        <div class="col-md-3">
            <table class="table">
                <tr>
                    <th>#</th>
                    <th>События</th>
                </tr>
                <?php foreach ($calendarList as $k => $calendar) { ?>
                <tr>
                    <td><?= ++$k ?></td>
                    <td><?= Html::a($calendar->summary, ['calendar/index', 'id' => $calendar->id], ['class' => 'profile-link']) ?></td>
                </tr>
                <?php } ?>
            </table> 
        </div>
        <div class="col-md-9">
<!--            <pre>
                <?php print_r($listEvents); ?>
            </pre>-->
                <?php
                $year = '2018';
                $month = '9';
                
//                $data_events = [];
//                foreach($listEvents as $events){
//                    $datat['']
//                    
//                }
                
                $data_events = [
                    [
                        'id' => 4450,
                        'event_id' => 1037,
                        'user_id' => 39,
                        'ote' => '',
                        'date_time' => 1536267223,
                        'result' => 25,
                        'price' => 0,
                        'additionally' => 'Примечание',
                        'source' => '',
                        'title_events' => 'Название календаря',
                        'color_events' => '#3800a8',
                    ]
                ];

                echo CalendareventsWidget::widget(['year' => $year, 'month' => $month, 'data_events' => $listEvents]);
                ?>
            
        </div>

    </div>

</div>
