<?php

namespace AoJ\ParallelTasks\Reporter;

use AoJ\ParallelTasks\Task;

class SeleniumTaskReporter extends SimpleTaskReporter
{
	use TFormatProgressTable;

	protected $splitter = "Time:";

	protected $splitterReplacement = "Time - ";

	protected $resultSize = 15000;


	public function __construct(array $options = array())
	{
		if(isset($options["splitter"])) $this->splitter = (string) $options["splitter"];
		if(isset($options["splitterReplacement"])) $this->splitterReplacement = (string) $options["splitterReplacement"];
		if(isset($options["resultSize"])) $this->resultSize = (integer) $options["resultSize"];
	}


	public function start(array $allTasks)
	{
		echo $this->getProgress($allTasks);
	}


	public function allDone(array $allTasks)
	{
		$tasks = $allTasks;
		$exitCode = 0;
		$totalTime = 0;
		$success = [];
		$fullErrors = [];
		$shortErrors = [];
		$shortLimit = round($this->resultSize / count($tasks));
		foreach($tasks as $task) {
			if($exitCode === 0) $exitCode = $task->getExitCode();
			$totalTime += $task->getDuration();
			$state = $task->getExitCode() > 0 ? "FAIL" : "SUCCESS";
			$header = "\n\n" . str_repeat("-", 60) . "\n$state\t" . $task->getCmd() . "\n" . str_repeat("-", 60). "\n";
			$full = str_replace($this->splitter, $this->splitterReplacement, $task->getResult());


			$timePos = strpos($task->getResult(), $this->splitter);
			# dísplay all after "Time:" or display whole error (system errors, php errors etc.)
			# display only relative part of 1000 chars (limit for email sending by jenkins)
			$nextLinePos = $timePos === false ? 0 : strpos($task->getResult(), "\n", $timePos);
			$short = trim(substr($task->getResult(), $nextLinePos, $shortLimit));

			# vypsat vše, kromě:
			# 	chybné hodit na konec výpisu
			# 	na úplném konci zobrazit tabulku se souhrnem
			# 	před tabulku dát zkrácený výpis chybných pokud plný výpis má více než X znaků nebo obsahuje "Time:"
			if($task->getExitCode() === 0) $success[] = $header . $full;
			elseif (strlen($task->getResult()) < $shortLimit) $shortErrors[] = $header . $full;
			elseif ($timePos !== false) $shortErrors[] = $header . $short;
			else {
				$fullErrors[] = $header . $full;
				$shortErrors[] = $header . $short;
			}
		}

		//echo self::formatSuccess(implode("", $success));
		//echo implode("", $fullErrors);
		echo "\n\n" . $this->splitter . " " . round($totalTime / 1000 / 60, 3) . " minutes";
		echo self::formatError(implode("", $shortErrors));
		echo parent::allDone($allTasks);

		exit($exitCode);
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