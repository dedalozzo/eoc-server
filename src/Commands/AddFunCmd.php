<?php

//! @file AddFunCmd.php
//! @brief This file contains the AddFunCmd class.
//! @details
//! @author Filippo F. Fadda


namespace Commands;


use Lint\Lint;


//! @brief Evaluates the function received from CouchDB, cheks for syntax errors and finally stores the function
//! implementation, so CouchDB can call it later.
//! @details When creating a view, the view server gets sent the view function for evaluation. The view server should
//! parse/compile/evaluate the function he receives to make it callable later. If this fails, the view server returns
//! an error. CouchDB might store several functions before sending in any actual documents.<br />
//! The argument provided by CouchDB has the following structure:
//! Array
//! (
//!     [0] => function($doc) use ($emit) {
//!              if ($doc->contributorName == "Filippo Fadda")
//!                $emit($doc->contributorName, $doc->idItem);
//!            };
//! )

class AddFunCmd extends AbstractCmd {
  const ADD_FUN = "add_fun";


  public final static function getName() {
    return self::ADD_FUN;
  }


  public final function execute() {
    $fn = reset($this->args);
    Lint::checkSourceCode($fn);
    $this->server->addFunc($fn);
    $this->server->writeln("true");
  }

}
