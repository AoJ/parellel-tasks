<?php

namespace AoJ\ParallelTasks\Interpret;


class SingleCommandInterpret extends Interpret
{

	public function formatCommand($cmdRaw, array $args)
	{
		$cmd = escapeshellcmd($cmdRaw);
		$argv = implode(" ", array_map("escapeshellarg", $args));
		return "$cmd $argv";
	}
}