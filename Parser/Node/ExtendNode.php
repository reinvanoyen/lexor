<?php

require_once 'Node.php';

class ExtendNode extends Node
{
	public function compile()
	{
		return 'extends';
	}
}
