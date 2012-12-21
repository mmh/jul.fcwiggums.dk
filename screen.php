<?
require('init.php');

?><!doctype html>
<html>
	<head>
		<title>Storskærmen</title>
		<meta charset="utf-8" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	</head>

	<body>

		<script>
			function updateContent() {
				jQuery.post('/screen_content.php', {}, function(html) {
					$('#content').html(html);
					setTimeout(function() {
						updateContent();
					}, 1000);
				}, "html");
			}

			jQuery(document).ready(function($) {
				updateContent();
			});
		</script>
		<style>
			html, body {margin:0; padding:0;}
		</style>
		<div id="content" style="width:1280px; height:720px; border: 1px solid grey; position:relative; overflow:hidden;"></div>
	
	</body>
</html>


