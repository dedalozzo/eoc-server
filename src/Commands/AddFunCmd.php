<?php

//! @file AddFunCmd.php
//! @brief This file contains the AddFunCmd class.
//! @details
//! @author Filippo F. Fadda


namespace Commands;


use Lint\Lint;


//! @brief TODO
//! @details When creating a view, the view server gets sent the view function for evaluation. The view server should
//! parse/compile/evaluate the function he receives to make it callable later. If this fails, the view server returns
//! an error. CouchDB might store several functions before sending in any actual documents.
class AddFunCmd extends AbstractCmd {
  const ADD_FUN = "add_fun";


  static public function getName() {
    return self::ADD_FUN;
  }


  public function execute() {
    $this->server->logMsg("AddFunCmd.execute()");
    Lint::checkSourceCode($this->args);
    $this->server->addFunc($this->args);
    $this->server->writeln("true");
  }

}
