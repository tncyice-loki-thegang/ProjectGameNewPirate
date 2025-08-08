<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Framework.def.php 16414 2012-03-14 02:48:18Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Framework.def.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:48:18 +0800 (三, 2012-03-14) $
 * @version $Revision: 16414 $
 * @brief
 *
 **/

class FrameworkDef
{

	const HEAD_SIZE = 8;

	const AMF_AMF3 = PHP_AMF3_PREFIX;
}

/**
 * 请求类型
 * @author hoping
 *
 */
class RequestType
{

	/**
	 *
	 * @var unknown_type
	 */
	const RELEASE = 1;

	const DEBUG = 2;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
