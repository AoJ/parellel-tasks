<?php

namespace AoJ\ParallelTasks\Reporter;

use AoJ\ParallelTasks\Task;

class SimpleTaskReporter extends ATaskReporter
{
	use TFormatProgressTable;

	public function taskDone(Task $task, array $allTasks)
	{
		if($result = $task->getResult()) {
			$state = $task->isSuccess() ? "SUCCESS" : "FAIL";
			echo "\n\n" . str_repeat("-", 60) . "\n$state\t" . $task->getCmd() . "\n" . str_repeat("-", 60). "\n";
			echo $task->getResult();
		}
	}

	public function allDone(array $allTasks)
	{
		echo "\n\n" . $this->getProgress($allTasks);
	}
}