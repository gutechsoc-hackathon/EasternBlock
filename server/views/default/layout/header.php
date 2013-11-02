<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" /> 
<title>
<?php echo $headerInfo['title']; ?>
</title>
<!-- sylesheets and js -->
<?php  foreach ($headerInfo['css'] as $val): ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $_webPath.'css/'.$val; ?>" />
<?php endforeach; ?>
<?php  foreach ($headerInfo['js'] as $val): ?>
    <script type="text/javascript" src="<?php echo $_webPath.'js/'.$val; ?>"></script>
<?php endforeach; ?>
</head>
<body onload="doOnLoad ();">
