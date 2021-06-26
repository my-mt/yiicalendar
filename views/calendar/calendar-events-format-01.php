<?php
namespace app\components;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\base\Widget;

/* @var $this yii\web\View */

$this->title = 'Calendar-events';
$monthStartNav = $monthStart;
$yearStartNav = $yearStart;
$month_arr = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
if ($_GET['nv'] == 1) {

    usort($dataEvents, function($a,$b){
        return $a['start'] > $b['start'];
    });

    $result = '';

    $arrDay = [
        'Mon' => 'Понедельник',
        'Tue' => 'Вторник',
        'Wed' => 'Среда',
        'Thu' => 'Четверг',
        'Fri' => 'Пятница',
        'Sat' => 'Суббота',
        'Sun' => 'Воскресенье',
    ];

    foreach ($dataEvents as $v) {
        $result .= ' | ';
        $result .= date_format(date_create_from_format('Y-m-d', substr($v['start'], 0, 10)), 'd.m.Y');
        $result .= ' | ' . $arrDay[date_format(date_create_from_format('Y-m-d', substr($v['start'], 0, 10)), 'D')];
        $result .= ' | ' . substr($v['start'], 11, 5) . ' - ' . substr($v['end'], 11, 5);
        $result .= ' | ';
        $result .= '<br>';
    }

    echo $result;
}



?>

<div class="body-content">
    <h3><?= @$dataEvents[0]['calendar_summary'] ?></h3>

    <h4><?= Html::a('Добавить событие <span class="profile-link glyphicon glyphicon-plus-sign"></span>', ['calendar/insert-event', 'calendarId' => $id], ['class' => '']) ?></h4>


    <?php
    $urlAction = Url::toRoute(['calendar/calendar-events']);
    $eventFilterId = $id;
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
        $calendarHtml .= '<div class="calendar-single">';
        $calendarHtml .= Calendarevents_singleWidget::widget(['year' => $yearStart, 'month' => $monthStart, 'data_events' => $dataEvents]);
        $calendarHtml .= '</div>';
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
        <?= Html::a('', ['calendar/calendar-events', 'id' => $id, 'monthStart' => $monthBack, 'yearStart' => $yearBack], ['class' => 'glyphicon glyphicon-chevron-left']) ?>
        <?= $yearStartNav . ' ' . $month_arr[(int)$monthStartNav - 1] ?>
        <?= Html::a('', ['calendar/calendar-events', 'id' => $id, 'monthStart' => $monthNext, 'yearStart' => $yearNext], ['class' => 'glyphicon glyphicon-chevron-right']) ?>
    </div>
    <?php } ?>

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" data-toggle="tab" href="#calendar-tab" role="tab">Календарь</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#table-tab" role="tab">Таблица</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="calendar-tab" role="tabpanel">
            <div class="row">
                <div class="col-xs-12">
                    <div class="calendar-events-single">
                        <?php echo $calendarHtml ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="table-tab" role="tabpanel">
            <div class="row">
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
                                            <?php if (strlen($event['start']) < 11) { ?>
                                            <div class="date-str"><?= strftime("<b>весь день</b><br><small>%b %Y</small>", strtotime($event['start'])) ?></div>
                                            <?php } else { ?>
                                            <div class="date-str"><?= strftime("<b>%H:%M</b><br><small>%b %Y</small>", strtotime($event['start'])) ?></div>
                                            <?php } ?>
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
                                                <b><?= strftime("%H:%M", strtotime($event['end'])) ?></b><br>
                                                <small class="<?= $hideDate ?>"><?= strftime("%b", strtotime($event['end'])) ?></small>
                                                <small class="<?= $hideDate ?>"><?= strftime("%Y", strtotime($event['end'])) ?></small>
                                            </div>
                                        </div>
                                    </td>

                                    <td><?= $event['summary']?></td>
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
                                                        echo "<td>" . str_replace("\n", "<br>", @$dataDescription[$k]) . "</td>";
                                                        break;
                                                }
                                            }
                                            echo "<td></td>";
                                        } else {  // если нет, то выводим пустые столбцы
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
                                </tr>
                                <?php } ?>
                                <tr>
                                    <th>Записей: <?= count($dataEvents) ?></th>
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
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
