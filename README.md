# Neonroot

This is a version off fikaba modified for the french imageboard https://boards.neonroot.net/ .

The only supported language is french.

# Fikaba
Fikaba is an imageboard engine forked from Futallaby aiming to be more standards-compatible, readable, usable, and generally an updated version of Futallaby, since Futallaby has become abandonware.

[See here for info on migrating from Futallaby](https://github.com/knarka/fikaba/blob/master/docs/migrate.md)

## Features
* Valid HTML5
* Fairly advanced admin/moderator/janitor panel
* [JSON API](https://github.com/mrbn100ful/fikaba/blob/master/docs/api.md)
* Tripcodes, capcodes
* Highly configurable
* Post references
* Oekaki
* [...and more](https://github.com/mrbn100ful/fikaba/blob/master/docs/features.md)

## Installation
This guide assumes you have a webserver with PHP (7.0 at least) already installed. Fikaba has only been tested on Apache, but should work on any server.

1. Clone the files from this repository into a folder on your webserver and set the permissions of that folder to 777
2. Edit/move config.example.php to config.php and edit it
3. If you want to use oekaki, execute `git submodule update --init --recursive`.
4. Navigate to imgboard.php in your browser
5. Log in to the default admin account (password REPLACEME) and create a new account. The default one will be automatically removed
6. Pour yourself some puerh and admire your professional adminship

## Updating
[See here](https://github.com/mrbn100ful/fikaba/blob/master/docs/update.md)

## License
See the file LICENSE.
