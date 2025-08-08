<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: RPCFramework.class.php 40059 2013-03-06 07:26:19Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/RPCFramework.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2013-03-06 15:26:19 +0800 (三, 2013-03-06) $
 * @version $Revision: 40059 $
 * @brief 整个框架的主控制类
 *
 **/

class RPCFramework
{

	/**
	 * 目前请求的session
	 * @var array
	 */
	private $arrSession;

	/**
	 * 内部session
	 * @var array
	 */
	private $arrISession;

	/**
	 * 前端发过来的请求数据
	 * @var array 格式如下：
	 * <code>
	 * {
	 * time:请求时间
	 * token:日志id
	 * method:方法名
	 * args:请求参数
	 * callback:前端回调
	 * }
	 * </code>
	 */
	private $arrRequest;

	/**
	 * 前端用户的ip
	 * @var string
	 */
	private $clientIp;

	/**
	 * 将请求发过来的lcserver地址
	 * @var string
	 */
	private $serverIp;

	/**
	 * 将请求发过来的组标识
	 * @var string
	 */
	private $group;

	/**
	 * 目前所使用的数据库
	 * @var string
	 */
	private $db;

	/**
	 * 请求开始时间
	 * @var float
	 */
	private $starTime;

	/**
	 * 请求结束时间
	 * @var float
	 */
	private $endTime;

	/**
	 * 方法结束时间
	 * @var int
	 */
	private $endMethodTime;

	/**
	 * 方法开始时间
	 * @var int
	 */
	private $startMethodTime;

	/**
	 * 当前的logid
	 * @var int
	 */
	private $logid;

	/**
	 * 原始的logid
	 * @var int
	 */
	private $originLogid;

	/**
	 * 请求大小
	 * @var int
	 */
	private $requestSize;

	/**
	 * 响应大小
	 * @var int
	 */
	private $responseSize;

	/**
	 * 请求的方法名
	 * @var string
	 */
	private $method;

	/**
	 * 会话是否改变
	 * @var bool
	 */
	private $sessionChanged;

	/**
	 * 请求发过来的时间
	 * @var int
	 */
	private $requestTime;

	/**
	 * 本地ip
	 * @var string
	 */
	private $localIp;

	/**
	 * 递归深度
	 * @var int
	 */
	private $recursLevel;

	/**
	 * 递归等级
	 */
	function getRecursLevel()
	{

		return $this->recursLevel;
	}

	/**
	 * 构造函数
	 */
	function __construct()
	{

		$this->starTime = microtime ( true );
		$this->endTime = 0;
		$this->arrSession = array ('global.dummy' => true );
		$this->arrISession = array ('global.dummy' => true );
		$this->startMethodTime = microtime ( true );
		$this->endMethodTime = 0;
		$this->requestSize = 0;
		$this->responseSize = 0;
		$this->serverIp = "";
		$this->clientIp = "";
		$this->localIp = "";
		$this->group = "";
		$this->db = "";
		$this->method = "unknown";
		$this->sessionChanged = false;
		$this->requestTime = 0;
		$this->recursLevel = 1;
	}

	public function getSession($key)
	{

		if (isset ( $this->arrSession [$key] ))
		{
			return $this->arrSession [$key];
		}
		else if (isset ( $this->arrISession [$key] ))
		{
			return $this->arrISession [$key];
		}
		else if (isset ( $this->arrSession [SessionHookConf::SESSION_KEY] ))
		{
			Logger::debug ( "key:%s not found, try to decode session", $key );
			if (SessionHookConf::SESSION_COMPRESS)
			{
				$arrData = Util::amfDecode ( $this->arrSession [SessionHookConf::SESSION_KEY], 
						true );
			}
			else
			{
				$arrData = Util::amfDecode ( $this->arrSession [SessionHookConf::SESSION_KEY] );
			}
			
			unset ( $this->arrSession [SessionHookConf::SESSION_KEY] );
			$this->arrSession += $arrData;
			return $this->getSession ( $key );
		}
		else
		{
			return null;
		}
	}

	public function sessionChanged()
	{

		return $this->sessionChanged;
	}

	public function getSessions()
	{

		return $this->arrSession;
	}

	public function setSessions($arrSession)
	{

		if ($this->arrSession == $arrSession)
		{
			return;
		}
		
		$this->arrSession = $arrSession;
		$this->sessionChanged = true;
		Logger::debug ( "session changed by setSessions" );
	}

	public function getLogid()
	{

		return ++ $this->logid;
	}

	public function setSession($key, $value)
	{

		if (isset ( $this->arrSession [$key] ))
		{
			if ($this->arrSession [$key] == $value)
			{
				Logger::debug ( "session:%s not changed", $key );
				return;
			}
			else
			{
				Logger::debug ( "session old:%s, new:%s", $this->arrSession [$key], $value );
			}
		}
		
		$this->arrSession [$key] = $value;
		$this->sessionChanged = true;
		Logger::debug ( "session changed by setSession key:%s", $key );
	}

	public function unsetSession($key)
	{

		if (! array_key_exists ( $key, $this->arrSession ))
		{
			return;
		}
		
		unset ( $this->arrSession [$key] );
		$this->sessionChanged = true;
		Logger::debug ( "session changed by unsetSession key:%s", $key );
	}

	public function getRequestTime()
	{

		if (empty ( $this->requestTime ))
		{
			$this->requestTime = time ();
		}
		return $this->requestTime;
	}

	public function getRequest()
	{

		return $this->arrRequest;
	}

	public function resetSession()
	{

		$globalPrefix = 'global.';
		$length = strlen ( $globalPrefix );
		foreach ( $this->arrSession as $key => $value )
		{
			if (substr ( $key, 0, $length ) == $globalPrefix)
			{
				continue;
			}
			unset ( $this->arrSession [$key] );
		}
		$this->sessionChanged = true;
	}

	public function getServerIp()
	{

		return $this->serverIp;
	}

	public function getGroup()
	{

		return $this->group;
	}

	public function getClientIp()
	{

		return $this->clientIp;
	}

	public function resetCallback()
	{

		$arrCallback = $this->arrRequest ['callback'];
		$this->arrRequest ['callback'] = array ('callbackName' => 'dummy' );
		return $arrCallback;
	}

	public function getCallback()
	{

		return $this->arrRequest ['callback'];
	}

	private function sendResponse($err, $ret = null)
	{

		if ($err == 'dummy')
		{
			$this->resetCallback ();
			$err = 'ok';
		}
		
		$arrRet = array ('err' => $err, 'callback' => $this->arrRequest ['callback'] );
		
		if ($err == 'ok')
		{
			$arrRet ['ret'] = $ret;
		}
		
		$arrCallback = RPCContext::getInstance ()->getCallback ();
		if (defined ( 'ScriptConf::CALLBACK_AS_SCRIPT' ) && ScriptConf::CALLBACK_AS_SCRIPT)
		{
			$arrCallback = array ();
		}
		
		$arrResp = array ('err' => $err, 'response' => $arrRet, 'callback' => $arrCallback );
		
		if ($this->sessionChanged)
		{
			$arrResp ['session'] = $this->arrSession;
		}
		else
		{
			Logger::debug ( "session not changed, ignore session" );
			$arrResp ['session'] = false;
		}
		
		Logger::debug ( "session:%s", $this->arrSession );
		Logger::trace ( "callback:%s", $arrCallback );
		Logger::trace ( "response:%s", $arrRet );
		
		if (FrameworkConfig::ENCODE_RESPONSE)
		{
			$arrResp ['response'] = Util::amfEncode ( $arrResp ['response'] );
		}
		
		$response = Util::amfEncode ( $arrResp );
		if (strlen ( $response ) > FrameworkConfig::MAX_RESPONSE_SIZE)
		{
			Logger::warning ( "response size:%d is too large", strlen ( $response ) );
		}
		
		ob_end_clean ();
		$this->responseSize = strlen ( $response );
		echo $response;
		if (function_exists ( 'fastcgi_finish_request' ))
		{
			fastcgi_finish_request ();
		}
	}

	private function executeMethod($file, $clazz, $method, $args, $record = false, $init = false)
	{

		if ($file)
		{
			if (! file_exists ( $file ))
			{
				Logger::fatal ( "file %s not exists", $file );
				throw new Exception ( 'file' );
			}
			else
			{
				require_once ($file);
			}
		}
		
		if (! class_exists ( $clazz ))
		{
			Logger::fatal ( "class %s not exists in file %s", $clazz, $file );
			throw new Exception ( 'class' );
		}
		
		if ($record)
		{
			$this->startMethodTime = microtime ( true );
		}
		$object = new $clazz ();
		
		if (! empty ( $init ) && method_exists ( $object, $init ))
		{
			$object->$init ();
		}
		
		if (! method_exists ( $object, $method ))
		{
			Logger::fatal ( "object has no method:%s in class:%s", $method, $clazz );
			throw new Exception ( 'method' );
		}
		
		$ret = call_user_func_array ( array ($object, $method ), $args );
		
		Logger::debug ( "call method done" );
		if ($record)
		{
			$this->endMethodTime = microtime ( true );
		}
		return $ret;
	}

	private function getHookLocation($hookClazz)
	{

		return $hookClazz . '.hook.php';
	}

	private function executeHook($hookClazz, $arrRequest)
	{

		$file = HOOK_ROOT . '/' . $this->getHookLocation ( $hookClazz );
		return $this->executeMethod ( $file, $hookClazz, 'execute', array ($arrRequest ), false );
	}

	public function getLocalIp()
	{

		return $this->localIp;
	}

	private function initRequest()
	{

		$this->clientIp = $this->getIp ();
		
		if (! empty ( $_SERVER ['HTTP_GAME_ADDR'] ))
		{
			$this->serverIp = $_SERVER ['HTTP_GAME_ADDR'];
		}
		else
		{
			$this->serverIp = $_SERVER ['SERVER_ADDR'];
		}
		
		if (! empty ( $_SERVER ['SERVER_ADDR'] ))
		{
			$this->localIp = $_SERVER ['SERVER_ADDR'];
		}
		
		if (! empty ( $_SERVER ['HTTP_GAME_GROUP'] ))
		{
			$this->group = $_SERVER ['HTTP_GAME_GROUP'];
		}
		else if (! empty ( $_SERVER ['SERVER_GROUP'] ))
		{
			$this->group = $_SERVER ['SERVER_GROUP'];
		}
		else
		{
			$this->group = $this->serverIp;
		}
		
		if (! empty ( $_SERVER ['HTTP_GAME_DB'] ))
		{
			$this->db = $_SERVER ['HTTP_GAME_DB'];
		}
		
		//FIXME 这里机器可能会比较多，建议写一个查询算法，或者配置，比如支持192.168.3.0/24这种写法
		if (! FrameworkConfig::DEBUG)
		{
			if (! in_array ( $this->serverIp, WhiteListConfig::$ARR_WHITE_LIST ))
			{
				Logger::fatal ( "invalid access, authorize not granted for server:%s", 
						$this->serverIp );
				throw new Exception ( 'close' );
			}
		}
		
		$request = file_get_contents ( 'php://input' );
		$this->requestSize = strlen ( $request );
		if (empty ( $request ))
		{
			Logger::fatal ( "invalid request, empty" );
			throw new Exception ( 'req' );
		}
		
		if (strlen ( $request ) > FrameworkConfig::MAX_REQUEST_SIZE)
		{
			Logger::warning ( "request size:%d is too large", strlen ( $request ) );
		}
		
		$ret = Util::amfDecode ( $request );
		if (empty ( $ret ))
		{
			Logger::fatal ( "invalid request, decode failed" );
			throw new Exception ( 'decode' );
		}
		
		return $ret;
	}

	public function initExtern($group, $serverIp, $logid, $db, $time, $serverId = 0)
	{

		$this->logid = $logid;
		$this->originLogid = $logid;
		$this->serverIp = $serverIp;
		$this->group = $group;
		$this->db = $db;
		$this->requestTime = $time;
		if (! empty ( $serverId ))
		{
			$this->setSession ( 'global.serverId', $serverId );
		}
	}

	/**
	 * 开始服务
	 */
	public function start()
	{

		ob_start ();
		set_error_handler ( array ($this, 'errorHandler' ) );
		Logger::init ( LOG_ROOT . '/' . FrameworkConfig::LOG_NAME, FrameworkConfig::LOG_LEVEL );
		
		try
		{
			$arrRequest = $this->initRequest ();
			if (empty ( $arrRequest ['request'] ['token'] ))
			{
				$this->logid = Util::genLogId ();
			}
			else
			{
				$this->logid = $arrRequest ['request'] ['token'];
			}
			$this->originLogid = $this->logid;
			Logger::addBasic ( 'logid', $this->logid );
			Logger::addBasic ( 'client', $this->clientIp );
			Logger::addBasic ( 'server', $this->serverIp );
			if (! empty ( $this->group ) && $this->group != $this->serverIp)
			{
				Logger::addBasic ( 'group', $this->group );
			}
			
			Logger::debug ( "request:%s", $arrRequest );
			
			if (empty ( $arrRequest ['session'] ) || empty ( $arrRequest ['request'] ))
			{
				Logger::fatal ( "invalid request:%s, no session or request argument", $arrRequest );
				throw new Exception ( 'close' );
			}
			
			if (empty ( $arrRequest ['time'] ))
			{
				$this->requestTime = time ();
			}
			else
			{
				$this->requestTime = intval ( $arrRequest ['time'] );
			}
			
			$arrSession = $arrRequest ['session'];
			
			if (! empty ( $arrSession ['global.pid'] ))
			{
				Logger::addBasic ( 'pid', intval ( $arrSession ['global.pid'] ) );
			}
			
			if (! empty ( $arrSession ['global.uid'] ))
			{
				Logger::addBasic ( 'uid', intval ( $arrSession ['global.uid'] ) );
			}
			
			$arrAsRequest = $arrRequest ['request'];
			if (empty ( $arrAsRequest ['method'] ))
			{
				Logger::fatal ( "invalid request, no method argument" );
				throw new Exception ( 'close' );
			}
			if (! isset ( $arrAsRequest ['args'] ))
			{
				Logger::fatal ( "invalid request, no args argument" );
				throw new Exception ( 'close' );
			}
			if (empty ( $arrAsRequest ['callback'] ))
			{
				Logger::fatal ( "invalid request, no callback argument" );
				throw new Exception ( 'close' );
			}
			
			if (! empty ( $arrAsRequest ['backend'] ))
			{
				Logger::addBasic ( 'backend', $arrAsRequest ['backend'] );
			}
			
			if (! empty ( $arrAsRequest ['recursLevel'] ))
			{
				$this->recursLevel = intval ( $arrAsRequest ['recursLevel'] );
			}
			
			if ($this->recursLevel > FrameworkConfig::MAX_RECURS_LEVEL)
			{
				Logger::fatal ( "recusLevel:%d is too large", $this->recursLevel );
				throw new Exception ( 'inter' );
			}
			
			if (FrameworkConfig::DEBUG)
			{
				Logger::debug ( "framework run in debug mode" );
			}
			else
			{
				Logger::debug ( "framework run in release mode" );
			}
			
			$this->arrSession = $arrRequest ['session'];
			
			if (isset ( $arrRequest ['isession'] ))
			{
				$this->arrISession = $arrRequest ['isession'];
			}
			
			if (! empty ( $arrAsRequest ['serverId'] ))
			{
				Logger::debug ( 'serverId from request found' );
				if ($this->getSession ( 'global.serverId' ) === null)
				{
					Logger::debug ( 'serverId from session not found' );
					$this->arrISession ['global.serverId'] = $arrAsRequest ['serverId'];
				}
				else
				{
					Logger::debug ( 'serverId from session found' );
				}
			}
			else
			{
				Logger::debug ( "serverId from request not found" );
			}
			
			$arrRequest = $arrRequest ['request'];
			Logger::trace ( "request:%s", $arrRequest );
			
			$group = $this->getGroup ();
			if (! empty ( $group ))
			{
				require_once (CONF_ROOT . "/gsc/$group/index.php");
			}
			
			foreach ( FrameworkConfig::$ARR_BEFORE_HOOK as $hookClazz )
			{
				Logger::debug ( "execute before hook:%s", $hookClazz );
				$arrRequest = $this->executeHook ( $hookClazz, $arrRequest );
			}
			$this->arrRequest = $arrRequest;
			$this->method = $this->arrRequest ['method'];
			
			try
			{
				$arrRet = $this->executeRequest ( $this->arrRequest, true );
			}
			catch ( Exception $e )
			{
				$this->endMethodTime = microtime ( true );
				throw $e;
			}
			$err = 'ok';
			
			foreach ( FrameworkConfig::$ARR_AFTER_HOOK as $hookClazz )
			{
				Logger::debug ( "exeucte after hook:%s", $hookClazz );
				$arrRet = $this->executeHook ( $hookClazz, $arrRet );
			}
		}
		catch ( Exception $e )
		{
			$this->endMethodTime = microtime ( true );
			$err = $e->getMessage ();
			if (($err != 'fake' && $err != 'dummy') || FrameworkConfig::DEBUG)
			{
				Logger::fatal ( "uncaught exception:%s for method:%s", $e->getMessage (), 
						$this->method );
				Logger::info ( "%s", $e->getTraceAsString () );
			}
			$arrRet = null;
		}
		
		$this->requestEnd ( $err, $arrRet );
	}

	public function setMethod($method)
	{

		$this->method = $method;
	}

	public function getRequestCount()
	{

		return $this->logid - $this->originLogid;
	}

	private function requestEnd($err, $ret)
	{

		$this->sendResponse ( $err, $ret );
		$this->endTime = microtime ( true );
		$totalCost = intval ( ($this->endTime - $this->starTime) * 1000 );
		$methodCost = intval ( ($this->endMethodTime - $this->startMethodTime) * 1000 );
		$frameCost = intval ( $totalCost - $methodCost );
		if (defined ( 'ScriptConf::MAX_EXECUTE_TIME' ))
		{
			$maxExecTime = ScriptConf::MAX_EXECUTE_TIME;
		}
		else
		{
			$maxExecTime = FrameworkConfig::MAX_EXECUTE_TIME;
		}
		
		if ($totalCost > $maxExecTime)
		{
			Logger::fatal ( 'method:%s execute time:%d(ms) is too long', $this->method, $totalCost );
		}
		Logger::notice ( 
				"method:%s, err:%s, request count:%d, total cost:%d(ms), framework cost:%d(ms), method cost:%d(ms), request size:%d(byte), response size:%d(byte)", 
				$this->method, $err, $this->getRequestCount (), $totalCost, $frameCost, $methodCost, 
				$this->requestSize, $this->responseSize );
		
		if (defined ( 'ScriptConf::CALLBACK_AS_SCRIPT' ) && ScriptConf::CALLBACK_AS_SCRIPT)
		{
			$arrCallback = RPCContext::getInstance ()->getCallback ();
			Logger::trace ( "send callback in script mode start" );
			Util::sendCallback ( $arrCallback, ScriptConf::CALLBACK_INTERVAL );
			Logger::trace ( "send callback in script mode done" );
		}
	}

	public function executeRequest($arrRequest, $record = false)
	{

		if (empty ( $arrRequest ['private'] ))
		{
			$isPrivate = false;
		}
		else
		{
			$isPrivate = true;
		}
		
		$login = true;
		$uid = $this->getSession ( 'global.uid' );
		if (empty ( $uid ))
		{
			$login = false;
		}
		
		$connect = true;
		$pid = $this->getSession ( 'global.pid' );
		if (empty ( $pid ))
		{
			$connect = false;
		}
		
		$requestMethod = $arrRequest ['method'];
		Logger::debug ( "call method:%s", $requestMethod );
		
		$arrMethod = explode ( '.', $requestMethod );
		if (count ( $arrMethod ) != 2)
		{
			Logger::fatal ( "invalid request:%s, invalid method", $arrRequest );
			throw new Exception ( 'close' );
		}
		
		$clazz = $arrMethod [0];
		$method = $arrMethod [1];
		
		//私有方法
		if (isset ( FrameworkConfig::$ARR_PRIVATE_METHOD [$clazz] [$method] ))
		{
			Logger::debug ( "private method:%s", $requestMethod );
			if (! $isPrivate)
			{
				Logger::fatal ( "private call, but not a private ip" );
				throw new Exception ( 'close' );
			}
		}
		else if (isset ( FrameworkConfig::$ARR_EXCLUDE_CONNECT_METHOD [$clazz] [$method] ))
		{
			Logger::debug ( "method:%s do not need login", $requestMethod );
		}
		else if (isset ( FrameworkConfig::$ARR_EXCLUDE_LOGIN_METHOD [$clazz] [$method] ))
		{
			Logger::debug ( "method:%s need connect", $requestMethod );
			if (! $connect)
			{
				Logger::fatal ( 'user do not connect' );
				throw new Exception ( 'close' );
			}
		}
		else if (isset ( FrameworkConfig::$ARR_PUBLIC_METHOD [$clazz] [$method] ))
		{
			if (! $login)
			{
				Logger::fatal ( "public method:%s need login", $requestMethod );
				throw new Exception ( 'close' );
			}
		}
		else
		{
			Logger::fatal ( "undefined method:'%s'", $requestMethod );
			throw new Exception ( 'close' );
		}
		
		//$clazzFile = $this->getClassLocation ( $clazz );
		$clazzFile = '';
		$arrArg = $arrRequest ['args'];
		
		$arrRet = $this->executeMethod ( $clazzFile, $clazz, $method, $arrArg, $record, 'init' );
		return $arrRet;
	}

	private function getClassLocation($clazz)
	{

		Logger::debug ( "get file for class:%s", $clazz );
		$clazz [0] = strtolower ( $clazz [0] );
		$file = MOD_ROOT . "/$clazz/index.php";
		return $file;
	}

	public function errorHandler($errcode, $errstr, $errfile, $errline, $errcontext)
	{

		if (! ($errcode & error_reporting ()))
		{
			return true;
		}
		
		$this->endMethodTime = microtime ( true );
		Logger::fatal ( 'errcode:%d, errstr:%s, errfile:%s, errline:%s', $errcode, $errstr, 
				$errfile, $errline );
		Logger::debug ( 'error context:%s', $errcontext );
		$this->requestEnd ( 'php', null );
		exit ( 0 );
	}

	private function getIp()
	{

		$realip = '0.0.0.0';
		if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ))
		{
			$arr = explode ( ',', $_SERVER ['HTTP_X_FORWARDED_FOR'] );
			/* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
			foreach ( $arr as $ip )
			{
				$ip = trim ( $ip );
				if ($ip != 'unknown')
				{
					$realip = $ip;
					break;
				}
			}
		}
		elseif (isset ( $_SERVER ['HTTP_CLIENT_IP'] ))
		{
			$realip = $_SERVER ['HTTP_CLIENT_IP'];
		}
		else if (isset ( $_SERVER ['REMOTE_ADDR'] ))
		{
			$realip = $_SERVER ['REMOTE_ADDR'];
		}
		
		return $realip;
	}

	public function getDb()
	{

		return $this->db;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
