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
<div class="contorl-head-calendar-events">
    <a href="<?= $url ?>year=<?= $year_prev ?>&month=<?= $month_prev ?>" class="glyphicon glyphicon-menu-left"></a>
    <strong class="year-month"><?= $month_arr[$month - 1] ?> <?= $year?></strong>
    <a href="<?= $url ?>year=<?= $year_next ?>&month=<?= $month_next ?>" class="glyphicon glyphicon-menu-right"></a>
</div>
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="smallModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Small Modal</h4>
            </div>
            <div class="modal-body">
                <div id="table"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <!--<button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div>
    </div>
</div>
<div id="calendar-tadev"></div>

<script>
function confirmDelete() {
    if (confirm("Вы подтверждаете удаление?")) {
            return true;
    } else {
            return false;
    }
}

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
    arrIdrec = arrIdrec.split(',');
    var calendarDescription = JSON.parse(getProperty(arrIdrec[0], 'calendar_description', dataEvents));
    if (calendarDescription) {
        var summary = calendarDescription.settings.summary;
    } else {
        var summary = '';
    }
    
    var color_events;
    var date;
    var title;
    var table = '<div class="table-responsive"><table class="table table-striped table-hover table-sm">';
    table += '<thead class="thead-inverse"><tr><th>#</th><th>' + summary + '</th><th>начало</th><th>конец</th>';
    try {
        if(calendarDescription.data) {
            for(key in calendarDescription.data) {
                table += '<th>' + key + '</th>';
            }
        } else {
            table += '<th>описание</th>';
        }

    } catch (e) {
        table += '<th>описание</th>';
    }
    
    table += '<th></th><th></th></tr></thead><tbody>';
    arrIdrec.forEach(function(item, i) {
        var rec = getProperty(item, 0, dataEvents);
        // Заголовок модального окна
        if (i === 0) {
            color_events = rec["calendar_backgroundColor"];
            date = rec["start"].substring(0, 10);
            title = '<span style="background-color: ' + color_events + '" class="circle-events"></span>';
            title += date + ' / ' + rec["calendar_summary"];
        }
        table += '<tr>';
        table += '<td>' + (i + 1) + '</td>';
        table += '<td>' + rec["summary"] + '</td>';
        table += '<td>' + rec["start"].substring(0,10) + ' ' + rec["start"].substring(11,16) + '</td>';
        table += '<td>' + rec["start"].substring(0,10) + ' ' + rec["start"].substring(11,16) + '</td>';

        // Получает строку с url изображений и отдает ссылки с картинками.
        // Строка разбивается в массив по символам перевода строки, допускается множественный перевод строк, лишняя итерация будет пропущена.
        function extractImg(strImg) {
            var imgArr = strImg.split('\n');
            console.log(imgArr);
            var result = '';
            for (urlImg in imgArr){
                if (!imgArr[urlImg]) continue;
                result += '<a target="_blank" href="' + imgArr[urlImg] + '" ><img class="event-img" src="' + imgArr[urlImg] + '"></a>';
            }
            return result;
        }

        try {
            var data = JSON.parse(rec["description"]);
            if(data) {
                var str = '';
                for(key in calendarDescription.data) {
                    switch(calendarDescription.data[key]) {
                        case 'url_image':
                            str += '<td>' + extractImg(data[key]) + '</td>';
                            break
                        default:
                            str += '<td>' + data[key] + '</td>';
                            break
                    }
                }
                if (str) {
                    table += str;
                } else {
                    table += '<td>' + rec["description"] + '</td>';
                }
            } else {
                table += '<td>';
                table += (rec["description"] === null) ? '' : rec["description"];
                table += '</td>';
            }
        } catch (e) {
            table += '<td>' + rec["description"] + '</td>';
        }
        table += '<td><a class="glyphicon glyphicon-cog" href="<?= $siteUrl ?>/calendar/update-event?calendarId=' + rec["calendar_id"] + '&eventId=' + rec["id"] + '"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
        table += '<a  onclick="return confirmDelete()" class="glyphicon glyphicon-remove-circle" href="<?= $siteUrl ?>/calendar/delete-event?calendarId=' + rec["calendar_id"] + '&eventId=' + rec["id"] + '"></a></td>';
        table += '</tr>';
    });
    table += '</tbody></table></div>'; 
    $("#eventModal .modal-body #table").html('').html(table);


    $("#eventModal .modal-title").html('').html(title);
//    $("#eventModal .modal-body h3").text('').text(dataEvent[0].result);
    $("#eventModal").modal('show');
}

var dataEvents = <?= json_encode($data_events) ?>;
console.log('dataEvents --- ', dataEvents);

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

    var calendar1 = '<tr>';
    // пустые клетки до первого дня текущего месяца
    if (D1Nfirst != 0) {
        for (var i = 1; i < D1Nfirst; i++)
            calendar1 += '<td class="empty-block"></td>';
    } else { // если первый день месяца выпадает на воскресенье, то требуется 7 пустых клеток 
        for (var i = 0; i < 6; i++)
            calendar1 += '<td class="empty-block"></td>';
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
        for (eventId in dataEventsDayObj) {
            var color_events = getProperty(dataEventsDayObj[eventId][0], 'calendar_backgroundColor', dataEvents);
            var sum = 0;
            dataEventsDayObj[eventId].forEach(function(item) {
                // Получить из настроек календаря поле, которое надо писать в span
//                try {
//                    var calendarDescription = JSON.parse(getProperty(item, 'calendar_description', dataEvents));
//                    var summary = calendarDescription.settings.summary;
//                    var description = JSON.parse(getProperty(item, 'description', dataEvents));
//                    var value = description[summary];
//                    sum += value*1;
//                } catch (e) {
                    // Если описание события не содержит строку json то берем из summary события
                    var value = getProperty(item, 'summary', dataEvents);
                    sum += value*1;
//                }
            });
            var titleEvents = getProperty(dataEventsDayObj[eventId][0], 'calendar_summary', dataEvents); // получаем название события для title при наведении
            // добавление букв названия события в зависимости от ширины календаря
            var letterTitle = 8;
            var widthTable = $('#calendar-tadev').width();
            if (widthTable < 1100) letterTitle = 6;
            if (widthTable < 930) letterTitle = 6;
            if (widthTable < 830) letterTitle = 5;
            if (widthTable < 730) letterTitle = 4;
            if (widthTable < 560) letterTitle = 3;
            if (widthTable < 430) letterTitle = 2;
            if (widthTable < 380) letterTitle = 1;
            
            // Если sum NaN то не выводим вообще результат суммирования
            if (isNaN(sum)) sum = 0;
            if (sum === 0) {
                sum = titleEvents.substring(0, 5);
            } else { 
                sum = titleEvents.substring(0, letterTitle) + " " + sum;
            }
            eventsList += '<span title="' + titleEvents + '" onclick="showEvent(&#39;' + dataEventsDayObj[eventId].join(',') + '&#39;)" class="event-item" style="background-color: ' + color_events + '">' + sum + '</span>';
        }

        var today = '';
        if (i == D1.getDate() &&  monthP === D1.getMonth() && yearP === D1.getFullYear())
            today = ' class="today"';

        calendar1 += '<td' + today + '><div class="main">';
        calendar1 += '<div class="date">' + i + '</div>' + eventsList;
        calendar1 += '</div></td>';

        if (new Date(yearP, monthP, i).getDay() == 0) {  // если день выпадает на воскресенье, то перевод строки
            calendar1 += '<tr>';
        }
    }

    // пустые клетки после последнего дня месяца
    if (D1Nlast != 0) {
        for (var i = D1Nlast; i < 7; i++)
            calendar1 += '<td class="empty-block"></td>';
    }


    var calendar2 = '<table><thead><tr><th>Пн</th><th>Вт</th><th>Ср</th><th>Чт</th><th>Пт</th><th>Сб</th><th>Вс</th></tr></thead>';
    calendar2 += '<tbody>' + calendar1 + '</tbody></table>';
    $("#calendar-tadev").append(calendar2);
      
// Таблица после календаря
     // создать объект с событиями и цветом, которые есть в месяце
     var arrEventsNameColor = {};
     for (var i = 0; i < dataEvents.length; i++) {
         arrEventsNameColor[dataEvents[i].event_id] = {
             'title_events': dataEvents[i].title_events,
             'color_events': dataEvents[i].color_events
         }
     }
 
     //var table = '<div class="table-responsive"><br><table class="table table-striped"><thead><tr><th>#</th><th>Название</th><th>Рез</th><th>ср</th><th>УЕ</th><th>ср</th><th>Зап</th><tr></thead>';
     var table = '<div class="table-responsive table-list-events"><br><table class="table table-striped"><thead><tr><th><span class="circle-events all"></span></th><th>Название</th><th>Рез</th><th>УЕ</th><th>Зап</th><tr></thead>';
     for (key in arrEventsNameColor) {
         table += '<tr>';
         table += '<td><span style="background-color: ' + arrEventsNameColor[key].color_events + '" class="circle-events" data-title="' + arrEventsNameColor[key].title_events + '"><span></td>';
         table += '<td><a href="/main/recnote.html?event_id=' + key +'">';
         table += arrEventsNameColor[key].title_events;
         table += '</a></td>';
         var eventsArrRec = dataEvents.filter(function(data) {
             return (data.event_id == key);
         });
         var eventResult = eventsArrRec.reduce(function(sum, current) {
             return sum + current.result*1;
         }, 0);
         var eventPrice = eventsArrRec.reduce(function(sum, current) {
             return sum + current.price*1;
         }, 0);
         
         // var evLength = eventsArrRec.length;
         table += '<td>';
         if (eventResult > 0) {
             table += eventResult;
         } else {
             table += '-';
         }
         table += '</td>';
         // среднее значение Результата
         //table += '<td>';
         //if (eventResult > 0) {
         //    table += (eventResult/evLength).toFixed(1);
         //} else {
         //    table += '-';
         //}
         //table += '</td>';
         table += '<td>';
         if (eventPrice > 0) {
             table += eventPrice;
         } else {
             table += '-';
         }
         table += '</td>';
         // среднее значение УЕ
         //table += '<td>';
         //if (eventPrice > 0) {
         //    table += (eventPrice/evLength).toFixed(1);
         //} else {
         //    table += '-';
         //}
         //table += '</td>';
         table += '<td>';
         table += eventsArrRec.length;
         table += '</td>';
         table += '</tr>';
     }
     table += '</table></div>';
//     $("#calendar-tadev").after(table);
     
     
     // скрывает все записи в календаке кроме записей событи title
     function hideRecEvent(title) {
         $('#calendar-tadev .event-item').show();
         $('#calendar-tadev .event-item').each(function() {
             if (title != $(this).attr('title')) {
                 $(this).hide();
             }
         });
     }
     
     // показывает все записи (если они были скрыты фукцией hideRecEvent(title)
     function showRecEvent() {
         $('#calendar-tadev .event-item').show();
     }
     
     $('.table-list-events .circle-events').click(function(){
         hideRecEvent($(this).attr('data-title'));
     });
     
     $('.table-list-events .circle-events.all').click(function(){
         showRecEvent();
     });
     
     $('#calendar-tadev .event-item').hover(
     function() {
         var title =  $(this).attr('title');
         $('#calendar-tadev .event-item').each(function() {
             if (title != $(this).attr('title')) {
                 $(this).css('visibility', 'hidden');
             }
         });
     }, function() {
        $('#calendar-tadev .event-item').each(function() {
             $(this).css('visibility', 'visible');
         });
     }
   );
  
      
 });
</script>