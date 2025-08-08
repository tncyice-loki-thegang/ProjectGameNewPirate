<?php
/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SvnProxy.php 32923 2012-12-12 07:01:31Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/SvnProxy.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-12-12 15:01:31 +0800 (三, 2012-12-12) $
 * @version $Revision: 32923 $
 * @brief
 *
 **/

$handle = fopen ( '/tmp/svn.log', 'a+' );
$data = print_r ( $_SERVER, true );
fputs ( $handle, $data );
$data = print_r ( $_COOKIE, true );
fputs ( $handle, $data );
fclose ( $handle );
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */