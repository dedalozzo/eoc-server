<?php

//! @file AbstractCmd.php
//! @brief This file contains the AbstractCmd class.
//! @details
//! @author Filippo F. Fadda


namespace Commands;


//! @brief This class defines the interface for all the concrete Server commands.
//! @details To create a new command you must inherit from this class. This is the only extension point for commands.
//! In case of CouchDB design documents' structure changes, you just need to create a new command, starting from here.
//! @nosubgrouping
abstract class AbstractCmd {
  protected $server;
  protected $args;


  function __construct(\Server $server, $args) {
    $this->server = $server;
    $this->args = $args;
  }


  //! @brief Returns the command's name.
  //! @return string
  abstract static public function getName();


  //! @brief Executes the command.
  //! @return string
  abstract public function execute();
}

?>