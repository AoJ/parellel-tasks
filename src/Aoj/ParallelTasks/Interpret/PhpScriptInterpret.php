<?php

namespace AoJ\ParallelTasks\Interpret;


class PhpScriptInterpret extends SingleCommandInterpret
{
	protected $scriptPath;

	public function __construct($cwd, $scriptPath)
	{
		parent::__construct($cwd);
		$this->scriptPath = (string) $scriptPath;
	}

	public function formatCommand($cmdRaw, array $args)
	{
		array_unshift($args, $cmdRaw);
		array_unshift($args, $this->scriptPath);
		return parent::formatCommand("php", $args);
	}
}