<?php

/**
 * @file AbstractCmd.php
 * @brief This file contains the AbstractCmd class.
 * @details
 * @author Filippo F. Fadda
 */


//! This namespace contains all the available concrete commands.
namespace ElephantOnCouch\Command;


use ElephantOnCouch\Server;


/*
 * @brief This class defines the ancestor for all the concrete Server commands.
 * @details To create a new command you must inherit from this class. This is the only extension point for commands.
 * In case of CouchDB design documents' structure changes, you just need to create a new command, starting from here.
 * @nosubgrouping
 */
abstract class AbstractCmd implements CmdInterface {

  protected $server;
  protected $args;


  /**
   * @brief Creates an instance of a concrete command.
   * @param[in] Server $server An instance of Server class.
   * @param[in] array $args An array of arguments.
   */
  public function __construct(Server $server, $args) {
    $this->server = $server;
    $this->args = $args;
  }

}