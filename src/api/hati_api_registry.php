<?php

/************************************************************************************
 *                        !!! Hati API Registry !!!
 * Register API endpoints here using the {@link HatiAPIHandler::register()} method.
 ************************************************************************************/
	
use A2\Eval\api\eval\EvalPHP;
use hati\api\HatiAPIHandler;

HatiAPIHandler::register([
	'method' => ['POST', 'GET', 'DELETE'],
	'path' =>  'eval/script',
	'handler' => EvalPHP::class,
	'extension' => ['create', 'fetch', 'rename', 'execute'],
	'description' => 'An API for EvalPHP webapp!'
]);