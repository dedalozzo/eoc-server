<?php

//! @file ResetCmd.php
//! @brief This file contains the ResetCmd class.
//! @details
//! @author Filippo F. Fadda


namespace Commands;


//! @brief Resets the internal state of the server and makes it forget all previous input.
class ResetCmd extends AbstractCmd {
  const RESET = "reset";


  static public function getName() {
    return self::RESET;
  }


  public function execute() {
    $this->server->log("ResetCmd.execute()");

    $this->server->resetFuncs();

    $this->server->log(json_encode($this->server->getFuncs()));

    $this->server->writeln("true");
  }
}
