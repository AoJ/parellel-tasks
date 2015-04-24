# PHP parallel tasks

## Install
It's simple! Just use composer commands

	composer require 'aoj/parallel-tasks:~1.0'

## Usage

	<?php

	use AoJ\ParallelTasks\Task;
	use AoJ\ParallelTasks\TaskRunner;

	$tasks = [
		new Task("sleep " . rand(1, 5)),
		new Task("sleep " . rand(1, 5)),
		new Task("sleep " . rand(1, 5)),
		new Task("sleep " . rand(1, 5)),
	];

	$taskRunner = new TaskRunner($tasks, 2);
	$taskRunner->run();

	foreach($tasks as $task) {
		var_dump($task->getResult());
	}

or try

	php example.php


## Methods

### TaskRunner(Task[], $parallel = 3)
- **`run()`** start tasks
- **`setParallel($newParallelCount)`** set number of simultaneously process
- `<integer>` **`getParallel()`**


### Task($cmd, $args = array(), $cwp = __DIR__)
- `run()` start process
- `<bool>` **`isRunning()`** check if process finished. You must run it before `getResult()`
- `<string>` **`getResult()`** return stdout of process. Returns data continuously when it appears at stdout
- `<integer|null>` **`getExitCode()`** returns exit code of process. Returns `null` if the process is still running or not yet started
- `<integer>` **`getDuration()`** Returns duration time in milliseconds, returns `0` if process not yet started
- `<integer>` **`getPid()`** Returns process pid. Returns `0` if process not yet started
- `<string>` **`getCmd()`** Returns command with arguments and without cwd


## TODO
- configurable output
- configurable stderr outputing
- add events (done, queued, started)
- add tests