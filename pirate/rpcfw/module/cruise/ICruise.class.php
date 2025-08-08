<?php

interface ICruise
{
	public function cruiseInfo();
	
	public function throwDice();
	
	public function chooseNode($mapId);
	
	public function reCruise($num);
	
	// public function arriveNode();
	
	public function answer($node, $answer);
}
