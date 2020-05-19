# 4feuilles.org

This is a version off fikaba modified for the french imageboard https://4feuilles.org .

4feuilles is a fork of Fikaba witch is an imageboard engine forked from Futallaby aiming to be more standards-compatible, readable, usable, and generally an updated version of Futallaby, since Futallaby has become abandonware.

## Features
* Valid HTML5
* Fairly advanced admin/moderator/janitor panel
* [JSON API](https://github.com/mrbn100ful/fikaba/blob/master/docs/api.md)
* Tripcodes, capcodes
* Highly configurable
* Post references
* [...and more](https://github.com/mrbn100ful/fikaba/blob/master/docs/features.md)

## Installation
This guide assumes you have a webserver with PHP (7.0 at least) already installed. 4feuilles has only been tested on Apache, but should work on any server.

1. Clone the files from this repository into a folder on your webserver and set the permissions of that folder to 777
2. Edit/move config.example.php to config.php and edit it
4. Navigate to index.php in your browser
5. Log in to the default admin account (password REPLACEME) and create a new account. The default one will be automatically removed
6. Pour yourself some puerh and admire your professional adminship

## Migrating
If you come from Fikaba simple drog and drop the new file and remove old one.
[See here for info on migrating from Futallaby](https://github.com/mrbn100ful/4feuilles/blob/master/docs/migrate.md)

## License
See the file LICENSE.


