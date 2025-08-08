<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FCrypt.php 31503 2012-11-21 07:12:15Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/FCrypt.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-11-21 15:12:15 +0800 (三, 2012-11-21) $
 * @version $Revision: 31503 $
 * @brief
 *
 **/
class FCrypt extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		if (empty ( $arrOption [0] ))
		{
			$this->usage ();
			return;
		}
		$oprand = $arrOption [0];

		if (empty ( $arrOption [1] ))
		{
			$this->usage ();
			return;
		}
		$input = $arrOption [1];

		if (! is_file ( $input ))
		{
			echo "file:$input not exists\n";
			return;
		}

		if (empty ( $arrOption [2] ))
		{
			$this->usage ();
			return;
		}

		$output = $arrOption [2];
		if (file_exists ( $output ))
		{
			echo "file:$output already exists\n";
			return;
		}

		$data = file_get_contents ( $input );
		switch ($oprand)
		{
			case 'encode' :
				$data = BabelCrypt::encrypt ( $data, true );
				break;
			case 'decode' :
				$data = BabelCrypt::decrypt ( $data, true );
				break;
			default :
				$this->usage ();
				return;
		}
		file_put_contents ( $output, $data );
		echo "done\n";
	}

	protected function usage()
	{

		echo "Usage: btscript FCrypt.php encode|decode input_file output_file\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */