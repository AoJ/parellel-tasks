<?php

namespace AoJ\ParallelTasks;

class TaskRunner
{

	const TICK = 10E4;

	protected $index = [];

	protected $queue = [];

	protected $running = [];

	protected $done = [];

	protected $parallel;

	protected $status = "";


	public function __construct(array $tasks, $parallel = 3)
	{
		$this->parallel = max((integer) $parallel, 1);
		$this->queue = $tasks;
		$this->index = $tasks;
	}


	public function run() {
		$this->printStatus();
		$this->running = array_splice($this->queue, 0, $this->parallel);

		while($task = array_shift($this->running)) {
			if($task->isRunning()) {
				$this->running[] = $task;
			} else {
				$this->done[] = $task;
				$next = array_shift($this->queue);
				if($next) $this->running[] = $next->run();
			}
			$this->printStatus();
			usleep(self::TICK);
		}

	}


	/**
	 * @param integer $newParallelCount
	 */
	public function setParallel($newParallelCount)
	{
		$this->parallel = max((integer) $newParallelCount, 1);

	}

	/**
	 * @return integer
	 */
	public function getParallel()
	{
		$this->parallel;
	}


	protected function printStatus()
	{
		$sizes = ["pid" => 10, "cmd" => 50, "state" => 10, "time" => 12];
		$rowWidth = array_sum(array_values($sizes)) /* + count($sizes) - 1 */;
		$rows = [];
		$rows[] = ["pid" => "pid", "cmd" => "cmd", "state" => "state", "time" => "time"];
		$rows[] = function() use ($rowWidth) { return str_repeat("=", $rowWidth); };

		foreach($this->index as $task) {
			$time = in_array($task, $this->done) ? $task->getDuration() . " ms" : "";
			if(in_array($task, $this->running)) $state = $task->getDuration() . " ms";
			elseif(in_array($task, $this->done)) $state = "done";
			elseif(in_array($task, $this->queue)) $state = "queued";
			else continue;

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

		if($this->status) echo sprintf("\e[%dA", count($this->index) + 2);
		if($this->status) echo sprintf("\e[%dD", $rowWidth);
		echo $this->status = $status;
		#exit(0);
	}
}