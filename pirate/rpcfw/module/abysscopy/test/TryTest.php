<?php

require_once('/home/pirate/test/UserClient.php');




class MyLog
{
	private static $fid;

	public static function init($filename)
	{
		self::$fid = fopen($filename, 'w');
	}

	private static function log($arrArg, $print = 0)
	{		
		
		$arrMicro = explode ( " ", microtime () );
		$content = '[' . date ( 'Ymd H:i:s ' );
		$content .= sprintf ( "%06d", intval ( 1000000 * $arrMicro [0] ) );
		$content .= "]";

		foreach ( $arrArg as $idx => $arg )
		{
			if ($arg instanceof BtstoreElement)
			{
				$arg = $arg->toArray ();
			}
			if (is_array ( $arg ))
			{
				$arrArg [$idx] = var_export ( $arg, true );
			}
		}
		$content .= call_user_func_array ( 'sprintf', $arrArg );
		$content .= "\n";

		if($print)
		{
			echo $content;
		}
		fprintf(self::$fid, $content);
			
	}
	public static function debug()
	{
		$arrArg = func_get_args ();
		self::log($arrArg, false);
	}
	public static function info()
	{
		$arrArg = func_get_args ();
		self::log($arrArg, true);
	}
	public static function fatal()
	{
		$arrArg = func_get_args ();
		self::log($arrArg, true);
	}
}



class TryTest extends BaseScript
{
	public static $OBJ_NAME = array(
	);
	
	public $curRoomInfo = array();
	
	public $passCopy = false;
	
	protected function executeScript ($arrOption)
	{
		foreach( AbyssCopyDef::$OBJ_TYPE as $key => $value)
		{
			self::$OBJ_NAME[$value] = $key;
		}
		
		MyLog::init("log");
		
		$allCopy = AbyssCopy::getCopyConf(0);
		
		foreach($allCopy as $copyId => $copyConf)
		{
			MyLog::info('==================[try copy:%d]', $copyId);
			$ret = $this->testCopy($copyId);
			if(!$ret)
			{
				MyLog::fatal("try copy failed： %d\n%s", $copyId, $copyConf);
				break;
			}
			sleep(2);
		}
		

		//$this->testAbyss();
		//$this->testTeam();
	}
	
	function testCopy($copyId)
	{
		$user = new UserClient(NULL, NULL, 55707, 23769);
		$user->setClass('console');
		$user->execute('abyss buynum 0');
	
		$user->setClass('abysscopy');
		$ret = $user->getUserInfo();
		if( !isset($ret['copyList'][$copyId]))
		{
			//MyLog::info('cant enter copy:%d', $copyId);
			//return false;
		}
		
		$user->setClass('team');
		$user->enter($copyId );
		$user->setClass('abysscopy');
	
		$ret = $user->buyChallengeNum(2);
		MyLog::info("buyChallengeNum:\n%s", $ret);		
	
		$ret = $user->create($copyId, false, 2);
		MyLog::info("-------------[create]:\n%s", $ret);
	
		$user1 = new UserClient(NULL, NULL, 29438, 20178);
		$user1->setClass('console');
		$user1->execute('abyss clgnum 10');
		$user1->setClass('team');
		$user1->enter($copyId);
		$user1->setClass('team.excute.abysscopy');
		$ret = $user1->join($copyId, $user->uid);
		MyLog::info("-------------[join]:\n%s", $ret);
	
		$user->setClass('team');
		$ret = $user->start( );

		$user->setClass('team.excute.abysscopy');

		MyLog::info("\n-------------[wait for start]");
		$ret = $user->receiveData('sc.abysscopy.start');
	
		$copyConf = AbyssCopy::getCopyConf($copyId);
		$curRoomId = current($copyConf['roomIdArr']->toArray());
		$this->curRoomInfo = $user->enterRoom($curRoomId,0,0);
		MyLog::info("-------------[enterRoom]:\n%s", $this->curRoomInfo);
	
		$ret = $user1->enterRoom($curRoomId,0,0);
	
		$ret = $user->getAllUser();
		MyLog::info("-------------[getAllUser]:\n%s", $ret);
	
		$ret = $user->chat("test123");
		MyLog::info("-------------[chat]:\n%s", $ret);
		
		//加点战斗次数，要不然打不过啊
		$user->setClass('console');
		$user->execute('abyss fightNum 1000');
		$user->setClass('team.excute.abysscopy');
		
		$this->passCopy = false;
		//打怪
		while(!$this->passCopy)
		{
			try 
			{
				foreach($this->curRoomInfo['enemyAnchors'] as $anchorId => $anchorInfo)
				{
					if($anchorInfo['state'] == AbyssCopyDef::$ENEMY_STATE['CAN_ATK'])
					{
						$ret = $user->onArmy($anchorId,NULL,NULL);
						MyLog::info("-------------[onArmy]:%s", $ret['ret']);
						
						$this->waitMsg($user,true,false);
					}
				}
				foreach($this->curRoomInfo['boxes'] as $boxId => $boxInfo)
				{
					if($anchorInfo['state'] == AbyssCopyDef::$BOX_STATE['CAN_OPEN'])
					{
						$ret = $user->onTrigger($boxId);
						MyLog::info("-------------[onTrigger:box]:%s", $ret['ret']);
						if(isset($ret['questId']))
						{
							MyLog::info('add question');
							$this->curRoomInfo['quests'][$boxId] = $ret['questId'];
						}
						$this->waitMsg($user, true, false);
					}	
				}
				foreach($this->curRoomInfo['vials'] as $vialId => $vialInfo)
				{
					$ret = $user->onTrigger($boxId);
					MyLog::info("-------------[onTrigger:vial]:%s", $ret['ret']);
						
					$this->waitMsg($user, true, false);
				}
				foreach($this->curRoomInfo['quests'] as $triggerId =>$questId)
				{
					$ret = $user->onQuestion($triggerId, 1);
					MyLog::info("-------------[onQuestion]:%s", $ret['ret']);
			
					$this->waitMsg($user, false, false);
				}
				
				foreach($this->curRoomInfo['teleports'] as $teleportId => $teleInfo)
				{
					$teleportConf = AbyssCopy::getTeleportConf($teleportId);
					if($teleportConf['toRoomId'] > $curRoomId)
					{
						$curRoomId = $teleportConf['toRoomId'] ;
						MyLog::info("\n---------------[new room open:%d]", $curRoomId);
						$ret = $user->leaveRoom();
						$this->curRoomInfo = $user->enterRoom($curRoomId, 0, 0);
						
						MyLog::info("-------------[enterRoom]:\n%s", $this->curRoomInfo);
					}
				}			
			}
			catch ( Exception $e )
			{
				var_dump($e->getMessage());
			}		
		}
		//通关
		$ret = $user->dealCard(1);

		MyLog::info("\n---------------[dealCard]:\n%s", $ret);
		$ret = $user->flopCard(1);
		MyLog::info("\n---------------[flopCard]:\n%s", $ret);
		$ret = $user->flopCard(3);
		MyLog::info("\n---------------[flopCard]:\n%s", $ret);
		$ret = $user->rewardCard(3);
		MyLog::info("\n---------------[rewardCard]:\n%s", $ret);
		$ret = $user->leave();
		MyLog::info("\n---------------[leave]:\n%s", $ret);
		
		unset($user);
		unset($user1);
		return true;
	}
	
	function waitMsg(&$user, $waitObj, $waitUser)
	{

		while($waitObj || $waitUser)
		{
			$ret = $user->receiveAnyData();
			MyLog::debug("-------------[anyData]:\n%s", $ret);
			foreach($ret as $msg)
			{
				switch($msg['callback']['callbackName'])
				{
					case AbyssCopyConf::$FRONT_CALLBACKS['modifyObj']:
						foreach($msg['ret']['data'] as $modify)
						{
							$this->modifyObj($modify);
						}
						$waitObj = false;
						break;
					case AbyssCopyConf::$FRONT_CALLBACKS['modifyUser']:
						MyLog::debug("-------------[modifyUser]:\n%s", $msg);
						$waitUser = false;
						break;
					case AbyssCopyConf::$FRONT_CALLBACKS['copyPassed']:
						MyLog::info("-------------[copyPassed]:\n%s", $msg);
						$this->passCopy = true;
						break;
				}
			}
		}
	}
	
	function modifyObj($data)
	{
		switch($data['op'])
		{
			case AbyssCopyDef::$OP_TYPE['ADD']:
				MyLog::info('add:%s id:%d room:%d', self::$OBJ_NAME[$data['type']],$data['id'],$data['roomId']);
				switch($data['type'])
				{
					case AbyssCopyDef::$OBJ_TYPE['BOX']:
						$this->addBox($data['id'], $data['data']);
						break;
					case AbyssCopyDef::$OBJ_TYPE['ENEMY']:
						$this->addEnemy($data['id'], $data['data']);
						break;
					case AbyssCopyDef::$OBJ_TYPE['TELEPORT']:
						$this->addTeleport($data['id'], $data['data']);
						break;
					default:
						MyLog::fatal('not suport');
						break;
				}
				break;
			case AbyssCopyDef::$OP_TYPE['DEL']:
				MyLog::info('del:%s id:%d room:%d', self::$OBJ_NAME[$data['type']],$data['id'],$data['roomId']);
				switch($data['type'])
				{
					case AbyssCopyDef::$OBJ_TYPE['BOX']:
						$this->delBox($data['id']);
						break;
					case AbyssCopyDef::$OBJ_TYPE['VIAL']:
						$this->delVial($data['id']);
						break;
					case AbyssCopyDef::$OBJ_TYPE['ENEMY']:
						$this->delEnemy($data['id']);
						break;
				}
				break;
			case AbyssCopyDef::$OP_TYPE['MODIFY']:
				MyLog::info('modify:%s id:%d room:%d', self::$OBJ_NAME[$data['type']],$data['id'],$data['roomId']);
				switch($data['type'])
				{
					case AbyssCopyDef::$OBJ_TYPE['BOX']:
						$this->modifyBox($data['id'], $data['data']);
						break;
					case AbyssCopyDef::$OBJ_TYPE['ENEMY']:
						$this->modifyEnemy($data['id'], $data['data']);
						break;
				}
				break;
		}
		
	}
	function addBox($id, $info)
	{
		if(isset($this->curRoomInfo['boxes'][$id]))
		{
			MyLog::fatal('box:%d exist', $id);
			exit();
		}
		$this->curRoomInfo['boxes'][$id] = $info;
	}
	function addVial($id, $info)
	{
		if(isset($this->curRoomInfo['vials'][$id]))
		{
			MyLog::fatal('vial:%d exist', $id);
			exit();
		}
		$this->curRoomInfo['vials'][$id] = $info;
	}
	function addEnemy($id, $info)
	{
		if(isset($this->curRoomInfo['enemyAnchors'][$id]))
		{
			MyLog::fatal('enemy:%d exist', $id);
			exit();
		}
		$this->curRoomInfo['enemyAnchors'][$id] = $info;
	}
	function addTeleport($id, $info)
	{
		if(isset($this->curRoomInfo['teleports'][$id]))
		{
			MyLog::fatal('teleports:%d exist', $id);
			exit();
		}
		$this->curRoomInfo['teleports'][$id] = $info;
	}
	function modifyBox($id, $info)
	{
		if(!isset($this->curRoomInfo['boxes'][$id]))
		{
			MyLog::fatal('box:%d not exist', $id);
			exit();
		}
		foreach($info as $k => $v)
		{
			$this->curRoomInfo['boxes'][$id][$k] = $v;
		}		
	}
	function modifyEnemy($id, $info)
	{
		if(!isset($this->curRoomInfo['enemyAnchors'][$id]))
		{
			MyLog::fatal('enemyAnchor:%d not exist', $id);
			exit();
		}
		foreach($info as $k => $v)
		{
			$this->curRoomInfo['enemyAnchors'][$id][$k] = $v;
		}
	}
	function delBox($id)
	{
		if(!isset($this->curRoomInfo['boxes'][$id]))
		{
			MyLog::fatal('box:%d not exist', $id);
			exit();
		}
		unset($this->curRoomInfo['boxes'][$id]);
	}
	function delVial($id)
	{
		if(!isset($this->curRoomInfo['vials'][$id]))
		{
			MyLog::fatal('vials:%d not exist', $id);
			exit();
		}
		unset($this->curRoomInfo['vials'][$id]);
	}
	function delEnemy($id)
	{
		if(!isset($this->curRoomInfo['enemyAnchors'][$id]))
		{
			MyLog::fatal('enemyAnchors:%d not exist', $id);
			exit();
		}
		unset($this->curRoomInfo['enemyAnchors'][$id]);
	}

	function testAbyss()
	{
		$copyId = 400001;
		$user = new UserClient(NULL, NULL, 55707, 23769);


		$user->setClass('team');
		$user->enter($copyId );


		$user->setClass('abysscopy');
		
		$ret = $user->getUserInfo($copyId, false, 1);
		echo "------------------------------getUserInfo\n";		
		var_dump($ret);


		$ret = $user->buyChallengeNum(10);
		echo "------------------------------buyChallengeNum\n";		
		var_dump($ret);



		$ret = $user->create($copyId, false, 2);
		echo "------------------------------create\n";		
		var_dump($ret);


		$user1 = new UserClient(NULL, NULL, 29438, 20178);
		$user1->setClass('team');
		$user1->enter($copyId);
		$user1->setClass('abysscopy');
		$ret = $user1->join($copyId, $user->uid);
		echo "------------------------------join\n";		
		var_dump($ret);

		$user->setClass('team');
		$ret = $user->start( ); 
		var_dump($ret);

		
		$user->setClass('team.excute.abysscopy');

		echo "wait for start\n";
		$ret = $user->receiveData('sc.abysscopy.start');
		var_dump($ret);

		$ret = $user->enterRoom(1,0,0);
		echo "------------------------------enterRoom\n";		
		var_dump($ret);

		$ret = $user->getAllUser();
		echo "------------------------------getAllUser\n";		
		var_dump($ret);

		$ret = $user->chat("test123");
		echo "------------------------------chat\n";		
		var_dump($ret);

		$ret = $user->onArmy(101,NULL,NULL);
		echo "------------------------------onArmy\n";		
		var_dump($ret);
		
		$ret = $user->onTrigger(1);
		echo "------------------------------onTrigger\n";		
		var_dump($ret);

		$ret = $user->onQuestion(1, 1);
		echo "------------------------------onQuestion\n";		
		var_dump($ret);

		$ret = $user->dealCard();
		echo "------------------------------dealCard\n";		
		var_dump($ret);

		$ret = $user->flopCard(1);
		echo "------------------------------flopCard\n";		
		var_dump($ret);

		$ret = $user->rewardCard(1);
		echo "------------------------------rewardCard\n";		
		var_dump($ret);

		while(true)
		{
			$ret = $user->receiveAnyData();
			var_dump($ret);
		}
	}
	
	

	function testTeam()
	{
		$copyId = 400001;
		$user = new UserClient(NULL, NULL, 55707, 23769);

		$user->setClass('team');
		$user->enter($copyId );
		$user->setClass('abysscopy');
		$ret = $user->create($copyId, false, 1);
		

		$user1 = new UserClient(NULL, NULL, 29438, 20178);
		$user1->setClass('team');
		$user1->enter($copyId);
		$user1->setClass('abysscopy');
		$ret = $user1->join($copyId, $user->uid);

		$user->setClass('team');
		$ret = $user->start( ); 


		$user->setClass('team.excute.abysscopy');
		$user1->setClass('team.excute.abysscopy');
		
		echo "wait for start\n";
		$ret = $user->receiveData('sc.abysscopy.start');
		var_dump($ret);


		$ret = $user->enterRoom(1,0,0);
		$ret = $user1->enterRoom(1,0,0);
		

		$user->setAsync(true);
		$user1->setAsync(true);

		$ret = getUserList(array(), 10, array($user->uid, $user1->uid) );
		$userObjList = array();	
		foreach($ret as $value)
		{
			$obj = new UserClient(NULL, NULL, $value['pid'], $value['uid']);
			$obj->setClass('team');
			$obj->enter($copyId);
			$obj->setClass('abysscopy');
			$ret = $obj->join($copyId, $user->uid);
			$obj->setClass('team.excute.abysscopy');
			$obj->setAsync(true);
			
			$userObjList[] = $obj;
			
			printf("prepare uid:%d\n", $value['uid']);
		}	
		
		while(1)
		{
			foreach( $userObjList as $obj)
			{
				$ret = $obj->getAllUser();
				echo $obj->uid."\n";
			}


			continue;
			$ret = $user->getAllUser();	
			$ret = $user1->getAllUser();	
			var_dump($ret);
		}	


		
	}
	function testAbyssGetInfo()
	{
		$user = new UserClient();	

		$user->setClass('abysscopy');
	
		$ret = $user->getUserInfo();
		var_dump($ret);
	}

	function testDataMem()
	{
		$ret = McClient::set("index_1", array("k1"=>"value1", "k2"=>"helo world"));
		var_dump($ret);



		$ret = McClient::get("index_1");
		var_dump($ret);
	}

	function testMem()
	{
		$proxy = new PHPProxy ( 'data' );
		$proxy->setClass('mem');


		$ret = $proxy->set(1, "index_1", array("k1"=>"value1", "k2"=>"helo world"));
        var_dump($ret);

        $ret = $proxy->get(1, "index_1");
        var_dump($ret);

        $ret = $proxy->getAll();
        var_dump($ret);


		for($i = 0; $i < 200; $i++)
		{
			$ret = $proxy->set(1, "index_{$i}", "helo world===");
			var_dump($ret);
		}
	}
}

