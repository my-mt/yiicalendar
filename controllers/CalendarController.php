<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use Google_Client;
use Google_Service_Calendar;
use app\models\User;
use app\models\Calendar;
use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Photos\Library\V1\PhotosLibraryClient;
use Google\Photos\Library\V1\PhotosLibraryResourceFactory;

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

        $monthStart = Yii::$app->request->get('monthStart');
        $yearStart = Yii::$app->request->get('yearStart');

        $monthEnd = Yii::$app->request->get('monthEnd');
        $yearEnd = Yii::$app->request->get('yearEnd');


        // $monthStart = '01';
        // $yearStart ='2019';

        // $monthEnd = '03';
        // $yearEnd = '2019';


        if (!$monthStart || !$yearStart || !$monthEnd || !$yearEnd ) {
        // если данные не переданы, то выводим текущий месяц
            if (!$monthStart || !$yearStart) {
                $monthStart = date('m', time());
                $yearStart = date('Y', time());
            }
            if ($monthStart == '12') {
            $monthEnd = 1;
            $yearEnd = $yearStart + 1;
            } else {
                $monthEnd = $monthStart + 1;
                $yearEnd = $yearStart;
            }
            if ($monthEnd < 10) {
                $monthEnd = '0' . $monthEnd;
            }
        }


        $timeMin = $yearStart.'-'.$monthStart.'-01T00:00:00+03:00';
        $timeMax = $yearEnd.'-'.$monthEnd.'-01T00:00:00+03:00';

//        $request = Yii::$app->request;
//        $calendarId = $request->get('id');

        $count = 1000; // ограничение по количеству записей

        $listEvents = [];
        foreach($calendarList as $calendar){
            $eventData = Calendar::getListEvents($service, $calendar, $timeMin, $timeMax, $count);
            $listEvents = array_merge($listEvents, $eventData);
        }

        return $this->render('index', [
            'calendarList' => $calendarList,
            'listEvents' => $listEvents,
            'yearStart' => (int)$yearStart,
            'monthStart' => (int)$monthStart,
            'yearEnd' => (int)$yearEnd,
            'monthEnd' => (int)$monthEnd,
        ]);
    }

    // вызывается в соответствии с конфигурационным файлом goole
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
            // если переход по ссылке для добавления записи события, то перенаправляем на форму
            if ($url = $_SESSION['before_url']) {
                $_SESSION['before_url'] = false;
                return $this->redirect($url);
            } else {
                return $this->redirect(['index']);
            }
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

        $calendarList = Calendar::getSimpleCalendarList();

        return $this->render('calendar-form', [
            'id' => $id,
            'summary' => $calendar->summary,
            'description' => $calendar->description,
            // 'calendar' => $calendar,
            'calendarList' => $calendarList,
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
        $calendarFormatVersion = @$calendarDescription->formatVersion;

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
            'calendarSettings' => $calendarSettings,
            'calendarFormatVersion' => $calendarFormatVersion,
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
        $calendarFormatVersion = @$calendarDescription->formatVersion;

        $dateStart = date('Y-m-d', time());
        $dateEnd = $dateStart;

        $timeStart = date('H:i:s', time());
        $timeEnd = $timeStart;

        if (@$calendarSettings->simpleMode[0] == 1) {
            $mode = '-simple';
        } else {
            $mode = '';
        }

        return $this->render('event-form' . $mode, [
            'calendarId' => $calendarId,
            'calendarSetSummary' => $calendarSetSummary, // настройки календаря - название основного поля (summary события)
            'calendarSummary' => $calendar->summary,
            'calendarFields' => $calendarFields,

            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'timeStart' => $timeStart,
            'timeEnd' => $timeEnd,
            'calendarSettings' => $calendarSettings,
            'calendarFormatVersion' => $calendarFormatVersion,
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

        $monthStart = Yii::$app->request->get('monthStart');
        $yearStart = Yii::$app->request->get('yearStart');
        $monthEnd = Yii::$app->request->get('monthEnd');
        $yearEnd = Yii::$app->request->get('yearEnd');

        if (!$monthStart || !$yearStart || !$monthEnd || !$yearEnd ) {
        // если данные не переданы, то выводим текущий месяц
            if (!$monthStart || !$yearStart) {
                $monthStart = date('m', time());
                $yearStart = date('Y', time());
            }
            if ($monthStart == '12') {
            $monthEnd = 1;
            $yearEnd = $yearStart + 1;
            } else {
                $monthEnd = $monthStart + 1;
                $yearEnd = $yearStart;
            }
            if ($monthEnd < 10) {
                $monthEnd = '0' . $monthEnd;
            }
        }

        $timeMin = $yearStart.'-'.$monthStart.'-01T00:00:00+03:00';
        $timeMax = $yearEnd.'-'.$monthEnd.'-01T00:00:00+03:00';

        $calendar = Calendar::getCalendar($id);

        $service = new Google_Service_Calendar($client);
        $count = 1000; // ограничение по количеству записей
        $dataEvents = Calendar::getListEvents($service, $calendar, $timeMin, $timeMax, $count);

        // сортировка данных по убыванию
        usort($dataEvents, function ($a, $b) {
            return $a['start'] < $b['start'];
        });

        return $this->render('calendar-events', [
            'calendarDescription' => json_decode($calendar->description, true),
            'dataEvents' => $dataEvents,
            'yearStart' => (int)$yearStart,
            'monthStart' => (int)$monthStart,
            'yearEnd' => (int)$yearEnd,
            'monthEnd' => (int)$monthEnd,
            'id' => $id,
        ]);
    }

    public function actionTest()
    {
        // $photosLibraryClient = User::getClient();

        // $client->setScopes('https://www.googleapis.com/auth/photoslibrary.appendonly');

        // $data = [
        //     'client_id' => '254365449149-dhrcpfstscpq93htnf1o6v23ut6vjtaa.apps.googleusercontent.com',
        //     'client_secret' => 'ZTzrDgLmMpyDJ4ro3QKJ4yYO',
        //     'refresh_token' => $client->getRefreshToken(),
        // ];

        // echo '<pre>';
        // print_r($photosLibraryClient);
        // echo '</pre>';


        // Use the OAuth flow provided by the Google API Client Auth library
        // to authenticate users. See the file /src/common/common.php in the samples for a complete
        // authentication example.
        // $authCredentials = new UserRefreshCredentials( 'https://www.googleapis.com/auth/photoslibrary.appendonly', $data );

        // Set up the Photos Library Client that interacts with the API
        // $photosLibraryClient = new PhotosLibraryClient(['credentials' => $client->getAccessToken()]);


        //echo '<pre>';
        //print_r($photosLibraryClient);
        //echo '</pre>';
        //exit;

        // Create a new Album object with at title
        // $newAlbum = PhotosLibraryResourceFactory::album("My Album");

        // // Make the call to the Library API to create the new album
        // $createdAlbum = $photosLibraryClient->createAlbum($newAlbum);

        // // The creation call returns the ID of the new album
        // $albumId = $createdAlbum->getId();


        // $authCredentials = new UserRefreshCredentials('254365449149-dhrcpfstscpq93htnf1o6v23ut6vjtaa.apps.googleusercontent.com');
        // $photosLibraryClient = new PhotosLibraryClient(['credentials' => $authCredentials]);
        echo 'test';
        exit;
    }

}
