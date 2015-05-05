<?php

namespace AoJ\ParallelTasks\Reporter;

use AoJ\ParallelTasks\Task;

/**
 *
 *	for task in tasks
 *		taskStart()
 *		<< exec >>
 *		taskFail() || taskSuccess()
 *		taskDone()
 *	allDone()
 *
 */
abstract class ATaskReporter
{

	/**
	 * start it!
	 * @param  Task[]  $allTasks
	 */
	public function start(array $allTasks) {}


	/**
	 * call after task done, success or fail
	 * @param  Task   $task
	 * @param  Task[]  $allTasks
	 */
	public function taskDone(Task $task, array $allTasks) {}


	/**
	 * call after task fail (exit code > 0)
	 * @param  Task   $task
	 * @param  Task[]  $allTasks
	 */
	public function taskFail(Task $task, array $allTasks) {}


	/**
	 * call after task successfully done (exit code == 0)
	 * @param  Task   $task
	 * @param  Task[]  $allTasks
	 */
	public function taskSuccess(Task $task, array $allTasks) {}


	/**
	 * call before task exec
	 * @param  Task   $task
	 * @param  Task[]  $allTasks
	 */
	public function taskStart(Task $task, array $allTasks) {}


	/**
	 * call after all tasks done
	 * @param  Task   $task
	 * @param  Task[]  $allTasks
	 */
	public function allDone(array $allTasks) {}
}