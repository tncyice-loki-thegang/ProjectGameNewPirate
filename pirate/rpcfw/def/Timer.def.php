<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Timer.def.php 16414 2012-03-14 02:48:18Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Timer.def.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:48:18 +0800 (三, 2012-03-14) $
 * @version $Revision: 16414 $
 * @brief
 *
 **/
class TimerDef
{

	static $ARR_CALLBACK = array ("callbackName" => "dummy" );
}

class TimerStatus
{

	const UNDO = 1;

	const FINISH = 2;

	const FAILED = 3;

	const RETRY = 4;

	const CANCEL = 5;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */