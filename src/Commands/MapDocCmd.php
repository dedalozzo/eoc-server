<?php

//! @file MapDocCmd.php
//! @brief This file contains the MapDocCmd class.
//! @details
//! @author Filippo F. Fadda



namespace Commands;


//! @brief Maps a document against every single map function stored into the server.
//! @details When the view function is stored in the server, CouchDB starts sending in all the documents in the
//! database, one at a time. The server calls the previously stored functions one after another with the document
//! and stores its result. When all functions have been called, the result is returned as a JSON string.
class MapDocCmd extends AbstractCmd {
  const MAP_DOC = "map_doc";


  static public function getName() {
    return self::MAP_DOC;
  }


  public function execute() {
    $doc = \Server::arrayToObject($this->arg);

    // We use a closure here, so we can just expose the emit() function to the closure provided by the user. He will not
    // be able to call sum() or any other helper function, because they are all available as closures. We have also another
    // advantage here: the $map variable is defined inside execute(), so we don't need to declare it as class member.
    $emit = function($key, $value = NULL) use (&$map) {
      $this->server->log("Key: $key");
      $this->server->log("Value: $key");
      $map[] = array($key, $value);
    };

    $closure = NULL; // This initialization is made just to prevent a lint error during development.

    $result = []; // Every time we map a document against all the registered functions we must reset the result.

    $this->server->log("====================================================");
    $this->server->log("MAP DOC: $doc->title");
    $this->server->log("====================================================");

    foreach ($this->server->getFuncs() as $fn) {
      $map = []; // Every time we map a document against a function we must reset the map.

      $this->server->log("Closure: $fn");

      // Here we call the closure function stored in the view. The $closure variable contains the function implementation
      // provided by the user. You can have multiple views in a design document and for every single view you can have
      // only one map function.
      // The closure must be declared like:
      //
      //     function($doc) use ($emit) { ... };
      //
      // This technique let you use the syntax '$emit($key, $value);' to emit your record. The function doesn't return
      // any value. You don't need to include any files since the closure's code is executed inside this method.
      eval("\$closure = ".$fn);

      if (is_callable($closure)) {
        call_user_func($closure, $doc);
        $result[] = $map;
        $this->server->log("Map: ".json_encode($map));
        $this->server->log("Partial Result: ".json_encode($result));
      }
      else
        throw new \Exception("The function you provided is not callable.");

      $this->server->log("----------------------------------------------------");
    }

    $this->server->log("Final Result: ".json_encode($result));

    // Sends mappings to CouchDB.
    $this->server->writeln(json_encode($result));
  }

}
