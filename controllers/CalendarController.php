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
        $service = new Google_Service_Calendar($client);
        
        // Получаем список календарей
        $calendarList = Calendar::getCalendarList($service);
        
        $timeMin = '2018-09-01T00:00:00+00:00';
        $timeMax = '2018-10-01T00:00:00+00:00';
        
//        $request = Yii::$app->request;
//        $calendarId = $request->get('id');

        $listEvents = [];
        foreach($calendarList as $calendar){
            $eventData = Calendar::getListEvents($service, $calendar, $timeMin, $timeMax, 100);
            $listEvents = array_merge($listEvents, $eventData);
        }
  
//        echo '<pre>';
//        print_r($listEvents);
//        echo '</pre>';
//        exit;
        
        return $this->render('index', [
            'calendarList' => $calendarList,
            'listEvents' => $listEvents
        ]);
    }

    public function actionLogin()
    {
//        echo '<pre>';
//        print_r($_GET);
//        echo '</pre>';
        
        $clientSecrets = '../config/api/client_secrets.json';
        if (!file_exists($clientSecrets)) 
            return;
        
        $client = new Google_Client();
        $client->setApplicationName('Google Calendar API PHP my test');
        $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
        $client->setAuthConfig($clientSecrets);
        $client->setAccessType('offline');
        
        $client->authenticate($_GET['code']);

        $_SESSION['access_token'] = $client->getAccessToken();
        return $this->goHome();
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
            $calendarId = $data['calendarId'];
            $eventId = $data['eventId'];
            $summary = $data['summary'];
            $description = $data['description'];
            if ($Etag = Calendar::updateEvent($calendarId, $eventId, $summary, $description)) {
                Yii::$app->session->setFlash('success', "Успех,  Etag=$Etag");
            }
        };

        $event = Calendar::getEvent($calendarId, $eventId);
        
        $calendar = Calendar::getCalendar($calendarId);
        $calendarDescription = @json_decode($calendar->description);
        $calendarSetSummary = @$calendarDescription->settings->summary;
        $calendarFields = @$calendarDescription->data;
        
        $dateStart = ($event->start->date) ? $event->start->date : substr($event->start->dateTime, 0, 10);
        $dateEnd = ($event->end->date) ? $event->end->date : substr($event->end->dateTime, 0, 10);
        
        $timeStart = substr($event->start->dateTime, 11, 5);
        $timeEnd = substr($event->end->dateTime, 11, 5);
     
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
        ]);
    }
    
    public function actionInsertEvent($calendarId)
    {

        echo Calendar::insertEvent($calendarId);
        exit;
    }

}
