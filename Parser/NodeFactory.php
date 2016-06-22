<?php

require_once 'Node/Expression.php';
require_once 'Node/ExtendNode.php';
require_once 'Node/IfNode.php';
require_once 'Node/Text.php';
require_once 'Node/Block.php';
require_once 'Node/Variable.php';
require_once 'Node/Operator.php';
require_once 'Node/String.php';
require_once 'Node/Number.php';
require_once 'Node/Raw.php';
require_once 'Node/IncludeNode.php';
require_once 'Node/LoopNode.php';

class NodeFactory
{
	public static function create( $type, $value = NULL )
	{
		switch( $type )
		{
			case Token::T_TEXT:

				return new Text( $value );
				break;

			case Token::T_STRING:

				return new String( $value );
				break;

			case Token::T_NUMBER:

				return new Number( $value );
				break;

			case Token::T_IDENT:

				if( $value === 'extends' )
				{
					return new ExtendNode();
				}

				if( $value === 'block' )
				{
					return new Block();
				}

				if( $value === 'if' )
				{
					return new IfNode();
				}

				if( $value === 'raw' || $value === 'r' )
				{
					return new Raw();
				}

				if( $value === 'include' )
				{
					return new IncludeNode();
				}

				if( $value === 'loop' )
				{
					return new LoopNode();
				}

				break;

			case Token::T_VAR:
				return new Variable( $value );
				break;

			case Token::T_OP:

				return new Operator( $value );
				break;
		}

		throw new Exception( 'Couldn\'t create node for type ' . $type . ' and value ' . $value );
	}
}