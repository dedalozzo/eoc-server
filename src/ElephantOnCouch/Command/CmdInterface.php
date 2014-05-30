<?php

/**
 * @file CmdInterface.php
 * @brief This file contains the CmdInterface interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace ElephantOnCouch\Command;


/**
 * @brief All the concrete Server commands must implement this interface.
 */
interface CmdInterface {


  /**
   * @brief Returns the command's name.
   * @return string
   */
  static function getName();


  /**
   * @brief Returns the complete class name, including his namespace.
   * @details The implementation must return simply __CLASS__. The CmdTrait already implements this method, and it is
   * available to all AbstractCmd subclasses.
   * @return string
   */
  static function getClass();


  /**
   * @brief Executes the command.
   * @return string
   */
  function execute();

}