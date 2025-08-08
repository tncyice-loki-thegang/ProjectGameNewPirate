<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Robot.php 37487 2013-01-29 09:52:21Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/groupwar/test/Robot.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2013-01-29 17:52:21 +0800 (二, 2013-01-29) $
 * @version $Revision: 37487 $
 * @brief 
 *  
 **/
require_once (LIB_ROOT . '/RPCProxy.class.php');
require_once('UserClient.php');





class MyLog
{
	private static $fid;
	//private static $showInCmd = false;
	
	public static function init($filename)
	{
		self::$fid = fopen($filename, 'w');
	}
	
	public static function log($msg)
	{
		$curTime = date('h:i:s');
		fprintf(self::$fid, "[%s]%s\n", $curTime, $msg);
	}
	public static function info($msg)
	{
		$curTime = date('h:i:s');
		printf("[%s]%s\n", $curTime, $msg);
		fprintf(self::$fid, "[%s]%s\n", $curTime, $msg);
	}
}




class Robot extends BaseScript
{
	public $battleId = 0;
	public static $roadNum = 5;
	
	public $pid = 20012;
	public $uid = 20100;
	
	
	const CALL_BACK_REFRESH = 'sc.groupwar.refresh';
	const CALL_BACK_FIGHT_RESULT = 'sc.groupwar.fightResult';
	const CALL_BACK_BATTLE_END = 'sc.groupwar.battleEnd';
	const CALL_BACK_TOUCH_DOWN = 'sc.groupwar.touchDown';
	

	/*
	public $mNewUserProb = 0.2;
	public $mJoinProb  = 0.6;
	public $mInspireProb  = 0;	
	public $mLeaveProb = 0;
	public $mLogoffProb = 0;
	public $mLeaveBackProb = 0.9;
	public $mLogoffBackProb = 0.9;
	*/
	
	public $lastJoinTime = 0;
	
	public $unitList;		//战斗中之人
	public $userList = array(1=>array(),2=>array());
	public $uidList = array(1=>array(),2=>array());

	public $userIndex = array(1=>0,2=>0);

	function __construct()
	{

		self::$roadNum = GroupWarConfig::$FIELD_CONF['roadNum'];
	}
	
	public function getUserList()
	{
		$i = 1;
		$uid = self::$uid;
		$this->userInfoList = array();
		while($i >0)
		{
			MyLog::log("getUserList");
			$list = getUserList(array(
					array('pid', '>',10000),
					array('uid', '>',$uid),
			),100 );
			if(count($list) <= 0)
			{
				break;
			}
			$this->userInfoList = array_merge($this->userInfoList, $list);
			$uid = $list[count($list)-1]['uid'];
			$i--;
		}
		$num  = count($this->userInfoList);
		MyLog::log( "get {$num} users");		
	}
	
	public function getUsersOfGroup($groupId, $offset, $num)
	{		
		$data = new CData();

		$uidList = $data->select(array('uid'))
					->where( array('group_id', '=', $groupId))
					->where( array('uid', '!=', 23769))
					->orderBy('uid', true)
					->limit($offset, $num)
					->from('t_group_war_user')->query();
		
		$uidList = Util::arrayExtract($uidList, 'uid');
		$num = count($uidList);
		
		
		
		
		$begin = 0;
		$userInfoList = array();
		while( $begin < $num)
		{
			$limit = min( $num-$begin, CData::MAX_FETCH_SIZE-1);
		
			$uids = array_slice($uidList, $begin, $limit);
	
			$ret = $data->select(array('pid','uid'))->where(array('uid', 'IN', $uids))
					->from('t_user')->query();
						
			$userInfoList = array_merge($userInfoList, $ret);
			$begin = $begin + $limit + 1;
		}
		
		$observer = array_shift($userInfoList);
		$this->pid = $observer['pid'];
		$this->uid = $observer['uid'];
		
		foreach($userInfoList as $value)
		{
			$this->userList[$groupId][$value['uid']] = new UserClient($value['pid'], $value['uid'], $groupId);
		}
	}
	
		
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	*/
	protected function executeScript($arrOption)
	{		
		$procId = posix_getpid();
		MyLog::init("log/robot_$procId");
		
		$index = intval($arrOption[0]);
		$num = 20;
		if( count($arrOption) > 1)
		{
			$num = intval($arrOption[1]);
		}
		$offset = ($index-1) * $num;
		
		MyLog::info("offset:$offset, num:$num");
		
		
		$this->getUsersOfGroup(1, $offset, $num);
		$this->getUsersOfGroup(2, $offset, $num);	
		
		
	
		foreach($this->userList as $groupId => &$group)
		{
			foreach($group as &$user)
			{
				$this->uidList[$groupId][] = $user->uid;
				unset($user);
			}
			unset($group);
		}
		
	

		$debugRoads = array(1,2,3);
		$fightNum = 0;
		while(true)
		{
			$curTime = date('h:i:s');
			echo "\n=======================fight:{$fightNum} {$curTime}=============================\n";
			$fightNum++;

			//观察者			
			$observer = new UserClient ($this->pid, $this->uid);
			MyLog::info( "add observer:{$observer->uid}");
			
			MyLog::info( "observer enter");
			$ret = $observer->tryEnter( true);
			if($ret['ret'] == 'fake')
			{
				echo "fake, maybe not a game time\n";
				exit(0);
			}
			$this->battleId= $ret['res']['battleId'];
			
			MyLog::info( "oberver ready, wait for game start, battleId:{$ret['res']['battleId']}");
			$this->unitList = array();
			foreach ( $ret['res']['field']['road'] as $unit)
			{
				$this->unitList[$unit['id']] = $unit;
			}
			Logger::info('unitList:%s', var_export($this->unitList, true));
							
			$startTime = time();
			$endTime = $ret['res']['field']['endTime']/1000;
			$leftTime = $endTime - time();
			
			
			$msgQueue = array();
			//战斗过程
			while(true)
			{
				if(empty($msgQueue))
				{
					try
					{
						$ret = $observer->receiveAnyData();
					}
					catch ( Exception $e )
					{
						echo "receive failed:".$e->getMessage()."\n";	
						continue;				
					}
					$msgQueue = array_merge($msgQueue, $ret);
				}
				$field = NULL;
				$msg =  array_shift($msgQueue);
				$callback = $msg['callback']['callbackName'];
				if($callback == self::CALL_BACK_REFRESH)
				{
					$field = $msg['ret'];
				}
				else if($callback == self::CALL_BACK_FIGHT_RESULT)
				{
					$fightResult = $msg['ret'];
					//echo "fightResult:".var_export($fightResult,true)."\n";
					MyLog::log("uid:{$fightResult['winnerId']} win");
					MyLog::log("uid:{$fightResult['loserId']} lose");
					//ASSERT_TRUE(isset($this->unitList[$fightResult['winnerId']] ));
					//ASSERT_TRUE(isset($this->unitList[$fightResult['loserId']] ));
				
					$this->userLeave($fightResult['loserId']);				
				}
				else if($callback == self::CALL_BACK_BATTLE_END)
				{
					echo "battleEnd\n\n\n";
					break 2;
				}
				else 
				{
					echo "get other msg: {$callback}\n";				
				}
				
				if(!$field)
				{
					continue;
				}

		
				$leftTime--;
				$now = time();
				
						
				MyLog::info( "refresh--------->left:{$leftTime}");				
	
				
				if(!empty($field['field']['touchdown']))
				{
					foreach($field['field']['touchdown'] as $unit)
					{
						MyLog::log("uid:{$unit['id']} touch down");
						//ASSERT_TRUE(isset(  $this->unitList[$unit['id']] ));
						$this->userLeave($unit['id']);
					}
				}
				if(!empty($field['field']['leave']))
				{
					foreach($field['field']['leave'] as $unit)
					{
						MyLog::log("uid:{$unit['id']} leave");
						//ASSERT_TRUE(isset(  $this->unitList[$unit['id']] ));
						$this->userLeave($unit['id']);
					}
				}
				
				//更新每个战斗单位的数据
				$unitDist = array();
				foreach($field['field']['transfer'] as $key => $value)
				{
					$unitDist[$key] = $value;
				}
				$roadNum = 0;
				if(!empty($field['field']['road']))
				{
					foreach($field['field']['road'] as $unit)
					{
						if( !isset($this->unitList[$unit['id']]) )
						{
							//ASSERT_EQUAL(12, count($unit) );
							$this->unitList[$unit['id']] = $unit;
							MyLog::log("uid:{$unit['id']} enter");
						}
						else
						{							
							MyLog::log("uid:{$unit['id']} new info");
							foreach ($unit as $key => $value)
							{
								$this->unitList[$unit['id']][$key] = $value;
							}
						}
						$unitDist[ $this->unitList[$unit['id']]['transferId'] ] ++;
					}
					$roadNum = count($field['field']['road'] );
				}
				
				
				//看看延迟大不大
				$trueLeft = $endTime - $now;
				if( $trueLeft + 2 < $leftTime)
				{
					echo "WARN:too delay, trueLeft:{$trueLeft}\n";
					continue;
				}

				
				/*
				echo 'transfer:'.implode(' ',$field['field']['transfer'])."\n";				
				echo 'road:'.$roadNum."\n";
				
			
				
				foreach($this->unitList as $unit)
				{
					$str = '';
					$needKeys = array('id','name','transferId','roadX','stopX','curHp','maxHp');
					foreach ($unit as $key => $value)
					{
						if(in_array($key, $needKeys))
						{
							$str .= "[{$key}]:{$value}  ";
						}
					}
					echo "$str\n";			
				}
				echo "\n";
					*/		
				MyLog::info("unit num:".count($this->unitList));
				
				if(time() - $this->lastJoinTime > 0 )
				{
					foreach ( $unitDist as $transferId=> $num)
					{
						if($num < 10  && 
								in_array($transferId%self::$roadNum , $debugRoads)   )
						{
							$this->addClientSilence($transferId);
							//break;
						}
					}
					$this->lastJoinTime = $now;
				}
				
				
			}
					
			exit(0);
			unset($observer);
			echo "wait for child process\n";
			pcntl_wait($status);
	
			sleep(5);
		}

	}
	

	public function userLeave($uid)
	{
		
		unset($this->unitList[$uid]);
		if(isset($this->userList[1][$uid]))
		{			
			$this->userList[1][$uid]->leaveBattle();
			return;
		}
		
		if(isset($this->userList[2][$uid]))
		{			
			$this->userList[2][$uid]->leaveBattle();
			return;
		}
		MyLog::log("invalid leave:$uid");
		//var_dump($this->userList);
		//throw new Exception('invalid leave');
	}
	
	public function addClientSilence($transferId)
	{
		$t1 = microtime(true);
		if( ( $this->battleId-1 ) % 2 == 0)
		{
			$attackGroupId = 1;
		}
		else
		{
			$attackGroupId = 2;
		}
		if($transferId<self::$roadNum)
		{
			$groupId = $attackGroupId;
		}
		else
		{
			$groupId = 3 - $attackGroupId;
		}
		
		$user = NULL;
		$index = $this->userIndex[$groupId];
		do
		{
			$uid = $this->uidList[$groupId][$index];
			if( $this->userList[$groupId][$uid]->state != UserClient::STATE_JOIN)
			{
				$user = &$this->userList[$groupId][$uid];
				break;
			}			
			$index = ($index+1)%count($this->uidList[$groupId]);
		}while($index != $this->userIndex[$groupId]);
		

		if( empty($user) )
		{
			MyLog::log("[FATAL]no user left");
			return false;
		}
		$this->userIndex[$groupId] = ($index+1)%count($this->uidList[$groupId]);
		try 
		{
			$user->tryJoin($transferId);
		}
		catch ( Exception $e )
		{
			echo "addClientSilence failed:".$e->getMessage()."\n";
			$this->userLeave($user->uid);
		}
		
		$delt = (microtime(true) - $t1) * 1000;
		MyLog::log("add client:$uid use time:$delt");
		
	}
	
	
	/*
	public function addClient($transferId)
	{
		
		$procId = pcntl_fork();
		if ($procId == -1)
		{
			die("could not fork");
		} 
		else if ($procId) 
		{
			return;
		} 
		else 
		{
			try
			{
				$this->runClient($pid, $uid, $transferId);
			}
			catch ( Exception $e )
			{
				echo "failed:user[$uid]".$e->getMessage()."\n";
				Logger::fatal ( $e->getTraceAsString () );
				exit();
			}
		}
	}
	
	
	
	public function runClient($pid, $uid, $transferId)
	{
		$isAttacker = $transferId<self::$roadNum;
		$state = 2;//0:offline, 1:out, 2:enter, 3:join
		$user =  addUser($pid, $uid, $isAttacker, $this->battleId);
		$ret = $user->tryJoin( $transferId%self::$roadNum);
		if($ret)
		{
			$state = 3;
		}

		//$procId = posix_getpid();
		MyLog::init("./log/client_{$uid}");
		MyLog::log("uid:$uid");
		while(true)
		{			
			switch($state)
			{
				case 0:
					$user =  addUser($pid, $uid, $isAttacker);
					$state = 2;
					break;
				case 1:
					MyLog::log( 'come back');
					$ret = $user->tryEnter();
					ASSERT_EQUAL('ok', $ret['ret']);
					$state = 2;
					break;
				case 2:
					MyLog::log( 'try join');
					$ret = $this->tryJoin($user, $transferId);
					if($ret)
					{
						$state = 3;
					}
					break;
				case 3:
					break;
				default:
					MyLog::log( 'invalid state');
					break 2;
			}
			$ret = $user->receiveAnyData();
			$field = null;
			$fightResult = null;
	
			//得到了什么消息
			foreach ($ret as $value)
			{
				$callback = $value['callback']['callbackName'];
				if($callback == self::CALL_BACK_REFRESH)
				{
					$field = $value['ret'];
				}
				else if($callback == self::CALL_BACK_FIGHT_RESULT)
				{										
					if($value['ret']['loserId'] == $user->uid)
					{
						MyLog::log( "I[{$user->uid}] lose");
						break 2;
					}	
				}
				else if($callback == self::CALL_BACK_BATTLE_END)
				{
					MyLog::log( "battle end");
					break 2;
				}
				else 
				{
					echo "get other msg: {$callback}\n";
				}
			}
			if(!$field)
			{
				continue;
			}		
			$leftTime = $field['field']['leftTime'];
			//$refreshTime = date('h:i:s', $field['time']);
			MyLog::log( "leftTime:$leftTime");
			
			foreach($field['field']['touchdown'] as $unit)
			{
				if($unit['id'] == $user->uid)
				{					
					MyLog::log("touch down");
					break 2;
				}
			}

			if( 0 )
			{
				//随机鼓舞
				if($this->mInspireProb > rand1())
				{
					$this->tryInspire($user);
				}
				//随机离开
				if($this->mLeaveProb > rand1())
				{
					$ret = $user->leave(self::$battleId);					
					ASSERT_EQUAL('ok', $ret);
			
					if($this->mLeaveBackProb <= rand1())
					{
						$user->close();	
						unset($user);
						exit("user[$uid] leave, not back\n");
					}			
					$state = 1;
				}
				//随机下线
				if($this->mLogoffProb > rand1())
				{
					$user->close();
					unset($user);					
					if($this->mLogoffBackProb <= rand1())
					{
						exit("user[$uid] logoff, not back\n");
					}
					$state = 0;
				}
			}
		}
				
		$user->close();
		unset($user);
		MyLog::log("exit");
		exit(0);
	}
	
	public function tryInspire(&$user)
	{
		//ok, cdtime, lack_cost, no_inspire, full
		$uid = $user->uid;
		$now = time();
		$gold = $user->getUserGold();
		$isGold = false;
		if(0.5 > rand1())
		{
			$ret = $user->inspire(false);			
		}
		else
		{
			$ret = $user->inspire(true);
			$isGold = true;
		}
		echo "user[$uid] inspire, ret:{$ret['ret']}\n";
		if($now - $user->lastInspireTime <= GroupWarConfig::$INSPIRE_CONF['cdTime'])
		{
			ASSERT_EQUAL('cdtime', $ret['ret']);			
		}
		if( $isGold&& $gold < GroupWarConfig::$INSPIRE_CONF['goldNum'] )
		{
			ASSERT_EQUAL('lack_gold', $ret['ret']);
		}
		
	}

*/
	




}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
