<?php

include('common.php');

$updateTime = round(microtime(true) * 1000); // milliseconds

if ($miniBrowser) {
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
} else {
	header('Content-Type: text/plain; charset=utf-8'); // plain text for JS parsing of normal result
}

try {
	if (!isset($_POST['shop'])) {
		throw new RuntimeException('Unknown shop number');
	}
	$shop = intval($_POST['shop']);

	if (!isset($_POST['pin'])) {
		throw new RuntimeException('Incorrect PIN');
	}
	if (intval($_POST['pin']) != $loginPin) {
		throw new RuntimeException('Incorrect PIN');
	}

	// check for upload errors
	if (!isset($_FILES['imageFile']['error']) || is_array($_FILES['imageFile']['error'])) {
		throw new RuntimeException('Unknown error'); // no error code = error
	}
	switch ($_FILES['imageFile']['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			throw new RuntimeException('Unknown error'); // no file sent
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			throw new RuntimeException('Unknown error'); // file too big
		default:
			throw new RuntimeException('Unknown error'); // unknown error
	}

	// check image dimensions (rather than file size in bytes), and validate format
	$result_array = getimagesize($_FILES['imageFile']['tmp_name']);
	if ($result_array !== false) {
		$mime_type = $result_array['mime'];
		switch($mime_type) {
			case "image/jpeg":
				break;
			default:
				throw new RuntimeException('Invalid photo format');
		}
	} else {
		throw new RuntimeException('Invalid photo format');
	}

	// archive previous versions; move into place
	$fileName = sprintf('%s%s.jpg', $uploadsLocation, $shop);
	if (file_exists($fileName)) {
		rename($fileName, $fileName . '.' . $updateTime . '.jpg');
	}
	if (!move_uploaded_file($_FILES['imageFile']['tmp_name'], $fileName)) {
		throw new RuntimeException('Unknown error'); // couldn't move the file
	}

	// validate image dimensions (for non-JS browsers)
	$result_array = getimagesize($fileName);
	$imageWidth = $result_array[0];
	$imageHeight = $result_array[1];
	$validSize &= $imageWidth <= $maxImageSize;
	$validSize &= $imageHeight <= $maxImageSize;
	if (!$validSize) {
		$ratio = $imageWidth / $imageHeight;
		if ($ratio > 1) {
			$outputWidth = $maxImageSize;
			$outputHeight = $maxImageSize / $ratio;
		} else {
			$outputWidth = $maxImageSize * $ratio;
			$outputHeight = $maxImageSize;
		}
		$src = imagecreatefromjpeg($fileName);
		$dst = imagecreatetruecolor($outputWidth, $outputHeight);
		$success = imagecopyresampled($dst, $src, 0, 0, 0, 0, $outputWidth, $outputHeight, $imageWidth, $imageHeight);
		if ($success !== false) {
			if (imagejpeg($dst, $fileName) === false) {
				throw new RuntimeException('Unknown error'); // failed to resize the file
			}
		} else {
			throw new RuntimeException('Unknown error'); // failed to resize the file
		}
	}

	// sanitise and trim the title
	if (isset($_POST['title'])) {
		$title = substr(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING), 0, $maxTitleLength);
		$titleFile = sprintf('%s%s.txt', $uploadsLocation, $shop);
		if (file_exists($titleFile)) {
			rename($titleFile, $titleFile . '.' . $updateTime . '.txt');
		}
		file_put_contents($titleFile, $title);
	}

	echo 'Done!';

	if ($miniBrowser) {
		header('Location: ' . $rootUrl . '?shop=' . $shop . '&update=' . time());
		exit;
	}

} catch (RuntimeException $e) {
	if ($miniBrowser) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Photo upload error</title>
<link rel="shortcut icon" href="favicon.ico">
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
div {
	display: block;
	position: absolute;
	height: auto;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	margin: 20px;
	text-align: center;
}
</style>
</head>
<body>
<div>
<?php
	}
	echo 'Error: ' . $e->getMessage();
	if ($miniBrowser) {
?>
<br><br><a href="" onclick="window.history.go(-1); return false;">Try again</a>
</div>
</body>
</html>
<?php
	}
}
?>
