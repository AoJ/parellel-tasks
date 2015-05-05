<?php

namespace AoJ\ParallelTasks;

class Task
{
	const STDIN = 0;
	const STDOUT = 1;
	const STDERR = 2;

	const RUNNING = true;
	const FINISHED = false;

	const INHERIT_ENV = null;

	const SUCCESS_CLOSE = -1;

	const AS_FLOAT = true;

	protected $cwd;

	protected $cmd;

	protected $args;

	protected $pipes;

	protected $resource;

	protected $status = [];

	protected $result = "";

	protected $exitCode = null;

	protected $startTime = null;

	protected $finishTime = null;


	/**
	 * @param string $cmd command
	 * @param string|array $args arguments
	 * @param string|null $cwd current working directory
	 */
	public function __construct($cmd, $args = array(), Interpret\Interpret $interpret = null)
	{
		$this->cmd = (string) $cmd;
		$this->args = is_array($args) ? $args : explode(" ", $args);
		$this->interpret = $interpret ?: new Interpret\Interpret;
	}


	/**
	 * @return Task
	 */
	public function run() {
		$this->isRunning();
		return $this;
	}


	/**
	 * @return [bool] return false if result finished or true if running
	 */
	public function isRunning()
	{
		if(!$this->startTime) $this->startTime = $this->start();
		if($this->finishTime) return self::FINISHED;
		if(!is_resource($this->pipes[self::STDOUT])) return self::FINISHED;
		if(!is_resource($this->pipes[self::STDERR])) return self::FINISHED;


		#read buffered stdout from process
		#process can continuously streaming results
		$this->result .= stream_get_contents($this->pipes[self::STDOUT]);
		$this->result .= stream_get_contents($this->pipes[self::STDERR]);
		$this->status = proc_get_status($this->resource);
		if($this->status["running"]) {
			return self::RUNNING;
		}

		#close and clean resources
		fclose($this->pipes[self::STDOUT]);
		fclose($this->pipes[self::STDERR]);
		$processCode = $this->status["exitcode"];
		$closeCode = proc_close($this->resource);
		$this->finishTime = microtime(self::AS_FLOAT);
		$this->exitCode = $closeCode === self::SUCCESS_CLOSE ? $processCode : $closeCode;
		return self::FINISHED;
	}


	/**
	 * Return partially result if process still running - isRunning() returning true
	 * @return string
	 */
	public function getResult()
	{
		return $this->result;
	}


	/**
	 * return process Exit Code
	 * @return integer|null Return null if process don't finished yet
	 */
	public function getExitCode()
	{
		return $this->exitCode;
	}


	/**
	 * @return boolean|null returns null if task doesn't finish yet
	 */
	public function isSuccess()
	{
		if(!$this->isDone()) return null;
		return $this->exitCode === 0;
	}


	/**
	 * @return boolean|null returns null if task doesn't finish yet
	 */
	public function isFail()
	{
		if(!$this->isDone()) return null;
		return !$this->isSuccess();
	}


	/**
	 * @return boolean
	 */
	public function isDone()
	{
		return (bool) $this->finishTime;
	}


	/**
	 * @return float miliseconds
	 */
	public function getDuration()
	{
		if(!$this->startTime) return 0;
		return (integer) ((($this->finishTime ?: microtime(self::AS_FLOAT)) - $this->startTime) * 1000);
	}


	/**
	 * @return integer
	 */
	public function getPid()
	{
		return isset($this->status["pid"]) ? $this->status["pid"] : 0;
	}


	/**
	 * @return string
	 */
	public function getCmd()
	{
		return isset($this->status["command"]) ? $this->status["command"] : $this->interpret->formatCommand($this->cmd, $this->args);
	}



	protected function start()
	{
		$this->resource = proc_open(
			$this->interpret->formatCommand($this->cmd, $this->args),
			[["pipe", "r"], ["pipe", "w"], ["pipe", "w"]],
			$pipes,
			$this->interpret->getCwd(),
			self::INHERIT_ENV,
			['bypass_shell' => TRUE] //skip windows cmd.exe
		);

		fclose($pipes[self::STDIN]);
		$this->pipes = $pipes;
		stream_set_blocking($pipes[self::STDOUT], 0);
		stream_set_blocking($pipes[self::STDERR], 0);
		$this->status = proc_get_status($this->resource);

		return microtime(self::AS_FLOAT);
	}

}
