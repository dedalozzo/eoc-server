<?php

/**
 * @file AddFunCmd.php
 * @brief This file contains the AddFunCmd class.
 * @details
 * @author Filippo F. Fadda
 */


namespace EoC\Command;


/**
 * @brief Evaluates the function received from CouchDB, checks for syntax errors and finally stores the function
 * implementation, so CouchDB can call it later.
 * @details When creating a view, the view server gets sent the view function for evaluation. The view server should
 * parse/compile/evaluate the function he receives to make it callable later. If this fails, the view server returns
 * an error. CouchDB might store several functions before sending in any actual documents.\n\n
 * The argument provided by CouchDB has the following structure:
 @code
   Array
   (
       [0] => function($doc) use ($emit) {
                if ($doc->contributorName == "Filippo Fadda")
                  $emit($doc->contributorName, $doc->idItem);
              };
   )
 @endcode
 */
final class AddFunCmd extends AbstractCmd {
  use CmdTrait;


  public static function getName() {
    return "add_fun";
  }


  public function execute() {
    $fn = reset($this->args);
    $this->server->addFunc($fn);
    $this->server->writeln("true");
  }

}