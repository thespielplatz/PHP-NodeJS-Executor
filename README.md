# PHP-NodeJS-Executor
_by [#thespielplatz](https://t.me/thespielplatz)_

[![MIT License Badge](license-badge.svg)](LICENSE)

A small, simple PHP class for executing Node.js files.

## Problem
In certain scenarios, you may need to execute a Node.js script from a PHP environment. While it's common to have a Node.js server running, setting one up might require significant effort. This class offers a straightforward solution.

## What It Does
- Verifies if PHP can execute Node.js and stores the version.
- Executes a script, returning the output and the exit code. (Refer to [Node.js Process Exit Codes](https://nodejs.org/api/process.html#process_exit_codes) for details.)

## Example Usage
```
require 'NodeJSExecutor.php';

$executor = new NodeJSExecutor();
$executor->run("test.js");

echo "Exit Code: " . $executor->exitCode . "<br>";
echo "Result: " . $executor->result . "<br>";
```

## Exceptions
The class can trigger two exceptions:

- Node.js is not found (checks the result of `node --version`).
- The specified JavaScript file is not found.

# Tip me

If you appreciate this project, feel free to use it, refactor it, enjoy it, or even expand on it. Why not [send some tip love?](https://getalby.com/p/thespielplatz)
