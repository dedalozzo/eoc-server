<?php

//! @file ResetCmd.php
//! @brief This file contains the ResetCmd class.
//! @details
//! @author Filippo F. Fadda


namespace Commands;


//! @brief Resets the internal state of the server and makes it forget all previous input.
//! @details The argument provided by CouchDB has the following structure:
//! @code
//! Array
//! (
//!     [0] => Array
//!     (
//!         [reduce_limit] => 1
//!         [timeout] => 5000
//!     )
//! )
//! @endcode
class ResetCmd extends AbstractCmd {
  const RESET = "reset";


  public final static function getName() {
    return self::RESET;
  }


  public final function execute() {
    $pippo = reset($this->args);

    $this->server->setReduceLimit = $pippo['reduce_limit'];
    $this->server->setTimeout = $pippo['timeout'];

    $this->server->resetFuncs();

    $this->server->writeln("true");
  }
}
