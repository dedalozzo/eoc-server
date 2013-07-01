<?php

//! @file ReduceCmd.php
//! @brief This file contains the ReduceCmd class.
//! @details
//! @author Filippo F. Fadda


namespace Command;


use Lint\Lint;


//! @brief The map command (MapDocCmd) generates a set of key/value pairs, which can then optionally be reduced to single
//! value or to a group of values by the reduce command (ReduceCmd). So that's the purpose of this class.
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
//!         [0] => Array
//!         (
//!             [0] => Array
//!             (
//!                 [0] => 48360
//!                 [1] => 48360
//!             )
//!             [1] =>
//!         )
//!         [1] => Array
//!         (
//!             [0] => Array
//!             (
//!                 [0] => 48365
//!                 [1] => 48365
//!             )
//!             [1] =>
//!         )
//!     )
//! )
//! @endcode
class ReduceCmd extends AbstractCmd {
  const REDUCE = "reduce";


  public final static function getName() {
    return self::REDUCE;
  }


  public final function execute() {
    // Extracts functions and pairs (keys, values) from the arguments array.
    @list($funcs, $pairs) = $this->args;

    $keys = [];
    $values = [];

    // Extracts keys and values.
    foreach ($pairs as $pair) {
      $keys[] = $pair[0];
      $values[] = $pair[1];
    }

    // Checks every function for syntax errors. We do the check only here to not repeat in rereduce.
    foreach ($funcs as $fn)
      Lint::checkSourceCode($fn);

    $this->server->reduce($funcs, $keys, $values, FALSE);
  }

}