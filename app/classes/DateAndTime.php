<?php
class DateAndTime {
	public static function europeanDateToTimestamp($date, $hours = '00', $minutes = '00') {
		$date = DateTime::createFromFormat('d-m-Y H:i', $date.' '.sprintf("%02s", $hours).':'.sprintf("%02s", $minutes));
		if ($date) {
			return  $date->getTimestamp();
		} else {
			return null;
		}
	}

	public static function floatToHours($val) {
		$mins = round($val*60);
		
		$m = $mins%60;
		if ($m < 10) {
			$m = '0'.$m;
		}
		return floor($mins/60) . ':' . $m;
	}
	
	public static function formatDateAndTime($timestamp) {
		return date('H:i:s', $timestamp);
	}

	public static function formatDate($timestamp) {
		return date('d-m-Y', $timestamp);
	}

	public static function createHourSelect($name) {
		$options = '';
		for ($i = 0; $i < 24; $i++) {
			$options .= '<option value="'.$i.'">'.sprintf("%02s", $i).'</option>';
		}
		return '<select name="'.$name.'">'.$options.'</select>';
	}

	public static function createMinuteSelect($name, $inteval) {
		$options = '';
		for ($i = 0; $i < 60; $i += $inteval) {
			$options .= '<option value="'.$i.'">'.sprintf("%02s", $i).'</option>';
		}
		return '<select name="'.$name.'">'.$options.'</select>';
	}
}