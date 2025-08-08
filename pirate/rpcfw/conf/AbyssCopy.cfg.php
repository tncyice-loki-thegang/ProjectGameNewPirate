<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AbyssCopy.cfg.php 39837 2013-03-04 10:28:34Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/AbyssCopy.cfg.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-04 18:28:34 +0800 (一, 2013-03-04) $
 * @version $Revision: 39837 $
 * @brief 
 *  
 **/

class AbyssCopyConf
{

	const CARD_NUM = 5;	//翻牌时，一共有几张牌

	public static $FRONT_CALLBACKS = array(
			'start' => 'sc.abysscopy.start',
			'modifyObj' => 'sc.abysscopy.modifyObj',
			'modifyUser' => 'sc.abysscopy.modifyUser',
			'battleResult' => 'sc.abysscopy.battleResult',
			'flopCard' => 'sc.abysscopy.flopCard',
			'rewadCard' => 'sc.abysscopy.rewadCard',
			'copyPassed' => 'sc.abysscopy.copyPassed',
			'chat' => 'sc.abysscopy.chat',
	);
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */