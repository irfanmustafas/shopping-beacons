<?php

include('common.php');

// don't cache - try to ensure we always get the latest image
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$shop = isset($_GET['shop']) ? intval($_GET['shop']) : 0;
$editMode = isset($_GET['edit']);

if (empty($shop)) {
	$editMode = false;
	$shop = 0;
	$title = 'Welcome to the photo shop beacon network';
	$description = 'Browse and explore the shops and beacons around you';
} else {
	$title = 'Shop ' . $shop;
	$titleFile = sprintf('%s%s.txt', $uploadsLocation, $shop);
	if (file_exists($titleFile)) {
		$title = file_get_contents($titleFile);
	}
	$description = $title . ' – part of the photo shop beacon network';
	
	$imageFile = sprintf('%s%s.jpg', $uploadsLocation, $shop);
	if (!file_exists($imageFile)) {
		$imageFile = false;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $title ?></title>
	<link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/favicons/manifest.json">
	<link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="/favicons/favicon.ico">
	<meta name="apple-mobile-web-app-title" content="My Shop">
	<meta name="application-name" content="My Shop">
	<meta name="msapplication-config" content="/favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
	<meta content="<?php echo $title; ?>" property="og:site_name" />
	<meta content="<?php echo $description; ?>" property="og:description" />
	<style type="text/css">
		html {
			width: 100%;
			height: 100%;
		}
		body {
			width: 100%;
			height: 100%;
			font-family: sans-serif;
			font-size: 160%;
			margin: 0;
			padding: 0;
		}
		.edit {
			font-size: 70%;
			padding: 15px;
		}
		#imageFile, #sendButton {
			margin-top: 30px;
			width: 90%;
			font-size: 90%;
			text-align: center;
		}
		.miniFormFileLabel {
			margin-top: 120px;
		}
		.miniFormFileLabel, .miniFormSubmitLabel {
			display: block;
		}
		#pin {
			width: 25%;
			font-size: 90%;
			margin-left: 10px;
			text-align: center;
			border: 3px solid #e9e9e9;
		}
		#title {
			width: 60%;
			font-size: 90%;
			margin-left: 10px;
			border: 3px solid #e9e9e9;
		}
		label.pin, label.title, div.shop {
			display: block;
			position: absolute;
			height: auto;
			top: 0;
			left: 0;
			right: 0;
			padding: 25px;
			text-align: center;
		}
		label.title {
			margin-top: 60px;
		}
		div.shop {
			bottom: 0;
		}
		div.form, form {
			width: 100%;
			height: 100%;
			text-align: center;
		}
		img.logo {
			width: auto;
		}
		img.camera {
			width: 20%;
		}
		img {
			width: 100%;
			height: auto;
		}
		label.formLabel input[type="file"] {
			position: fixed;
			top: -1000px;
		}
		label.formLabel {
			display:block;
			width: 100%;
			height: 100%;
			background: #f6f6f6;
			cursor:pointer;
			display:flex;
			align-items: center;
			justify-content: center;
		}
		label.formLabel:hover {
			background: #f6f6f6;
		}
		label.formLabel:active {
			background: #00bd5e;
		}
		label.formLabel :valid + span {
			color: #000;
		}
		input[type=number] {
			-webkit-text-security: disc;
		}
	</style>
<?php if ($editMode) { ?>
		<script type="text/javascript" src="ImageTools.min.js"></script>
		<script type="text/javascript">
			function fileSelected() {
				document.getElementById('progress').innerHTML = 'Sending&hellip;';
				var selectedFile = document.getElementById('imageFile').files[0];
				ImageTools.resize(selectedFile, {
					width: <?php echo $maxImageSize; ?>,
					height: <?php echo $maxImageSize; ?>
				}, function(blob, resized) {
					var fd = new FormData();
					fd.append('imageFile', blob);
					fd.append('shop', <?php echo $shop; ?>);
					fd.append('pin', document.getElementById('pin').value);
					fd.append('title', document.getElementById('title').value);
					var xhr = new XMLHttpRequest();
					xhr.upload.addEventListener('progress', uploadProgress, false);
					xhr.addEventListener('load', uploadComplete, false);
					xhr.addEventListener('error', uploadFailed, false);
					xhr.addEventListener('abort', uploadCancelled, false);
					xhr.open('POST', 'savefile.php');
					xhr.send(fd);
				});
			}
			function uploadProgress(evt) {
				if (evt.lengthComputable) {
					var percentComplete = Math.round(evt.loaded * 100 / evt.total);
					document.getElementById('progress').innerHTML = 'Sending&hellip; ' + percentComplete.toString() + '%';
				} else {
					document.getElementById('progress').innerHTML = 'Sending&hellip;';
				}
			}
			function uploadComplete(evt) {
				document.getElementById('progress').innerHTML = evt.target.responseText;
				if (evt.target.responseText === 'Done!') {
					document.location.replace('<?php echo $rootUrl; ?>?shop=<?php echo $shop; ?>');
				} else {
					document.getElementById('imageFile').value = null;
				}
			}
			function uploadFailed(evt) {
				document.getElementById('imageFile').value = null;
				document.getElementById('progress').innerHTML = 'Photo error – please try again';
			}
			function uploadCancelled(evt) {
				document.getElementById('imageFile').value = null;
				document.getElementById('progress').innerHTML = 'Upload cancelled or interrupted – please try again';
			}
		</script>
<?php } ?>
</head>
<body>
<?php if ($editMode) { ?>
	<div class="form">
		<form enctype="multipart/form-data" method="post" action="savefile.php" id="imageForm">
			<input type="hidden" name="shop" value="<?php echo $shop; ?>" />
			<label class="pin">Enter your PIN:<input type="number" id="pin" name="pin" maxlength="4" autofocus></label>
			<label class="title">Shop title:<input type="text" id="title" name="title" value="<?php echo $title; ?>" maxlength="<?php echo $maxTitleLength; ?>"></label>
			<?php if ($miniBrowser) { ?>
				<span class="miniFormFileLabel">
					<img class="camera" src="/camera.png"><input type="file" name="imageFile" id="imageFile" placeholder="Take a photo" accept="image/*" capture="camera" />
				</span>
				<span class="miniFormSubmitLabel">
					<button type="submit" form="imageForm" id="sendButton">Send</button>
				</span>
			<?php } else { ?>
				<label class="formLabel">
					<input type="file" name="imageFile" id="imageFile" onchange="fileSelected();" accept="image/*" capture="camera" />
				 	<span id="progress"><br><br><img class="camera" src="/camera.png"><br>Touch to add a photo<br><br></span>
				</label>
			<?php } ?>
		</form>
	</div>
<?php } else { ?>
	<?php if (intval($shop) <= 0) { ?>
	<div class="shop">
		<div><strong>Welcome!</strong><br><br>Walk around and explore the shops and beacons around you.<br><br>Come back to this page at any time for more information or to get in touch.<br><br><br>
		<strong>Shop list:</strong>&nbsp;<?php
			for ($i = 1; $i <= $numBeacons; $i++) {
				if ($i > 1) {
					echo '&nbsp; | ';
				}
				echo '&nbsp;<a href="?shop=' . $i . '">' . $i . '</a>';
			}
		?><br><br><br>
		<img class="logo" src="beacon.png"><br><br><a class="edit" href="mailto:hello@myshop.photo">Contact us</a><br><br></div>
	</div>
	<?php } else { ?>
		<div class="shop" onclick="location.reload();">
			<span><?php echo $title; ?></span><br><br>
			<?php if (!empty($imageFile)) { echo '<img src="' . $imageFile . '?update=' . time() . '">'; } ?><br><br>
			<span class="edit"><a href="?shop=<?php echo $shop; ?>&edit=1">Update shop</a>&nbsp; | &nbsp;<a href="/">About</a></span>
		</div>
	<?php } ?>
<?php } ?>
</body>
</html>
