<?php
	require_once 'app/DatabaseConnection.php';
	if (! defined ( 'ROOT' )) {
		define ( 'ROOT', "/home/pirate/rpcfw" );
		define ( 'LIB_ROOT', ROOT . '/lib' );
		define ( 'EXLIB_ROOT', ROOT . '/exlib' );
		define ( 'DEF_ROOT', ROOT . '/def' );
		define ( 'CONF_ROOT', ROOT . '/conf' );
		define ( 'LOG_ROOT', ROOT . '/log' );
		define ( 'MOD_ROOT', ROOT . '/module');
		define ( 'HOOK_ROOT', ROOT . '/hook' );
		define ( 'COV_ROOT', ROOT . '/cov' );
	}

	require_once (DEF_ROOT . '/Define.def.php');

	if (!function_exists( 'btstore_get' )) {
		require_once (LIB_ROOT . '/SimpleBtstore.php');
	}

	if (file_exists(DEF_ROOT . '/Classes.def.php' )) {
		require_once (DEF_ROOT . '/Classes.def.php');

		function __autoload($className)
		{
			$className = strtolower ( $className );
			if (isset ( ClassDef::$ARR_CLASS [$className] )){
				require (ROOT . '/' . ClassDef::$ARR_CLASS [$className]);
			}
			else{
				trigger_error ( "class $className not found", E_USER_ERROR );
			}
		}
	}
	
	$db = DatabaseConnection::getInstance();
	$logid = Util::genLogId();
	RPCContext::getInstance()->getFramework()->initExtern($db -> getGroup(), $db -> getServerIP(), $logid, $db -> getDatabseName(), '', 0);

	$GLOBALS['config'] = getJsonFromFile("./config/config.json");
	getLanguageConfig();
	
	function getLanguageConfig() {
		$languageURL = $GLOBALS['config']['language'];
		$GLOBALS['language'] = getJsonFromFile("./config/".$languageURL);
	}
	function getLanguageByKey($key) {
		$data = $GLOBALS['language'][$key];
		if ($data == null || $data == "") {
			return $key;
		}
		return $data;
	}
    function isExist($table, $where) {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		
		$sql = sprintf("SELECT count(*) FROM %s WHERE %s", $table, $where);
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count;
    }
    function isExistT9($table, $where) {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getT9Connect();
		
		$sql = sprintf("SELECT count(*) FROM %s WHERE %s", $table, $where);
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count;
    }
    function getListLevelByID($idLevel) {
		$listLevelXML = getJsonFromFileXML("./config/level_up_exp.xml");
		$listLevelXML = $listLevelXML['level_up_exp'];
		
		foreach($listLevelXML as $data) {
			if (array_key_exists('@attributes', $data)) {
				$detail = $data['@attributes'];
			} else {
				$detail = $data;
			}
			if ($idLevel == $detail['id']) {
				return $detail;
			}
		}
		return null;
    }
    function getMainHero($field) {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		
		$sql = sprintf("SELECT %s FROM t_hero WHERE uid=%s AND (htid = '11001' OR htid = '11002' OR htid = '11003' OR htid = '11004' OR htid = '11005' OR htid = '11006')", $field, $GLOBALS['accID']);
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetch();
		return $result[$field];
    }
	function convertArrayQueryToArray($queryArray, $id) {
		$result = array();
		foreach($queryArray as $index => $query) {
			$result[$index] = $query[$id];
		}
		return $result;
	}
	function getDataFromPostKey($postKey) { 
		if (!isset($_POST[$postKey])){
			return "";
		} else { 
			return trim($_POST[$postKey]);
		}
	}
	function getJsonFromFile($filePath) {
		// Read JSON file
		$json = file_get_contents($filePath);

		//Decode JSON
		return json_decode($json,true);
	}
	function getJsonFromFileXML($filePath) {
		// Read JSON file
		$json = file_get_contents($filePath);

		$new = simplexml_load_string($json);
		// Convert into json 
		$con = json_encode($new); 

		// Convert into associative array 
		return json_decode($con,true);
	}
	function findObjectFromArray($array, $id) {
		foreach($array as $data) {
			if ($data['@attributes']['ident'] == $id){
				return $data['@attributes'];
			}
		}
	}
	function findObjectFromArrayWhere($array, $where, $id) {
		foreach($array as $data) {
			$detail = $data['@attributes'];
			if ($detail[$where] == $id){
				return $data['@attributes'];
			}
		}
	}
	function isEnable($id) {
		$item = getItemMenuIndex($id);
		if ($item['enable']) {
			return true;
		} else {
			return false;
		}
	}
	function isUpdate($id) {
		if (isEnable($id)) {
			if (isAdmin($id)){
				if (isLoginAdmin()) {
					return true;
				} else {
					return false;
				}
			} else { 
				return true;
			}
		} else {
			alert(getLanguageByKey('UTILS_FUNCTION_NOT_ENABLE'));
			return false;
		}
	}
	function isAdmin($id) {
		$item = getItemMenuIndex($id);
		return $item['isAdmin'];
	}
	function getItemMenuIndex($id) {
		$menuIndex = $GLOBALS['config']['menuIndex'];
		return $menuIndex[$id];
	}
	function isServerRunning() {
		exec("pgrep lcserver", $pids);
		if(empty($pids)) {
			return false;
		} else {
			return true;
		}
	}
	function isServerCheckFullRunning($server) {
		exec("pgrep ".$server, $pids);
		if(empty($pids)) {
			return false;
		} else {
			return true;
		}
	}
    function updateSuccess($functionText) {
		alert(sprintf(getLanguageByKey('UTILS_FUNCTION_UPDATE_SUCCESS'), $functionText));
    }
    function insertSuccess($functionText) {
		alert(sprintf(getLanguageByKey('UTILS_FUNCTION_INSERT_SUCCESS'), $functionText));
    }
    function alert($functionText) {
		echo '<script language="javascript">';
		echo 'alert("'.$functionText.'")';
		echo '</script>';
    }
?>