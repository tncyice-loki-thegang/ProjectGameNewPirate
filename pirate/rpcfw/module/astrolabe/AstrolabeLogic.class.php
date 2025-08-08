<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AstrolabeLogic.class.php 33682 2012-12-25 05:38:38Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/astrolabe/AstrolabeLogic.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-12-25 13:38:38 +0800 (二, 2012-12-25) $
 * @version $Revision: 33682 $
 * @brief 
 *  
 **/
class  AstrolabeLogic
{
	/*
	 * 初始化主星盘、天赋星盘
	 */
	public static  function initAstInfo($uid)
	{
		$retAry=array();
		
		//初始化主星盘
		$val=AstrolabeLogic::generateEmptyBasicAstInfo($uid);
		AstrolabeDAO::insertIntoAstTable($val);
		
		//设置主星盘的等级（初始为0）
		$level=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL];
		$retary[AstrolabeDef::ASTROLABE_RET_BASAST_LEV] = $level; 
		
		//初始化天赋星盘
		$data = btstore_get()->ASTROLABE_TRAYDOWNER;
		foreach ($data as $Id => $stars)
		{
			//检查第一个星
			if (isset($stars[0]))$astconids[]=intval($stars[0]);
		}

		//找天赋星盘的状态
		$aststat=array();
		if (!empty($astconids))
		{
			$ConsStat=AstrolabeLogic::getConsStatus($uid, $astconids);
			foreach ($ConsStat as $id => $status )
			{
				$ast_id = btstore_get()->ASTROLABE_STARS[$id]['trayId'];
				$aststat[$ast_id]=$status;
			}
		}
		$retary[AstrolabeDef::ASTROLABE_RET_AST_STATUS]=$aststat;
		
		//天赋星盘对应普通技能的状态
		$skilldepids=array();$idlevels=array();
		$giftdata = btstore_get()->ASTROLABE_STARGIFT;
		foreach ($giftdata as $astid=> $val)
		{
			$tmpdata=$val['norattneed'];
			$idlevel= explode('|', $tmpdata);
			$id=empty($idlevel[0]) ? 0 : $idlevel[0];
			$needlevel = empty($idlevel[1]) ? 0 : $idlevel[1];
			$idlevels[$id]=$needlevel;
			if (!in_array($id, $skilldepids))
			{
				$skilldepids[]=$id;
			}
		}
		$skilstat=array();
		$retidlevels=AstrolabeLogic::getConsLevel($uid, $skilldepids);
		foreach ($idlevels as $id => $needlevel)
		{
			$skilstat[$id]=AstrolabeDef::ASTROLABE_AST_SKILL_STATUS_NOT_ACTIVE;
		}
		foreach ($retidlevels as $id =>$curlevel)
		{
			if ($curlevel >= $idlevels[$id])
			{
				$skilstat[$id]=AstrolabeDef::ASTROLABE_AST_SKILL_STATUS_OK_ACTIVE;
			}
		}
		//把天赋星盘的信息插入数据库
		foreach ($aststat as $astid=>$stat)
		{
			$type=AstrolabeDef::ASTROLABE_AST_TYPE_TALENT ;
			$notactiv=AstrolabeDef::ASTROLABE_AST_SKILL_STATUS_NOT_ACTIVE;
			$skillstat=empty($skilstat[$astid])?$notactiv:$skilstat[$astid];
			$ary= array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_UID => $uid,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID => $astid,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE =>$type,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL =>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS =>$stat,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS =>$skillstat,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_EXP_TIME=>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ALL_EXP=>0);
			AstrolabeDAO::insertIntoAstTable($ary);
		}
		return $retary;
	}
	
	public static function generateEmptyBasicAstInfo($uid)
	{
		$id=AstrolabeDef::ASTROLABE_AST_MAIN_ID;
		$type=AstrolabeDef::ASTROLABE_AST_TYPE_MAIN;
		$stat=AstrolabeDef::ASTROLABE_AST_STATUS_NOT_ACTIVE;
		$skillstat=AstrolabeDef::ASTROLABE_AST_SKILL_STATUS_NOT_ACTIVE;
		$ary= array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_UID => $uid,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID => $id,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE =>$type,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL =>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS =>$stat,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS =>$skillstat,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_EXP_TIME=>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ALL_EXP=>0);
		return $ary;
	}
	
	/*
	 * 获得当前各个天赋星盘的状态,即检查天赋星盘星序表的第一颗星的状态
	 */
	public static  function getcurAllAstStatus($uid)
	{
		$aststat=array();$astconids=array();
		$data = btstore_get()->ASTROLABE_TRAYDOWNER;
		foreach ($data as $Id => $stars)
		{
			//检查第一个星
			if (isset($stars[0]))$astconids[]=intval($stars[0]);
		}
		
		if (!empty($astconids))
		{
			$ConsStat=AstrolabeLogic::getConsStatus($uid, $astconids);
			foreach ($ConsStat as $id => $status )
			{
				$ast_id = btstore_get()->ASTROLABE_STARS[$id]['trayId'];
				$aststat[$ast_id]=$status;
			}
		}
		return $aststat;
	}
	
	/*
	 * 获得当前当前天赋星盘内各个星座的等级、状态
	*/
	public static  function getConsLevStatIncurTalentAst($uid,$astid)
	{
		$retary=array();
		$retConsLevel=array();$retConsStat=array();
		if (isset(btstore_get()->ASTROLABE_TRAYDOWNER[$astid]))
		{
			$consids=array();
			$ids = btstore_get()->ASTROLABE_TRAYDOWNER[$astid];
			foreach ($ids as $val)
			{
				$consids[]=intval($val);
			}
			$retConsStat=AstrolabeLogic::getConsStatus($uid, $consids);
			$retConsLevel=AstrolabeLogic::getConsLevel($uid, $consids);
		}
		$retary[AstrolabeDef::ASTROLABE_RET_CONS_STATUS ]=$retConsStat;
		$retary[AstrolabeDef::ASTROLABE_RET_CONS_LEV ]=$retConsLevel;
		return $retary;
	}
	
	/*
	 * 获得主星盘的信息
	 */
	public static  function getBasicAstInfo($uid)
	{
		$type=AstrolabeDef::ASTROLABE_AST_TYPE_MAIN;
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=', $uid),
						array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=', $type),);
		$selectfield = array(  AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL,
				 			   AstrolabeDef::ASTROLABE_SQL_FIELD_AST_EXP_TIME,
							   AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ALL_EXP);
		return AstrolabeDAO::getInfoFromAstTable($selectfield, $wheres);
	}
	/*
	 * 检查是不是策划更新了天赋星盘表
	*/
	public static  function checkIsUpdateTalentAstTable($uid,$curstatus)
	{
		$curids=array();$allids=array();$updateids=array();
		$type=AstrolabeDef::ASTROLABE_AST_TYPE_TALENT;
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=', $uid),
				  array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=', $type),);
		$selectfield = array(  AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID);
		$retdata=AstrolabeDAO::getInfoFromAstTable($selectfield, $wheres);
		foreach ($retdata as $val)
		{
			$curids[]=intval($val[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID]);
		}
		
		$data = btstore_get()->ASTROLABE_TRAYDOWNER;
		foreach ($data as $Id => $stars)
		{
			if (!in_array($Id, $curids))
			{
				$updateids[]=intval($Id);
			}
		}
		
		foreach ($updateids as $astid) 
		{
			$type=AstrolabeDef::ASTROLABE_AST_TYPE_TALENT ;
			$notactiv=AstrolabeDef::ASTROLABE_AST_SKILL_STATUS_NOT_ACTIVE;
			$stat= isset($curstatus[$astid])?$curstatus[$astid]:AstrolabeDef::ASTROLABE_AST_STATUS_NOT_ACTIVE;
			$skillstat=AstrolabeDef::ASTROLABE_AST_SKILL_STATUS_NOT_ACTIVE;
			$ary= array(
					AstrolabeDef::ASTROLABE_SQL_FIELD_UID => $uid,
					AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID => $astid,
					AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE =>$type,
					AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL =>0,
					AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS =>$stat,
					AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS =>$skillstat,
					AstrolabeDef::ASTROLABE_SQL_FIELD_AST_EXP_TIME=>0,
					AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ALL_EXP=>0);
			AstrolabeDAO::insertIntoAstTable($ary);
		}
		
	}

	//获得当前装备的天赋星盘id 、普通技能状态
	public static  function getCurTalentAstInfo($uid)
	{
		//获得装备的天赋id
		$retary=array(AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID =>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS=>0,);
		$type=AstrolabeDef::ASTROLABE_AST_TYPE_TALENT;
		$stat=AstrolabeDef::ASTROLABE_AST_STATUS_EQUIPED;
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=', $uid),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=', $type),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS, '=', $stat)
				);
		$selectfield = array(  AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID,
							   AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS);
		$retast= AstrolabeDAO::getInfoFromAstTable($selectfield, $wheres);
		if (!empty($retast))
		{
			$retary=$retast[0];
		}
		return  $retary;
	}
	
	public static function checkLevelUpCondition($uid,$consid,$stonNum,$level,$uerlevel,&$needexp)
	{
		//通过该星座找到对应的限制条件（星灵石够不够，主角等级够不够）
		$expid=intval(btstore_get()->ASTROLABE_STARS[$consid]['astExpId']);
		$ismain=intval(btstore_get()->ASTROLABE_STARS[$consid]['isMain']);
		if (!empty($expid)&& $expid > 0)
		{
			//如果是主星盘，则只找第一个升级限制
			if ($ismain > 0 ){
				$level=1;
			}
			//检查这些id的等级是否满足
			$aryexp=btstore_get()->ASTROLABE_EXP[intval($expid)];
			if ($level <= count($aryexp))
			{
				$ary=$aryexp[$level];
				foreach ($ary as $exp => $needlevel)
				{
					//星灵石检查
					$needexp+=intval($exp);
					$stonNum-=intval($exp);
					if (($stonNum)< 0)
					{
						return false;
					}
					//检查主角等级
					if ($uerlevel < intval($needlevel))
					{
						return false;
					}
				}
			}
		}
		return true;
	}
	/*
	 * 检查某个星座的状态（未激活  可以激活 已经激活），
	 * 不是去查数据库，getConsStatusFromDb函数已经检查过了
	 */
	public static function checkConsStatus($uid,$consId)
	{
		/*是否可以激活需要三个判断:
		 * 1、开启星座需要的星座ID级别组
		 * 2、开启星座需要主角转职次数
		 * 3、开启星座需要主角等级
		 * 4、是不是依赖主星盘等级
		 */
		if (empty($consId))
		{
			Logger::debug('checkConsStatus consId err');
			return  AstrolabeDef::ASTROLABE_CONSTE_STATUS_UNKHOWN;
		}
		
		//主星盘里的星座不需要激活的，只能检查天赋星盘里星座的激活
		$isman = btstore_get()->ASTROLABE_STARS[$consId]['isMain'];
		if ( $isman  > 0)
		{
			Logger::debug('checkConsStatus isman err consId:%s',$consId);
			return  AstrolabeDef::ASTROLABE_CONSTE_STATUS_UNKHOWN;
		}
		
		//检查主角等级
		$user = EnUser::getUserObj($uid);
		$userlevel = btstore_get()->ASTROLABE_STARS[$consId]['astOpenUserLevel'];
		if (!empty($userlevel) && $user->getMasterHeroLevel() < $userlevel)
		{
			Logger::debug('checkConsStatus userlevel err consId:%s needlevl:%s curlevel:%s',$consId,$userlevel, $user->getMasterHeroLevel());
			return AstrolabeDef::ASTROLABE_CONSTE_STATUS_NOT_ACTIVE;
		}
		
		//检查转职次数
		$userooc =btstore_get()->ASTROLABE_STARS[$consId]['astOpenUserOcc'];
		$hero = $user->getMasterHeroObj();
		$transferNum = $hero->getTransferNum();
		if ($userooc > $transferNum)
		{
			Logger::debug('checkConsStatus userooc err consId:%s $needooc:%s curtransferNum:%s',$consId,$userooc,$transferNum);
			return AstrolabeDef::ASTROLABE_CONSTE_STATUS_NOT_ACTIVE;
		}
		
		//检查星座ID级别组
		$talids=array();$mainlevel=-1;
		$idgroups=btstore_get()->ASTROLABE_STARS[$consId]['astOpenPremiss'];
		foreach ($idgroups as $id => $level)
		{
			if ( $id == AstrolabeDef::ASTROLABE_SPECIAL_CONS_ID)//依赖主星盘
				$mainlevel=$level;//一个玩家只有一个主星盘，不会是数组
		    elseif ($id > 0)
		    	$talids[]=$id;
		}	
		//如果依赖主星盘，去检查主星盘的等级
		if ( $mainlevel > 0)
		{
			$selectfield = array(AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL);
			//把等级检查放到sql语句里，省得后面再检查了
			$type=AstrolabeDef::ASTROLABE_AST_TYPE_MAIN;
			$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid),
					  array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL, '>=',$mainlevel),
					  array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=',$type));
			$ret= AstrolabeDAO::getInfoFromAstTable($selectfield, $wheres);
			if (empty($ret))
			{
				Logger::debug('checkConsStatus mainlevel err consId:%s',$consId);
				return AstrolabeDef::ASTROLABE_CONSTE_STATUS_NOT_ACTIVE;
			}
		}
		//依赖其他星座
		if (!empty($talids))
		{
			$selectfield = array(AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID,
					AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_LEV);
			$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID, 'IN', $talids),
					array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
			$ret= AstrolabeDAO::getInfoFromConsTable($selectfield, $wheres);
			
			//如果查询结果个数小于$ids里的个数，说明要求的某些id还没开启
			if (count($talids)!=count($ret))
			{
				return AstrolabeDef::ASTROLABE_CONSTE_STATUS_NOT_ACTIVE;
			}
			
			//检查对应的等级够不够
			foreach ($ret as $val)
			{
				$id=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID];
				$levl=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_LEV];
				if ($idgroups[$id] > $levl )
				{
					return AstrolabeDef::ASTROLABE_CONSTE_STATUS_NOT_ACTIVE;
				}
			}
		}
		//各项条件检查通过，可以开启
		return  AstrolabeDef::ASTROLABE_CONSTE_STATUS_CAN_ACTIVE;
	}
	
	/*
	 * 传入一个或者多个星座id，返回这些id在数据库中的状态（未激活  可以激活 已经激活）
	*/
	public static function getConsStatusFromDb($uid,$consIds)
	{
		//先去数据库查下，看是不是已经激活了
		$retary=array();
		if (empty($consIds))
		{
			return $retary;
		}
		$selectfield = array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_STAT);
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID, 'IN', $consIds),
				  array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		
		$ret= AstrolabeDAO::getInfoFromConsTable($selectfield, $wheres);
		foreach ($consIds as $id)
		{
			$retary[$id]=AstrolabeDef::ASTROLABE_CONSTE_STATUS_UNKHOWN;
		}
		foreach ($ret as $val)
		{
			$curid=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID];
			$curstat=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_STAT];
			$retary[$curid]=$curstat;
		}
		return $retary;
	}

	/*
	 * 输入一组 星座id的array，返回这些id的状态
	 */
	public static function getConsStatus($uid,$consIds)
	{
		$statary=array();
		if (empty($consIds))return $statary;
		
		//先从数据库里去查状态
		$consstat=AstrolabeLogic::getConsStatusFromDb($uid,$consIds);
		
		//如果这些星星在数据库里没有状态，则去检查当前的状态
		foreach ($consstat as $id => $stat )
		{
			if ($stat == AstrolabeDef::ASTROLABE_CONSTE_STATUS_UNKHOWN)
			{
				$status=AstrolabeLogic::checkConsStatus($uid,$id);
				$consstat[$id] = $status;
				//把检查后的状态插入数据库
				$type=btstore_get()->ASTROLABE_STARS[$id]['isMain'];
				$arryfiled= array(AstrolabeDef::ASTROLABE_SQL_FIELD_UID => $uid,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID => $id,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_LEV =>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_STAT =>$status,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_TYPE =>$type,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ALLEXP =>0,);
				AstrolabeDAO::insertIntoConsTable($arryfiled);
			}
			elseif ($stat == AstrolabeDef::ASTROLABE_CONSTE_STATUS_NOT_ACTIVE)
			{
				$status=AstrolabeLogic::checkConsStatus($uid,$id);
				$consstat[$id] = $status;
				//更新状态到数据库
			    if ($status >$stat )
			    {
			    	$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_STAT =>$status);
			    	$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid),
			    			array (AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID, '=',$id),);
			    	AstrolabeDAO::updateConsTable($set, $wheres);
			    }
			}
			else
			{
				$consstat[$id] = $stat;
			}
		}
		return $consstat;
	}
    
	/*
	 * 输入一组 星座id的array，返回这些id的等级
	*/
	public static function getConsLevel($uid,$consIds)
	{
		$retary=array();
		if (empty($consIds))
		{
			return $retary;
		}
		$selectfield = array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_LEV);
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID, 'IN', $consIds),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		
		$ret= AstrolabeDAO::getInfoFromConsTable($selectfield, $wheres);
		foreach ($consIds as $id)
		{
			$retary[$id]=0;
		}
		foreach ($ret as $val)
		{
			$curid=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID];
			$curlev=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_LEV];
			$retary[$curid]=$curlev;
		}
		return $retary;
	}
	
	/*
	 * 获得当前星座的升级所用总经验
	*/
	public static function getConsCurLevlUpExp($uid,$consid)
	{
		$retval=0;
		if (empty($consid))
		{
			return $retval;
		}
		$selectfield = array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ALLEXP);
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID, '=', $consid),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
	
		$ret= AstrolabeDAO::getInfoFromConsTable($selectfield, $wheres);
		foreach ($ret as $val)
		{
			$retval=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ALLEXP];
		}
		return $retval;
	}
	
	/*
	 * 主星盘升级
	 */
	public static function levelUpMainAst($uid,$level,$exp,$curlevelupexp)
	{
		
	    //先扣除经验
		$set1=array(AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM =>$exp);
		$wheres1  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		if(!AstrolabeDAO::updateStoneTable($set1, $wheres1))
		{
			return false;
		}
		
		//再升级
		$set2=array(AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL =>$level,
				    AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ALL_EXP =>$curlevelupexp);
		$type=AstrolabeDef::ASTROLABE_AST_TYPE_MAIN ;
		$wheres2  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=', $type),
				        array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		if(!AstrolabeDAO::updateAstTable($set2, $wheres2))
		{
			return false;
		}
		return true;
	}
	
	public static function LevelUpTalentCons($uid,$id,$level,$exp,$curlevelupexp)
	{
		//先扣除经验
		$set1=array(AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM =>$exp);
		$wheres1  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		if(!AstrolabeDAO::updateStoneTable($set1, $wheres1))
		{
			return false;
		}
		
		//再升级
		$set2=array(AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_LEV =>$level,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ALLEXP =>$curlevelupexp);
		$wheres2  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID, '=', $id),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		if(!AstrolabeDAO::updateConsTable($set2, $wheres2))
		{
			return false;
		}
		return true;
	}

	public static function getStoneInfo($uid)
	{	
		$retary=array();
		$selectfield = array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM,
				AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT,
				AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT);
		$wheres  =array (array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		
		$ret= AstrolabeDAO::getInfoFromStoneTable($selectfield, $wheres);
		if (empty($ret))
		{
			$retary=array(
					AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM=>0,
					AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT=>0,
					AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT=>0
					);
		}
		else
		{
			$retary=$ret[0];
		}
		return $retary;
	}
	public static function getAstConsStatusByLevulUp($uid,$consid)
	{
		$retAry=array();
		
		//星座升级后，要通知天赋星盘的星座，看他们是不是可以被激活
		$astconids=array();
		$data = btstore_get()->ASTROLABE_TRAYDOWNER;
		foreach ($data as $Id => $stars)
		{
			//检查第一个星
			if (isset($stars[0])&&intval($stars[0])>0)$astconids[]=intval($stars[0]);
		}
		$talconids=array();
		if ($consid > 0)
		{
			if(isset(btstore_get()->ASTROLABE_DEPEND[$consid]))
			{
				$ary=btstore_get()->ASTROLABE_DEPEND[$consid];
				foreach ($ary as $id )
				{
					if (intval($id)>0) $talconids[]=intval($id);
				}
			}
		}
		else
		{
			$ary=btstore_get()->ASTROLABE_DEPEND_MAIN;
			foreach ($ary as $id => $type)
			{
				//主星盘上的星星没有激活的概念
				if ($type ==AstrolabeDef::ASTROLABE_CONSTE_AST_TYPE_TALENT )
				{
					if (intval($id)>0) $talconids[]=intval($id);
				}
			}
		}
		$aryids=$talconids;
		foreach ($astconids as $id)
		{
			if (!in_array($id, $talconids))
			{
				$aryids[]=$id;
			}
		}
		$retStat=array();
		if (!empty($aryids))
		{
			$retStat=AstrolabeLogic::getConsStatus($uid, $aryids);
		}
		
		//生成星盘和星座的状态
		$aststat=array();$constat=array();
		foreach ($retStat as $id => $stat)
		{
			if (in_array($id, $astconids))
			{
				$ast_id = btstore_get()->ASTROLABE_STARS[$id]['trayId'];
				$aststat[$ast_id]=$stat;
			}
			if (in_array($id, $talconids))
			{
				$constat[$id]=$stat;
			}
		}
		$retAry['activeTray']=$aststat;
		$retAry['activeStart']=$constat;
		
		return $retAry;
	}
	
	public static function checkSkillStatusByLevulUp($uid,$consid,$level)
	{
		$retary=array();
		$ary=btstore_get()->ASTROLABE_STARGIFT;
		foreach ($ary as $astid=>$val)
		{
			$tmpdata=$val['norattneed'];
			$idlevel= explode('|', $tmpdata);
			$id=empty($idlevel[0]) ? 0 : $idlevel[0];
			$needlevel = empty($idlevel[1]) ? 0 : $idlevel[1];
			if ($id == $consid && $level >= $needlevel )
			{
				//激活该技能
				$stat=AstrolabeDef::ASTROLABE_AST_SKILL_STATUS_OK_ACTIVE;
				$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS =>$stat);
				$type=AstrolabeDef::ASTROLABE_AST_TYPE_TALENT ;
				$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=', $type),
						array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID, '=', $astid),
						array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
				AstrolabeDAO::updateAstTable($set, $wheres);
				
				$retary[$astid]=$stat;
			}
		}
		return $retary;
	}
	
	/*
	 *更新数据库里天赋星盘的状态，只更新天赋星盘，基本星盘没状态
	 */
	public static  function updateTalentAstStatus($uid,$aststat)
	{
		$type=AstrolabeDef::ASTROLABE_AST_TYPE_TALENT;
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=', $uid),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=', $type));
		$selectfield = array(  AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS);
		$retast= AstrolabeDAO::getInfoFromAstTable($selectfield, $wheres);

		$needupdate=array();
		if (!empty($retast))
		{
			foreach ($retast as $val)
			{
				$id=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID];
				$stat=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS];
				if (isset($aststat[$id]) && $aststat[$id] != $stat)
				{
					$needupdate[$id] =$aststat[$id];
				}
			}
		}
		if (!empty($needupdate)) 
		{
			foreach ($needupdate as $id =>$stat)
			{
				//
				$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS =>$stat);
				$type=AstrolabeDef::ASTROLABE_AST_TYPE_MAIN ;
				$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID, '=', $id),
						array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
				AstrolabeDAO::updateAstTable($set, $wheres);
			}
		}
	}
	
	/*
	 * 获得天赋星盘的状态
	 */
	public static  function getTalentAstInfo($uid,$astid)
	{
		$retary=array(AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS =>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS=>0,);
		$type=AstrolabeDef::ASTROLABE_AST_TYPE_TALENT;
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=', $uid),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID, '=', $astid),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=', $type));
		$selectfield = array(  AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS);
		$retast= AstrolabeDAO::getInfoFromAstTable($selectfield, $wheres);
		if (!empty($retast))
		{
			$retary=$retast[0];
		}
		return  $retary;
	}
	/*
	 * 激活天赋星盘
	 */
	public static  function activeTalentAst($uid,$astid)
	{
		//激活该技能,等级变成1
		$stat=AstrolabeDef::ASTROLABE_AST_STATUS_OK_ACTIVE;
		$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS =>$stat,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL =>1);
		$type=AstrolabeDef::ASTROLABE_AST_TYPE_TALENT ;
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=', $type),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID, '=', $astid),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		AstrolabeDAO::updateAstTable($set, $wheres);
	}
	
	/*
	 * 激活天赋星座
	*/
	public static  function activeTalentCons($uid,$consid)
	{
		$status=AstrolabeDef::ASTROLABE_CONSTE_STATUS_OK_ACTIVE;
		$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_STAT =>$status);
			    	$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid),
			    			array (AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID, '=',$consid),);
		AstrolabeDAO::updateConsTable($set, $wheres);
	}
	
	/*
	 * 设置天赋星盘的装备情况
	 */
	public static  function setTalentAstEquipInfo($uid,$astid,$stat)
	{
		$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS =>$stat,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL =>1);
		$type=AstrolabeDef::ASTROLABE_AST_TYPE_TALENT ;
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=', $type),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID, '=', $astid),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		AstrolabeDAO::updateAstTable($set, $wheres);
	}
	
	public static function getAllStoneInfo($uid)
	{
		$selectfield = array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM,
				AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT,
				AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME);
		$wheres  =array (array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		$ret= AstrolabeDAO::getInfoFromStoneTable($selectfield, $wheres);
		return $ret;
	}
	
	//更新灵石表的次数信息
	public static  function updateStoneCountInfo($uid)
	{
		// 获取用户VIP等级
		$vipLv = EnUser::getUserObj($uid)->getVip();
		//获得配置信息
		$stoneconf=btstore_get()->ASTROLABE_STONE;
		$bellycount=intval(btstore_get()->ASTROLABE_STONE['dailyBelly']);
		$vipary=btstore_get()->ASTROLABE_STONE['vip'];
		$index= $vipLv -1;//数组下标从0开始
		$vipcount=isset($vipary[$index])?$vipary[$index]:0; 
		
		//返回值
		$valary= array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_UID => $uid,
				AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM => 0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT =>$bellycount,
				AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT=>$vipcount,
				AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME =>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME=> 0);
		
		//获得灵石的次数和时间，并和当前时间比较，如果是不同的天，则更新信息
		$selectfield = array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM,
				AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT,
				AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT,
				AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME,
				AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME);
		$wheres  =array (array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		$retdata= AstrolabeDAO::getInfoFromStoneTable($selectfield, $wheres);
		
		//如果为空，则初始化
		if(empty($retdata))
		{
			//要算累计次数，这里把时间记上
			$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME]=Util::getTime();
			AstrolabeDAO::insertIntoStoneTable($valary);
		}
		else
		{
			$retdata=$retdata[0];
			$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM ]=
			$retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM ];
			
			$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT ]=
			$retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT ];
			
			$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT ]=
			$retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT ];
			
			$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME ]=
			$retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME ];
			
			$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME ]=
			$retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME ];
			
			//如果不为空，则看看是不是同一天，若不是同一天则重置
			$curbellytime=$retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME];
			if (!Util::isSameDay($retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME])&& $curbellytime > 0)
			{
				$curcount=$retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT];
				$days=Util::getDaysBetween($curbellytime);
				$newcount=$curcount+$bellycount*$days;
				$newcount=($newcount> 5)?5:$newcount;//最多累计5次
				if ($curcount!=$newcount)
				{
					$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT =>$newcount,
							   AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME=>Util::getTime());
					$wheres =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
					AstrolabeDAO::updateStoneTable($set, $wheres);
					$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT ]=$newcount;
					$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME ]=Util::getTime();
				}
			}
			if ($retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME]==0)
			{
				$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME=>Util::getTime());
				$wheres =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
				AstrolabeDAO::updateStoneTable($set, $wheres);
				$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME ]=Util::getTime();
			}
			
			//检查vip购买次数
			$curviptime=$retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME];
			if (!Util::isSameDay($retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME])&& $curviptime > 0)
			{
				$curcount=$retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT];
				$days=Util::getDaysBetween($curviptime);
				$newcount=$curcount+$vipcount*$days;
				$newcount=($newcount> 5)?5:$newcount;//最多累计5次
				if ($curcount!= $newcount)
				{
					$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT =>$newcount,
							AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME=>Util::getTime());
					$wheres =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
					AstrolabeDAO::updateStoneTable($set, $wheres);
					$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT ]=$newcount;
					$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME ]=Util::getTime();
				}
			}
			
			if ($retdata[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME]==0)
			{
				$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME=>Util::getTime());
				$wheres =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
				AstrolabeDAO::updateStoneTable($set, $wheres);
				$valary[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME ]=Util::getTime();
			}
		}
		return $valary;
	}
	
	public static  function buyStone($uid,$type,&$num,&$bellycount,&$vipcount,&$blflag)
	{
		$blflag= false;
		$userObj = EnUser::getUserObj($uid);
		if ($type==AstrolabeDef::ASTROLABE_STONE_BUY_TYPE_BELLY)
		{
			$curbelly=$userObj->getBelly();
			$costbelly=intval(btstore_get()->ASTROLABE_STONE['bellyCost']);
			if ($bellycount > 0 && $curbelly >= $costbelly )
			{
				$num+=intval(btstore_get()->ASTROLABE_STONE['bellyStone']);
				
				$retnum=$num;--$bellycount;
				$userObj->subBelly($costbelly);
				$userObj->update();
				$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM =>$num,
							AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT=>$bellycount,
						    AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME=>Util::getTime());
				$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
				AstrolabeDAO::updateStoneTable($set, $wheres);
				$blflag=true;
			}
			else
			{
				Logger::debug('buyStone err type:%s curbelly:%s costbelly:%s',$type,$curbelly,$costbelly);
			}
		}
		elseif ($type==AstrolabeDef::ASTROLABE_STONE_BUY_TYPE_GOLD)
		{
			$curgold=$userObj->getGold();
			$costgold=intval(btstore_get()->ASTROLABE_STONE['goldCost']);
			if ( $curgold >= $costgold)
			{
				$num+=intval(btstore_get()->ASTROLABE_STONE['goldStone']);

				$retnum=$num;
				$userObj->subGold($costgold);
				$userObj->update();
				$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM =>$num);
				$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
				AstrolabeDAO::updateStoneTable($set, $wheres);
				$blflag=true;
				//金币统计
				Statistics::gold(StatisticsDef::ST_FUNCKEY_ASTROLABE_SINGLE_GOLD, $costgold,  Util::getTime());
			}
			else
			{
				Logger::debug('buyStone err type:%s curgold:%s costgold:%s',$type,$curgold,$costgold);
			}
		}
		elseif ($type==AstrolabeDef::ASTROLABE_STONE_BUY_TYPE_VIP)
		{
			if ($vipcount > 0)
			{
				$num+=intval(btstore_get()->ASTROLABE_STONE['goldStone']);
				
				$retnum=$num;--$vipcount;
				$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM =>$num,
							AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT=>$vipcount,
						    AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_TIME=>Util::getTime());
				$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
				AstrolabeDAO::updateStoneTable($set, $wheres);
				$blflag=true;
			}
			else
			{
				Logger::debug('buyStone err type:%s vipcount:%s',$type,$vipcount);
			}
		}
		elseif ($type==AstrolabeDef::ASTROLABE_STONE_BUY_TYPE_ADV)
		{
			$curgold=$userObj->getGold();
			$advcost=intval(btstore_get()->ASTROLABE_STONE['advCost']);
			if ( $curgold >= $advcost)
			{
				$num+=intval(btstore_get()->ASTROLABE_STONE['advStone']);
			
				$retnum=$num;
				$userObj->subGold($advcost);
				$userObj->update();
				$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM =>$num);
				$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
				AstrolabeDAO::updateStoneTable($set, $wheres);
				$blflag=true;
				//金币统计
				Statistics::gold(StatisticsDef::ST_FUNCKEY_ASTROLABE_ADV_GOLD, $advcost,  Util::getTime());
			}
			else 
			{
				Logger::debug('buyStone err type:%s curgold:%s advcost:%s',$type,$curgold,$advcost);
			}
		}
		elseif ($type==AstrolabeDef::ASTROLABE_STONE_BUY_TYPE_BAIJIN)
		{
			$curgold=$userObj->getGold();
			$baijincost=intval(btstore_get()->ASTROLABE_STONE['baijinCost']);
			if ( $curgold >= $baijincost)
			{
				$num+=intval(btstore_get()->ASTROLABE_STONE['baijinStone']);
					
				$retnum=$num;
				$userObj->subGold($baijincost);
				$userObj->update();
				$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM =>$num);
				$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
				AstrolabeDAO::updateStoneTable($set, $wheres);
				$blflag=true;
				//金币统计
				Statistics::gold(StatisticsDef::ST_FUNCKEY_ASTROLABE_BAIJIN, $baijincost,  Util::getTime());
			}
			else
			{
				Logger::debug('buyStone err type:%s curgold:%s advcost:%s',$type,$curgold,$baijincost);
			}
		}
	}
	public static  function addStone($uid,$num)
	{
		$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM =>$num);
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		return AstrolabeDAO::updateStoneTable($set, $wheres);
	}
	
	/*
	 * 获得当前主星盘的属性信息
	 */
	public static  function getCurMainAstAttr($uid)
	{
	  $retary=array();
	  //获得主星盘等级
	  $valbasast=AstrolabeLogic::getBasicAstInfo($uid);
	  if (empty($valbasast))
	  {
	  	return $retary;
	  }
	  $starids=array();
	  $level=$valbasast[0][AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL];
	  if ($level < 1 )
	  {
	  	return $retary;
	  }
	  //根据等级找主星序表里的所有id
	  $stage=intval($level/AstrolabeDef::ASTROLABE_AST_MAIN_STAGE );
	  for ($i=1;$i <= $stage; $i++)
	  {
	  	$aryids=btstore_get()->ASTROLABE_TRAYMAIN[$i];
	  	foreach ($aryids as $id)
	  	{
	  		$starids[]=intval($id);
	  	}
	  }
	  $index=$level%AstrolabeDef::ASTROLABE_AST_MAIN_STAGE ;
	  for ($i=0;$i<$index;$i++)
	  {
	  	$starids[]=intval(btstore_get()->ASTROLABE_TRAYMAIN[$stage+1][$i]);
	  }
	  //计算这些id的属性
	  foreach ($starids as $id)
	  {
	  	$attrs=btstore_get()->ASTROLABE_STARS[$id];
	  	$attr=isset($attrs['astAttr1'])?$attrs['astAttr1']:0;
	  	$attrval=isset($attrs['astAttr1Value'])?$attrs['astAttr1Value']:0;
	  	if ($attr > 0 &&empty( $retary[$attr] ))$retary[$attr]=0;
	    if ($attr > 0 && $attrval > 0) $retary[$attr] +=	$attrval;
	    
	    $attr=isset($attrs['astAttr2'])?$attrs['astAttr2']:0;
	    $attrval=isset($attrs['astAttr2Value'])?$attrs['astAttr2Value']:0;
	    if ($attr > 0 &&empty( $retary[$attr] ))$retary[$attr]=0;
	    if ($attr > 0 && $attrval > 0) $retary[$attr] +=	$attrval;
	    
	    $attr=isset($attrs['astAttr3'])?$attrs['astAttr3']:0;
	    $attrval=isset($attrs['astAttr3Value'])?$attrs['astAttr3Value']:0;
	    if ($attr > 0 &&empty( $retary[$attr] ))$retary[$attr]=0;
	    if ($attr > 0 && $attrval > 0) $retary[$attr] +=	$attrval;
	    
	    $attr=isset($attrs['astAttr4'])?$attrs['astAttr4']:0;
	    $attrval=isset($attrs['astAttr4Value'])?$attrs['astAttr4Value']:0;
	  	if ($attr > 0 &&empty( $retary[$attr] ))$retary[$attr]=0;
	    if ($attr > 0 && $attrval > 0) $retary[$attr] +=	$attrval;
	    
	    $attr=isset($attrs['astAttr5'])?$attrs['astAttr5']:0;
	    $attrval=isset($attrs['astAttr5Value'])?$attrs['astAttr5Value']:0;
	    if ($attr > 0 &&empty( $retary[$attr] ))$retary[$attr]=0;
	    if ($attr > 0 && $attrval > 0) $retary[$attr] +=	$attrval;
	    
	  }
	  return $retary;
	}
	/*
	 * 获得当前天赋星盘的属性信息
	*/
	public static  function getCurTalentAstAttr($uid)
	{
		$retary=array();
		
		//当前装备的天赋星盘id
		$retidskill=AstrolabeLogic::getCurTalentAstInfo($uid);
		$talentId=$retidskill[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID];
		if ($talentId < 1)
		{
			return $retary;
		}
		if (!isset(btstore_get()->ASTROLABE_TRAYDOWNER[$talentId]))
		{
			return $retary;
		}
		
		//获得该天赋星盘的天赋
		if (isset(btstore_get()->ASTROLABE_STARGIFT[$talentId]))
		{
			$gift=btstore_get()->ASTROLABE_STARGIFT[$talentId];
			$retary[CreatureInfoKey::phyFDmgRatio]=$gift['phyFDmgRatio'];//调整物理伤害倍率	物理伤害倍率
			$retary[CreatureInfoKey::phyFEptRatio]=$gift['phyFEptRatio'];//调整物理免伤倍率	物理免伤倍率
			$retary[CreatureInfoKey::killFDmgRatio]=$gift['killFDmgRatio'];//调整物理免伤倍率	物理免伤倍率
			$retary[CreatureInfoKey::killFEptRatio]=$gift['killFEptRatio'];//调整物理免伤倍率	物理免伤倍率
			$retary[CreatureInfoKey::mgcFDmgRatio]=$gift['mgcFDmgRatio'];//调整物理免伤倍率	物理免伤倍率
			$retary[CreatureInfoKey::mgcFEptRatio]=$gift['mgcFEptRatio'];//调整物理免伤倍率	物理免伤倍率
		}	
			
		//找天赋星盘内的星座id 
		$ids=array();
		$consids=btstore_get()->ASTROLABE_TRAYDOWNER[$talentId];
		//把id转换成int类型的，不然用不了
		foreach ($consids as $id)
		{
			$ids[]=intval($id);
		}
		if (empty($ids))
		{
			return $retary;
		}
		
		//获得这些星星的等级
		$retdata=AstrolabeLogic::getConsLevel($uid,$ids);
		foreach ($retdata as $id=> $level)
		{
			if ($level < 1 )continue;
			
			$attrs=btstore_get()->ASTROLABE_STARS[$id];
			$attr=isset($attrs['astAttr1'])?$attrs['astAttr1']:0;
			$attrval=isset($attrs['astAttr1Value'])?$attrs['astAttr1Value']:0;
			if ($attr > 0 &&empty( $retary[$attr] ))$retary[$attr]=0;
			if ($attr > 0 && $attrval > 0) $retary[$attr] +=	$level*$attrval;
			
			$attr=isset($attrs['astAttr2'])?$attrs['astAttr2']:0;
			$attrval=isset($attrs['astAttr2Value'])?$attrs['astAttr2Value']:0;
			if ($attr > 0 &&empty( $retary[$attr] ))$retary[$attr]=0;
			if ($attr > 0 && $attrval > 0) $retary[$attr] +=	$level*$attrval;;
			
			$attr=isset($attrs['astAttr3'])?$attrs['astAttr3']:0;
			$attrval=isset($attrs['astAttr3Value'])?$attrs['astAttr3Value']:0;
			if ($attr > 0 &&empty( $retary[$attr] ))$retary[$attr]=0;
			if ($attr > 0 && $attrval > 0) $retary[$attr] +=	$level*$attrval;;
			
			$attr=isset($attrs['astAttr4'])?$attrs['astAttr4']:0;
			$attrval=isset($attrs['astAttr4Value'])?$attrs['astAttr4Value']:0;
			if ($attr > 0 &&empty( $retary[$attr] ))$retary[$attr]=0;
			if ($attr > 0 && $attrval > 0) $retary[$attr] +=	$level*$attrval;;
			
			$attr=isset($attrs['astAttr5'])?$attrs['astAttr5']:0;
			$attrval=isset($attrs['astAttr5Value'])?$attrs['astAttr5Value']:0;
			if ($attr > 0 &&empty( $retary[$attr] ))$retary[$attr]=0;
			if ($attr > 0 && $attrval > 0) $retary[$attr] +=	$level*$attrval;;
		}
		return $retary;
	}
	
	/*
	 * 获得今天的经验
	 */
	public static  function updateAstExpTime($uid)
	{
		$id=AstrolabeDef::ASTROLABE_AST_MAIN_ID;
		$type=AstrolabeDef::ASTROLABE_AST_TYPE_MAIN ;
		$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_AST_EXP_TIME =>Util::getTime());
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=', $type),
						array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid),
						array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID, '=',$id));
		return AstrolabeDAO::updateAstTable($set, $wheres);
	}
	
	/*
	 * 重置某个天赋星盘，并将经验返回给玩家。
	 * 其实数据库里已经记录了玩家每次升级时的经验，但是不能保证策划
	 * 何时更新过数据，如果策划更新了csv文件，那么用数据库里的经验可能升级不到当前等级。
	 * 但策划坚持用数据库里的，没辙！
	*/
	public static  function resetTalentAst($uid,$astid,$cost)
	{
		$ret=array();$baginfo=array();
		
		//条件检查，金币
		$userObj = EnUser::getUserObj($uid);
		$bag = BagManager::getInstance()->getBag();
		$curgold=$userObj->getGold();
		$costgold=btstore_get()->ASTROLABE_STONE[$cost]['gold'];
		if ($costgold > 0 && $costgold > $curgold)
		{
			Logger::warning('resetTalentAst gold err, curgold:%d costGold:%d', $curgold,$costgold);
			return $ret;
		}
		//条件检查，物品
		$itemid= btstore_get()->ASTROLABE_STONE[$cost]['item'];
		$itemnum= btstore_get()->ASTROLABE_STONE[$cost]['itemnum'];
		if ($itemid > 0 && $itemnum > 0 )
		{
			if (!$bag->deleteItembyTemplateID($itemid, $itemnum))
			{
				Logger::warning('resetTalentAst item err, item:%d num:%d', $itemid,$itemnum);
				return $ret;
			}
		}
		
		//获得该天赋星盘下的所有星座,并把id转换成int类型
		$consids=array();
		foreach (btstore_get()->ASTROLABE_TRAYDOWNER[$astid] as $id)
		{
			$consids[]=intval($id);
		}
		if (empty($consids))
		{
			Logger::warning('resetTalentAst id err, astid:%d', $astid);
			return $ret;
		}
		//获得各个天赋星座的经验
		$allexp=0;$alllevel=0;
		$selectfield = array(AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID,
							 AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_LEV,
							 AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_STAT,
							 AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ALLEXP);
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID, 'IN',$consids),
						array (AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_STAT, '>', 0),
				  		array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		$retcons= AstrolabeDAO::getInfoFromConsTable($selectfield, $wheres);
		foreach ($retcons as $val)
		{
			$allexp+=intval($val[AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ALLEXP]);
			$alllevel+=intval($val[AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_LEV]);
		}
		if ($allexp == 0 || $alllevel==0 )
		{
			Logger::warning('resetTalentAst exp or level err !allexp:%d allleve:%d',$allexp,$alllevel);
			return $ret;
		}
		
		//把天赋星座降为0 
		$firstid=btstore_get()->ASTROLABE_TRAYDOWNER[$astid][0];
		foreach ($retcons as $val)
		{
			$consid=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID];
			$status=AstrolabeDef::ASTROLABE_CONSTE_STATUS_NOT_ACTIVE;
			if ($firstid==$consid)
				$status=$val[AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_STAT];
			$set=array(	AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_LEV =>0,
						AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_STAT =>$status,
					    AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ALLEXP =>0);
			$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid),
					array (AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID, '=',$consid),);
			if (!AstrolabeDAO::updateConsTable($set, $wheres))
			{
				Logger::info('resetTalentAst updateDb err astid:%s cost:%s set:%s where:%s retcons:%s ',$astid,$cost,$set,$wheres,$retcons);
				return $ret;
			}
		}
		
		//修改天赋星盘状态和等级
		/*$stat=AstrolabeDef::ASTROLABE_AST_STATUS_NOT_ACTIVE;
		$skillstat=AstrolabeDef::ASTROLABE_AST_SKILL_STATUS_NOT_ACTIVE;
		$set=array(AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS =>$stat,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS=>$skillstat,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL =>0);
		$type=AstrolabeDef::ASTROLABE_AST_TYPE_TALENT ;
		$wheres  =array(array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE, '=', $type),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID, '=', $astid),
				array (AstrolabeDef::ASTROLABE_SQL_FIELD_UID, '=',$uid));
		AstrolabeDAO::updateAstTable($set, $wheres);
		*/
		
		//各项条件检查都通过了，扣除金币、删除物品
		if ($costgold > 0)
		{
			if (!$userObj->subGold($costgold))
			{
				Logger::info('resetTalentAst:subGold err! costGold:%s curgold:%s',$costgold,$curgold);
				return $ret;
			}
			//金币统计
			Statistics::gold(StatisticsDef::ST_FUNCKEY_ASTROLABE_RESET_CONS, $costgold,  Util::getTime());
		}
		$userObj->update();
		//删除物品
		if ($itemid > 0 && $itemnum > 0)
		{
			$baginfo=$bag->update();
		}
		
		//返给玩家经验
		$stoneNum = intval($allexp);
		$retAry=AstrolabeLogic::updateStoneCountInfo($uid);		
		$curnum=$retAry[AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM];
		$num=$stoneNum+$curnum;
		$num= ($num<0)?0:$num;
		self::addStone($uid,$num);
		
		//操作完成，记录日志
		Logger::info('resetTalent ok addexp:%s costgold:%s  costitem:%s  costitemnu:%s retcons:%s',$allexp,$costgold,$itemid,$itemnum,$retcons);
		
		//返回给前端
		$ret['addstone']=$stoneNum;
		$ret['curstone']=$num;
		$ret['baginfo']=$baginfo;
		return $ret;
		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */