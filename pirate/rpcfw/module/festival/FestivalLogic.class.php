<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FestivalLogic.class.php 32232 2012-12-03 08:45:55Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/FestivalLogic.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-03 16:45:55 +0800 (一, 2012-12-03) $
 * @version $Revision: 32232 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : FestivalLogic
 * Description : 节日活动实际逻辑实现类， 对内接口实现类
 * Inherit     :
 **********************************************************************************************************************/
class FestivalLogic
{
	/**
	 * 节日活动时间检查
	 * 
	 * @param int $date							当前时间
	 * @return array $festival					节日增益信息
	 */
	public static function checkFestivalDate($date)
	{
		// 节日活动信息表
		$festival = array();
		$arrayFestivalGainInfo = btstore_get()->JIERIHUODONG;
		foreach ($arrayFestivalGainInfo as $value)
		{
			// 节日活动时间内
			if ($date >= strtotime($value[FestivalDef::FESTIVAL_BEGIN_DATA]) && 
				$date <= strtotime($value[FestivalDef::FESTIVAL_END_DATA]))
			{
				$festival = $value;
				Logger::debug('festival value is %s', $festival);
				break;
			}
		}
		return $festival;
	}

	/**
	 * 取得节日活动信息
	 * 
	 * @param int $uid							用户id
	 * @param array $gainInfo					节日活动配置表信息
	 * @return array $festival					节日增益信息
	 */
	public static function getFestival($uid, $gainInfo)
	{	
		// 结果
		$arrRet = array();
		// 检索条件
		$arrCond = array(array('uid', '=', $uid));
		// 检索项目
		$arrBody = array('times', 'point', 'update_time');
		$arrRet = FestivalDAO::selectFestival($arrCond, $arrBody);
		if (!Empty($arrRet))
		{
			// 每日凌晨4点清空积分和翻牌次数、活动结束数据清空
			// 如果上次更新的时间是今天之前
			if (!Util::isSameDay($arrRet['update_time'], FestivalConf::REFRESH_TIME))
			{
				self::clearFlopCardsResult($uid);
				$arrRet['times'] = 0;
				$arrRet['point'] = 0;
			}
			else 
			{
				// 翻牌总次数
				$tempTimes = floor($arrRet['point']/
								$gainInfo[FestivalDef::FESTIVAL_FLOPCARD_POINT]);
				// 总次数 <= 剩余次数
				if ($tempTimes <= $arrRet['times'])
				{
					$arrRet['times'] = 0;
				}
				else 
				{
					$arrRet['times'] = $tempTimes - $arrRet['times'];
				}	
			}
		} 
		else
		{
			// 节日活动刚开始用户数据没有的话，插入新的数据
			self::creatFestivalRec($uid, 0);
			$arrRet['times'] = 0;
			$arrRet['point'] = 0;
		}
		return array('times' => $arrRet['times'],
					 'point' => $arrRet['point']);
	}
	
	/**
	 * 翻牌次数检查
	 * 
	 * @param int $uid							用户id
	 * @param array $gainInfo					节日活动配置表信息
	 * @return boolean ture						可以翻牌
	 * 				   false					不可翻牌
	 */
	public static function checkFlopCardsTimes($uid, $gainInfo)
	{
		// 结果
		$ret = array();
		// 检索条件
		$arrCond = array(array('uid', '=', $uid));
		// 检索项目
		$arrBody = array('times', 'point', 'update_time');
		// 剩余翻牌次数取得
		$ret = FestivalDAO::selectFestival($arrCond, $arrBody);
		if (Empty($ret))
		{
			return false;
		}
		// 每日凌晨4点清空积分和翻牌次数、活动结束数据清空
		// 如果上次更新的时间是今天之前
		if (!Util::isSameDay($ret['update_time'], FestivalConf::REFRESH_TIME))
		{
			self::clearFlopCardsResult($uid);
			return false;
		}

		// 翻牌总次数
		$tempTimes = floor($ret['point']/
					 $gainInfo[FestivalDef::FESTIVAL_FLOPCARD_POINT]);
		// 总次数 <=剩余次数 或 超过一日最大翻牌次数
		if ($tempTimes <= $ret['times'])
		{
			return false;
		}
		return true;
	}
	
	/**
	 * 更新翻牌结果、翻牌次数+1
	 * 
	 * @param int $uid							用户id
	 * @param array $cardsInfo					5张牌的信息
	 */
	public static function updateFlopCardsResult($uid)
	{
		// 更新条件
		$arrCond = array(array('uid', '=', $uid));
		// 更新项目
		$arrBody = array('times' => new IncOperator(1), 
						  'update_time' => Util::getTime());
		FestivalDAO::updateFestival($arrCond, $arrBody);
	}
	
	/**
	 * 清空积分、翻牌次数、翻牌结果
	 * 
	 * @param int $uid							用户id
	 */
	public static function clearFlopCardsResult($uid)
	{
		// 更新条件
		$arrCond = array(array('uid', '=', $uid));
		// 更新项目
		$arrBody = array('times' => 0, 
						  'point' => 0, 
						  'update_time' => Util::getTime());
		FestivalDAO::updateFestival($arrCond, $arrBody);
	}

	/**
	 * 更新活动积分
	 * 
	 * @param int $point						节日活动积分
	 */
	public static function addRewardPoint($point)
	{
		// 是否是节日活动，返回值是活动增益表的值
		$gainInfo = self::checkFestivalDate(Util::getTime());
		if (Empty($gainInfo))
		{
			Logger::debug('today is not festival');
			return;
		}

		// 翻牌活动是否开启
		if($gainInfo[FestivalDef::FESTIVAL_FLOPCARD_ONOFF] == 0)
		{
			Logger::debug('the flopcard game is off.');
			return;
		}

		$uid = RPCContext::getInstance()->getUid();
		// 结果
		$ret = array();
		// 检索、更新、插入条件
		$arrCond = array(array('uid', '=', $uid));

		// 检索项目
		$arrBody = array('point', 'update_time');
		// 节日活动积分取得
		$ret = FestivalDAO::selectFestival($arrCond, $arrBody);

		if (Empty($ret))
		{
			// 节日活动刚开始用户数据没有的话，插入新的数据
			self::creatFestivalRec($uid, $point);
		}
		else 
		{
			$arrBody = array();
			// 每日凌晨4点清空积分和翻牌次数、活动结束数据清空
			// 如果上次更新的时间是今天之前
			if (!Util::isSameDay($ret['update_time'], FestivalConf::REFRESH_TIME))
			{
				$arrBody['point'] = $point;
				$arrBody['times'] = 0;
			} 
			else
			{
				// 更新项目
				if ($ret['point'] + $point > $gainInfo[FestivalDef::FESTIVAL_POINT_MAX])
				{
					$arrBody = array('point' => $gainInfo[FestivalDef::FESTIVAL_POINT_MAX]);
				}
				else
				{
					$arrBody = array('point' => new IncOperator($point));
				}
			}
			$arrBody['update_time'] = Util::getTime();
			FestivalDAO::updateFestival($arrCond, $arrBody);
		}
	}
	
	/**
	 * 随机牌取得
	 * 
	 * @param int $uid 	 						用户ID
	 * @param int point 	 					积分
	 * 
	 */
	private static function creatFestivalRec($uid, $point, $exPoint = 0, $exGoldPoint = 0, $gold = 0)
	{
		$arrRet = array ('uid' => $uid,
						 'times' => 0, 
						 'point' => $point, 
						 'update_time' => Util::getTime(),
						 'exchange_point' => $exPoint, 
						 'exchange_point_gold' => $exGoldPoint,
						 'exchange_point_spend' => 0,
						 'exchange_gold' => $gold,
						 'exchange_point_time' => Util::getTime(),
						 'status' => 1);
		FestivalDAO::insertFestivalInfo($arrRet);
		return $arrRet;
	}

	/**
	 * 随机牌取得
	 * 
	 * @param int $uid							用户id
	 * @return array ('cardInfo',				5张牌的奖励信息
	 * 				  'cardId')					5张牌的ID和装备模板ID(前端显示用)
	 * 
	 */
	public static function getRandCard($uid)
	{
		// 随即牌的信息组
		$arrayCardsInfo = btstore_get()->JIERIHUODONG_JIANGLI;
		// 随即结果(5张牌的信息)
		$cardsId = Util::backSample($arrayCardsInfo,
									FestivalDef::FESTIVAL_CARD_NUM, 
									FestivalDef::FESTIVAL_CARD_WEIGHT);
		// 装备随即结果
		for ($i = 0; $i < FestivalDef::FESTIVAL_CARD_NUM; $i++) {
			$tempId = Util::backSample($arrayCardsInfo[$cardsId[$i]][FestivalDef::FESTIVAL_SONCARD],
									1, 
									FestivalDef::FESTIVAL_CARD_WEIGHT);
			$sonCardId[] = $tempId[0];
		}

		// 合并结果
		$arrayCards = array();
		for ($i = 0; $i < FestivalDef::FESTIVAL_CARD_NUM; $i++)
		{
			$arrayCards[] = $arrayCardsInfo[$cardsId[$i]]
							[FestivalDef::FESTIVAL_SONCARD][$sonCardId[$i]];
		}

		// 取得5张牌的装备信息
		$cardsInfo = array();
		for ($i = 0; $i < FestivalDef::FESTIVAL_CARD_NUM; $i++) {
			// 奖励掉落表ID组
			if (!Empty($arrayCards[$i][FestivalDef::FESTIVAL_CARD_ITEM]))
			{
				$itemTemplateArr = Drop::dropItem($arrayCards[$i][FestivalDef::FESTIVAL_CARD_ITEM]);
				if ( count($itemTemplateArr) != 1 )
				{
					Logger::FATAL('invalid good will drop id:%d', 
										$arrayCards[$i][FestivalDef::FESTIVAL_CARD_ITEM]);
					throw new Exception('config');
				}

				Logger::debug('itemTemplateIDs is %s', $itemTemplateArr);
				// 牌ID和装备模板ID
				$cardsInfo[] = array(
								'cardId' => $arrayCards[$i][FestivalDef::FESTIVAL_CARD_ID],
								'item_template_id' => $itemTemplateArr[0][DropDef::DROP_ITEM_TEMPLATE_ID],
								'item_num' => $itemTemplateArr[0][DropDef::DROP_ITEM_NUM]);
			}
			else
			{
				$cardsInfo[] = array(
								'cardId' => $arrayCards[$i][FestivalDef::FESTIVAL_CARD_ID],
								'item_template_id' => "",
								'item_num' => "");
			}
		}
		return array('cardInfo' => $arrayCards,
					 'cardId' => $cardsInfo);
	}

	/**
	 * 翻牌结果奖励更新到用户
	 * 
	 * @param int $uid							用户id
	 * @return array $bagInfo					用户背包信息
	 */
	public static function updateFpCardsRetToUser($uid, $cardInfo, $items)
	{
		$userObj = EnUser::getUserObj($uid);
		$userLevel = $userObj->getLevel();
		// 奖励贝里
		if (!Empty($cardInfo[FestivalDef::FESTIVAL_CARD_BELLY]))
		{
			$userObj->addBelly($cardInfo[FestivalDef::FESTIVAL_CARD_BELLY]*$userLevel);
		}
		// 奖励阅历
		if (!Empty($cardInfo[FestivalDef::FESTIVAL_CARD_EXPE]))
		{
			$userObj->addExperience($cardInfo[FestivalDef::FESTIVAL_CARD_EXPE]*$userLevel);
		}
		// 奖励金币
		if (!Empty($cardInfo[FestivalDef::FESTIVAL_CARD_GOLD]))
		{
			$userObj->addGold($cardInfo[FestivalDef::FESTIVAL_CARD_GOLD]);
		}
		// 奖励行动力
		if (!Empty($cardInfo[FestivalDef::FESTIVAL_CARD_EXEC]))
		{
			$userObj->addExecution($cardInfo[FestivalDef::FESTIVAL_CARD_EXEC]);
		}
		// 奖励声望
		if (!Empty($cardInfo[FestivalDef::FESTIVAL_CARD_PRES]))
		{
			$userObj->addPrestige($cardInfo[FestivalDef::FESTIVAL_CARD_PRES]);
		}
		// 数据更新
		$userObj->update();

		// 声明背包信息返回值
		$bagInfo = array();
		$itemArr = array();
		
		// 奖励掉落表ID组
		if (!Empty($items['item_template_id']))
		{
			$itemIDs = ItemManager::getInstance()->
						addItem($items['item_template_id'], $items['item_num']);
			Logger::debug('itemIDs is %s', $itemIDs);
		}
		if (!Empty($itemIDs)) 
		{
			// 背包
			$bag = BagManager::getInstance()->getBag();
			// 标志是否背包已经满了
			$deleted = FALSE;
			// 循环处理所有的掉落物品
			foreach ($itemIDs as $itemID)
			{
				// 背包没满时
				if ($deleted == FALSE)
				{
					// 先获取数据信息，保存。
					$itemTmp = ItemManager::getInstance()->itemInfo($itemID);
					// 塞一个货到背包里，可以使用临时背包
					if ($bag->addItem($itemID, TRUE) == FALSE)
					{
						// 如果连临时背包都满了的话， 删除该物品
						ItemManager::getInstance()->deleteItem($itemID);
						// 修改标志量
						$deleted = TRUE;
					}
					else
					{
						// 保留物品详细信息，传给前端
						$itemArr[] = $itemTmp;
					}
				}
				// 背包满了
				else 
				{
					// 删除该物品
					ItemManager::getInstance()->deleteItem($itemID);
				}
			}
			// 保存用户背包数据，并获取改变的内容
			$bagInfo = $bag->update();
		}
		Logger::debug('bag is %s', $bagInfo);
		return $bagInfo;
	}
	
	/**
	 * 节日活动时间检查-积分商城
	 * 
	 * @return array $festival					节日增益信息
	 */
	public static function checkFestivalMallDate()
	{
		// 节日活动信息表
		$festivalMallInfo = btstore_get()->FESTIVALMALL;
		return self::checkDate(Util::getTime(), $festivalMallInfo);
	}

	private static function checkDate($date, $festivalMallInfo)
	{
		$festival = array();
		foreach ($festivalMallInfo as $value)
		{
			// 节日活动时间内
			if ($date >= strtotime($value[FestivalDef::FESTIVAL_BEGIN_DATA]) && 
				$date <= strtotime($value[FestivalDef::FESTIVAL_END_DATA]) &&
				strtotime(GameConf::SERVER_OPEN_YMD) < strtotime($value[FestivalDef::FESTIVAL_SERVER_OPENDATE]))
			{
				$festival = $value;
				Logger::debug('festival value is %s', $festival);
				break;
			}
		}
		return $festival;
	}

	/**
	 * 节日活动积分商城增加积分
	 * 
	 * @param int $type							操作类型
	 */
	public static function addExchangePoint($type, $festivalMallInfo)
	{
		// 普通操作获得的积分
		$point = self::getExPoint($type);
		// 使用的金币(累计)
		$gold = self::getSpendGold($festivalMallInfo[FestivalDef::FESTIVAL_BEGIN_DATA]);
		// 使用的金币(累计)获得的积分
		$goldPoint = self::getExGoldPoint($gold);
		
		self::getUserExInfo($festivalMallInfo, $point, $goldPoint, $gold);
	}
	private static function getExPoint($type)
	{
		// 积分配置表
		$festivalExPoint = btstore_get()->FESTIVALEXPOINT;
		// 获得的积分
		$point = 0;
		switch ($type)
		{
		case FestivalDef::FESTIVAL_EXPOINT_SAIL:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_SAIL][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_COOK:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_COOK][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_ORDER:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_ORDER][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;	
		case FestivalDef::FESTIVAL_EXPOINT_DAY_TASK:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_DAY_TASK][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_SALARY:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_SALARY][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_SLAVE:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_SLAVE][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_REINFORCE:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_REINFORCE][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_ELITE_COPY:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_ELITE_COPY][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_EXPLOR:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_EXPLOR][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_ARENA:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_ARENA][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_ROB:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_ROB][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_PORT_ATK:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_PORT_ATK][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_DONATE:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_DONATE][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_RESOURCE:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_RESOURCE][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_TALKS:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_TALKS][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_TREASURE:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_TREASURE][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_SMELTING:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_SMELTING][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_RAPID:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_RAPID][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_GOOD_WILL:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_GOOD_WILL][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_GOOD_SOUL:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_GOOD_SOUL][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		case FestivalDef::FESTIVAL_EXPOINT_GOOD_ASTRO:
			$point = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_GOOD_ASTRO][FestivalDef::FESTIVAL_EXPOINT_POINT];
			break;
		default:
			$point = 0;
			break;
		}
		Logger::debug('the type is %s. the point is %s.', $type, $point);
		return $point;
	}
	private static function getSpendGold($date)
	{
		// 花费金币积分计算
		$user = EnUser::getUserObj();
		$arrDateAccum = $user->getAccumSpendGold();
		$beginDate = strftime("%Y%m%d", strtotime($date));
		$goldAccum = 0;
		foreach ($arrDateAccum as $date => $gold)
		{
			if ($date >= $beginDate)
			{
				$goldAccum += $gold;
			}
		}
		Logger::debug('the total gold(spend) is %s.', $goldAccum);
		return $goldAccum;
	}
	private static function getExGoldPoint($gold)
	{
		$point = 0;
		if($gold <= 0)
		{
			return $point;
		}
		// 积分配置表
		$festivalExPoint = btstore_get()->FESTIVALEXPOINT;
		// 使用金币获得的积分
		$goldPoint = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_GOLD][FestivalDef::FESTIVAL_EXPOINT_POINT];
		// 金币兑换积分基础值
		$goldBase = $festivalExPoint[FestivalDef::FESTIVAL_EXPOINT_GOLD][FestivalDef::FESTIVAL_EXPOINT_BASEGOLD];
		
		// 花费金币积分计算
		Logger::debug('the total gold(spend) is %s.', $gold);
		$point = floor($gold/$goldBase) * $goldPoint;
		Logger::debug('the gold point is %s.', $point);
		return $point;
	}

	/**
	 * 节日商城-获取当前积分
	 * 
	 * @param int $exItemId						兑换装备ID
	 */
	public static function getExchangePoint($festivalMall)
	{
		// 使用的金币(累计)
		$gold = self::getSpendGold($festivalMall[FestivalDef::FESTIVAL_BEGIN_DATA]);
		// 使用的金币(累计)获得的积分
		$goldPoint = self::getExGoldPoint($gold);
		
		$uid = RPCContext::getInstance()->getUid();
		// 用户信息取得
		$userInfo = self::getUserExInfo($festivalMall, 0, $goldPoint, $gold);
		$totalPoint = $userInfo['exchange_point'] + $userInfo['exchange_point_gold'];
		if($totalPoint > $userInfo['exchange_point_spend'])
		{
			$point = $totalPoint - $userInfo['exchange_point_spend'];
		}
		else 
		{
			$point = 0;
		}
		return array('point'=>$point);
	}

	/**
	 * 节日商城-装备兑换
	 * 
	 * @param int $exItemId						兑换装备ID
	 */
	public static function exchangeItem($exItemId, $festivalMall)
	{
		// 使用的金币(累计)
		$gold = self::getSpendGold($festivalMall[FestivalDef::FESTIVAL_BEGIN_DATA]);
		// 使用的金币(累计)获得的积分
		$goldPoint = self::getExGoldPoint($gold);
		
		$uid = RPCContext::getInstance()->getUid();
		// 用户信息取得
		$userInfo = self::getUserExInfo($festivalMall, 0, $goldPoint, $gold);
		// 减积分
		$ret = self::subExPoint($userInfo, $exItemId, $festivalMall);
		if($ret != 'ok')
		{
			return array('ret' => $ret);
		}
		// 给装备
		$ret = self::exItem($uid, $exItemId);
		return array('ret' => 'ok',
					 'items' => $ret);
	}
	private static function getUserExInfo($festivalMallInfo, $point, $goldPoint, $gold)
	{
		// 用户ID
		$uid = RPCContext::getInstance()->getUid();
		// 结果
		$ret = array();
		// 检索、更新、插入条件
		$arrCond = array(array('uid', '=', $uid));
		// 检索项目
		$arrBody = array('uid', 'exchange_point', 'exchange_point_gold', 
						 'exchange_point_time', 'exchange_point_spend',
						 'exchange_gold');
		// 节日活动积分取得
		$ret = FestivalDAO::selectFestival($arrCond, $arrBody);
		if (Empty($ret))
		{
			// 节日活动刚开始用户数据没有的话，插入新的数据
			return self::creatFestivalRec($uid, 0, $point, $goldPoint, $gold);
		}
		else 
		{
			$arrBody = array();
			// 如果上次更新的时间是本次活动之外, 清空积分
			if ($ret['exchange_point_time'] > strtotime($festivalMallInfo[FestivalDef::FESTIVAL_END_DATA]) ||
				$ret['exchange_point_time'] < strtotime($festivalMallInfo[FestivalDef::FESTIVAL_BEGIN_DATA]))
			{
				$arrBody['uid'] = $uid;
				$arrBody['exchange_point'] = $point;
				$arrBody['exchange_point_gold'] = $goldPoint;
				$arrBody['exchange_point_spend'] = 0;
				$arrBody['exchange_gold'] = $gold;
				Logger::debug('init exchangeInfo is %s.', $arrBody);
			} 
			else 
			{
				$arrBody['uid'] = $uid;
				$arrBody['exchange_point'] = $ret['exchange_point'] + $point;
				$arrBody['exchange_point_gold'] = $goldPoint;
				$arrBody['exchange_gold'] = $gold;
				$arrBody['exchange_point_spend'] = $ret['exchange_point_spend'];
				Logger::debug('update exchangeInfo is %s.', $arrBody);
			}
			$arrBody['exchange_point_time'] = Util::getTime();
			FestivalDAO::updateFestival($arrCond, $arrBody);
		}
		return $arrBody;
	}
	private static function subExPoint($userInfo, $exItemId, $festivalMall)
	{
		$itemArray = $festivalMall[FestivalDef::FESTIVAL_EXCHANGEITEMS];
		if(EMPTY($itemArray))
		{
			return 'err';
		}
		if(!isset($itemArray[$exItemId]))
		{
			return 'err';
		}
		$point = $itemArray[$exItemId];
		
		// 用户剩余积分
		Logger::debug('the point is %s.', $userInfo['exchange_point']);
		Logger::debug('the gold point is %s.', $userInfo['exchange_point_gold']);
		Logger::debug('the spend point is %s.', $userInfo['exchange_point_spend']);
		$rmPoint = $userInfo['exchange_point'] + 
					$userInfo['exchange_point_gold'] - 
					$userInfo['exchange_point_spend'];
		Logger::debug('the remain point is %s.', $rmPoint);
		if($rmPoint < $point)
		{
			Logger::debug('the point of user is not enough. point %s.', $rmPoint);
			return 'noPoint';
		}
		// 检索、更新、插入条件
		$arrCond = array(array('uid', '=', $userInfo['uid']));
		// 更新项目
		$arrBody = array('exchange_point_spend' => new IncOperator($point),
						 'exchange_point_time' => Util::getTime());
		FestivalDAO::updateFestival($arrCond, $arrBody);
		return 'ok';
	}
	private static function exItem($uid, $exItemId)
	{
		// 取得物品
		if (Empty($exItemId))
		{
			return;
		}
		$itemIdAry = ItemManager::getInstance()->addItem($exItemId, 1);
		if(EMPTY($itemIdAry))
		{
			return;
		}
		$userObj = EnUser::getUserObj();
		ChatTemplate::sendFextivalExItem($userObj->getTemplateUserInfo(), $itemIdAry[0]);
		$bagObj = BagManager::getInstance()->getBag();
		$bagObj->addItem($itemIdAry[0], TRUE);
		$bagInfo = $bagObj->update();
		Logger::debug('bagInfo %s.', $bagInfo);
		return $bagInfo;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */