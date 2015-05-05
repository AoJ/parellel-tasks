<?php

namespace AoJ\ParallelTasks\Reporter;

use AoJ\ParallelTasks\Task;

trait TFormatProgressTable
{

	public function getProgress(array $allTasks)
	{
		$sizes = ["pid" => 10, "cmd" => 50, "state" => 10, "time" => 12];
		$rowWidth = array_sum(array_values($sizes)) /* + count($sizes) - 1 */;
		$rows = [];
		$rows[] = function() use ($rowWidth) { return str_repeat("=", $rowWidth); };
		$rows[] = array_combine(array_keys($sizes), array_map("strtoupper", array_keys($sizes)));
		$rows[] = function() use ($rowWidth) { return str_repeat("=", $rowWidth); };

		foreach($allTasks as $task) {
			$time = $task->isDone() ? $task->getDuration() . " ms" : "";
			if($task->isFail()) $state = "FAIL";
			elseif($task->isDone()) $state = "done";
			elseif($task->getDuration() > 0) $state = $task->getDuration() . " ms";
			else $state = "queued";

			$rows[] = [
				"pid" => $task->getPid(),
				"cmd" => $task->getCmd(),
				"state" => $state,
				"time" => $time,
			];
		}

		$status = "";
		foreach($rows as $key => $row) {
			if(is_callable($row)) {
				$status .= $row() . "\n";
				continue;
			}
			foreach($sizes as $column => $size) {
				$status .= str_pad(isset($row[$column]) ? substr((string) $row[$column], 0, $size - 1) : "", $size, " ");
			}
			$status .= "\n";
		}

		return $status;
	}
}