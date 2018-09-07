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
        echo $client->getRefreshToken();
        echo '<br>';
        echo 'redirect ???';
//        echo '==<pre>';
//        print_r($client);
//        echo '</pre>';
        exit;
    }

}
