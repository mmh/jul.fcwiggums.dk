<?php
require('init.php');

	
	if (isset($_POST['action'])) {
		if ($_POST['action'] == 'snaps' && $_POST['user_id'] == 'omgang') {
		        $users = $db->select('users');
		        foreach ($users as $u) {
				$db->insert('events', array(
					'type' => 'snaps', 
					'user_id' => $u['user_id'],
					'description' => $u['name'] . ' har drukket en snaps',
					'snaps' => 1,
					'created' => time(), 
				));
			}
			echo '{}';
		} else if ($_POST['action'] == 'snaps' && $_POST['user_id']) {
			$user = $db->selectRow('users', array('user_id' => $_POST['user_id']));

			$db->insert('events', array(
				'type' => 'snaps', 
				'user_id' => $_POST['user_id'],
				'description' => $user['name'] . ' har drukket en snaps',
				'snaps' => 1,
				'created' => time(), 
			));
			
			echo '{}';
		} else if ($_POST['action'] == 'custom' && $_POST['user_id']) {
			$user = $db->selectRow('users', array('user_id' => $_POST['user_id']));

			$db->insert('events', array(
				'type' => 'custom', 
				'user_id' => $_POST['user_id'],
				'description' => $user['name'] .' '. $_POST['description'],
				'snaps' => (int)$_POST['snaps'],
				'beers' => (int)$_POST['beer'],
				'created' => time(), 
			));
			
			echo '{}';
		} else if ($_POST['action'] == 'deleteEvent') {
			$db->delete('events', array('event_id' => $_POST['event_id']));
			echo '{}';
		}
		error_log($_POST['action'].' kaldt fra admin af sessionId: ' . $sessionId .' fra IP '.$_SERVER['REMOTE_ADDR']);
		exit;
	}

?><!doctype html>
<html>
	<head>
		<title>Admin</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	</head>
	<body>
		<script>
			jQuery(document).ready(function($) {
				$('#snaps').submit(function() {
					jQuery.post(window.location.pathname, jQuery(this).serializeArray(), function(result) {
						if (result.error) {
							alert('Error: ' + result.error);
						} else {
							window.location.reload(true);
						}
					}, "json");
					return false;
				});
				$('#custom').submit(function() {
					jQuery.post(window.location.pathname, jQuery(this).serializeArray(), function(result) {
						if (result.error) {
							alert('Error: ' + result.error);
						} else {
							window.location.reload(true);
						}
					}, "json");
					return false;
				});
			});
			
			function deleteEvent(event_id) {
				jQuery.post(window.location.pathname, {'event_id': event_id, 'action':'deleteEvent'}, function(result) {
					if (result.error) {
						alert('Error: ' + result.error);
					} else {
						window.location.reload(true);
					}
				}, "json");
			}
		</script>
		<h1>Admin</h1>
		<h3>Tildel snaps</h3>
		<form id="snaps">
			<input type="hidden" name="action" value="snaps" />
			<select name="user_id">
				<option value="">-</option>
				<option value="omgang">Omgang</option>
<?
	$users = $db->select('users');
	foreach ($users as $u) { ?>		
				<option value="<?=$u['user_id']?>"><?=$u['name']?></option>
<?
	}
?>
			</select>
			<input type="submit" value="Tildel" />
		</form>


		<h3>Opret freestyle event</h3>
		<form id="custom">
			<input type="hidden" name="action" value="custom" />
			<div>User + description:</div>
			<div><select name="user_id">
				<option value="">-</option>
<?
	$users = $db->select('users');
	foreach ($users as $u) { ?>		
				<option value="<?=$u['user_id']?>"><?=$u['name']?></option>
<?
	}
?>
			</select>
			<input type="text" name="description" autocapitalize="off" /></div>
			<div>Beers:</div>
			<div><input type="text" name="beer" /></div>
			<div>Snaps:</div>
			<div><input type="text" name="snaps" /></div>
			<div></div>
			<input type="submit" value="Opret" />
		</form>



		<h3>Events</h3>
		<table border="1">
<?
	$events = $db->query('SELECT * FROM events ORDER BY event_id DESC');
	foreach ($events as $e) { ?>
			<tr>
				<td><?=DateAndTime::formatDateAndTime($e['created'])?></td>
				<td><?=$e['description']?></td>
				<td><input type="button" value="Slet" onclick="deleteEvent(<?=$e['event_id']?>)" /></td>
			</tr>
<?
	}
?>
		</table>

		
	</body>
</html>
