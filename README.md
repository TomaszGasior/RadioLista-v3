RadioLista-v3
===

RadioLista is a place for people interested in the media to publish various
radio lists and TV lists — bandscans, typical lists of received stations,
private lists with favorite stations. This application is available online
at https://radiolista.pl. More information (Polish):
https://radiolista.pl/o-stronie, https://tomaszgasior.pl/pomysly/radiolista.

Local development — Docker/Podman containers
---

    git clone https://github.com/TomaszGasior/RadioLista-v3.git
    cd RadioLista-v3
    sudo docker-compose up

If you prefer Podman, you may use `podman-compose` or the following script:

    ./podman-setup
    podman pod start radiolista-v3

After containers building process, the first start of the application can take
**more than one minute**: dependencies installation and database with example
data are handled automatically.

The application will be started at `http://127.0.0.1:2012` with `radiolista`
username and `radiolista` password. MySQL database will be available at
`127.0.0.1:2013` and kept in `var/mysql`.

Local development — built-in PHP server
---

Requirements: PHP 7.4 with `intl`, `pdo_sqlite` extensions and `composer`.

    git clone https://github.com/TomaszGasior/RadioLista-v3.git
    cd RadioLista-v3
    composer install
    bin/console doctrine:database:create
    bin/console doctrine:schema:create
    bin/console doctrine:fixtures:load -n
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
