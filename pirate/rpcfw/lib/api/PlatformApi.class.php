<?php
/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PlatformApi.class.php 35637 2013-01-14 02:33:13Z ZhichaoJiang $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/api/PlatformApi.class.php $
 * @author $Author: ZhichaoJiang $(dh0000@babeltime.com)
 * @date $Date: 2013-01-14 10:33:13 +0800 (一, 2013-01-14) $
 * @version $Revision: 35637 $
 * @brief
 *
 **/

class PlatformApi {
    private $md5Key;
    private $server_addr;

    public function __construct()
    {
        $this->md5Key=PlatformApiConfig::MD5KEY;
        $this->server_addr=PlatformApiConfig::$SERVER_ADDR;
    }

    public function users($method,$array=array())
    {
        $params=$array;
        $params['action'] = $method;
        $params['ts'] = time();
//        $params['server_id'] =
        switch($method){
            case 'addRole':
            case 'delRole':
            case 'reg':
            case 'loginServer':
            case 'verifySession':
            case 'getGiftByCard':
            case 'getServerGroup':
            case 'getServerGroupAll':
            case 'getNameAll':
            case 'getServerGroupBySpanid':
                break;
        }
        return $this->post_request($method,$params);
    }

    protected function post_request($method, $params ,$addr='user') {
        ksort($params);
        $tmp='';
        foreach($params as $key => &$val){
            if(in_array($key,array('pid','action','ts'))){
                $tmp .= $key.$val;
            }
        }
        $params['sig'] = md5($tmp.$this->md5Key);
        $params['logid'] = RPCContext::getInstance ()->getFramework ()->getLogid ();

        Logger::debug ( $method.":".$this->server_addr[$method] ."params:%s", $params );

        $proxy = new HTTPClient( $this->server_addr[$method] );
        switch($method){
            case 'addRole':
            case 'delRole':
            case 'reg':
            case 'loginServer':
            case 'getGiftByCard':
                break;
            case 'verifySession':
                if(!isset($params['sessionId']) || empty($params['sessionId'])){
                    Logger::warning('PlatformApi verifySession. params error sessionId');
                    throw new Exception('error');
                }
                $proxy->setCookie ('PHPSESSID', $params['sessionId']);
                break;
            case 'getServerGroup':
            case 'getServerGroupAll':
            case 'getNameAll':
            case 'getServerGroupBySpanid':
                break;
        }
        $postData = http_build_query($params);
        $res = $proxy->post($postData);
        switch($method){
            case 'addRole':
            case 'delRole':
            case 'reg':
            case 'loginServer':
                break;
            case 'getGiftByCard':
                $res = unserialize($res);
                break;
            case 'getServerGroup':
                $res = unserialize($res);
                break;
            case 'getServerGroupAll':
                $res = unserialize($res);
                break;
            case 'getNameAll':
                $res = unserialize($res);
                break;
            case 'getServerGroupBySpanid':
                $res = unserialize($res);
                break;
        }
        return $res;
    }
}