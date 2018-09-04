<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use Google_Client;
use Google_Service_Calendar;
use app\models\User;

class CalendarController extends Controller
{
    
    public function actionIndex()
    {
        $client = User::getClient();
        
        echo $client->getRefreshToken();
//        echo '<pre>';
//        print_r($client);
//        echo '</pre>';
        
        $service = new Google_Service_Calendar($client);
        
        // Print the next 10 events on the user's calendar.
        $calendarId = 'primary';
// $calendarId = 'tjgn23029jreccmvite9g70hmo@group.calendar.google.com';
        $optParams = array(
            'maxResults' => 100,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => '2018-02-12T15:19:21+00:00',
            'timeMax' => '2018-12-12T15:19:21+00:00',
                // 'timeMin' => date('c'),
        );
// https://developers.google.com/calendar/v3/reference/events/list
        $results = $service->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();
        
//        echo '==<pre>';
//        print_r($events);
//        echo '</pre>';

        if (empty($events)) {
            print "No upcoming events found.\n";
        } else {
            print "Upcoming events:\n";
            foreach ($events as $event) {
                $start = $event->start->dateTime;
                if (empty($start)) {
                    $start = $event->start->date;
                }
                printf("%s (%s)\n%s\n", $event->getSummary(), $start, $event->getDescription());
                echo '<br>';
            }
        }
        exit;
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
