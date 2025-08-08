<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: UserClient.php 37487 2013-01-29 09:52:21Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/groupwar/test/UserClient.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2013-01-29 17:52:21 +0800 (二, 2013-01-29) $
 * @version $Revision: 37487 $
 * @brief 
 *  
 **/


function ASSERT_EQUAL($expected, $actual)
{
	if ($expected !== $actual)
	{
		$bt = debug_backtrace();
		exit('['.date('h:i:s').']'.'FAILED:['.$bt[1]['function'].':'.$bt[0]['line'].']'.
				'expected:['.$expected.'] actual:['.$actual.']'."\n");
	}
}
function ASSERT_TRUE($var)
{
	if (!$var)
	{
		$bt = debug_backtrace();
		exit('['.date('h:i:s').']'.'FAILED:['.$bt[1]['function'].':'.$bt[0]['line'].']'."\n");
	}
}
function EXPECT_EQUAL($expected, $actual)
{
	if ($expected !== $actual)
	{
		$bt = debug_backtrace();
		echo('['.date('h:i:s').']'.'FAILED:['.$bt[1]['function'].':'.$bt[0]['line'].']'.
				'expected:['.$expected.'] actual:['.$actual.']'."\n");
	}
}

function createBattle($battleId)
{
	echo "createBattle\n";
	Logger::debug('createBattle:%d', $battleId);
	$groupWar = new GroupWar();
	$groupWar->createBattle( $battleId );

}

function endBattle($battleId)
{

	Logger::debug('endBattle:%d', $battleId);
	$attackGroup = array(
			'groupId' => 2,
			'resource' => 1000,
			'userList' => array());
	$defendGroup = array(
			'groupId' => 3,
			'resource' => 1000,
			'userList' => array());
	$proxy = new ServerProxy ();
	$proxy->asyncExecuteRequest ( 0,  'groupwar.battleEnd',
			array($battleId, $attackGroup, $defendGroup));
	echo "endBattle sleep 2 second\n";
	sleep(2);
}



function getUserList($wheres, $num=0)
{
	$badUids = array(20102);//烂号
	
	$select = array('pid', 'uid');

	$data = new CData();

	$data->select($select)->from('t_user');

	$wheres[] = array('dtime', '>', 0);
	$wheres[] = array('vip', '=', 0);

	foreach ( $wheres as $where )
		$data->where($where);

	if($num>0)
	{
		$num += count($badUids); 
		if($num > CData::MAX_FETCH_SIZE)
		{
			$num = CData::MAX_FETCH_SIZE;
		}
		$data->limit(0, $num);
	}
	
	$ret =  $data->query();
	
	$returnData = array();
	foreach($ret as $key => $value)
	{
		//$user  = EnUser::getUserObj($value['uid']);
		if(  !in_array($value['uid'],$badUids) )// && $user->getLevel() < 50  )
		{
			$returnData[] = $value;
		}
		
	}
	return $returnData;
}	

function rand1()
{
	return  rand(0,10000)/10000;
}




class UserClient
{

	const HOST = '192.168.1.37';
	
	const PORT = 7777;

	private $socket;

	private $public;

	private $clazz;

	private $requestType;

	private $token;

	private $async;

	private $callback;
	
	public $roadNum = 0;
	
	const STATE_LOGOFF = 0;
	const STATE_LOGIN = 1;
	const STATE_ENTER = 2;
	const STATE_JOIN = 3;	
	
	
	public $pid = 0;	
	public $uid = 0;	
	public $battleId = 0;
	public $groupId = 0;	
	public $transferId = -1;		
	
	public $state = self::STATE_LOGOFF;
	
	public $attackLevel = 0;
	public $defendLevel = 0;
	public $lastInspireTime = 0;

	function __construct($pid, $uid, $groupId = 0)
	{
		$this->pid = $pid;		
		$this->uid = $uid;
		$this->groupId = $groupId;
		
		$this->roadNum = GroupWarConfig::$FIELD_CONF['roadNum'];
		
		$this->public = true;
		$this->clazz = null;
		$this->requestType = RequestType::RELEASE;
		$this->token = "0";
		$this->async = false;
		$this->server = UserClient::HOST;
		$this->port = UserClient::PORT;
		$this->socket = null;
		$this->resetCallback();		
				
	}
	function loginGame()
	{
		if($this->state != self::STATE_LOGOFF)
		{
			return;
		}
		echo "[UserClient]user:{$this->uid} login\n";
		
		self::setClass ( 'user' );
		$ret = self::login ( $this->pid );
		if ($ret != 'ok')
		{
			exit( "login failed. pid:".$this->pid."\n");
		}
		$ret = self::userLogin($this->uid);
		if($ret != 'ok')
		{
			exit("userLogin failed. uid:".$this->uid."\n");
		}
		$this->setClass ( 'groupwar' );
		
		$this->state = self::STATE_LOGIN;
	}
	
	function tryEnter($needData = false)
	{			
		if($this->state != self::STATE_LOGIN)
		{
			$this->loginGame();
		}
		/*
		$minLevel = btstore_get()->GROUP_BATTLE['joinMinLevel'];
		$level = $this->getUserLevel();
		if( $level < $minLevel)
		{
			Logger::debug('user:%d level:%d too low. set to %d', $this->uid, $level, $minLevel);
			$this->setUserLevel( $minLevel);
		}
		*/
				
		$ret = $this->enter($this->battleId);
		if($ret['ret'] != 'ok')
		{
			return $ret;
		}
		$this->battleId = $ret['res'];
		$this->state = self::STATE_ENTER;
		echo "[UserClient]user:{$this->uid} enter\n";
		if(!$needData)
		{
			return $ret;
		}
		$ret = $this->getEnterInfo($this->battleId);
		if($ret['ret'] == 'ok')
		{			
			if($this->groupId > 0)
			{
				ASSERT_EQUAL($ret['res']['user']['groupId'], $this->groupId );
			}
			$this->groupId = $ret['res']['user']['groupId'];
			
			$ret['res']['battleId'] = $this->battleId;
		}
		return $ret;
	}
	
	public function tryJoin($transferId)
	{
		if($this->state != self::STATE_ENTER)
		{
			$this->tryEnter();
		}
		
		$this->transferId = $transferId;
		$ret = $this->join($this->battleId, $this->transferId);
		
		ASSERT_EQUAL('ok', $ret['ret']);
		$this->state = self::STATE_JOIN;
		
		echo "[UserClient]user:{$this->uid} join\n";
		return $ret;
	}
	
	public function tryLeave()
	{
		$ret = $this->leave($this->battleId);
		
		ASSERT_EQUAL('ok', $ret['ret']);
		
		$this->state = self::STATE_LOGIN;
		echo "[UserClient]user:{$this->uid} leave game\n";
		return $ret;
	}
	public function leaveBattle()
	{
		$this->state = self::STATE_ENTER;
		echo "[UserClient]user:{$this->uid} leave battle\n";
	}
	
	public  function getTransferId($groupId)
	{
		if( ( $this->battleId-1 ) % 2 == 0)
		{
			$attackGroupId = 1;
		}
		else
		{
			$attackGroupId = 2;
		}
		if($attackGroupId == $groupId)
		{
			$transferId = 0;
		}
		else
		{
			$transferId = $this->roadNum;
		}
		return $transferId;
	}
	


	public function getUserGold()
	{
		$this->setClass ( 'console' );
		$goldNum = $this->execute( 'getAttr  gold'  );
		$this->setClass ( 'groupwar' );
		return $goldNum;
	}
	public function setUserGold($num)
	{
		$this->setClass ( 'console' );
		$goldNum = $this->execute( "gold  $num"  );
		$newGoldNum = $this->execute( 'getAttr  gold'  );
		ASSERT_EQUAL($goldNum, $newGoldNum);
	
		$this->setClass ( 'groupwar' );
		return $goldNum;
	}
	public function getUserExp()
	{
		$this->setClass ( 'console' );
		$expNum = $this->execute( 'getAttr  exp'  );
		$this->setClass ( 'groupwar' );
		return $expNum;
	}
	public function setUserExp($num)
	{
		$this->setClass ( 'console' );
		$expNum = $this->execute( "experience  $num"  );
		$newExpNum = $this->execute( 'getAttr  exp'  );
		ASSERT_EQUAL($expNum, $newExpNum);
		$this->setClass ( 'groupwar' );
		return $expNum;
	}
	public function getUserLevel()
	{
		$this->setClass ( 'console' );
		$level = $this->execute( "getAttr  level"  );
		$this->setClass ( 'groupwar' );
		return $level;
	}
	public function setUserLevel($num)
	{
		$this->setClass ( 'console' );
		$level = $this->execute( "level  $num"  );
		$newLevel = $this->execute( 'getAttr  level'  );
		
		ASSERT_EQUAL($num, $newLevel);
		$this->setClass ( 'groupwar' );
	}
	
	function connect($server, $port)
	{
		$this->socket = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP );
		$this->socketError ( 'socket_create', $this->socket );

		Logger::debug ( "connecting to server:%s at port:%d", $server, $port );
		socket_set_option ( $this->socket, SOL_SOCKET, SO_SNDTIMEO,
		array ('sec' => FrameworkConfig::PROXY_CONNECT_TIMEOUT, 'usec' => 0 ) );

		$ret = socket_connect ( $this->socket, $server, $port );
		$this->socketError ( 'socket_connect', $ret );

		socket_set_option ( $this->socket, SOL_SOCKET, SO_RCVTIMEO,
		array ('sec' =>30, 'usec' => 0 ) );
		socket_set_option ( $this->socket, SOL_SOCKET, SO_SNDTIMEO,
		array ('sec' => FrameworkConfig::PROXY_WIRTE_TIMEOUT, 'usec' => 0 ) );
	}
	function close()
	{
		if ($this->socket)
		{
			socket_close ( $this->socket );
			echo "close socket\n";
			$this->socket = null;
		}
	}
	function setToken($token)
	{
		$this->token = "" . $token;
	}

	function setClass($clazz)
	{
		$this->clazz = $clazz;
	}

	function setRequestType($requestType)
	{
		$this->requestType = $requestType;
	}

	function setPublic($public)
	{
		$this->public = $public;
	}

	function setAsync($async)
	{
		$this->async = $async;
	}

	function resetCallback()
	{
		$this->callback = array (  'callbackName' => 'dummy');
	}
	function __call($method, $arrArg)
	{
		if (empty ( $this->socket ))
		{
			$this->connect ( $this->server, $this->port );
		}
		if (! empty ( $this->clazz ))
		{
			$method = $this->clazz . '.' . $method;
		}
		$arrRequest = array ('method' => $method, 'args' => $arrArg, 'type' => $this->requestType,
				'time' => time (), 'token' => $this->token, 'callback' =>array (  'callbackName' => $method)  );

		$this->callback = $method;
		$request = Util::amfEncode ( $arrRequest );
		if (empty ( $request ) || strlen ( $request ) > FrameworkConfig::MAX_REQUEST_SIZE)
		{
			Logger::warning ( "request:%s", bin2hex ( $request ) );
			throw new Exception ( 'sys' );
		}

		$flag = 0;
		if (strlen ( $request ) > FrameworkConfig::PROXY_COMPRESS_THRESHOLD)
		{
			$size = strlen ( $request );
			$flag |= 0x00ff0000;
			$request = gzcompress ( $request );
			Logger::debug ( "request too large, compress from %d to %d", $size,
			strlen ( $request ) );
		}
		$this->writeU32 ( strlen ( $request ) );
		if ($this->public)
		{
			$flag |= 0xff000000;
			$this->writeU32 ( $flag );
			$this->writeBytes ( $this->encrypt ( $request ) );
		}
		else
		{
			$this->writeU32 ( $flag );
		}
		$this->writeBytes ( $request );
		Logger::trace ( "userclient request:%s", $method );
		if (! $this->async)
		{
			return $this->receiveData ();
		}
	}

	public function receiveData($callback = NULL)
	{
		if(!isset($callback))
		{
			$callback = $this->callback;
		}
		Logger::addBasic ( 'uid', $this->uid );
		Logger::trace("userclient wait for {$callback}");
		while ( true )
		{
			$bodyLength = $this->readU32 ();
			$flag = $this->readU32 ();
			if (($flag & 0xff000000))
			{
				$this->readBytes ( 16 );
			}
			$response = $this->readBytes ( $bodyLength );
			$uncompress = false;
			if (($flag & 0x00ff0000))
			{
				Logger::debug ( "uncompress data" );
				$uncompress = true;
			}
			$arrResponse = Util::amfDecode ( $response, $uncompress );
			Logger::trace ( "userclient callback:[%s] return:[%s]", $callback, $arrResponse );

			if (isset ( $arrResponse ['token'] ))
			{
				$this->token = $arrResponse ['token'];
			}

			if (($flag & 0x0000ff00))
			{
				foreach($arrResponse ['ret'] as  $value)
				{
					if (is_string ($value ))
					{
						$value = Util::amfDecode ( $value, false );
					}
					Logger::trace("userclient get {$value['callback']['callbackName']}");
					
					if( $value ['callback']['callbackName'] == $callback)
					{
						break;
					}
				}
				$arrResponse = $value;
			}

			if ($arrResponse ['err'] == 'ping')
			{
				Logger::trace("userclient wait for {$callback}, err=ping");
				continue;
			}
			if ($arrResponse ['err'] == 'fake' )
			{
				Logger::trace("userclient wait for {$callback}, err=fake");
				return array('ret' => 'fake');
			}
			if ($arrResponse ['err'] != 'ok' )
			{
				Logger::trace("userclient wait for {$callback}, err=".$arrResponse ['err']);
				throw new Exception ( $arrResponse ['err'] );
			}
			
			if (isset ( $arrResponse ['callback'] ) && $callback != $arrResponse ['callback']['callbackName'])
			{
				Logger::debug("$callback  vs ".$arrResponse ['callback']['callbackName']);
				continue;
			}
			$this->resetCallback();
			return $arrResponse ['ret'];
		}
	}
	public function receiveAnyData()
	{		
		while ( true )
		{
			$bodyLength = $this->readU32 ();
			$flag = $this->readU32 ();
			if (($flag & 0xff000000))
			{
				$this->readBytes ( 16 );
			}
			$response = $this->readBytes ( $bodyLength );
			$uncompress = false;
			if (($flag & 0x00ff0000))
			{
				Logger::debug ( "uncompress data" );
				$uncompress = true;
			}
			$arrResponse = Util::amfDecode ( $response, $uncompress );

			Logger::trace ( "userclient return:[%s]",  $arrResponse );
				
			if (isset ( $arrResponse ['token'] ))
			{
				$this->token = $arrResponse ['token'];
			}
	
			$returnData = array();
			if (($flag & 0x0000ff00))
			{
				foreach($arrResponse ['ret'] as  $key => $value)
				{
					if (is_string ($value ))
					{
						$value = Util::amfDecode ( $value, false );
					}		
					$returnData[] = $value;
				}				
			}
			else
			{
				$returnData = array($arrResponse);
			}

			return $returnData;
		}
	}

	private function readU32()
	{

		$bytes = $this->readBytes ( 4 );
		$length = 0;
		$length += ord ( $bytes [0] ) << 24;
		$length += ord ( $bytes [1] ) << 16;
		$length += ord ( $bytes [2] ) << 8;
		$length += ord ( $bytes [3] );
		return $length;
	}

	private function readBytes($length)
	{

		$content = '';
		while ( $length )
		{
			$ret = @socket_read ( $this->socket, $length );
			$this->socketError ( 'socket_read', $ret );
			$length -= strlen ( $ret );
			$content .= $ret;
		}
		return $content;
	}

	private function writeU32($length)
	{

		$bytes = '';
		$bytes .= chr ( ($length & 0xff000000) >> 24 );
		$bytes .= chr ( ($length & 0x00ff0000) >> 16 );
		$bytes .= chr ( ($length & 0x0000ff00) >> 8 );
		$bytes .= chr ( ($length & 0x000000ff) );
		$this->writeBytes ( $bytes );
	}

	private function writeBytes($bytes)
	{

		$length = strlen ( $bytes );
		while ( $length )
		{
			$ret = @socket_write ( $this->socket, $bytes );
			$this->socketError ( 'socket_write', $ret );
			$bytes = substr ( $bytes, $ret );
			$length -= $ret;
		}
	}

	private function socketError($method, $ret)
	{

		if ($ret == false)
		{
			$errno = socket_last_error ( $this->socket );
			Logger::warning('socketError:%d', $errno);
			if ($errno == SOCKET_EINTR || $errno == SOCKET_EAGAIN)
			{
				return;
			}
			if($errno == 0)
			{
				$bt = debug_backtrace();
				Logger::warning('bt:%s', $bt);
				throw new Exception ( 'no data' );
			}
			Logger::fatal ( "%s:%s", $method, socket_strerror ( $errno ) );
			socket_close ( $this->socket );
			$this->socket = null;
			throw new Exception ( 'lcclient' );
		}
	}

	private function encrypt($data)
	{

		$raw = FrameworkConfig::MESS_CODE;
		for($index = 0; $index < strlen ( $data ); $index ++)
		{
			$a = ord ( $data [$index] );
			if ($index == strlen ( $data ) - 1)
			{
				$b = strlen ( $data );
			}
			else
			{
				$b = ord ( $data [$index + 1] );
			}
			$raw .= chr ( $a ^ $b );
		}
		return md5 ( $raw, true );
	}

	function __destruct()
	{

		if ($this->socket)
		{
			socket_close ( $this->socket );
			$this->socket = null;
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
