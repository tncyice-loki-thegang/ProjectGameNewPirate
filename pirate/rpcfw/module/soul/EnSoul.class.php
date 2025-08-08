<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

class EnSoul
{
	public static function openSoul()
	{
		$uid = RPCContext::getInstance()->getUid();
		SoulDao::update($uid, array("belly_time"=>Util::getTime()));
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */