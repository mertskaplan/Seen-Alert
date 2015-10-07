<!DOCTYPE html>
<html ng-app="size" ng-controller="sizeCtrl">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<title>Seen Alert for Link</title>
		
		<link type="image/x-icon" href="favicon.ico" rel="shortcut icon">

		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/bootstrap-select.min.css">
		<link rel="stylesheet" href="css/animate.min.css">
		<link rel="stylesheet" href="css/style.css">
		
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js"></script>
		<script src="js/jquery.min.js"></script>
		<script src="js/angular.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/bootstrap-select.min.js"></script>
		<script src="js/script.js"></script>
	</head>
	<body>

		<?php
			include 'connect.php';
			include 'function.php';
		
			if	($_GET["short"])	{
				
				header("Cache-Control: no-cache, must-revalidate");
				header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
				
				$short = $_GET["short"];
				$short_url = "http://". $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
				
				$get = mysql_query( "SELECT `url`,`mail`,`click`,`maxclick` FROM `seendata` WHERE `seendata`.`short` = '$short'" );
				while ( $kayit = mysql_fetch_array($get) )	{
					$url = $kayit[0];
					$email = $kayit[1];
					$click = $kayit[2] + 1;
					$maxclick = $kayit[3];
				}
				
				if (empty($url)) {
					
					header("Location: http://mertskaplan.com/seenlink/");
					die();	
					
				}
				else {
					
					if	($click == $maxclick) {
						$message_header = "Your link was seen <strong>$click</strong> times and your URL was deleted because the link was seen <strong>$maxclick</strong> times.<br><br>"; 
						$message_short_link = "<strike>$short_url</strike>";
					}
					else {
						$message_header = "Your link was seen <strong>$click</strong> times. You will receive e-mail for each click and your URL will be deleted when <strong>$maxclick</strong> times clicked.<br><br>";
						$message_short_link = "<a href=\"$short_url\">$short_url</a>";
					}
					
					$ip = gethostbyaddr($_SERVER['REMOTE_ADDR']);
					$referer = $_SERVER['HTTP_REFERER'];
						if	(isset($referer))	{$referer = "<li><strong>Referer</strong>: $referer</li>";}
						else					{$referer = "<br>";}
					
					$site = "http://ip-api.com/php/$ip";
					$content = file_get_contents($site);
					$timezone = ara('"timezone";s:15:"', '";', $content);
						$timezone = $timezone[0];
					$city = ara('city";s:6:"', '";', $content);
						$city = $city[0];
					$country = ara('country";s:6:"', '";', $content);
						$country = $country[0];
					$org = ara('org";s:48:"', '";', $content);
						$org = $org[0];
					$ips = ara('isp";s:12:"', '";', $content);
						$ips = $ips[0];

					date_default_timezone_set("Europe/London");
					$date = date("d.m.Y - H:i");
					
					date_default_timezone_set("$timezone");
					$visitors_date = date("d.m.Y - H:i");

					$message = "
						<strong>Hi User,</strong><br><br>
						
						$message_header

						<strong>Your link</strong>: <a href=\"$url\">$url</a><br>
						<strong>Your short link</strong>: $message_short_link<br><br>
						
						<strong>$click. visitor information:</strong>
						
						<ul>
							<li><strong>Date of click</strong>: $date (Time Zone: Europe/London - UTC±00:00)</li>
							<li><strong>Date of click</strong> (<em>According to the visitor's time zone</em>): $visitors_date (Time Zone: $timezone)</li>
							<li><strong>IP address</strong>: <a href=\"http://whatismyipaddress.com/ip/$ip\">$ip</a></li>
							<li><strong>Country</strong>: $country</li>
							<li><strong>City</strong>: $city (<a href=\"https://www.google.com/maps/place/$city,+$country\">Google Maps</a>)</li>
							<li><strong>ISP name</strong>: $ips</li>
							<li><strong>Organization name</strong>: $org</li>
							<li><strong>Internet browser</strong>: $browser</li>
							<li><strong>Operation system</strong>: $os</li>
							$referer
						</ul>

						<strong>Seen Alert for Link</strong><br>
						<strong>mail:</strong> mail@mertskaplan.com
						";
						
					require("mail/class.phpmailer.php");
					$mail = new PHPMailer();
					$mail->IsSMTP();
					$mail->SMTPDebug = 1;
					$mail->SMTPAuth = true;
					$mail->SMTPSecure = 'ssl';
					$mail->Host = "smtp.zoho.com";
					$mail->Port = 465;
					$mail->IsHTML(true);
					$mail->SetLanguage("tr", "phpmailer/language");
					$mail->CharSet  ="utf-8";
					$mail->Username = "web@mertskaplan.com";
					$mail->Password = "password";
					$mail->SetFrom("web@mertskaplan.com", "Seen Alert for Link");
					$mail->AddAddress("$email");
					$mail->Subject = "Your link was seen: $url";
					$mail->Body = "$message";
					if	(!$mail->Send())	{
						echo "";
					}
					else	{
						echo "";
					}

					if	($click == $maxclick) {
						// sil
						$delete = "DELETE FROM `seendata` WHERE `seendata`.`short` = '$short'";
						@mysql_query($delete);
					}
					else {
						$update = "UPDATE `seendata` SET `click` = '$click' WHERE `seendata`.`short` = '$short'";
						@mysql_query($update);
					}
					
					header("Location: $url");
					die();	
					
				}	
			}
			else {

				if		($_POST["url"])		{
				
					$url	= $_POST["url"];
					$email	= $_POST["email"];
					$range	= $_POST["range"];
					
					$add	= "INSERT INTO `seendata` (`url`, `short`, `mail`,`maxclick`) VALUES ('$url', '$short', '$email', '$range')";

					if		( mysql_query($add) )	{ echo "<div class=\"alert animated fadeInDown alert-success text-center col-xs-12\" role=\"alert\"><span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span> <strong> The URL was added and alert was set successfully.</strong></div>"; }
					else							{ echo "<div class=\"alert animated fadeInDown alert-danger text-center col-xs-12\" role=\"alert\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span> <strong> Error: The URL was not added!</strong><br>Try again or browse to the following issues: " . mysql_error() . "</div>"; }	
					
					$short_url = "http://". $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']. "$short";
					$show_short_link = "";
					
				}
				else {
					$show_short_link = "none";
				}

				echo "
					<div class=\"bg\"></div>

					<div class=\"container\">
						<form class=\"form-horizontal col-sm-12\" action=\"\" method=\"post\">
						<h1 class=\"text-center title\">Seen Alert for Link</h1>
							<div class=\"form-group\">
								<label class=\"control-label col-sm-3\" for=\"url\">URL:</label>
								<div class=\"input-group col-md-6\">
									<span class=\"input-group-addon\" id=\"basic-addon1\"><span class=\"glyphicon glyphicon-link\" aria-hidden=\"true\"></span></span>
									<input class=\"form-control\" type=\"text\" name=\"url\" id=\"url\" autofocus required>
								</div>
							</div>
							
							<div class=\"form-group\">
								<label class=\"control-label col-sm-3\" for=\"email\">Email:</label>
								<div class=\"input-group col-md-6\">
									<span class=\"input-group-addon\" id=\"basic-addon1\"><span class=\"glyphicon glyphicon-envelope\" aria-hidden=\"true\"></span></span>
									<input class=\"form-control\" type=\"mail\" name=\"email\" id=\"email\" required>
								</div>
							</div>

							<div class=\"form-group\">
								<label class=\"control-label col-sm-3\" for=\"range\">Mail alert:</label>
								<div class=\"input-group col-md-6\">
									<span class=\"input-group-addon\" id=\"basic-addon1\"><span class=\"glyphicon glyphicon-repeat\" aria-hidden=\"true\"></span></span>
									<div class=\"range\">
										<input class=\"input-lg\" type=\"range\" id=\"range\" name=\"range\" min=\"1\" max=\"10\" ng-model=\"range\">
									</div>
									<span class=\"input-group-addon\" id=\"basic-addon2\">First {{range}} click</span>
								</div>
							</div>

							<div class=\"form-group\">
								<label class=\"control-label col-sm-3\"></label>
								<div class=\"input-group col-md-6 col-sm-9 col-xs-12 text-center\">
									<button class=\"btn btn-primary col-xs-12 strong\" name=\"submit\" type=\"submit\" id=\"submit\">Save the URL and set the alert</button>
								</div>
							</div>
							
							<div class=\"form-group animated fadeInDown $show_short_link\">
								<label class=\"control-label col-sm-3\" for=\"short_link\">Short Link:</label>
								<div class=\"input-group col-md-6\">
									<span class=\"input-group-addon\" id=\"basic-addon1\"><span class=\"glyphicon glyphicon-link\" aria-hidden=\"true\"></span></span>
									<input class=\"form-control copy\" type=\"text\" name=\"short_link\" id=\"short_link\" value=\"$short_url\">
								</div>
							</div>
							
						</form>
					</div>
					
					<div class=\"footer\">
						<div class=\"github\">
							<p class=\"code\">coded with <a class=\"tip\" data-toggle=\"tooltip\" title=\"PHP, HTML, CSS\">❤</a> by <a href=\"http://mertskaplan.com/\" target=\"_blank\">mertskaplan</a> | <a class=\"tooltip-left\" target=\"_blank\" href=\"https://raw.githubusercontent.com/mertskaplan/Seen-Alert/master/LICENSE\" data-tooltip=\"Seen Alert is released under GNU General Public License, version 3.\">GPLv3</a></p> <a href=\"https://github.com/mertskaplan/Seen-Alert\" target=\"_blank\"><img width=\"62\" height=\"16\" src=\"images/github.svg\" alt=\"GitHub\"></a>
						</div>
					</div>
					
				";	
			}
			
			mysql_close($connect);
		?>
	</body>
</html>