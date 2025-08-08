<?php
	if($_POST){
		if (isUpdate('gotoQueryDatabase')) {
			if (isset($_POST['clearAllAccount'])){
				clearAllAccount();
			} else if (isset($_POST['runSQL'])){
				if($_POST['query']=="") {
					alert(getLanguageByKey('QUERY_DATEBASE_QUERY_EMPTY'));
				} else {
					$sql = trim($_POST['query']);
					query($sql);
				}
			}
		}
	}
    function clearAllAccount() {
		// Read Query In File
		$sql = file_get_contents("./sql/ClearAllAccount.sql");
		// Query
		query($sql);
		
		$sqlT9 = file_get_contents("./sql/ClearAllT9Account.sql");
		// Query
		queryT9($sqlT9);
    }
    function query($sql) {
		try {
			$db = DatabaseConnection::getInstance();
			$conn = $db -> getConnect();
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			alert(getLanguageByKey('QUERY_DATEBASE_QUERY_SUCCESS'));
		} catch (\PDOException $e) {
			echo $e->getMessage();
			alert(getLanguageByKey('QUERY_DATEBASE_QUERY_ERROR'));
		}
	}
    function queryT9($sql) {
		try {
			$db = DatabaseConnection::getInstance();
			$conn = $db -> getT9Connect();
			$stmt = $conn->prepare($sql);
			$stmt->execute();
		} catch (\PDOException $e) {
			echo $e->getMessage();
		}
	}
?>