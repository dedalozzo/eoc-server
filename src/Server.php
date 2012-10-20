<?php

//! @file Server.php
//! @brief This file contains the Server class.
//! @details
//! @author Filippo F. Fadda


//! @brief This class represents the implementation of a Query Server.
//! @details CouchDB delegates computation of views, shows, filters, etc. to external query servers. It communicates
//! with them over standard input/output, using a very simple, line-based protocol. The default query server is written
//! in JavaScript. You can use other languages by setting a MIME type in the language property of a design document or
//! the Content-Type header of a temporary view. Design documents that do not specify a language property are assumed to
//! be of type javascript, as are ad-hoc queries that are POSTed to _temp_view without a Content-Type header.<br />
//! CouchDB launches the query server and starts sending commands. The server responds according to its evaluation
//! of the commands.<br />
//! To use this server just add to <i>local.ini</i> CouchDB configuration file the following line:
//! @code
//! [query_servers]
//! php=/usr/bin/eocsvr.php
//! @endcode
//! @warning This class won't work with CGI because uses standard input (STDIN) and standard output (STDOUT).
//! @see http://wiki.apache.org/couchdb/View_server
class Server {
  const TMP_DIR = "/tmp/";
  const LOG_FILENAME = "viewserver.log";

  const EOCSVR_ERROR = "eocsvr_error";

  const EXIT_SUCCESS = 0;
  const EXIT_FAILURE = 1;

  private static $commands = [];
  private $funcs;

  private $fd;


  public final function __construct() {
    $this->funcs = [];

    $this->fd = fopen(self::TMP_DIR.self::LOG_FILENAME, "w");

    self::scanForCommands();
  }


  public final function __destruct() {
    fflush($this->fd);
    fclose($this->fd);
  }


  //! @brief Scans the commands' directory.
  //! @details CouchDB communicates with a Query Server over standard input/output. Each line represents a command.
  //! Every single command must be interpreted and executed by a specific command handler. This method scans a directory
  //! in search of every available handler.
  private static function scanForCommands() {
    foreach (glob(dirname(__DIR__)."/src/Commands/*.php") as $fileName) {
      //$className = preg_replace('/\.php\z/i', '', $fileName);
      $className = "Commands\\".basename($fileName, ".php"); // Same like the above regular expression.

      if (class_exists($className) && array_key_exists("Commands\\AbstractCmd", class_parents($className)))
        self::$commands[$className::getName()] = $className;
    }
  }


  //! @brief TODO
  public static function arrayToObject($array) {
    return is_array($array) ? (object) array_map(__FUNCTION__, $array) : $array;
  }


  //! @brief TODO
  public final function run() {
    $this->log("run");

    while ($line = trim(fgets(STDIN))) {
      @list($cmd, $args) = json_decode($line);

      $this->log($cmd);

      if (array_key_exists($cmd, self::$commands)) {
        try {
          $className = self::$commands[$cmd];
          $this->log($className);
          $cmdObj = new $className($this, $args);
          $cmdObj->execute();
        }
        catch (Exception $e) {
          $this->logError(self::EOCSVR_ERROR, $e->getMessage());
          exit(Server::EXIT_FAILURE);
        }
      }
      else
        $this->logError(self::EOCSVR_ERROR, "'$cmd' command is not supported.");

      fflush($this->fd);
    }
  }


  //! @brief TODO
  public final function writeln($str) {
    // CouchDB's message terminator is: \n.
    fputs(STDOUT, $str."\n");
    flush();
  }


  //! @brief TODO
  public final function resetFuncs() {
    unset($this->funcs);
    $this->funcs = [];
    $this->writeln("true");
  }


  //! @brief TODO
  public final function getFuncs() {
    return $this->funcs;
  }


  //! @brief TODO
  public final function addFunc($fn) {
    $this->funcs[] = $fn;
  }


  //! @brief TODO
  public final function sum() {
    //$this->log("sto facendo la somma");
  }


  /*public final function count() {
    //$this->log("sto facendo la somma");
  }*/


  /*public final function stats() {
    //$this->log("sto facendo la somma");
  }*/


  //! @brief Tells CouchDB to append the specified message in the couch.log file.
  //! @details Any message will appear in the couch.log file, as follows:
  //!   [Tue, 22 May 2012 15:26:03 GMT] [info] [<0.80.0>] This is a log message
  //! You can't force the message's level. Every message will be marked as [info] even in case of an error, because
  //! CouchDB doesn't let you specify a different level. In case or error use <i>logError</i> instead.
  //! @warning Keep in mind that you can't use this method inside <i>reset</i> or <i>addFun</>, because you are going to
  //! generate an error. CouchDB in fact doesn't expect a message when it sends <i>reset</i> or <i>add_fun</i> commands.
  //! For debugging purpose you can use the <i>log</i> method, to write messages in a log file of your choice.
  //! @param[in] string $msg The message to store into the log file.
  private final function logMsg($msg) {
    $this->writeln(json_encode(array("log", $msg)));
  }


  //! @brief In case of error CouchDB doesn't take any action. We simply notify the error, sending a special message to it.
  public final function logError($error, $reason) {
    $this->writeln(json_encode(array("error" => $error, "reason" => $reason)));
  }


  //! @brief Use this method when you want log something in a log file of your choice.
  public final function log($msg) {
    if (empty($msg))
      fputs($this->fd, "\n");
    else
      fputs($this->fd, date("Y-m-d H:i:s")." - ".$msg."\n");
  }

}

?>