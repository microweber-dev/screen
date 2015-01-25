<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Screenshot</title>
<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
<link href="//netdna.bootstrapcdn.com/bootswatch/2.3.2/spacelab/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<form target="shot"  action="shot.php"   method="post">
	<fieldset>
		<legend>Take a screenshot</legend>
		<div class="form-group">
			<label for="url">URL for screenshot</label>
			<input type="text" class="form-control" name="url" placeholder="Enter URL">

		</div>
		<button type="submit" class="btn btn-default">Submit</button>
	</fieldset>
</form>
<br>
<iframe src="shot.php" name="shot" id="shot" width="100%" height="1000" frameborder="0"></iframe>
</body>
</html>
