<?php

/**
 * @file ResetCmd.php
 * @brief This file contains the ResetCmd class.
 * @details
 * @author Filippo F. Fadda
 */


namespace EoC\Command;


/**
 * @brief Resets the internal state of the server and makes it forget all previous input.
 * @details The argument provided by CouchDB has the following structure:
 @code
   Array
   (
       [0] => Array
       (
           [reduce_limit] => 1
           [timeout] => 5000
       )
   )
 @endcode
 */
final class ResetCmd extends AbstractCmd {
  use CmdTrait;


  public static function getName() {
    return "reset";
  }


  public function execute() {
    $args = reset($this->args);

    $this->server->setReduceLimit($args['reduce_limit']); // Not used.
    $this->server->setTimeout($args['timeout']); // Not used.

    $this->server->resetFuncs();

    $this->server->writeln("true");
  }
}