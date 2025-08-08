<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: CheckFile.php 16420 2012-03-14 02:53:05Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/CheckFile.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:53:05 +0800 (三, 2012-03-14) $
 * @version $Revision: 16420 $
 * @brief
 *
 **/

function checkDir($dirname)
{

	$dir = opendir ( $dirname );
	if (empty ( $dir ))
	{
		echo "open dir:$dirname failed\n";
		return;
	}
	while ( true )
	{
		$file = readdir ( $dir );
		if (empty ( $file ))
		{
			break;
		}
		if ($file == '.' || $file == '..')
		{
			continue;
		}
		$filename = $dirname . '/' . $file;
		if (is_file ( $filename ))
		{
			checkFile ( $filename );
		}
		else
		{
			checkDir ( $filename );
		}
	}
}

function checkFile($filename)
{

	if (strtolower ( substr ( $filename, - 4 ) ) != '.php')
	{
		return;
	}
	$content = file_get_contents ( $filename );
	$head = strtolower ( substr ( $content, 0, 5 ) );
	if($head != '<?php')
	{
		echo "[$filename]head:$head\n";
		return;
	}
	$content = trim($content);
	$tail = strtolower ( substr ( $content, - 2 ) );
	if ($tail == "?>")
	{
		echo "[$filename]tail:$tail\n";
		return;
	}
}

global $argc, $argv;
if ($argc == 1)
{
	echo "usage $argv[0] dir|file ...\n";
	exit ( 0 );
}
else
{
	for($i = 1; $i < $argc; $i ++)
	{
		$name = $argv [$i];
		if (is_dir ( $name ))
		{
			checkDir ( $name );
		}
		else if (is_file ( $name ))
		{
			checkFile ( $name );
		}
		else
		{
			echo "$name is neither file nor directory\n";
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */