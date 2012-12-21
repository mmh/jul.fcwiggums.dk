<?
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');
ini_set('date.timezone', 'Europe/Copenhagen');

header('Pragma: no-cache');
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Expires: -1');

function __autoload($Class) {
	$FileName = 'app/classes/'.$Class.'.php';
	if (is_file($FileName)) {
		include($FileName);
	}
}

$_REQUEST = array_merge($_GET, $_POST);	//for security reasons
$db = new DbConnection('localhost', 'jul_fcwiggums_dk', 'jul_fcwiggums_dk', 'XXXXXXXX');

$sessionId = '';
if(isset($_COOKIE['sessionId'])) {
	$sessionId = $_COOKIE['sessionId'];
}
if(isset($_REQUEST['sessionId'])) {
	$sessionId = $_REQUEST['sessionId'];
}

header('Content-type: text/html; charset=utf-8');

function getRound() {
	return floor(getTotalBeer()/20);
}

function getTotalBeer() {
	global $db;
	$result = $db->queryRow('SELECT SUM(beers) AS total FROM events');
	return $result['total'];
}

function getTotalBeerForUser($user_id) {
	global $db;
	$result = $db->queryRow('SELECT SUM(beers) AS total FROM events WHERE user_id = :user_id', array('user_id' => $user_id));
	return $result['total'];
}

function getLastBeerTimeForUser($user_id) {
	global $db;
	$result = $db->queryRow('SELECT created FROM events WHERE user_id = :user_id AND type = :type ORDER BY created DESC LIMIT 1', array('user_id' => $user_id, 'type' => 'beer'));
	return $result['created'];
}

function getTotalSnapsForUser($user_id) {
	global $db;
	$result = $db->queryRow('SELECT SUM(snaps) AS total FROM events WHERE user_id = :user_id', array('user_id' => $user_id));
	return $result['total'];
}


function userHasCheckedRound($user_id) {
	global $db;
	return $db->count('events', array('type' => 'button_round_'.getRound(), 'user_id' => $user_id)) == 1;
}


?>
