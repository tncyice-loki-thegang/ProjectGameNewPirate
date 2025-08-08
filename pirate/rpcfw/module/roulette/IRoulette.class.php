<?php

interface IRoulette
{
	public function getInitInfo();
	public function start();
	public function batch($type, $num);
	public function recieveExp($type);
}
