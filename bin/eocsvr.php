#! /usr/bin/php
<?php

//! @file eocsvr.php
//! @author Filippo F. Fadda

error_reporting (E_ALL & ~(E_NOTICE | E_STRICT));

// Initializes the Composer autoloading system.
require_once __DIR__ . "/../vendor/autoload.php";

// Creates and starts the server instance.
$server = new Server();
$server->run();