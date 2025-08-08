<?php

class GemMatrixLogic
{
	private static $arrField = array('uid', 'score', 'elite', 'count', 'level', 'todaybomb', 'optime', 'va_info');
	
	public static function getInfo($uid)
	{		
		$ret = GemMatrixDao::get($uid, self::$arrField);
		if (empty($ret))
		{		
			self::insertDefault($uid);
			$ret = GemMatrixDao::get($uid, self::$arrField);
		}
		if (!Util::isSameDay($ret['optime'], 14400))
		{
			$ret['count'] = 10;
			$ret['optime'] = Util::getTime();			
			GemMatrixDao::update($uid, $ret);			
		}
		return $ret;
	}
	
	private static function insertDefault($uid)
	{
		GemMatrixDao::insert($uid, array(
			'count' => 10,
			'level' => 1,
			'optime' => Util::getTime(),
			'va_info' => array('lucky'=>array(), 'matrix'=>self::creatematrix(1)),			
		));
	}
	
	private static function creatematrix($id)
	{
		for ($y=0; $y<8; $y++)
		{
			for ($x=0; $x<8; $x++)
			{
				$bomb = rand(1,1000);
				if ($bomb>900)
				{
					$array[strval($x).",".strval($y)] = 22;
				} else
				{
					switch ($id)
					{
						case 1 :
							$array[strval($x).",".strval($y)] = rand(1,21);
							break;
						case 2 :
							$array[strval($x).",".strval($y)] = rand(101,121);
							break;
					}
				}
			}
		}
		return $array;
	}
	
	public static function levelUp($uid)
	{
		$info = self::getInfo($uid);
		$user = EnUser::getUserObj();
		$retPoint = self::getDisapperScore($info['va_info']['matrix']);
		$info['score'] += $retPoint[0];
		$user->addGemExp($retPoint[1]);
		$info['elite'] += $retPoint[2];
		$info['level'] = 2;
		$user->update();
		GemMatrixDao::update($uid, $info);
		$ret = array(
					'id' => $info['level'],					
					'score' => $info['score'], 
					'gemexp' => $user->getGemExp(), 
					'elite' => $info['elite']
					);
		$ret['newmatrix'] = self::creatematrix(2);
		return $ret;
	}
	
	
	private static function getDisapperScore($selectedMatrix)
	{
		$score = $gemExp = $elite = 0;
		$info = btstore_get()->GEM_JIFEN;
		foreach ($selectedMatrix as $pos => $idGem)
		{
			
			// if(in_array($info[$idGem][1]), $luckyArr)
			// {
				// $luckilyRate = $info[$idGem][1] / 10000;
			// } else $luckilyRate = 1;
			// $score += floor($info[$idGem][0] * $luckilyRate);
			$score += $info[$idGem][0];
			$gemExp += $info[$idGem][0];
			if ($idGem == 121)
			{
				$elite += 10;
			}			
		}
		return array($score, $gemExp, $elite);
	}
	
	private static $matrix = array();
	private static $initArr = array();
	// private static $gemArr = array();
	private static $bombArr = array();
	private static $resetArr = array();
		
	private static function getDissapperGems($type, $pos)
	{
		$space = ceil($type-1) / 2;
		$tmp = explode(",", $pos);

		$_x = $tmp[0] - $space;
		$_y = $tmp[1] - $space;

		$row = 0;		
		while ($row < $type)
		{
			$colume = 0;
			while ($colume < $type)
			{				
				$x = $colume + $_x;
				$y = $row + $_y;
				if (!($x<0 || $y<0 || $x>=8 || $y>=8))
				{
					if (!in_array(strval($x).",".strval($y), self::$initArr))
					{
						if (self::$matrix[strval($x).",".strval($y)] == 22)
						{
							if (!in_array(strval($x).",".strval($y), self::$bombArr))
							{
								array_push(self::$bombArr, strval($x).",".strval($y));
							}
						}// else array_push(self::$gemArr, $x.",".$y);
					}
					array_push(self::$initArr, strval($x).",".strval($y));
				}
				$colume++;
			}
			$row++;
		}		
	}
	
	public static function explode($uid, $type, $pos)
	{		
		$needGold = 0;
		$needBelly = 0;
		switch ($type)
		{
			case 3:
				$needBelly = 10000;
				break;
			case 5:
				$needGold = 30;
				break;
			case 7:
				$needGold = 75;
				break;
			case 8:
				$needGold = 100;
				break;
		}
		$user = EnUser::getUserObj();
		if ($user->subGold($needGold)==FALSE || $user->subBelly($needBelly)==FALSE)
		{
			return 'err';
		}
		$info = self::getInfo($uid);
		self::$matrix = $info['va_info']['matrix'];
		if ($type == 8)
		{			
			$retPoint = self::getDisapperScore($info['va_info']['matrix']);
			self::$matrix = self::creatematrix($info['level']);
		}else
		{
			self::getDissapperGems($type, $pos);
			while (count(self::$bombArr) > 0)
			{
				$tmp = self::$bombArr;
				self::$bombArr = array();
				foreach ($tmp as $key => $val)
				{				
					unset($tmp[$val]);
					self::getDissapperGems(3, $val);
				}
			}
			self::$initArr = array_unique(self::$initArr);		
			// logger::warning(self::$initArr);
			// logger::warning(self::$gemArr);
			$minX = 7; $maxX = 0;		
			foreach (self::$initArr as $val)
			{
				$tmp = explode (",", $val);
				$minX = $tmp[0] <= $minX ? $tmp[0] : $minX;
				$maxX = $tmp[0] <= $maxX ? $maxX : $tmp[0];			
			}
			// logger::warning('minX=%d, maxX=%d', $minX, $maxX);
			for ($colume=$minX; $colume<=$maxX; $colume++)
			{
				$moveArr =array();
				foreach (self::$initArr as $val)
				{
					$tmp = explode (",", $val);
					if ($tmp[0] == $colume)
					{
						array_push($moveArr, strval($colume).",".strval($tmp[1]));
					}				
				}
				// logger::warning('moveArr = %s', $moveArr);
				$minY = 7; $maxY = 0;
				foreach ($moveArr as $val)
				{
					$tmp = explode (",", $val);
					$minY = $tmp[1] <= $minY ? $tmp[1] : $minY;
					$maxY = $tmp[1] <= $maxY ? $maxY : $tmp[1];
					// logger::warning('tmp=%d, maxY=%d', $tmp[1], $maxY);
				}
				
				$distance = $maxY - $minY + 1;
				for ($y=$maxY; $y>=0; $y--)
				{
					$_y = $y - $distance; 
					if ($_y>=0)
					{
						self::$matrix[strval($colume).",".strval($y)] = self::$matrix[strval($colume).",".strval($_y)];
						// logger::warning('%s <= %s', $colume.",".$y, $colume.",".$_y);						
					} else array_push(self::$resetArr, strval($colume).",".strval($y));
				}
				// logger::warning('resetArr = %s', self::$resetArr);
			}
			
			foreach (self::$resetArr as $key)
			{
				$bomb = rand(1,1000);
				if ($bomb>900)
				{
					if ($info['todaybomb']<10)
					{
						self::$matrix[$key] = 22;
						$info['todaybomb']++;
					}else
					{
						switch ($info['level'])
						{
							case 1:
								self::$matrix[$key] = rand(1,21);
								break;
							case 2:
								self::$matrix[$key] = rand(101,121);
								break;
						}
					}
				} else
				{
					switch ($info['level'])
					{
						case 1:
							self::$matrix[$key] = rand(1,21);
							break;
						case 2:
							self::$matrix[$key] = rand(101,121);
							break;
					}
				}
			}
			foreach (self::$initArr as $val)
			{
				$selected[$val] = $info['va_info']['matrix'][$val];
			}
			$retPoint = self::getDisapperScore($selected);			
		}
		// logger::warning($retPoint);
		--$info['count'];
		$info['score'] += $retPoint[0];
		$user->addGemExp($retPoint[1]);
		$info['elite'] += $retPoint[2];		
		$info['va_info']['matrix'] = self::$matrix;
		$user->update();
		GemMatrixDao::update($uid, $info);

		$ret = array(
			'score' => $info['score'],
			'gemexp' => $user->getGemExp(),
			'elite' => $info['elite'],
			'newmatrix' => self::$matrix
			);
		return $ret;
	}
	
	public static function updateScoreElite($uid ,$score, $elite)
	{
		$set=array();
		if (!($score===NULL))
		{
			$score= ($score< 0)?0:$score;
			$set['score']=$score;
		}
		if (!($elite===NULL))
		{
			$elite= ($elite< 0)?0:$elite;
			$set['elite']=$elite;
		}
		if (empty($set))
		{
			Logger::warning('GemMatrixLogic.updateScoreElite empty set');
			return false;
			throw new Exception('fake');
		}
		logger::warning($set);
		GemMatrixDao::update($uid, $set);
	}
}