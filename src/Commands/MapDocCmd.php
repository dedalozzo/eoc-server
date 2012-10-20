<?php
//! @file MapDocCmd.php
//! @brief This file contains the MapDocCmd class.
//! @details
//! @author Filippo F. Fadda


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

  }
}
