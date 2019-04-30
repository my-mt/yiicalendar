<?php

namespace app\components;

use yii\helpers\Html;
use yii\base\Widget;

/* @var $this yii\web\View */

$this->title = 'Calendar-events';

?>

<div class="body-content">
    <h3><?= @$dataEvents[0]['calendar_summary'] ?></h3>

    <h4><?= Html::a('Добавить событие <span class="profile-link glyphicon glyphicon-plus-sign"></span>', ['calendar/insert-event', 'calendarId' => $id], ['class' => '']) ?></h4>

    <div class="row">
        <div class="col-xs-12">
            <?php if ($calendarView) {
                echo Html::a('Показать все события (10000)', ['calendar/calendar-events', 'id' => $id, 'count' => 10000], ['class' => '']);
            } else {
                echo Html::a('Показать текущий месяц', ['calendar/calendar-events', 'id' => $id], ['class' => '']);
            } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <?php
        if ($calendarView) {
            echo CalendareventsWidget::widget(['year' => $year, 'month' => $month, 'data_events' => $dataEvents, 'url' => \Yii::$app->params['siteUrl'] . '/calendar/calendar-events?id=' . $id . '&']);
        }
        ?> 
        </div>
        <!-- <pre> -->
            <?php
            // $calendarDescription - это данные свойстава календаря
            // $dataEvents - это события
            // print_r($calendarDescription);
            // print_r($dataEvents);

            // Получает строку с url изображений и отдает ссылки с картинками.
            // Строка разбивается в массив по символам перевода строки, допускается множественный перевод строк, лишняя итерация будет пропущена.
            function extractImg($strImg) {
                $imgArr = explode("\n", $strImg);
                $result = '';
                foreach($imgArr as $urlImg){
                    if (!$urlImg) continue;
                    $result .= '<a target="_blank" href="' . $urlImg . '" ><img class="event-img" src="' . $urlImg . '"></a>';
                }
                return $result;
            }
            ?>
        <!-- </pre> -->
        <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-responsive table-calendar-events">
                <thead class="thead-inverse">
                    <tr>
                        <th>Начало</th>
                        <th>Оконч.</th>
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
                        <th>Ред.</th>
                        <th></th>  
                    </tr>
                </thead>
                <tbody>
                    <?php
                    setlocale(LC_TIME, "ru_RU.utf8");
                    $sum = [];
                    $i = count($dataEvents) + 1;
                    foreach($dataEvents as $event) { $i--; ?>
                    <tr>
                        <td>
                            <div  class="date-time"> 
                                <div class="date-str number-start"><?= strftime("%d", strtotime($event['start'])) ?></div>
                                <div class="date-str"><?= strftime("%b <b>%H:%M</b><br>%Y", strtotime($event['start'])) ?></div>
                            </div>
                        </td>
                        <?php
                        // скрываем дату и время если они повторяются
                        if ($event['start'] == $event['end']) {
                            $hideDateAll = 'hide';
                        } else {
                            $hideDateAll = '';
                        }
                        if (substr($event['start'], 0, 10) == substr($event['end'], 0, 10)) {
                            $hideDate = 'hide';
                        } else {
                            $hideDate = '';
                        }
                        ?>
                        <td>
                            <div  class="date-time <?= $hideDateAll ?>"> 
                                <div class="date-str number-end <?= $hideDate ?>"><?= strftime("%d", strtotime($event['end'])) ?></div>
                                <div class="date-str">
                                    <span class="<?= $hideDate ?>"><?= strftime("%b", strtotime($event['end'])) ?></span>
                                    <b><?= strftime("%H:%M", strtotime($event['end'])) ?></b><br>
                                    <span class="<?= $hideDate ?>"><?= strftime("%Y", strtotime($event['end'])) ?></span>
                                </div>
                            </div>
                        </td>

                        <td><?= $event['summary']?> </td>
                        <?php
                        // подсчет чисел из основного поля
                        // summaryProp 0 - ничего не считает, 1 - суммирует, 2 - вычисляет среднее значение
                        $sumSummary = in_array(1, @$calendarDescription['settings']['summaryCalc']);
                        if ($sumSummary) {
                            if (isset($sum['summary'])) {
                               $sum['summary'] += (double)$event['summary'];
                            } else {
                                $sum['summary'] = (double)$event['summary'];
                            }
                        }
                        
                        if (is_array($calendarDescription)):
                            $dataDescription = json_decode($event['description'], true);
                            if (is_array($dataDescription)) { // если в поле description данные json то обрабатываем как надо
                                foreach($calendarDescription['data'] as $k => $v) {
                                    if (isset($sum[$k])) {
                                        $sum[$k] += (double)@$dataDescription[$k];
                                    } else {
                                        $sum[$k] = (double)@$dataDescription[$k];
                                    }
                                    switch ($v) {
                                        case 'url_image':
                                            echo "<td>";
                                            echo extractImg(@$dataDescription[$k]);
                                            echo "</td>";
                                            break;
                                        default:
                                            echo "<td>" . @$dataDescription[$k] . "</td>";
                                            break;
                                    }
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
                        <td><?= Html::a('', ['calendar/update-event', 'calendarId' => $event['calendar_id'], 'eventId' => $event['id']], ['class' => 'profile-link glyphicon glyphicon-cog']); ?></td>
                        <td><?= Html::a($i, ['calendar/update-event', 'calendarId' => $event['calendar_id'], 'eventId' => $event['id']], ['class' => '']); ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th></th>
                        <th></th>
                        <?php if (is_array($calendarDescription)) { ?>
                        <th><?= (@$sum['summary']) ? @$sum['summary'] : '' ?></th>
                        <?php
                        foreach($calendarDescription['data'] as $k => $v) {
                            echo '<th>';
                            if (@$sum[$k]) {
                                echo $sum[$k];
                            }
                            echo '</th>';
                        }
                        ?>
                        <?php } else { ?>
                        <th></th>
                        <?php } ?>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tbody>
            </table>
            <?php
//            echo '<pre>';
//            print_r($sum);
//            echo '</pre>'
            ?>
       
        </div>   
        </div>
    </div>

</div>
