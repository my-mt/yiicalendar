<?php

// echo '<pre>';
// print_r($calendarDescription);
// echo '</pre>';

if (!isset($calendarDescription['formatVersion']) || $calendarDescription['formatVersion'] != "02") {
    require (__DIR__  . '/calendar-events-format-01.php');
} else {
    require (__DIR__  . '/calendar-events-format-02.php');
}

