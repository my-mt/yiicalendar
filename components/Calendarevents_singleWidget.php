<?php
namespace app\components;
use yii\base\Widget;


class Calendarevents_singleWidget extends Widget {

    public $year;
    public $month;
    public $data_events;

    public function run(){

        return $this->render(
            'calendarevents_single',
            [
            'year' => $this->year,
            'month' => $this->month,
            'data_events' => $this->data_events,
            ]
            );
    }
}