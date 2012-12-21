<?
require('init.php');
?>

		<div><?=DateAndTime::formatDateAndTime(time())?></div>
		<div style="position:absolute; width:100%; top:4px; left:0; text-align:center; font-size:40px; z-index:10;">Antal fadøl drukket: <?=getTotalBeer();?></div>

<?
$users = $db->select('users');
$sorted_users = array();

$max = 10;

$playerWidth = 120;
$maxPixelHeight = 500;
foreach ($users as $u) {
	$beers = getTotalBeerForUser($u['user_id']);
	$snaps = getTotalSnapsForUser($u['user_id']);
	$max = max($max, $beers);
	$sorted_users[] = array(
		'beers' => $beers, 
		'snaps' => $snaps, 
		'lastbear' => getLastBeerTimeForUser($u['user_id']),
		'name' => $u['name'], 
		'user_id' => $u['user_id'],
	);
}

function mySortFunc($a, $b) {
	return ($b['beers'] - $a['beers']) * 100000000 + ($a['lastbear'] - $b['lastbear']);
}

usort($sorted_users, 'mySortFunc');
?>
		<div style="position:absolute; bottom:20px; left:0;">
<?
foreach ($sorted_users as $u) {	
?>
	<div style="height: 120px; width: <?=$playerWidth?>px; float:left; cursor:pointer; position:relative;" onclick="addBeer('<?=$u['name']?>', <?=$u['user_id']?>);">
		<img src="/img/<?=$u['user_id']?>-small.png" height="100%" style="display:block; margin:0 auto;"/>
		<div style="position:absolute; bottom:-20px; left:0; width:100%; text-align:center;">
			<div style="border:1px solid black; background-color:<?= userHasCheckedRound($u['user_id']) ? 'cyan' : 'white' ?>; border-radius:10px;">
			<?=$u['name']?>
			</div>
		</div>
	</div>

<?
}
?>
			<div style="clear:both;"></div>
		</div>
<?
$player_index = 0;
foreach ($sorted_users as $u) {	
	echo '<div style=" width: 50px; background-image:url(\'/img/beers.png\');height:'.(int)(((float)$u['beers']*$maxPixelHeight)/$max).'px; position:absolute; bottom:150px; text-align:center; font-weight:bold; font-size:150%; left:'.($player_index*$playerWidth +20).'px">'.($u['beers'] ? $u['beers'] : '').'</div>';
	echo '<div style=" width: 19px; background-image:url(\'/img/snaps.png\');height:'.($u['snaps']*30).'px; position:absolute; bottom:150px; text-align:center; left:'.($player_index*$playerWidth + 80).'px"></div>';
	$player_index++;
}
?>
		
		<table style="position:absolute; top:0; right:0;">
<?
	$events = $db->query('SELECT * FROM events ORDER BY event_id DESC LIMIT 30');
	foreach ($events as $e) { ?>
			<tr>
				<td><?=DateAndTime::formatDateAndTime($e['created'])?></td>
				<td width="250"><?=$e['description']?></td>
			</tr>
<?
	}
?>
		</table>
