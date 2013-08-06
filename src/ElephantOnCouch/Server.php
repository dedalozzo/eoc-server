<?php

//! @file Server.php
//! @brief This file contains the Server class.
//! @details
//! @author Filippo F. Fadda


namespace ElephantOnCouch;


use ElephantOnCouch\Command;


//! @brief This class represents the implementation of a Query Server.
//! @warning This class won't work with CGI because uses standard input (STDIN) and standard output (STDOUT).
//! @see http://wiki.apache.org/couchdb/View_server
final class Server {
  const EOCSVR_ERROR = "eocsvr_error";

  const EXIT_SUCCESS = 0;
  const EXIT_FAILURE = 1;

  private $fd; // Stores the log file descriptor.

  private $commands = []; // Stores the commands' list.

  private $funcs; // Stores the functions' list.

  private $reduceLimit = 1;
  private $timeout = 5000;


  //! @brief Creates a Server instance.
  public function __construct() {
    // Get all available commands.
    $this->loadCommands();

    $this->funcs = [];
  }


  //! @brief Destroy the Server instance previously created.
  public function __destruct() {
    if (is_resource($this->fd)) fclose($this->fd);
  }


  //! @brief Initializes the commands list.
  //! @details CouchDB communicates with a Query Server over standard input/output. Each line represents a command.
  //! Every single command must be interpreted and executed by a specific command handler.
  private function loadCommands() {
    $this->commands[Command\AddFunCmd::getName()] = Command\AddFunCmd::getClass();
    $this->commands[Command\MapDocCmd::getName()] = Command\MapDocCmd::getClass();
    $this->commands[Command\ReduceCmd::getName()] = Command\ReduceCmd::getClass();
    $this->commands[Command\RereduceCmd::getName()] = Command\RereduceCmd::getClass();
    $this->commands[Command\ResetCmd::getName()] = Command\ResetCmd::getClass();
  }


  //! @brief Starts the server.
  public function run() {

    while ($line = trim(fgets(STDIN))) {
      // We decode the JSON string into an array. Returned objects will be converted into associative arrays.
      $args = json_decode($line, TRUE);

      // We know that the first part of the JSON encoded string represent the command.
      // Only the command implementation knows which and how many arguments are provided for the command itself.
      $cmd = array_shift($args);

      //$this->logMsg("Command: $cmd");
      //$this->logMsg("Type: ".gettype($args));
      //$this->logMsg("Arguments: ".json_encode($args));

      if (array_key_exists($cmd, $this->commands)) {
        try {
          $class = $this->commands[$cmd];
          $cmdObj = new $class($this, $args);
          $cmdObj->execute();
        }
        catch (\Exception $e) {
          $this->error(self::EOCSVR_ERROR, $e->getMessage());
          exit(Server::EXIT_FAILURE);
        }
      }
      else
        $this->error(self::EOCSVR_ERROR, "'$cmd' command is not supported.");

    }
  }


  //! @brief Sends a response to CouchDB via standard output.
  //! @param[in] string $str The string to send.
  public function writeln($str) {
    // CouchDB's message terminator is: \n.
    fputs(STDOUT, $str."\n");
    flush();
  }


  //! @brief Resets the array of the functions.
  public function resetFuncs() {
    unset($this->funcs);
    $this->funcs = [];
  }


  //! @brief Returns the array of the functions.
  public function getFuncs() {
    return $this->funcs;
  }


  //! @brief Add the given function to the internal functions' list.
  //! @param[in] string $fn The function implementation.
  public function addFunc($fn) {
    $this->funcs[] = $fn;
  }


  //! @brief The Map step generates a set of key/valu pairs which can then optionally be reduced to a single value - or
  //! to a grouping of values - in the Reduce step.
  //! @details If a view has a reduce function, it is used to produce aggregate results for that view. A reduce function
  //! is passed a set of intermediate values and combines them to a single value. Reduce functions must accept, as input,
  //! results emitted by its corresponding map function as well as results returned by the reduce function itself. The
  //! latter case is referred to as a rereduce.<br />
  //! This function is called by commands ReduceCmd and RereduceCmd.
  //! @param[in] array $funcs An array of reduce functions.
  //! @param[in] array $keys An array of mapped keys and document IDs in the form of [key, id].
  //! @param[in] array $values An array of mapped values.
  public function reduce($funcs, $keys, $values, $rereduce) {
    $closure = NULL; // This initialization is made just to prevent a lint error during development.

    $reductions = [];

    // Executes the reductions.
    foreach ($funcs as $fn) {
      eval("\$closure = ".$fn);

      if (is_callable($closure))
        $reductions[] = call_user_func($closure, $keys, $values, $rereduce);
      else
        throw new \BadFunctionCallException("The reduce function is not callable.");

    }

    // Sends mappings to CouchDB.
    $this->writeln("[true,".json_encode($reductions)."]");
  }


  //! @brief Tells CouchDB to append the specified message in the couch.log file.
  //! @details Any message will appear in the couch.log file, as follows:
  //!   [Tue, 22 May 2012 15:26:03 GMT] [info] [<0.80.0>] This is a log message
  //! You can't force the message's level. Every message will be marked as [info] even in case of an error, because
  //! CouchDB doesn't let you specify a different level. In case or error use error(), forbidden() or unauthorized()
  //! instead.
  //! @warning Keep in mind that you can't use this method inside reset() or addFun(), because you are going to
  //! generate an error. CouchDB in fact doesn't expect a message when it sends <i>reset</i> or <i>add_fun</i> commands.
  //! @param[in] string $msg The message to log.
  public function log($msg) {
    $this->writeln(json_encode(["log", $msg]));
  }


  //! @brief In case of error CouchDB doesn't take any action. We simply notify the error, sending a special message to it.
  //! @param[in] string $error The error keyword.
  //! @param[in] string $reason The error message.
  public function error($keyword, $reason) {
    $this->writeln(json_encode(["error", $keyword, $reason]));
  }


  //! @brief The forbidden error are widely used by validate document update functions to stop further function processing
  //! and prevent on disk store of the new document version.
  //! @details Since this errors actually is not an error, but an assertion against user actions, CouchDB doesn't log it
  //! at “error” level, but returns HTTP 403 Forbidden response with error information object.
  //! @param[in] string $reason The error message.
  public function forbidden($reason) {
    $this->writeln(json_encode(["forbidden" => $reason]));
  }


  //! @brief The unauthorized error mostly acts like forbidden one, but with semantic as please authorize first.
  //! @details CouchDB doesn't log it at “error” level, but returns HTTP 401 Unauthorized response with error information
  //! object.
  //! @param[in] string $reason The error message.
  public function unauthorized($reason) {
    $this->writeln(json_encode(["unauthorized" => $reason]));
  }


  //! @brief Sets the limit of times a reduce function can be called.
  public function setReduceLimit($value) {
    $this->reduceLimit = (integer)$value;
  }


  //! @brief Sets the timeout for the reduce process.
  public function setTimeout($value) {
    $this->timeout = (integer)$value;
  }

}