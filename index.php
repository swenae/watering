<?php

define('PSK',        'a9b8c7d6e5f4');
define('LOG_FILE',   'tmp/rpi_control.log');
define('QUEUE_FILE', 'tmp/rpi_control.queue');

$COMMANDS = ['ACTIVATE', 'DEACTIVATE', '10SEC', 'SHUTDOWN', 'REBOOT'];

if (isset($_POST['nextCommand'])) {
	$queue = @file(QUEUE_FILE, FILE_SKIP_EMPTY_LINES);
	if (!is_array($queue)) {
		$queue = [];
	}

	$command = trim($_POST['nextCommand']);
	if (!in_array($command, $COMMANDS)) {
		logAction('ERROR: Invalid command "'.$command.'" provided');
	} else {
		$queue[] = $command . PHP_EOL;
		if (file_put_contents(QUEUE_FILE, $queue)) {
			logAction('INFO: Added new command "' . $command . '" to queue');
		} else {
			logAction('ERROR: Cannot write new command "' . $command . '" to queue');
		}
	}
}


if (isset($_GET['request'])) {
	authorize();
	$queue = @file(QUEUE_FILE, FILE_SKIP_EMPTY_LINES);
	if (!is_array($queue) || count($queue) == 0) {
		exit();
	}
	$command = trim(array_shift($queue));
	if (file_put_contents(QUEUE_FILE, $queue) !== false) {
		logAction('INFO: Delivered command "'.$command.'"');
		exit($command);
	} else {
		logAction('ERROR: Cannot remove command "'.$command.'" from queue', true);
	}
}


function authorize() {
	if (!isset($_GET['psk']) || $_GET['psk'] != PSK) {
		logAction('ERROR: Unauthorized request', true);
	}
}

function logAction ($message, $doExit = false) {
	$ipAddr = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'?';
	$message = date('c', time()) . ' - ' . $message . ' (IP: ' . $ipAddr . ')' . PHP_EOL;
	error_log($message, 3, LOG_FILE);
	if ($doExit) {
	    exit();
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="5" URL="index.php">
<meta http-equiv="expires" content="0">
<meta http-equiv="pragma" content="no-cache">

<title>Bewässerungsautomat</title>
	
<style type="text/css" media="screen">



body { background: #efefe7; background-image: url("bgr1.jpg"); font-family: Verdana, sans-serif; font-size: 13pt; padding: 20px; text-align: center;  }
header { background: #339966; border: 0px; text-align: center; padding: 0px; color: #ffffff; }
header h1 { color: #ffffff; }
page { background: #ffffff; margin: 25px; border: 2px solid #c0c0c0; padding: 10px; }
.column { background-color: #F0F0F0;padding: 10px 10px 10px 10px;border: 1px solid #D0D0D0;	}
span.tt { font-family: monospace; }
span.bold { font-weight: bold; }
a:link { text-decoration: none; font-weight: bold; color: #C00; }
img { max-width : 80%; }
form{ display: inline-block;}
button{
	width:114px;
	height:58px;
	display:inline-block;
	margin:12px;
	text-decoration:none;
	font-family:Arial;
	font-size:17px;
	border:1px solid #fff;
	border-radius:16px;
	color:#fec;
	background-color:#7B97B1;
	padding:2px 11px;
	font-weight:bold;
	box-shadow:3px 3px 4px #8CA0B2;
	text-shadow:1px 1px 2px #6E91B2;
}
button:hover{
	background-color:#887CB0;
}

</style>
	
</head>

<body>

<div id="page">
   <div id="header">
     <h2>Bewässerungsautomat</h2>
   </div>
   <div id="body">
		<div class="column">	
			
			<form method="post" action="">
				<input type = "hidden" id="nextCommand" name="nextCommand" value="ACTIVATE"/>
		        <button type="submit" formmethod="post" value="send" >Aktiv</button>
			</form>
			<form method="post" action="">
				<input type = "hidden" id="nextCommand" name="nextCommand" value="DEACTIVATE"/>
		        <button type="submit" formmethod="post" value="send" >Aus</button>
			</form>
			<form method="post" action="">
				<input type = "hidden" id="nextCommand" name="nextCommand" value="10SEC"/>
		        <button type="submit" formmethod="post" value="send" >Wasser</button>
			</form>
			<form method="post" action="">
				<input type = "hidden" id="nextCommand" name="nextCommand" value="REBOOT"/>
		        <button type="submit" formmethod="post" value="send" >Neustart</button>
			</form>
			<form method="post" action="">
				<input type = "hidden" id="nextCommand" name="nextCommand" value="SHUTDOWN"/>
		        <button type="submit" formmethod="post" value="send" >Down</button>
			</form>
			
			
			
			
		</div>
		<br/>
		<div class="column">
			<h3>Warteschlange</h3>
			<ul>
				<?php
				$queue = @file(QUEUE_FILE, FILE_SKIP_EMPTY_LINES);
				if (is_array($queue)) {
					foreach ($queue as $command) {
						print "</br>" . $command . "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
					}
				}
				?>
			</ul>
		</div>
	  </div>
   </div>
</body>
</html>
