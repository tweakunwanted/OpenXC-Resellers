# OpenXC Resellers UI (No installation script yet)

## What is this?

A web interface that connects with the database of XtreamCodes 2.9 for resellers to work

#### Features:

- Add Lines
- Edit lines
- Renew lines
- Add more connections
- Generate short links
- Create subresellers
- Open tickets

## How to install

Use Ubuntu 18.04 with PHP 7.2 and apache. Just put the files into `/var/html/www` and put your database settings into `/sys/config.php`.

You have to use the .sql files in /db to create a database for resellers panel and then add it to the `config.php`

## Known Issues

This version still have some bugs, and it's portuguese only as of yet. We hope to improve it with the contributions of everyone!
