<?php

interface IGemMatrix
{
	public function getInfo();
	public function getScore();
	public function explode($type, $pos);
	public function levelUp();
}
