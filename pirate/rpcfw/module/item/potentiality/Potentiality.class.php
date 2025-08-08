<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Potentiality.class.php 30073 2012-10-19 12:10:03Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/potentiality/Potentiality.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-10-19 20:10:03 +0800 (五, 2012-10-19) $
 * @version $Revision: 30073 $
 * @brief
 *
 **/

/**
 *
 * 潜能类,用于处理潜能相关的操作
 * @author Administrator
 *
 */
class Potentiality
{
	/**
	 *
	 * 随机潜能
	 *
	 * @param int $potentiality_id
	 *
	 * @throws Exception			如果$potentiality_id不存在,则throw exception
	 *
	 * @return array
	 */
	public static function randPotentiality($potentiality_id)
	{
		$potentialitys = array();

		$potentiality = self::getPotentiality($potentiality_id);

		//计算潜能的数量
		$keys = Util::backSample($potentiality[PotentialityDef::POTENTIALITY_TYPE_NUM_LIST], 1, PotentialityDef::POTENTIALITY_WEIGHT);
		$potentiality_num = $potentiality[PotentialityDef::POTENTIALITY_TYPE_NUM_LIST][$keys[0]][PotentialityDef::POTENTIALITY_TYPE_NUM];

		if ( $potentiality_num == 0 )
			return $potentialitys;


		//计算产生的潜能列表
		$keys = Util::noBackSample($potentiality[PotentialityDef::POTENTIALITY_LIST]->toArray(), $potentiality_num);
		foreach ( $keys as $key )
		{
			$value = $potentiality[PotentialityDef::POTENTIALITY_LIST][$key];
			$rand = rand($potentiality[PotentialityDef::POTENTIALITY_INIT_VALUE_LOWER], $potentiality[PotentialityDef::POTENTIALITY_INIT_VALUE_UPPER]);
			$rand = max($rand, $potentiality[PotentialityDef::POTENTIALITY_VALUE_LOWER]);
			$potentialitys[$value[PotentialityDef::POTENTIALITY_ATTR_ID]] = $rand;
		}
		return $potentialitys;
	}

	/**
	 *
	 * 固定洗潜能
	 *
	 * @param int $potentiality_id
	 * @param array $original
	 * @param boolean $special
	 *
	 * @throws Exception			如果$potentiality_id不存在,则throw exception
	 *
	 * return array
	 */
	public static function refreshPotentiality($potentiality_id, $original, $type)
	{
		$return = array();

		$potentiality = self::getPotentiality($potentiality_id);

		if ( !isset($potentiality[PotentialityDef::POTENTIALITY_REFRESH_TYPE][$type]) )
		{
			Logger::FATAL('invalid type:%d', $type);
			throw new Exception('fake');
		}

		$type_info = $potentiality[PotentialityDef::POTENTIALITY_REFRESH_TYPE][$type];
		foreach ( $original as $attr_id => $attr_value )
		{
			$value = 0;
			if ( isset($potentiality[PotentialityDef::POTENTIALITY_LIST][$attr_id]) )
			{
				$rand_lower = $type_info[PotentialityDef::POTENTIALITY_VALUE_ADD] - $type_info[PotentialityDef::POTENTIALITY_VALUE_MODIFY];
				$rand_upper = $type_info[PotentialityDef::POTENTIALITY_VALUE_ADD] + $type_info[PotentialityDef::POTENTIALITY_VALUE_MODIFY];
				$rand = rand($rand_lower, $rand_upper);
				$value = $attr_value + $rand;
				$value = min($type_info[PotentialityDef::POTENTIALITY_VALUE_UPPER], $value);
				$value = max($potentiality[PotentialityDef::POTENTIALITY_VALUE_LOWER], $value);
			}
			else
			{
				Logger::FATAL('item_template_id:%d, invalid potentiality attr_id:%d', $attr_id);
				throw new Exception('invalid attr_id:%d', $attr_id);
			}
			$return[$attr_id] = $value;
		}
		return $return;
	}

	/**
	 *
	 * 得到固定潜能
	 *
	 * @param int $potentiality_id
	 *
	 * @throws Exception			如果$potentiality_id不存在,则throw exception
	 *
	 * return array
	 */
	public static function fixedPotentiality($potentiality_id)
	{
		if ( !isset(btstore_get()->FIXEDPOTENTIALITY[$potentiality_id]) )
		{
			Logger::FATAL('invalid fixed potentiality id:%d!', $potentiality_id);
		}

		$potentiality = btstore_get()->FIXEDPOTENTIALITY[$potentiality_id][PotentialityDef::POTENTIALITY_LIST];

		return $potentiality;
	}

	/**
	 *
	 * 得到潜能
	 *
	 * @param int $potentiality_id
	 *
	 * @throws Exception			如果$potentiality_id不存在,则throw exception
	 *
	 * return array
	 */
	public static function getPotentiality($potentiality_id)
	{
		if ( !isset(btstore_get()->POTENTIALITY[$potentiality_id]) )
		{
			Logger::FATAL('invalid potentiality id:%d!', $potentiality_id);
		}
		$potentiality = btstore_get()->POTENTIALITY[$potentiality_id];

		if ( empty($potentiality) )
		{
			Logger::warning("invalid potentiality id=%d", $potentiality_id);
			throw new Exception("fake");
		}
		return $potentiality;
	}

	/**
	 *
	 * 得到潜能属性价值
	 *
	 * @param int $potentiality_id						潜能ID
	 * @param int $attr_id								属性ID
	 *
	 * @return int										潜能属性价值
	 */
	public static function getPotentialityAttrValue($potentiality_id, $attr_id)
	{
		$potentiality = self::getPotentiality($potentiality_id);
		if ( !isset($potentiality[PotentialityDef::POTENTIALITY_LIST][$attr_id]) )
		{
			Logger::FATAL('invalid potentiality_id:%d, attr_id:%d', $potentiality_id, $attr_id);
			throw new Exception('fake');
		}
		return $potentiality[PotentialityDef::POTENTIALITY_LIST][$attr_id][PotentialityDef::POTENTIALITY_ATTR_VALUE];
	}

	/**
	 *
	 * 得到潜能属性的最大值
	 *
	 * @param int $potentiality_id						潜能ID
	 * @param int $attr_id								属性ID
	 * @param array $valid_types						可用的洗练类型
	 *
	 * @throws Exception
	 *
	 * @return int										潜能属性最大值
	 */
	public static function getMaxPotentialityAttrValue($potentiality_id, $attr_id, $valid_types)
	{
		$potentiality = self::getPotentiality($potentiality_id);
		if ( !isset($potentiality[PotentialityDef::POTENTIALITY_REFRESH_TYPE]) )
		{
			Logger::FATAL('invalid potentiality_id:%d, no refresh type!', $potentiality_id);
			throw new Exception('fake');
		}
		$max_value = 0;
		foreach ( $potentiality[PotentialityDef::POTENTIALITY_REFRESH_TYPE] as $type => $type_value )
		{
			if ( in_array($type, $valid_types) )
			{
				if ( $max_value < $type_value[PotentialityDef::POTENTIALITY_VALUE_UPPER] )
				{
					$max_value = $type_value[PotentialityDef::POTENTIALITY_VALUE_UPPER];
				}
			}
		}

		return $max_value;
	}

	/**
	 *
	 * 潜能是否具有属性
	 *
	 * @param int $potentiality_id						潜能ID
	 * @param int $attr_id								属性ID
	 *
	 * @return boolean
	 */
	public static function hasAttrId($potentiality_id, $attr_id)
	{
		$potentiality = self::getPotentiality($potentiality_id);
		if ( !isset($potentiality[PotentialityDef::POTENTIALITY_LIST][$attr_id]) )
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 *
	 * 潜能转移
	 *
	 * @param int $src_potentiality_id			源装备随机潜能ID
	 * @param int $target_potentiality_id		目标装备随机潜能ID
	 * @param array $src_potentiality			源转杯随机潜能
	 * @param array $refresh_type				随机潜能可刷新类型
	 *
	 * @throws Exception
	 *
	 * @return array							潜能转移后的潜能
	 */
	public static function transferPotentiality($src_potentiality_id, $target_potentiality_id, $src_potentiality, $refresh_type )
	{
		$target_potentiality = array();
		foreach ( $src_potentiality as $attr_id => $value )
		{
			//如果相应的潜能属性存在,则进行处理,否则直接跳过
			if ( Potentiality::hasAttrId($target_potentiality_id, $attr_id) == TRUE )
			{
				//如果相应的潜能属性在不同潜能上的价值不同,则抛出异常
				if ( Potentiality::getPotentialityAttrValue($src_potentiality_id, $attr_id)
					!= Potentiality::getPotentialityAttrValue($target_potentiality_id, $attr_id) )
				{
					Logger::FATAL('invalid attr value, attr_id:%d, src potentiality_id:%d, tar potentiality_id:%d',
						$attr_id, $src_potentiality_id, $target_potentiality_id);
					throw new Exception('config');
				}

				//如果源潜能属性最大值大于目标潜能属性最大值,则抛出异常
				if ( Potentiality::getMaxPotentialityAttrValue($src_potentiality_id, $attr_id, $refresh_type)
					> Potentiality::getMaxPotentialityAttrValue($src_potentiality_id, $attr_id, $refresh_type) )
				{
					Logger::DEBUG('invalid attr max value, attr_id:%d, src potentiality_id:%d, tar potentiality_id:%d',
						$attr_id, $src_potentiality_id, $target_potentiality_id);
					throw new Exception('config');
				}
				$target_potentiality[$attr_id] = $value;
			}
		}
		return $target_potentiality;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */