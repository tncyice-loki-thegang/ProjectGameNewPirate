<?php
	showServerFullInfo();

	function showServerFullInfo() {
		echo "<div class='serverController'>";
			echo "<div style='width:71%'>";
				echo "<div class='lineTop'>";
				echo "</div>";
				echo "<div class='serverControllerBottom'>";
					echo "<br/>";
					showInfo("mysql", "My SQL");
					echo "<br/>";
					echo "<br/>";
					showInfo("battle", "Battle");
					echo "<br/>";
					echo "<br/>";
					showInfo("dataproxy", "Data Proxy");
					echo "<br/>";
					echo "<br/>";
					showInfo("phpproxy", "PHP Proxy");
					echo "<br/>";
					echo "<br/>";
					showInfo("memcached", "Mem Cached");
					echo "<br/>";
					echo "<br/>";
					showInfo("php", "PHP");
					echo "<br/>";
					echo "<br/>";
					showInfo("lcserver", "Lcserver Game20002");
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}
	function showInfo($server, $title) {
		$status = "";
		$color = "";
		if (isServerCheckFullRunning($server)){
			$status = getLanguageByKey('SERVER_CONTROLLER_RUNNING');
			$color = "green";
		} else {
			$status = getLanguageByKey('SERVER_CONTROLLER_OFF');
			$color = "red";
		}
		echo "<span style='color:{$color}'>";
			echo "<font size='5'>{$title} -----------------> {$status}</font>";
		echo "</span>";
	}
?>