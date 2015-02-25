#! /usr/bin/php
<?php

/*
 * @file eocsvr.php
 * @brief Configure CouchDB `[query_servers]` section to run this script for php language as follows:
 * `php=/opt/local/share/couchdb/server/eocsvr/bin/eocsvr.php`. This will run ElephantOnCouch Query Server for
 * every document that uses PHP as language.
 * @author Filippo F. Fadda
 */


use EoC\Server;


error_reporting(E_ALL & ~(E_NOTICE | E_STRICT));

// MapReduce requires a lot of memory, so we remove the default memory limit.
ini_set('memory_limit', -1);

// Initializes the Composer autoloading system.
require_once __DIR__ . "/../vendor/autoload.php";

// Creates and starts the server instance.
$server = new Server();
$server->run();