<?php

namespace AoJ\ParallelTasks\Reporter;

use AoJ\ParallelTasks\Task;

class BashTaskReporter extends ATaskReporter
{
	use TFormatProgressTable;

	protected $status = "";

	public function start(array $allTasks)
	{
		echo $this->status = $this->getProgress($allTasks);
	}


	public function taskDone(Task $task, array $allTasks)
	{
		$status = $this->getProgress($allTasks);
		$rowWidth = strlen(substr($this->status, 0, strpos($status, "\n")));

		echo sprintf("\e[%dA", count(explode("\n", $this->status)) - 1);	#replace last printed rows
		echo sprintf("\e[%dD", $rowWidth);	#go to start of line
		echo $this->status = $status;
	}


	static function formatSuccess($text)
	{
		if(posix_isatty(STDOUT)) {
			if($text) return "\033[01;32m" . $text . "\033[0m";
		} else return $text;
	}


	static function formatError($text)
	{
		if(posix_isatty(STDOUT)) {
			if($text) return "\033[01;31m" . $text . "\033[0m";
		} else return $text;
	}
}