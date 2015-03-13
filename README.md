# PHP-NodeJS-Executor
Small Simple PHP Class for executing Node Js Files

#### Problem:
I needed to execute a Node JS Script. Well yes sounds funny theses times because everybody has an node js server running, but in my case it would have been too much effort.

#### What does it do?
- Checks if php can execute node and saves the version
- Execute a script and returns the output and the exit Code (https://nodejs.org/api/process.html#process_exit_codes / v0.12.0 Manual & Documentation)

#### Example
```
require 'NodeJSExecutor.php';

$executor = new NodeJSExecutor();
$executor->run("test.js");

echo "Exit Code: " . $executor->exitCode . "<br>";
echo "Result: " . $executor->result . "<br>";
```

#### Exceptions
The class can fire two exceptions if ...
- there is no node js found. The command "node --version" is executed and the result code checked.
- the js file is not found
