<?php
class NodeJSExecutor {
	public $result = "";
	public $exitCode = 0;
	public $cmd = "";
	public $nodeVersion = "";
	public $timeout = 30;
	
	public function __construct() {
		if (! $this->isNodeInstalled ()) {
			throw new Exception ( 'NodeJSExecutor // Node not found! (Shell Command: "node --version")' );
		}
	}
	
	public function isNodeInstalled() {
		$exitCode = 1;
	
		$descriptorspec = array(
				0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
				1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
				2 => array('pipe', 'a') // stderr // stderr is a file to write to
		);
		
		$process = proc_open ( "node --version", $descriptorspec, $pipes, NULL, NULL );
		
		if (is_resource ( $process )) {
				// $pipes now looks like this:
				// 0 => writeable handle connected to child stdin
				// 1 => readable handle connected to child stdout
				// Any error output will be appended to /tmp/error-output.txt
			$this->nodeVersion = stream_get_contents ($pipes[1]);
			fclose ($pipes[1]);
			
			// It is important that you close any pipes before calling
			// proc_close in order to avoid a deadlock
			$exitCode = proc_close ($process);
		}
		
		return $exitCode == 0;
	}
	
	public function run($file, $parameters = "") {
		$this->exitCode = 0;
		$this->result ="";
		
		if (!file_exists($file) || !is_file($file)) {
			throw new Exception('NodeJSExecutor // File: ' . $file . ' not found!');
		}
		
		$this->cmd = 'node ' . $file . ' ' . $parameters;
		
		$descriptorspec = array(
				0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
				1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
				2 => array('pipe', 'a') // stderr // stderr is a file to write to
		);
		
		$process = proc_open ( $this->cmd, $descriptorspec, $pipes, NULL, NULL );
		
		if (is_resource ( $process )) {
			// $pipes now looks like this:
			// 0 => writeable handle connected to child stdin
			// 1 => readable handle connected to child stdout
			// Any error output will be appended to /tmp/error-output.txt
			
			stream_set_timeout($pipes[1], 30);

			while (($content = stream_get_contents($pipes[1])) != false) {
				$this->result = $this->result . $content;
				echo "Content: " . $content . "<br>";
			}
			
			//print_r(stream_get_meta_data($pipes[1]));
			
			fclose($pipes[1]);
			
			// It is important that you close any pipes before calling
			// proc_close in order to avoid a deadlock
			$this->exitCode = proc_close($process);
		}
		
		return $this->exitCode;
	}
	
	public function getNodeExitCode() {
		return NodeJSExecutor::getNodeReturnMessage($this->exitCode);
	}
	
	// https://nodejs.org/api/process.html#process_exit_codes / Node.js v0.12.0 Manual & Documentation
	public static function getNodeReturnMessage($code) {
		if ($code == 0) {
			return array("code" => $code, "title" => "ok", "message" => "");
		}		
		
        switch ($code) {
        	case 1: return array("code" => $code, "title" => "Uncaught Fatal Exception", "message" => "There was an uncaught exception, and it was not handled by a domain or an uncaughtException event handler.");
        	case 2: return array("code" => $code, "title" => "Unused", "message" => "(reserved by Bash for builtin misuse)");
        	case 3: return array("code" => $code, "title" => "Internal JavaScript Parse Error", "message" => "The JavaScript source code internal in Node's bootstrapping process caused a parse error. This is extremely rare, and generally can only happen during development of Node itself.");
        	case 4: return array("code" => $code, "title" => "Internal JavaScript Evaluation Failure", "message" => "The JavaScript source code internal in Node's bootstrapping process failed to return a function value when evaluated. This is extremely rare, and generally can only happen during development of Node itself.");
        	case 5: return array("code" => $code, "title" => "Fatal Error", "message" => "There was a fatal unrecoverable error in V8. Typically a message will be printed to stderr with the prefix FATAL ERROR.");
        	case 6: return array("code" => $code, "title" => "Non-function Internal Exception Handler", "message" => "There was an uncaught exception, but the internal fatal exception handler function was somehow set to a non-function, and could not be called.");
        	case 7: return array("code" => $code, "title" => "Internal Exception Handler Run-Time Failure", "message" => "There was an uncaught exception, and the internal fatal exception handler function itself threw an error while attempting to handle it. This can happen, for example, if a process.on('uncaughtException') or domain.on('error') handler throws an error.");
        	case 8: return array("code" => $code, "title" => "Unused", "message" => "In previous versions of Node, exit code 8 sometimes indicated an uncaught exception.");
        	case 9: return array("code" => $code, "title" => "Invalid Argument", "message" => "Either an unknown option was specified, or an option requiring a value was provided without a value.");
        	case 10: return array("code" => $code, "title" => "Internal JavaScript Run-Time Failure", "message" => "The JavaScript source code internal in Node's bootstrapping process threw an error when the bootstrapping function was called. This is extremely rare, and generally can only happen during development of Node itself.");
        	case 12: return array("code" => $code, "title" => "Invalid Debug Argument", "message" => "The --debug and/or --debug-brk options were set, but an invalid port number was chosen.");
        }
		
		if ($code > 128) {
			return array("code" => $code, "title" => "Signal Exits", "message" => "If Node receives a fatal signal such as SIGKILL or SIGHUP, then its exit code will be 128 plus the value of the signal code. This is a standard Unix practice, since exit codes are defined to be 7-bit integers, and signal exits set the high-order bit, and then contain the value of the signal code.");
		}
		
		return array("code" => $code, "title" => "", "message" => "... code could no be translated ..."); 
    }
}
?>
