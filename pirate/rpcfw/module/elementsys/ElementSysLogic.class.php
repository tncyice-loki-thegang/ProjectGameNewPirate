<?php

class ElementSysLogic
{
	private static $arrField = array('uid', 'element_score', 'playtimes', 'movetimes', 'refreshtimes', 'goldrefreshtimes', 'time', 'va_info');
	
	private static $matrix = array();
	private static $resetArr = array();
	
	private static function insertDefault($uid)
	{
		ElementSysDao::insert($uid, array(
			'playtimes' => 3,
			'movetimes' => 15,
			'refreshtimes' => 5,
			'time' => Util::getTime(),
			'va_info' => array('matrix' => self::creatematrix(),
								'times' => array(0,5,20,20,20),
								),			
		));
	}
	
	private static function creatematrix()
	{
		$black = rand(0,7).','.rand(0,6);
		
		for ($y=0; $y<7; $y++)
		{
			for ($x=0; $x<8; $x++)
			{
				if ($black == strval($x).",".strval($y))					
				{
					$array[strval($x).",".strval($y)] = array(7,1);
				} else $array[strval($x).",".strval($y)] = array(rand(1,6));			
			}
		}
		return $array;
	}
	
	private static function testmatrix()
	{
		$array = array(
'0,0'=>array(1,0),	'1,0'=>array(1,0),	'2,0'=>array(1,0),	'3,0'=>array(1,0),	'4,0'=>array(2,0),	'5,0'=>array(3,0),	'6,0'=>array(4,0),	'7,0'=>array(5,0),
'0,1'=>array(1,0),	'1,1'=>array(2,0),	'2,1'=>array(1,0),	'3,1'=>array(5,0),	'4,1'=>array(1,0),	'5,1'=>array(3,0),	'6,1'=>array(1,0),	'7,1'=>array(6,0),
'0,2'=>array(1,0),	'1,2'=>array(5,0),	'2,2'=>array(4,0),	'3,2'=>array(1,0),	'4,2'=>array(2,0),	'5,2'=>array(1,0),	'6,2'=>array(4,0),	'7,2'=>array(5,0),
'0,3'=>array(2,0),	'1,3'=>array(4,0),	'2,3'=>array(1,1),	'3,3'=>array(1,0),	'4,3'=>array(2,0),	'5,3'=>array(3,0),	'6,3'=>array(1,0),	'7,3'=>array(6,0),
'0,4'=>array(3,0),	'1,4'=>array(1,1),	'2,4'=>array(3,1),	'3,4'=>array(5,1),	'4,4'=>array(1,0),	'5,4'=>array(3,0),	'6,4'=>array(1,0),	'7,4'=>array(5,0),
'0,5'=>array(1,0),	'1,5'=>array(2,0),	'2,5'=>array(1,1),	'3,5'=>array(1,0),	'4,5'=>array(2,0),	'5,5'=>array(1,0),	'6,5'=>array(4,0),	'7,5'=>array(6,0),
'0,6'=>array(1,0),	'1,6'=>array(3,0),	'2,6'=>array(1,0),	'3,6'=>array(3,0),	'4,6'=>array(1,0),	'5,6'=>array(3,0),	'6,6'=>array(1,0),	'7,6'=>array(7,1),
		);
		return $array;
	}
	
	public static function getGameInfo($uid) 
	{
		$ret = ElementSysDao::get($uid, self::$arrField);
		if (empty($ret))
		{		
			self::insertDefault($uid);
			$ret = ElementSysDao::get($uid, self::$arrField);
		}
		if (!Util::isSameDay($ret['time']))
		{
			$ret['playtimes'] = 3;
			$ret['movetimes'] = 15;
			$ret['refreshtimes'] = 5;
			$ret['time'] = Util::getTime();
			ElementSysDao::update($uid, $ret);
		}
		return $ret;
	}
	
	private static function getMatchHoriz($x, $y)
	{
		$ret =array($x.','.$y);
		for ($i=1; $x+$i<8; $i++)
		{
			if (self::$matrix[strval($x).",".strval($y)] == self::$matrix[strval($x+$i).",".strval($y)])
			{
				array_push($ret, strval($x+$i).",".strval($y));
				continue;
			}
			return $ret;
		}
		return $ret;
	}
	
	private static function getMatchVert($x, $y)
	{
		$ret =array($x.','.$y);
		for ($j=1; $y+$j<7; $j++)
		{			
			if (self::$matrix[strval($x).",".strval($y)] == self::$matrix[strval($x).",".strval($y+$j)])
			{
				array_push($ret, strval($x).",".strval($y+$j));
				continue;
			}
			return $ret;
		}
		return $ret;
	}
      
	private static function lookForMatches()
	{
		$ret = array();
		for ($y=0; $y<7; $y++)
		{
			for ($x=0; $x<8; $x++)
			{
				$check = self::getMatchHoriz($x,$y);
				if (count($check)>2)
				{
					array_push($ret, $check);
					$x += count($check)-1;
				}
			}
		}
		
		for ($x=0; $x<8; $x++)
		{
			for ($y=0; $y<7; $y++)
			{
				$check = self::getMatchVert($x,$y);
				if (count($check)>2)
				{
					array_push($ret, $check);
					$y += count($check)-1;
				}
			}
		}
		return $ret;
	}
	
	private static function calcScore($array)
	{		
		$score = 0;
		foreach ($array as $key => $val)
		{
			$score += floor (count($val) * 200 / 30);
		}
		$comboReward = array(2=>15,3=>20,4=>25,5=>30,6=>35,7=>40,8=>45,9=>50,10=>55,11=>60,12=>65,13=>70,14=>75,15=>80,16=>85,17=>90,18=>95,19=>100,20=>105,21=>110,22=>115,23=>120,24=>125,25=>130,26=>135,27=>140,28=>145,29=>150,30=>155);
		if (count($array) > 1)
		{
			$score += intval($comboReward[count($array)]);
		}
		return $score;
	}
	
	public static function moveStone($uid, $oldPos, $newPos)
	{
		$info = self::getGameInfo($uid);
		$info['va_info']['matrix'][$oldPos] = $info['va_info']['matrix'][$newPos];
		$info['va_info']['matrix'][$newPos] = array(7,1);
		--$info['movetimes'];
		ElementSysDao::update($uid, $info);
		$ret['playtimes'] = $info['playtimes'];
		$ret['movetimes'] = $info['movetimes'];
		if ($info['movetimes'] == 0)
		{
			self::clear($uid, NULL);
		}
		return $ret;
	}
	
	public static function clear($uid, $type)
	{
		$info = self::getGameInfo($uid);		
		if ($type == 1)
		{
			$needGold = 200;
			$score = 370;			
			$user = EnUser::getInstance();
			$user->subGold($needGold);
			$user->update();
			$info['va_info']['matrix'] = self::testmatrix();
			$ret['va_info']['matrix'] = $info['va_info']['matrix'];
		} else
		{			
			self::$matrix = $info['va_info']['matrix'];
			$checkMatch = self::lookForMatches();
			// logger::warning($checkMatch);
			$score = self::calcScore($checkMatch);
			// $minX = 7; $maxX = 0;		
			// foreach ($checkMatch as $array)
			// {
				// foreach ($array as $pos)
				// {
					// $tmp = explode (',', $pos);
					// $minX = $tmp[0] <= $minX ? $tmp[0] : $minX;
					// $maxX = $tmp[0] <= $maxX ? $maxX : $tmp[0];			
				// }
			// }
			// for ($colume=$minX; $colume<=$maxX; $colume++)
			// {
				// $moveArr =array();
				// foreach ($checkMatch as $array)
				// {
					// foreach ($array as $pos)
					// {
						// $tmp = explode (",", $pos);
						// if ($tmp[0] == $colume)
						// {
							// array_push($moveArr, strval($colume).",".strval($tmp[1]));
						// }
					// }
				// }
				// $minY = 6; $maxY = 0;
				// foreach ($moveArr as $val)
				// {
					// $tmp = explode (",", $val);
					// $minY = $tmp[1] <= $minY ? $tmp[1] : $minY;
					// $maxY = $tmp[1] <= $maxY ? $maxY : $tmp[1];
				// }
				
				// $distance = $maxY - $minY + 1;
				// for ($y=$maxY; $y>=0; $y--)
				// {
					// $_y = $y - $distance; 
					// if ($_y>=0)
					// {
						// if(self::$matrix[strval($colume).",".strval($y)] == array(7,1))
						// {
							// $distance--;
							// continue;
						// } elseif (self::$matrix[strval($colume).",".strval($_y)] == array(7,1))
						// {
							// if ($_y==0)
							// {
								// array_push(self::$resetArr, strval($colume).",".strval($y));
								// break;
							// }
							// $distance++;
							// continue;
						// } else self::$matrix[strval($colume).",".strval($y)] = self::$matrix[strval($colume).",".strval($_y)];
					// } else array_push(self::$resetArr, strval($colume).",".strval($y));
				// }			
			// }
			// foreach (self::$resetArr as $key)
			// {
				// self::$matrix[$key] = array(rand(1,6));
			// }
			$ret['stauts'] = 1;
			$ret['clearData'] = array(array(),array(),array());
			// self::$matrix['0,0'] = array(2,0);
			// self::$matrix['0,1'] = array(2,0);
			// self::$matrix['0,2'] = array(2,0);
			// $ret['clearData'][0]['matrix'] = array();
			// $ret['clearData'][0]['clearStones'] = $checkMatch[0];
			// unset(self::$matrix['0,0'], self::$matrix['0,1'], self::$matrix['0,2']);
			// $ret['clearData'][0]['matrix'] = self::$matrix;
			// $ret['clearData'][0]['clearStones'] = array('0,1','0.2','0,3');
			// self::$matrix['1,0'] = array(3,0);
			// self::$matrix['2,0'] = array(3,0);
			// self::$matrix['3,0'] = array(3,0);
			// $ret['clearData'][1]['matrix'] = self::$matrix;
			// $ret['clearData'][1]['clearStones'] = array();
			// $info['va_info']['matrix'] = self::$matrix;
			// $ret['final_stone_score'] = array();
		}
		--$info['playtimes'];
		if ($info['playtimes']==0)
		{
			$info['movetimes'] = 0;
		} else $info['movetimes'] = 15;
		$info['element_score'] += $score;
		
		// $info['va_info']['matrix'] = self::$matrix;
		ElementSysDao::update($uid, $info);
		
		$ret['playtimes'] = $info['playtimes'];
		$ret['movetimes'] = $info['movetimes'];
		$ret['element_score'] = $info['element_score'];
		
		
		// logger::warning($ret);
		return $ret;
	}
	
	public static function refresh($uid)
	{
		$info = self::getGameInfo($uid);
		$needGold = 0;
		if ($info['refreshtimes'] <= 0)
		{
			++$info['goldrefreshtimes'];
			$needGold = 5 * $info['goldrefreshtimes'];
		} else --$info['refreshtimes'];
		$user = EnUser::getInstance();
		if ($user->subGold($needGold)==FALSE)
		{
			return 'err';
		}		
		$info['va_info']['matrix'] = self::creatematrix();
		$user->update();
		ElementSysDao::update($uid, $info);
		return $info;
	}
}
