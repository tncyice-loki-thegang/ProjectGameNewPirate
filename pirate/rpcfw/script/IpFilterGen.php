<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IpFilterGen.php 33091 2012-12-14 02:21:22Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/IpFilterGen.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-12-14 10:21:22 +0800 (五, 2012-12-14) $
 * @version $Revision: 33091 $
 * @brief
 *
 **/
class IpFilterGen extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		if (empty ( $arrOption [0] ))
		{
			echo "usage: btscript $0 file\n";
			return;
		}

		$file = $arrOption [0];
		if (! file_exists ( $file ))
		{
			echo "file:$file not exists\n";
			return;
		}

		$fh = fopen ( $file, 'r' );
		if (empty ( $fh ))
		{
			echo "open file:$file failed\n";
			return;
		}

		$arrIpRange = array ();
		while ( ! feof ( $fh ) )
		{
			$line = fgets ( $fh );
			if (empty ( $line ))
			{
				continue;
			}

			$arrLine = explode ( '-', $line, 2 );
			$ip1 = $arrLine [0];
			if (empty ( $arrLine [1] ))
			{
				$ip2 = $ip1;
			}
			else
			{
				$ip2 = $arrLine [1];
			}
			$ipn1 = ip2long ( trim ( $ip1 ) );
			$ipn2 = ip2long ( trim ( $ip2 ) );
			if (false === $ipn1 || false === $ipn2 || $ipn1 > $ipn2)
			{
				echo "invalid line:$line\n";
				return;
			}
			$arrIpRange [] = array ($ipn1, $ipn2 );
		}

		usort ( $arrIpRange, array ($this, 'sortIpRange' ) );
		array_merge ( $arrIpRange );

		$lastIndex = 0;
		foreach ( $arrIpRange as $index => $arrIp )
		{
			if ($lastIndex == 0)
			{
				$lastIndex = $index;
				continue;
			}

			if ($arrIpRange [$lastIndex] [1] > $arrIp [0])
			{
				$arrIpRange [$lastIndex] [1] = $arrIp [1];
				unset ( $arrIpRange [$index] );
			}
			else
			{
				$lastIndex = $index;
			}
		}

		array_merge ( $arrIpRange );
		echo "<?php\n  return ";
		var_export ( $arrIpRange );
		echo ";\n";
	}

	function sortIpRange($arrData1, $arrData2)
	{

		return $arrData1 [0] - $arrData2 [0];
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
