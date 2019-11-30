<?php

// var_dump($calendarFields);

// echo '<pre>';
// print_r($calendarFields);
// echo '</pre>';

/*
$calendarFields массив или объект с данными полей, в случае формата 01 он плоский (одномерный)
[
	[name1] => type1,
	[name2] => type2
]

формат 02
[
	[key1] => [
		[name] => name1,
		[type] => type1

	],
	[key2] => [
		[name] => name2,
		[name] => type2

	]
]

*/

$formatVersion = "01";

// if (is_object($calendarFields) ||  is_array($calendarFields)) {

// 	foreach ($calendarFields as $k => $v) {

// 	    if (is_object($v) ||  is_array($v)) {
// 			$formatVersion = "02";
// 	    }

// 	    break;
// 	}
// }

if ($calendarFormatVersion == '02') {
	$formatVersion = '02';
}

require(__DIR__  . '/event-form-format-' . $formatVersion . '.php');

