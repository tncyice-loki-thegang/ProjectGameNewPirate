<?php
	$noteConfig = $GLOBALS['config']['note'];
	if ($noteConfig['enable']) { 
		showNote($noteConfig);
	}

	function showNote($noteConfig) {
		echo "<div class='note'>";
			echo "<div style='width:71%'>";
				echo "<div class='lineTop'>";
				echo "</div>";
				echo "<div class='noteBottom'>";
					echo "<br/>";
					if ($noteConfig['usingDefault']) {
						if (isServerRunning()) {
							$serverRunningTitle = getLanguageByKey('SHOW_NOTE_DEFAULT_SERVER_RUNNING');
							echo "<span class='blinking'>";
								echo "<font size='5'>{$serverRunningTitle}</font>";
							echo "</span>";
						} else {
							$serverNotRunningTitle = getLanguageByKey('SHOW_NOTE_DEFAULT_SERVER_NOT_RUNNING');
							echo "<span style='color:green'>";
								echo "<font size='5'>{$serverNotRunningTitle}</font>";
							echo "</span>";
						}
					} else {
						echo "<span style='color:red'>";
							echo "<font size='4'>{$noteConfig['title']}</font>";
						echo "</span>";
					}
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}
?>