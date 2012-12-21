<?php
require('init.php');

if ($sessionId) {
	$user = $db->selectRow('users', array('user_id' => $sessionId));
	$totalBeer = getTotalBeer();
	$round = getRound();
	
	if (isset($_POST['action'])) {
		if ($_POST['action'] == 'buttonPushed') {
			$numUsers = $db->count('users');
			
			$numEventsThisRound = $db->count('events', array('type' => 'button_round_'.$round));
			
			$description = $user['name'] . ' har trykket på ØL-knappen';
			$snaps = 0;
			if ($numUsers - $numEventsThisRound == 1) {
				$description .= ' som den sidste. <span style="color:red">Drik en snaps!</span>';
				$snaps = 1;
			}
			
			
			$db->insert('events', array(
				'type' => 'button_round_'.$round, 
				'user_id' => $user['user_id'],
				'description' => $description,
				'snaps' => $snaps,
				'created' => time(), 
			));
			
			echo '{}';
		}
		exit;
	}
?><!doctype html>
<html>
	<head>
		<title>Øl-knappen</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	</head>
	<body>
		<script>
			function buttonPushed(button) {
				$(button).remove();
				$('#spinner').show();
					
				if (screen.height < 500) {
					alert('Det kører vist lidt langsomt på den gamle telefon du har');
					setTimeout(function() {
						sendButtonPushed();	
					}, 5000);
				} else {
					sendButtonPushed();
				}
			}
			
			function sendButtonPushed(button) {
				jQuery.post(window.location.pathname, {'action': 'buttonPushed'}, function(result) {
					if (result.error) {
						alert('Error: ' + result.error);
					} else {
						window.location.reload(true);
					}
				}, "json");
			}
			
		</script>
<?
	$disabled = true;
	if ($round > 0) {
		$num_events = $db->count('events', array('type' => 'button_round_'.$round, 'user_id' => $user['user_id']));
		if ($num_events == 0) {
			$disabled = false;
		}
	}

?>
                <div style="text-align:center;">Fadøl drukket: <?=getTotalBeer();?></div>
        <div style="text-align:center;">
<? if ($disabled) { ?>
        	<div style="display:inline-block; width:200px; height:200px; background-image: url(img/beer_button_empty.png);" /></div>
<? } else { ?>
        	<div style="display:inline-block; width:200px; height:200px; background-image: url(img/beer_button.png);" onclick="buttonPushed(this);" /></div>
<? } ?>
        </div>
        <div id="spinner" style="text-align:center; display:none;">
        	<div style="display:inline-block; width:60px; height:60px; background-image: url(img/spinner.gif);" /></div>
        </div>
		<?=$disabled ? '<script>setTimeout(function() {document.location.reload(true);}, 10000);</script>' : ''?>
		<div style="text-align:center;">Logged ind som  <span style="font-weight:bold"><?=$user['name']?></span></div>
	</body>
</html>
<?	
} else {
	if (isset($_POST['action'])) {
		if ($_POST['action'] == 'chooseUser') {
			setcookie('sessionId', $_POST['user_id'], time()+60*60*24*365, '/');
			$user = $db->selectRow('users', array('user_id' => $_POST['user_id']));
			error_log($_POST['user_id'].' logged in from '.$_SERVER['REMOTE_ADDR']);
	                $db->insert('events', array(
	                        'user_id' => $_POST['user_id'],
	                        'created' => time(),
	                        'description' => $user['name'] . ' har checket ind og er klar på fest',
	                        'points' => 0,
	                        'tokens' => 0,
	                        'beers' => 0,
	                        'type' => 'login',
	                ));

			echo '{}';
		}
		exit;
	}

?>
<!doctype html>
<html>
	<head>
		<title>Hvem er du?</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	</head>
	<body>
		<script>
			var ua = navigator.userAgent.toLowerCase();
			var isAndroid = ua.indexOf("android") > -1;
			if(isAndroid) {
				alert('Hov hov, det er da vist ikke en iPhone. Tag en snaps!');
			}
		</script>

		<script>
			jQuery(document).ready(function($) {
/*				alert('Screen size: ' + screen.width + " x " + screen.height + " - " + window.orientation);  */
				$('#chooseUser').submit(function() {
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
		</script>
		<h1>Hvem er du?</h1>
		<form id="chooseUser">
			<input type="hidden" name="action" value="chooseUser" />
			<select name="user_id">
<?
	$users = $db->select('users');
	foreach ($users as $u) { ?>		
				<option value="<?=$u['user_id']?>"><?=$u['name']?></option>
<?
	}
?>
			</select>
			<input type="submit" value="Vælg"/>
		</form>
	</body>
</html>
<?	
}
?>
