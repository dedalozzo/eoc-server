EOCSvr - A complete CouchDB Query Server written in PHP
=======================================================
EOCSvr stands for ElephantOnCouch Server. EOCSvr is a CouchDB's Query Server implementation made in PHP programming language.

CouchDB delegates computation of views, shows, filters, etc. to external query servers. It communicates with them over
standard input/output, using a very simple, line-based protocol. The default query server is written in JavaScript.
You can use other languages by setting a MIME type in the language property of a design document or the Content-Type
header of a temporary view. Design documents that do not specify a language property are assumed to be of type JavaScript,
as are ad-hoc queries that are POSTed to _temp_view without a Content-Type header.
CouchDB launches the query server and starts sending commands. The server responds according to its evaluation
of the commands.


Installation of Composer
------------------------

To install EOCSvr, you first need to install [Composer](http://getcomposer.org/), a Package Manager for PHP, following those few [steps](http://getcomposer.org/doc/00-intro.md#installation-nix):

``` sh
curl -s https://getcomposer.org/installer | php
```

You can run this command to easily access composer from anywhere on your system:

``` sh
sudo mv composer.phar /usr/local/bin/composer
```

Installation of EOCSvr
----------------------
Once you have installed Composer, it's easy install OECSvr.

1. Move into the directory where is located `main.js` file:
``` sh
cd /opt/local/share/couchdb/server
```
If you are using MacPorts on Mac OS X, you can find it on `/opt/local/share/couchdb/server`, instead if you installed CouchDB from source you'll probably find it `/usr/share/couchdb/server/`. Please refer to the CouchDB installation [instructions](http://wiki.apache.org/couchdb/Installation).

2. Create a project for EOCSvr:
``` sh
sudo composer create-project 3f/eocsvr
```

Configuration of CouchDB
----------------------------------
You are finally ready to configure CouchDB to use EOCSvr. At this point you just need to edit `local.ini` configuration file.

`vim /opt/local/etc/couchdb/local.ini`

``` sh
[query_servers]
php=/opt/local/share/couchdb/server/eocsvr/eocsvr.php
```


Requirements
------------
PHP 5.4.7 or above.


Authors
-------
Filippo F. Fadda - <filippo.fadda@programmazione.it> - <http://www.linkedin.com/in/filippofaddan>


License
-------
EOCSvr is licensed under the Apache License, Version 2.0 - see the LICENSE file for details.
