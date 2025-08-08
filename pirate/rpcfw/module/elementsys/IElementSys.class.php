<?php

interface IElementSys
{
	public function getGameInfo();
	public function moveStone($oldPos, $newPos);
	public function refresh();
	public function clear($type);	
}
