<?php

//! @file RereduceCmd.php
//! @brief This file contains the RereduceCmd class.
//! @details
//! @author Filippo F. Fadda


namespace Commands;


//! @brief The map command (MapCmd) generates a set of key/value pairs, which can then optionally be reduced to single
//! value or to a group of values by the reduce command (ReduceCmd). The rereduce command (RereduceCmd) try to call your
//! reduce function recursively on its own input.
//! @details The reduce step primarily involves working with keys and values, not document IDs. Either a single computed
//! reduction of all values will be produced, or reductions of values grouped by keys, will ultimately be produced.
//! Grouping is controlled by parameters passed to your view, not by the reduce function itself.<br /><br />
//! The argument provided by CouchDB has the following structure:
//! @code
//! Array
//! (
//!     [0] => Array
//!     (
//!         [0] => function($keys, $values, $rereduce) {
//!                  if ($rereduce)
//!                    return array_sum($values);
//!                  else
//!                    return sizeof($values);
//!                };
//!     )
//!     [1] => Array
//!     (
//!         [0] => 48360
//!         [1] => 48311
//!         [2] => 48324
//!     )
//! )
//! @endcode
class RereduceCmd extends AbstractCmd {
  const REREDUCE = "rereduce";


  public final static function getName() {
    return self::REREDUCE;
  }


  public final function execute() {
    // Extracts functions and values from the arguments array.
    @list($funcs, $values) = $this->args;

    $this->server->reduce($funcs, NULL, $values, TRUE);
  }

}