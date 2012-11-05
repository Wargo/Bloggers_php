<?php
extract($post);
echo $this->Html->link(__('Volver', true), array('controller' => 'feeds', 'action' => 'view', $Post['blog_id']));

echo '<h1>' . utf8_decode($Post['title']) . '</h1>';

echo '<p style="border-top:solid 1px #333; border-bottom:solid 1px #333;">Por ' . $Post['author'] . ', ' . timeago($Post['date']) . '</p>';

echo '<img src="' . $Post['image'] . '" align="left" style="margin-right: 10px; margin-bottom: 1px;" />';

echo '<p style="text-align: justify;">' . utf8_decode($Post['description']) . '</p>';

function timeago($datefrom, $dateto = -1) {
	if ($datefrom <= 0) {
		return "Hace mucho tiempo";
	}
	if($dateto==-1) {
		$dateto = time();
	}
	$difference = $dateto - $datefrom;
	if($difference < 60) {
		$interval = "s";
	}
	elseif($difference >= 60 && $difference < 60*60) {
		$interval = "n";
	}
	elseif($difference >= 60*60 && $difference < 60*60*24) {
		$interval = "h";
	}
	elseif($difference >= 60*60*24 && $difference < 60*60*24*7) {
		$interval = "d";
	}
	elseif($difference >= 60*60*24*7 && $difference < 60*60*24*30) {
		$interval = "ww";
	}
	elseif($difference >= 60*60*24*30 && $difference < 60*60*24*365) {
		$interval = "m";
	}
	elseif($difference >= 60*60*24*365) {
		$interval = "y";
	}
	switch($interval) {
		case "m":
			$months_difference = floor($difference / 60 / 60 / 24 / 29);
			while (mktime(date("H", $datefrom), date("i", $datefrom),
						date("s", $datefrom), date("n", $datefrom) + ($months_difference),
						date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$datediff = $months_difference;
			if($datediff == 12) {
				$datediff--;
			}
			$res = ($datediff==1) ? "hace $datediff mes" : "hace $datediff meses";
			break;
		case "y":
			$datediff = floor($difference / 60 / 60 / 24 / 365);
			$res = ($datediff==1) ? "hace $datediff año" : "hace $datediff años";
			break;
		case "d":
			$datediff = floor($difference / 60 / 60 / 24);
			$res = ($datediff==1) ? "hace $datediff día" : "hace $datediff días";
			break;
		case "ww":
			$datediff = floor($difference / 60 / 60 / 24 / 7);
			$res = ($datediff==1) ? "la semana pasada" : "hace $datediff semanas";
			break;
		case "h":
			$datediff = floor($difference / 60 / 60);
			$res = ($datediff==1) ? "hace $datediff hora" : "hace $datediff horas";
			break;
		case "n":
			$datediff = floor($difference / 60);
			$res = ($datediff==1) ? "hace $datediff minuto" : "hace $datediff minutos";
			break;
		case "s":
			$datediff = $difference;
			$res = ($datediff==1) ? "hace $datediff segundo" : "hace $datediff segundos";
			break;
	}
	return $res;
}

