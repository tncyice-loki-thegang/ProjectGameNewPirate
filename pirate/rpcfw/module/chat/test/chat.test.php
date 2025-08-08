<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: chat.test.php 36592 2013-01-22 03:41:06Z ZhichaoJiang $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/chat/test/chat.test.php $
 * @author $Author: ZhichaoJiang $(jhd@babeltime.com)
 * @date $Date: 2013-01-22 11:41:06 +0800 (二, 2013-01-22) $
 * @version $Revision: 36592 $
 * @brief
 *
 **/
class chat extends BaseScript 
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption) {
		
//		while (TRUE) {
//			$user1 = array('uid' => 49806,
//							'utid' => 2,
//							'uname' => '54m55Lym5pav6LWb5ouJ');
//			$user2 = array('uid' => 23100,
//							'utid' => 2,
//							'uname' => '54m55L1b5ouJ');

		
	Logger::debug('begin');
	$winGropuUserInfo = array(  'lose' =>
  array (
    'uid' => 20225,
    'utid' => 11004,
    'uname' => '5bCP5p+ULnM3MA==',
  ),
  'win' =>
  array (
    'uid' => 74226,
    'utid' => 11001,
    'uname' => '4401',
  ));
	
	
		$loserGropuUserInfo = array(  'lose' =>
  array (
    'uid' => 20225,
    'utid' => 11004,
    'uname' => '5bCP5p+ULnM3MA==',
  ),
  'win' =>
  array (
    'uid' => 74226,
    'utid' => 11001,
    'uname' => '4401',
  ));
	ChatTemplate::sendWorldWarFinal($winGropuUserInfo, $loserGropuUserInfo);
	Logger::debug('end');

	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
