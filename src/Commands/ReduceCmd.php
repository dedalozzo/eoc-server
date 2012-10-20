<?php

//! @file ReduceCmd.php
//! @brief This file contains the ReduceCmd class.
//! @details
//! @author Filippo F. Fadda


namespace Commands;


class ReduceCmd extends AbstractCmd {
  const REDUCE = "reduce";


  static public function getName() {
    return self::REDUCE;
  }


  public function execute() {
    // TODO: Implement execute() method.
  }

}
