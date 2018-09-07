<?php

namespace app\models;

use Yii;
use yii\base\Model;
use Google_Service_Calendar;

class Calendar extends Model
{
    public static function getCalendarList($service)
    {
        $result = [];
        
        $calendarList = $service->calendarList->listCalendarList();
        
        while (true) {
            foreach ($calendarList->getItems() as $calendarListEntry) {
//                $result[] = $calendarListEntry->getSummary();
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
//        echo '<pre>';
//        print_r($result);
//        exit;
        return $result;
    }
    
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
            $eventData['title'] = $event->getSummary();
            $eventData['description'] = $event->getDescription();
            $result[] = $eventData;
        }

        return $result;
    }

}
