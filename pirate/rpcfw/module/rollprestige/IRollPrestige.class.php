<?php

interface IRollPrestige
{
	public function getInitInfo();
	public function start();
	public function batch($type, $num);
	public function recievePrestige($type);
}
