<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Potentiality.def.php 16414 2012-03-14 02:48:18Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Potentiality.def.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:48:18 +0800 (三, 2012-03-14) $
 * @version $Revision: 16414 $
 * @brief
 *
 **/

class PotentialityDef
{
	const POTENTIALITY_PERCENT_MODULUS						=				10000;

	const POTENTIALITY_ID									= 				'potentiality_id';
	const POTENTIALITY_LIST									=				'potentiality_list';
	const POTENTIALITY_LIST_NUM								=				'potentiality_list_num';
	const POTENTIALITY_TYPE_NUM								=				'potentiality_type_num';
	const POTENTIALITY_TYPE_NUM_LIST						=				'potentiality_type_num_list';
	const POTENTIALITY_WEIGHT								=				'weight';

	const POTENTIALITY_ATTR_ID								=				'potentiality_attr_id';
	const POTENTIALITY_ATTR_VALUE							=				'potentiality_attr_value';
	const POTENTIALITY_VALUE_LOWER							=				'potentiality_value_lower';
	const POTENTIALITY_VALUE_UPPER							=				'potentiality_value_upper';
	const POTENTIALITY_VALUE_ADD							=				'potentiality_value_add';
	const POTENTIALITY_VALUE_MODIFY							=				'potentiality_value_modify';
	const POTENTIALITY_INIT_VALUE_LOWER						=				'potentiality_init_value_lower';
	const POTENTIALITY_INIT_VALUE_UPPER						=				'potentiality_init_value_upper';
	const POTENTIALITY_REFRESH_TYPE							=				'potentiality_refresh_type';
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */