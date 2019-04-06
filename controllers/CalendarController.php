<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use Google_Client;
use Google_Service_Calendar;
use app\models\User;
use app\models\Calendar;

class CalendarController extends Controller
{
    
    public function actionIndex()
    {
        $client = User::getClient();
        
        if (!$client) {
            echo '!$client';
            exit;
        }
        
        $service = new Google_Service_Calendar($client);

        // Получаем список календарей
        $calendarList = Calendar::getCalendarList($service);
        
        $month = Yii::$app->request->get('month');
        $year = Yii::$app->request->get('year');
        
        if(!$month || !$year) {
            $month = date('m', time());
            $year = date('Y', time());
        }
        
        if ($month == '12') {
            $monthEnd = 1;
            $yearEnd = $year + 1;
        } else {
            $monthEnd = $month + 1;
            $yearEnd = $year;
        }
        if ($monthEnd < 10)
            $monthEnd = '0' . $monthEnd;

        $timeMin = $year.'-'.$month.'-01T00:00:00+03:00';
        $timeMax = $yearEnd.'-'.$monthEnd.'-01T00:00:00+03:00';
        
//        $request = Yii::$app->request;
//        $calendarId = $request->get('id');

        $listEvents = [];
        foreach($calendarList as $calendar){
            $eventData = Calendar::getListEvents($service, $calendar, $timeMin, $timeMax, 1000);
            $listEvents = array_merge($listEvents, $eventData);
        }
        
        return $this->render('index', [
            'calendarList' => $calendarList,
            'listEvents' => $listEvents,
            'year' => $year,
            'month' => $month,
        ]);
    }

    public function actionLogin()
    {
        $clientSecrets = __DIR__ . '/../config/api/client_secrets.json';
        if (!file_exists($clientSecrets)) {
            echo '!file_exists';
            echo '<br>';
            echo $clientSecrets;
            exit;
        }

        $client = new Google_Client();
        $client->setApplicationName('Google Calendar API PHP my test');
        $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
        $client->setAuthConfig($clientSecrets);
        $client->setAccessType('offline');
        
        $client->authenticate($_GET['code']);

        $_SESSION['access_token'] = $client->getAccessToken();
        
        // если доступен getRefreshToken() это значит, что это первый вход после разрешения доступа пользователя  к своему аккаунту. В этом случае делаем переход на главную.
        // если refreshToken отсутствует, то это значит, что просто закончился срок действия access_token (примерно 3600 c), в этом случае возвращаемся на предыдущую страницу.
        
        

        if ($client->getRefreshToken()) {
            Yii::$app->session->setFlash('warning', 'Доступ к календарю предоставлен<br> ' . 'RefreshToken' . $client->getRefreshToken());
            return $this->goHome();
        } else {
            //return $this->goHome();
            Yii::$app->session->setFlash('success', 'Вход успешно осуществлен');
            return $this->redirect(['index']);
            //return $this->redirect(Yii::$app->request->referrer);
        }
    }
    
    public function actionUpdateCalendar($id)
    {
        if ($calendar = Yii::$app->request->post()) {
            $id = $calendar['id'];
            $summary = $calendar['summary'];
            $description = $calendar['description'];
            if ($Etag = Calendar::updateCalendar($id, $summary, $description)) {
                Yii::$app->session->setFlash('success', "Успех,  Etag=$Etag");
            }
        };

        $calendar = Calendar::getCalendar($id);
        
        return $this->render('calendar-form', [
            'id' => $id,
            'summary' => $calendar->summary,
            'description' => $calendar->description,
        ]);
    }
    
    public function actionUpdateEvent($calendarId, $eventId)
    {
        if ($data = Yii::$app->request->post()) {
            if ($event = Calendar::updateEvent($data)) {
                Yii::$app->session->setFlash('success', "Успех,  <a href='$event[1]'>Ссылка на событие в Google calendar</a>");
                //return $this->redirect(['calendar/update-event', 'calendarId' =>  $data['calendarId'], 'eventId' => $event[0]]);
                return $this->redirect(['calendar/calendar-events', 'id' =>  $data['calendarId']]);
            }
        };

        $event = Calendar::getEvent($calendarId, $eventId);

        
        $calendar = Calendar::getCalendar($calendarId);
        $calendarDescription = @json_decode($calendar->description);
        $calendarSetSummary = @$calendarDescription->settings->summary;
        $calendarSettings = @$calendarDescription->settings;
        $calendarFields = @$calendarDescription->data;
        
        $dateStart = ($event->start->date) ? $event->start->date : substr($event->start->dateTime, 0, 10);
        $dateEnd = ($event->end->date) ? $event->end->date : substr($event->end->dateTime, 0, 10);
        
        $timeStart = substr($event->start->dateTime, 11, 5);
        $timeEnd = substr($event->end->dateTime, 11, 5);
        
                
//        echo '<pre>';
//        print_r($event->attachments);
//        exit;
     
        return $this->render('event-form', [
            'calendarId' => $calendarId,
            'calendarSetSummary' => $calendarSetSummary, // настройки календаря - название основного поля (summary события)
            'calendarSummary' => $calendar->summary,
            'calendarFields' => $calendarFields,
            'eventId' => $eventId,
            'event' => $event,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'timeStart' => $timeStart,
            'timeEnd' => $timeEnd,
            'calendarSettings' => $calendarSettings,
        ]);
    }
    
    public function actionInsertEvent($calendarId)
    {
         if ($data = Yii::$app->request->post()) {
            if ($event = Calendar::insertEvent($data)) {
                Yii::$app->session->setFlash('success', "Успех,  <a href='$event[1]'>Ссылка на событие в Google calendar</a>");
                //return $this->redirect(['calendar/update-event', 'calendarId' =>  $data['calendarId'], 'eventId' => $event[0]]);
                return $this->redirect(['calendar/calendar-events', 'id' =>  $data['calendarId']]);
            }
        };

        $calendar = Calendar::getCalendar($calendarId);
        $calendarDescription = @json_decode($calendar->description);
        $calendarSetSummary = @$calendarDescription->settings->summary;
        $calendarSettings = @$calendarDescription->settings;
        $calendarFields = @$calendarDescription->data;
        
        $dateStart = date('Y-m-d', time());
        $dateEnd = $dateStart;
        
        $timeStart = date('H:i:s', time());
        $timeEnd = $timeStart;
     
        return $this->render('event-form', [
            'calendarId' => $calendarId,
            'calendarSetSummary' => $calendarSetSummary, // настройки календаря - название основного поля (summary события)
            'calendarSummary' => $calendar->summary,
            'calendarFields' => $calendarFields,

            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'timeStart' => $timeStart,
            'timeEnd' => $timeEnd,
            'calendarSettings' => $calendarSettings,
        ]);
    }
    
    public function actionDeleteEvent($calendarId, $eventId)
    {
        echo Calendar::deleteEvent($calendarId, $eventId);
        Yii::$app->session->setFlash('success', "Запись удалена");
        return $this->redirect(['calendar/calendar-events', 'id' =>  $calendarId]);
        // return $this->redirect(Yii::$app->request->referrer);
    }

    public static function arrayToStr($arr, $pad = '')
    {
        $str = '';
        foreach ($arr as $k => $v) {
            if (is_object($v) || is_array($v)) {
                $str .= $pad . $k . "<br>" . self::arrayToStr($v, $pad . '&nbsp&nbsp&nbsp&nbsp&nbsp');
            } else {
                $str .= $pad . $k . ' => ' . $v . "<br>";
            }
        }
        return $str;
    }
    
    public function actionCalendarEvents($id)
    {
        $client = User::getClient();
        
        if (!$client) {
            echo '!$client';
            exit;
        }

        
        $month = Yii::$app->request->get('month');
        $year = Yii::$app->request->get('year');
        $count = Yii::$app->request->get('count');

        if ($count) {
            $timeMin = '1900-01-01T00:00:00+03:00';
            $timeMax = '2100-01-01T00:00:00+03:00';
            $calendarView = false;
        } else {
            $count = 1000;
            $calendarView = true;

            if(!$month || !$year) {
                $month = date('m', time());
                $year = date('Y', time());
            }
            
            if ($month == '12') {
                $monthEnd = 1;
                $yearEnd = $year + 1;
            } else {
                $monthEnd = $month + 1;
                $yearEnd = $year;
            }
            if ($monthEnd < 10)
                $monthEnd = '0' . $monthEnd;

            $timeMin = $year.'-'.$month.'-01T00:00:00+03:00';
            $timeMax = $yearEnd.'-'.$monthEnd.'-01T00:00:00+03:00';
        }

        $calendar = Calendar::getCalendar($id);
      
        $service = new Google_Service_Calendar($client);
        $dataEvents = Calendar::getListEvents($service, $calendar, $timeMin, $timeMax, $count);
        
        // сортировка данных по убыванию
        usort($dataEvents, function ($a, $b) {
            return $a['start'] < $b['start'];
        });
        
        return $this->render('calendar-events', [
            'calendarDescription' => json_decode($calendar->description, true),
            'dataEvents' => $dataEvents,
            'year' => $year,
            'month' => $month,
            'id' => $id,
            'calendarView' => $calendarView, 
        ]);
    }
    
}
