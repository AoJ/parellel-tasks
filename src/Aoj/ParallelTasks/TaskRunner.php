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

	protected $taskReporter;


	public function __construct(array $tasks, $parallel = 3, Reporter\ATaskReporter $taskReporter = null)
	{
		$this->parallel = max((integer) $parallel, 1);
		$this->taskReporter = $taskReporter ?: (posix_isatty(STDOUT)
			? new Reporter\BashTaskReporter
			: new Reporter\SimpleTaskReporter
		);
		$this->queue = $tasks;
		$this->index = $tasks;
	}


	public function run() {
		$this->taskReporter->start($this->index);
		$this->running = array_splice($this->queue, 0, $this->parallel);

		while($task = array_shift($this->running)) {
			if($task->isRunning()) {
				$this->running[] = $task;
			} else {
				$this->done[] = $task;
				$next = array_shift($this->queue);
				if($next) $this->running[] = $next->run();
				if($next) $this->taskReporter->taskStart($task, $this->index);
			}
			$this->taskDone($task);
			usleep(self::TICK);
		}
		$this->taskReporter->allDone($this->index);

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

	protected function taskDone(Task $task)
	{
		if($task->isSuccess()) $this->taskReporter->taskSuccess($task, $this->index);
		if($task->isFail()) $this->taskReporter->taskFail($task, $this->index);
		if($task->isDone()) $this->taskReporter->taskDone($task, $this->index);
	}
}