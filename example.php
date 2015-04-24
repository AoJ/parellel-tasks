<?php
error_reporting(E_ALL);
require("vendor/autoload.php");

use AoJ\ParallelTasks\Task;
use AoJ\ParallelTasks\TaskRunner;

$tasks = [
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
	new Task("sleep " . rand(1, 5)),
];

$taskRunner = new TaskRunner($tasks, 3);
$taskRunner->run();