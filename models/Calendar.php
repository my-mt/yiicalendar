<?php

namespace app\models;

use Yii;
use yii\base\Model;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class Calendar extends Model
{
    // Получить список календарей
    public static function getCalendarList($service)
    {
        $result = [];
        
        $calendarList = $service->calendarList->listCalendarList();
        
        while (true) {
            foreach ($calendarList->getItems() as $calendarListEntry) {
                $result[] = $calendarListEntry;
            }
            $pageToken = $calendarList->getNextPageToken();
            if ($pageToken) {
                $optParams = array('pageToken' => $pageToken);
                $calendarList = $service->calendarList->listCalendarList($optParams);
            } else {
                break;
            }
        }
        return $result;
    }
    
    // Получить список событий каледаря $calendar за период времени
    // $timeMin, $timeMax '2018-02-12T15:19:21+00:00'
    public static function getListEvents($service, $calendar, $timeMin, $timeMax, $maxResults = 100)
    {
        $result = [];
        
        $optParams = array(
            'maxResults' => $maxResults,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
        );
        
        $eventsData = $service->events->listEvents($calendar->id, $optParams);
        $events = $eventsData->getItems();
        
        foreach ($events as $event) {
            $start = $event->start->dateTime;
            if (empty($start)) {
                $start = $event->start->date;
            }
            $end = $event->end->dateTime;
            if (empty($end)) {
                $end = $event->end->date;
            }
            $eventData['calendar_description'] = $calendar->description;
            $eventData['calendar_summary'] = $calendar->summary;
            $eventData['calendar_backgroundColor'] = $calendar->backgroundColor;
            $eventData['calendar_id'] = $calendar->id;
            $eventData['id'] = $event->id;
            $eventData['start'] = $start;
            $eventData['end'] = $start;
            $eventData['summary'] = $event->getSummary();
            $eventData['description'] = $event->getDescription();
            $result[] = $eventData;
        }

        return $result;
    }

    // Получить свойства календаря
    public static function getCalendar($id)
    {   
        $service = new Google_Service_Calendar(User::getClient());
        return $service->calendars->get($id);    
    }
    
    // Получить свойства события
    public static function getEvent($calendarId, $eventId)
    {   
        $service = new Google_Service_Calendar(User::getClient());
        return $service->events->get($calendarId, $eventId);    
    }
    
    // Редактирование календаря
    public static function updateCalendar($id, $summary, $description)
    {   
        $service = new Google_Service_Calendar(User::getClient());
        $calendar = $service->calendars->get($id);
        $calendar->setSummary($summary);
        $calendar->setDescription($description);
        $updatedCalendar = $service->calendars->update($id, $calendar);

        return $updatedCalendar->getEtag();
    }
    
    // Редактирование события
    public static function updateEvent($calendarId, $eventId, $summary, $description)
    {   
        $service = new Google_Service_Calendar(User::getClient());
        $event = $service->events->get($calendarId, $eventId);
        $event->setSummary($summary);
        $event->setDescription($description);
        $updatedEvent = $service->events->update($calendarId, $event->getId(), $event);

        return $updatedEvent->getUpdated();
    }
    
    // Создание события
    public static function insertEvent($calendarId)
    {   
        $service = new Google_Service_Calendar(User::getClient());
        $event = new Google_Service_Calendar_Event(array(
          'summary' => 'Google I/O 2015',
          'location' => '800 Howard St., San Francisco, CA 94103',
          'description' => 'A chance to hear more about Google\'s developer products.',
          'start' => array(
            'dateTime' => '2018-09-14T09:00:00-07:00',
            'timeZone' => 'America/Los_Angeles',
          ),
          'end' => array(
            'dateTime' => '2018-09-14T17:00:00-07:00',
            'timeZone' => 'America/Los_Angeles',
          ),
          'recurrence' => array(
            'RRULE:FREQ=DAILY;COUNT=2'
          ),
          'attendees' => array(
            array('email' => 'lpage@example.com'),
            array('email' => 'sbrin@example.com'),
          ),
          'reminders' => array(
            'useDefault' => FALSE,
            'overrides' => array(
              array('method' => 'email', 'minutes' => 24 * 60),
              array('method' => 'popup', 'minutes' => 10),
            ),
          ),
        ));

        $calendarId = $calendarId;
        $event = $service->events->insert($calendarId, $event);
        return $event->htmlLink;
    }

}
