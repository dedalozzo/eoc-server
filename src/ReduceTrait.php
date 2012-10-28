<?php

//! @file ReduceTrait.php
//! @brief This file contains the ReduceTrait trait.
//! @details
//! @author Filippo F. Fadda


//! @brief This trait provide the <i>reduce</i> method used by ReduceCmd and RereduceCmd classes.
trait ReduceTrait {

  public function reduce($funcs, $keys, $values, $rereduce) {
    $this->server->logMsg();
    $this->server->logMsg("-------------------------------------------------------------------------");
    $bool = ($rereduce) ? 'true' : 'false';
    $this->server->logMsg("RED REREDUCE: ".$bool);
    //$this->server->logMsg("RED JSON: ".json_encode($this->args));
    $this->server->logMsg("RED KEYS: ".json_encode($keys));
    $this->server->logMsg("RED VALUES: ".json_encode($values));

    $closure = NULL; // This initialization is made just to prevent a lint error during development.

    $reductions = [];

    // Executes the reductions.
    foreach ($funcs as $fn) {
      eval("\$closure = ".$fn);

      $this->server->logMsg("RED CLOSURE: $fn");

      if (is_callable($closure)) {
        $reductions[] = call_user_func($closure, $keys, $values, $rereduce);

        $this->server->logMsg("RED PARTIAL RESULT: ".json_encode($reductions));
      }
      else
        throw new \Exception("The function you provided is not callable.");

    }

    // Sends mappings to CouchDB.
    $this->server->writeln("[true,".json_encode($reductions)."]");

    $this->server->logMsg("RED FINAL RESULT: [true,".json_encode($reductions)."]");
  }

}
