<?php

//! @file CouchHandler.php
//! @brief This file contains the CouchHandler class.
//! @details
//! @author Filippo F. Fadda


//! @brief This namespace contains the error handler.
namespace ElephantOnCouch\Handler;


use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

use ElephantOnCouch\Server;


//! @brief This special handler writes logging messages directly into the <i>couch.log</i> file.
//! @details It doens't handle debug messages, because this handler is always pushed to the logger. This handler logs
//! info messages and errors.
class CouchHandler extends AbstractProcessingHandler {
  private $server;


  //! @brief Constructor.
  //! @param[in] Server $server The ElephantOnCouch Query Server instance.
  public function __construct(Server $server) {
    $this->server = $server;
    parent::__construct(Logger::INFO, TRUE);
  }


  //! @brief Writes the record down to the log of the implementing handler
  //! @param[in] array $record The log record to be written.
  protected function write(array $record) {

    switch ($record['level']) {
      case Logger::DEBUG: // Ignores it.
        break;

      case Logger::INFO: // Sends a message.
        $this->server->log($record['message']);
        break;

      case Logger::NOTICE: // Sends a message.
        $this->server->log($record['message']);
        break;

      case Logger::WARNING: // Sends a message.
        $this->server->log($record['message']);
        break;

      case Logger::ERROR:
        $this->server->error($record['channel'], $record['message']);
        break;

      case Logger::CRITICAL:
        $this->server->error($record['channel'], $record['message']);
        break;

      case Logger::ALERT:
        $this->server->error($record['channel'], $record['message']);
        break;

      case Logger::EMERGENCY:
        $this->server->error($record['channel'], $record['message']);
        break;
    }

  }

}