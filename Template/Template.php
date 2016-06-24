<?php

require_once 'Lexer/Lexer.php';
require_once 'Parser/Parser.php';
require_once 'Compiler/Compiler.php';

class Template
{
	const TPL_DIR = 'templates/';
	const CACHE_DIR = 'cache/templates/';

	private $cacheFilename;

	private $variables = [];
	private $blocks = [];

	public function render( $filename )
	{
		$this->cacheFilename = static::CACHE_DIR . urlencode( $filename ) . '.php';

		// Get string to render
		$string = file_get_contents( static::TPL_DIR . $filename );

		// Tokenize the string
		$lexer = new Lexer();
		$tokenStream = $lexer->tokenize( $string );

		// Parse the token stream to a node tree
		$parser = new Parser();
		$parsedTree = $parser->parse( $tokenStream );

		// Create the compiler
		$compiler = new Compiler( $parsedTree );

		// Compile and save
		file_put_contents( $this->cacheFilename, $compiler->compile() );

		// Execute
		$this->execute();
	}

	private function execute()
	{
		require $this->cacheFilename;
	}
	
	// Runtime

	public function __get( $k )
	{
		return $this->variables[ $k ];
	}

	public function __set( $k, $v )
	{
		$this->variables[ $k ] = $v;
	}

	public function setBlock( $id, $callable )
	{
		$this->blocks[ $id ] = $callable;
	}

	public function getBlock( $id )
	{
		$this->blocks[ $id ]();
	}
	
	public function extend( $filename )
	{
		$this->render( $filename );
	}
}