<?php
	if($_POST){
		if (isUpdate('toolCompareFileMainSWF')) {
			if (isset($_POST['runToolMain'])) {
				runningToolXMLS();
			}
		}
	}
    function runningToolXMLS() {
		$filePathCN = "./config/toolCompareMain/Main-CN.txt";
		$filePathVI = "./config/toolCompareMain/Main-VI.txt";

		$statusSuccess = getLanguageByKey('TOOL_COMPARE_FILE_MAIN_STATUS_SUCCESS');
		$statusFail = getLanguageByKey('TOOL_COMPARE_FILE_MAIN_STATUS_FAIL');

		$breakLine = "\n";

		$fileArrayCN = explode($breakLine, file_get_contents($filePathCN));
		$fileArrayVI = explode($breakLine, file_get_contents($filePathVI));

		$fileArrayKeyVI = array();
		for ($indexVI = 1; $indexVI < count($fileArrayVI); $indexVI = $indexVI + 2) { 
			$key = $fileArrayVI[$indexVI - 1];
			$fileArrayKeyVI[$key] = $fileArrayVI[$indexVI];
		}

		$resultText = "";
		for ($indexCN = 1; $indexCN < count($fileArrayCN); $indexCN = $indexCN + 2) { 
			$key = $fileArrayCN[$indexCN - 1];
			if (array_key_exists($key, $fileArrayKeyVI)) {
				$fileArrayCN[$indexCN] = $fileArrayKeyVI[$key];
				showStatusInWeb($key, true, $statusSuccess, $statusFail);
			} else {
				showStatusInWeb($key, false, $statusSuccess, $statusFail);
			}
			$resultText .= $key.$breakLine;
			$resultText .= $fileArrayCN[$indexCN];
			if ($indexCN + 2 < count($fileArrayCN)) {
				$resultText .= $breakLine;
			}
		}

		$filePathResult = "./config/toolCompareMain/Main-Result.txt";
		file_put_contents($filePathResult, $resultText);
	}
	
	function showStatusInWeb($key, $isOK, $statusSuccess, $statusFail) {
		if($isOK) {
			$color = 'green';
			$status = $statusSuccess;
		} else {
			$color = 'orange';
			$status = $statusFail;
		}
		echo "<span style='color:{$color}'>";
			echo "<font size='3'><b>{$key} -----------------> {$status}</b></font>";
		echo "</span>";
		echo "<br/>";
	}
?>