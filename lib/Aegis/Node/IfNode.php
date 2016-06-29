<?php

namespace Aegis\Node;

use Aegis\Token;

class IfNode extends Node
{
	public static function parse( $parser )
	{
		if( $parser->accept( Token::T_IDENT, 'if' ) ) {

			$parser->traverseUp();
			$parser->parseAttribute();
			$parser->skip( Token::T_CLOSING_TAG );

			$parser->parseOutsideTag();

			$parser->skip( Token::T_OPENING_TAG );
			$parser->skip( Token::T_IDENT, '/if' );
			$parser->skip( Token::T_CLOSING_TAG );

			$parser->traverseDown();
			$parser->parseOutsideTag();
		}
	}

	public function compile( $compiler )
	{
		$compiler->write('<?php if( ' );

		foreach( $this->getAttributes() as $a )
		{
			$a->compile( $compiler );
		}

		$compiler->write( ' ): ?>');
		
		foreach( $this->getChildren() as $c )
		{
			$c->compile( $compiler );
		}

		$compiler->write( '<?php endif; ?>' );
	}
}