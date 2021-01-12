RadioLista-v3
===

RadioLista is a place for people interested in the media to publish various
radio lists and TV lists — bandscans, typical lists of received stations,
private lists with favorite stations. This application is available online
at https://radiolista.pl. More information (Polish):
https://radiolista.pl/o-stronie, https://tomaszgasior.pl/pomysly/radiolista.

If you would like to contribute, please take a look on unassigned issues
labeled with [*help wanted*](https://github.com/TomaszGasior/RadioLista-v3/issues?q=is%3Aissue+is%3Aopen+label%3A%22help+wanted%22)
or [*good first issue*](https://github.com/TomaszGasior/RadioLista-v3/issues?q=is%3Aissue+is%3Aopen+label%3A%22good+first+issue%22).

Local development — containers
---

    git clone https://github.com/TomaszGasior/RadioLista-v3.git
    cd RadioLista-v3
    sudo docker-compose up

After containers building process, the first start of the application can take
**more than one minute**: dependencies installation and database with example
data are handled automatically.

The application will be started at `http://127.0.0.1:2012` with `radiolista`
username and `radiolista` password. MySQL database will be available at
`127.0.0.1:2013` and kept in `var/mysql`.

Local development — built-in PHP server
---

Requirements: PHP 7.4 with `intl`, `pdo_sqlite` extensions, `composer`,
also Node.js 14 and `npm`.

    git clone https://github.com/TomaszGasior/RadioLista-v3.git
    cd RadioLista-v3
    composer install
    bin/console doctrine:database:create
    bin/console doctrine:schema:create
    bin/console doctrine:fixtures:load -n
    npm install
    npm run watch &
    php -S 127.0.0.1:2012 -t ./public

The application will be started at `http://127.0.0.1:2012` with `radiolista`
username and `radiolista` password.

### These features are not supposed to work

* List searching — this requires MySQL/MariaDB database instead of SQLite.
* Exporting lists to PDF format — this requires `wkhtmltopdf` CLI utility.

Tests
---

    bin/phpunit

Fresh SQLite database for test environment is generated automatically each time
PHPUnit is started.
