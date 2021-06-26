<?php
namespace app;
use Yii;

$siteUrl = Yii::$app->params['siteUrl'];

$month_arr = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];

switch ($month) {
    case 1:
        $year_prev = $year - 1;
        $month_prev = 12;
        $year_next = $year;
        $month_next = $month + 1;
        break;
    case 12:
        $year_prev = $year;
        $month_prev = $month - 1;
        $year_next = $year + 1;
        $month_next = 1;
        break;
    default:
        $year_prev = $year;
        $month_prev = $month - 1;
        $year_next = $year;
        $month_next = $month + 1;
}

?>
<div>
    <h4><?= $year . ' ' . $month_arr[(int)$month - 1] ?></h4>
    <div id="calendar-tadev-<?= $year . '-' . $month ?>"></div>
</div>

<script>

// функция, которая будет выдавать любое property по id записи (которое так же является свойством объекта - элемента массива данных data
function getProperty(id, property = 0, data) {
    var arr = data.filter(function(item) {
        return (item.id == id);
    });
    if (property === 0) return arr[0];
    return arr[0][property];
}

// получаем строку arrIdrec с id записей
// выводит модальное окно с таблицей записей
function showEvent(arrIdrec) {
   console.log("Выводит модальное окно с таблицей записей --- ");
}

var dataEvents = <?= json_encode($data_events) ?>;

// функция строит календарь в блоке с id="calendar-tadev"
$(function() {
    var D1 = new Date();
    var month = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"]; // название месяца, вместо цифр 0-11

// Определить месяц, который будем выводить - Апрель-3 2018г.
    var monthP = <?= $month - 1 ?>;
    var MonthStr = (monthP + 1 < 10) ? '0' + (monthP + 1) : monthP + 1;
    var yearP = <?= $year ?>;
    var D1last = new Date(yearP, monthP + 1, 0).getDate(); // последний день месяца
    var D1Nlast = new Date(yearP, monthP, D1last).getDay(); // день недели последнего дня месяца
    var D1Nfirst = new Date(yearP, monthP, 1).getDay(); // день недели первого дня месяца

    var styleRow = '{height: 70px; text-align: right;}. ';
    var calendar1 = '';
    // пустые клетки до первого дня текущего месяца
    if (D1Nfirst != 0) {
        for (var i = 1; i < D1Nfirst; i++)
            calendar1 += '| ';
    } else { // если первый день месяца выпадает на воскресенье, то требуется 7 пустых клеток
        for (var i = 0; i < 6; i++)
            calendar1 += '| ';
    }

    // дни месяца
    for (var i = 1; i <= D1last; i++) {
        var curDayStr = (i < 10) ? '0' + i : i;
        var curDateStr = yearP + '-' + MonthStr + '-' + curDayStr;

        // отфильровываем события текущей даты
        var dataEventsDay = dataEvents.filter(function(data) {
            return (data.start.substring(0, 10) == curDateStr);
        });


        // Объект, {id-event:[id-recnote,...],...}
        var dataEventsDayObj = {};

        dataEventsDay.forEach(function(item) {
            if (!Array.isArray(dataEventsDayObj[item.calendar_id]))
                dataEventsDayObj[item.calendar_id] = [];
            dataEventsDayObj[item.calendar_id].push(item.id);
        });

        var eventsList = '';
        var timeStartEnd = '';
        for (eventId in dataEventsDayObj) {
        	const start = getProperty(dataEventsDayObj[eventId][0], 'start', dataEvents).substring(11, 16);
        	const end = getProperty(dataEventsDayObj[eventId][0], 'end', dataEvents).substring(11, 16);
        	timeStartEnd += `${start} - ${end}`
    	}

    	const styleCell = timeStartEnd ? '{background:#628DB6; color:#fff;}' : '';
    	const addTime = timeStartEnd ? "\n&amp;nbsp;\n" : '';
        calendar1 +=  `|^${styleCell}. ${i}` + addTime;
        calendar1 += timeStartEnd;

        if (new Date(yearP, monthP, i).getDay() == 0) {  // если день выпадает на воскресенье, то перевод строки
            calendar1 += "|\n" + styleRow;
        }
    }

    // пустые клетки после последнего дня месяца
    if (D1Nlast != 0) {
        for (var i = D1Nlast; i < 7; i++)
            calendar1 += '| ';
        calendar1 += '|';
    }


    var calendar2 = '|_{width: 90px;}. Пн |_{width: 90px;}. Вт |_{width: 90px;}. Ср |_{width: 90px;}. Чт |_{width: 90px;}. Пт |_{width: 90px;}. Сб |_{width: 90px;}. Вс |' + "\n" + styleRow;
    $("#calendar-tadev-<?= $year . '-' . $month ?>").append('<pre>' + calendar2 + calendar1 + '</pre>');

 });
</script>