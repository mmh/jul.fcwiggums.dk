<?
require('init.php');

if (isset($_POST['action'])) {
	if ($_POST['action'] == 'addBeer') {
		$user = $db->selectRow('users', array('user_id' => $_POST['user_id']));
		$db->insert('events', array(
			'user_id' => $_POST['user_id'], 
			'created' => time(), 
			'description' => $user['name'] . ' har hentet en fadøl',
			'points' => 1,
			'tokens' => 1,
			'beers' => 1,
			'type' => 'beer',
		));
		echo '{}';
	}
	exit;
}

?><!doctype html>
<html>
	<head>
		<title>Øl iPad'en</title>
		<meta charset="utf-8" />
		<meta name="apple-mobile-web-app-capable" content="yes">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	</head>
	<body>
		<style>
			html, body {margin:0; padding:0;}
		</style>
		<script>
			function addBeer(name, user_id) {
				if (confirm('Er du sikker på du hedder ' + name + ' og har fyldt glasset helt op?')) {
					jQuery.post(window.location.pathname, {'user_id':user_id, 'action':'addBeer'}, function(result) {
						if (result.error) {
							alert('Error: ' + result.error);
						} else {
							window.location.reload(true);
						}
					}, "json");
				}
			}
		</script>
		<div style="position:relative;">
			<div style="position:absolute; width:100%; bottom:140px; left:0; text-align:center; font-size:40px; z-index:10;">Antal fadøl drukket: <?=getTotalBeer();?></div>
			<img src="/img/skum.png" width="100%" style="position:absolute; bottom:0; left:0;"/>
			<div>
<?
$users = $db->select('users');
foreach ($users as $u) { ?>
	<div style="height: 200px; margin: 20px 40px; float:left; cursor:pointer; position:relative;" onclick="addBeer('<?=$u['name']?>', <?=$u['user_id']?>);">
		<img src="/img/<?=$u['user_id']?>-small.png" height="100%" />
		<div style="position:absolute; bottom:-20px; left:0; width:100%; text-align:center;">
			<div style="border:1px solid black; background-color:white; border-radius:10px;">
			<?=$u['name']?>
			</div>
		</div>
	</div>

<?
}
?>
			<div style="clear:both;"></div>
		</div>
		
		<table>
<?
	$events = $db->query('SELECT * FROM events WHERE type = :type ORDER BY event_id DESC LIMIT 10', array('type' => 'beer'));
	foreach ($events as $e) { ?>
			<tr>
				<td><?=DateAndTime::formatDateAndTime($e['created'])?></td>
				<td><?=$e['description']?></td>
			</tr>
<?
	}
?>
		</table>
		
		</div>
	</body>
</html>


