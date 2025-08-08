<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: PlatformApiDefault.class.php 35637 2013-01-14 02:33:13Z ZhichaoJiang $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/api/PlatformApiDefault.class.php $
 * @author $Author: ZhichaoJiang $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-14 10:33:13 +0800 (一, 2013-01-14) $
 * @version $Revision: 35637 $
 * @brief
 *
 **/

class PlatformApiDefault extends PlatformApi
{
	public function users($method,$array=array())
    {
        $params=$array;
        $params['action'] = $method;
        $params['ts'] = time();
        //礼品卡往平台发请求, 其他直接返回空
        switch($method){
            case 'getGiftByCard':
            case 'getServerGroup':
            case 'getServerGroupAll':
            case 'getNameAll':
             case 'getServerGroupBySpanid':
//            	$ret = array('error'=>4);
//            	Logger::debug('PlatformApiDefault::getGiftByCard return %s', $ret);
            	//return $ret;
                break;

            default:
            	Logger::debug('PlatformApiDefault return nothing');
            	return '';
        }
        return $this->post_request($method,$params);
    }
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */