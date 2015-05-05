<?php

if($args = array_slice($_SERVER["argv"], 1)) {
	echo implode(" ", $args);
	exit(0);
}

error_reporting(E_ALL);
require("vendor/autoload.php");

use AoJ\ParallelTasks\Task;
use AoJ\ParallelTasks\TaskRunner;
use AoJ\ParallelTasks\Interpret;
use AoJ\ParallelTasks\Reporter;

$tasks = [
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5) . "; exit 1"),
	new Task("sleep " . rand(1, 5)),
	new Task("echo test; exit 111", [], new Interpret\SingleCommandInterpret),
	new Task("hello", ["world"], new Interpret\PhpScriptInterpret(__DIR__, "./example.php")),
];

$taskRunner = new TaskRunner($tasks, 3/*, new Reporter\SimpleTaskReporter*/);
$taskRunner->run();