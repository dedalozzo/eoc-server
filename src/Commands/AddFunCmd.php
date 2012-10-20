<?php
//! @file AddFunCmd.php
//! @brief This file contains the AddFunCmd class.
//! @details
//! @author Filippo F. Fadda


class AddFunCmd extends AbstractCmd {
  const ADD_FUN = "add_fun";


  static public function getName() {
    return self::ADD_FUN;
  }


  public function execute() {
    $this->server->addFunc(reset($this->args));
    $this->writeln("true");

    //$this->server->logError("eval_failed", "The function you provided is not a closure");
  }
}
