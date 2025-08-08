<?php
/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PlatformApi.class.php 17309 2012-03-24 09:55:21Z ZongzheYang $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-52/lib/PlatformApi.class.php $
 * @author $Author: ZongzheYang $(dh0000@babeltime.com)
 * @date $Date: 2012-03-24 17:55:21 +0800 (Sat, 24 Mar 2012) $
 * @version $Revision: 17309 $
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
        switch($method){
            case 'addRole':
                break;
            case 'delRole':
                break;
            case 'reg':
                break;
            case 'loginServer':
                break;
            case 'verifySession':
                break;
            case 'getGiftByCard':
                break;
        }
        return $this->post_request($method,$params);
    }

    private function post_request($method, $params ,$addr='user') {
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
        }
        return $res;
    }
}