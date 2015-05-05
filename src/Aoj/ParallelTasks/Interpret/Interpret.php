<?php

namespace AoJ\ParallelTasks\Interpret;


class Interpret
{
	protected $cwd;


	public function __construct($cwd = __DIR__)
	{
		$this->cwd = (string) $cwd;
	}


	public function getCwd()
	{
		return $this->cwd;
	}


	public function formatCommand($cmd, array $args)
	{
		$argv = implode(" ", $args);
		return "$cmd $argv";
	}
}