ElephantOnCouch Server
======================
ElephantOnCouch Server is a CouchDB's Query Server implementation made in PHP programming language.
CouchDB delegates computation of views, shows, filters, etc. to external query servers. It communicates with them over
standard input/output, using a very simple, line-based protocol. CouchDB launches the query server and starts sending
commands.
The server responds according to its evaluation of the commands.
The default query server is written in JavaScript. You can use other languages by setting a MIME type in the language
property of a design document or the Content-Type header of a temporary view. Design documents that do not specify a
language property are assumed to be of type JavaScript, as are ad-hoc queries that are POSTed to temporary view without
a Content-Type header.
Using EOCSvr you can finally write your views, updates, filters, shows directly in PHP. No more JavaScript, just pure PHP.


Composer Installation
---------------------

To install ElephantOnCouch Server, you first need to install [Composer](http://getcomposer.org/), a Package Manager for
PHP, following those few [steps](http://getcomposer.org/doc/00-intro.md#installation-nix):

``` sh
curl -s https://getcomposer.org/installer | php
```

You can run this command to easily access composer from anywhere on your system:

``` sh
sudo mv composer.phar /usr/local/bin/composer
```

ElephantOnCouch Server Installation
-----------------------------------
Once you have installed Composer, it's easy install ElephantOnCouch Server.

1. Move into the directory where is located `main.js` file:
``` sh
cd /opt/local/share/couchdb/server
```
If you are using MacPorts on Mac OS X, you can find it on `/opt/local/share/couchdb/server`, instead if you installed
CouchDB from source you'll probably find it `/usr/share/couchdb/server/`. Please refer to the CouchDB installation
[instructions](http://wiki.apache.org/couchdb/Installation).

2. Create a project for EOCSvr:
``` sh
sudo composer create-project 3f/eoc-server
```

CouchDB Configuration
---------------------
You are finally ready to configure CouchDB to use ElephantOnCouch Server. At this point you just need to edit `local.ini`
configuration file:

``` sh
vim /opt/local/etc/couchdb/local.ini
```

Then, under the `[query_servers]` section, add the following line:
``` sh
[query_servers]
php=/opt/local/share/couchdb/server/eocsvr/bin/eocsvr.php
```


Usage
-----
To benefit of ElephantOnCouch Server you must use [ElephantOnCouch Client](https://github.com/dedalozzo/eoc-client), a PHP client for CouchDB.
Using ElephantOnCouch Client, you can interact with CouchDB, and you can write your views directly in PHP.
You don't need to know about CouchDB internals, neither JSON, just learn ElephantOnCouch Client and use it.
All you need is to learn the MapReduce concept and an high level guide on CouchDB.


Requirements
------------
PHP 5.4.7 or above.


Authors
-------
Filippo F. Fadda - <filippo.fadda@programmazione.it> - <http://www.linkedin.com/in/filippofadda>


License
-------
ElephantOnCouch Server is licensed under the Apache License, Version 2.0 - see the LICENSE file for details.