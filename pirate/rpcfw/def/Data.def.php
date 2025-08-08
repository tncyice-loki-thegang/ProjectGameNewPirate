<?php

/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Data.def.php 18068 2012-04-06 07:01:21Z YangLiu $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Data.def.php $
 * @author $Author: YangLiu $(jhd@babeltime.com)
 * @date $Date: 2012-04-06 15:01:21 +0800 (五, 2012-04-06) $
 * @version $Revision: 18068 $
 * @brief
 *
 **/

define ( 'MYSQLI_OPT_READ_TIMEOUT', 11 );
define ( 'MYSQLI_OPT_WRITE_TIMEOUT', 12 );

class DataDef
{
	const DELETED = 0;

	const NORMAL = 1;

	const MAX_FETCH = 100;

	const AFFECTED_ROWS = "affected_rows";

	const INSERT_ID = "last_insert_id";

	const COUNT = "count";
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
