RadioLista-v3
===

RadioLista to serwis internetowy umożliwiający użytkownikom publikację własnych bandscanów oraz wykazów radiowych i telewizyjnych. Serwis funkcjonuje pod adresem https://radiolista.pl. Więcej informacji: https://radiolista.pl/o-stronie, https://tomaszgasior.pl/pomysly/radiolista.

Instalacja lokalna — kontenery Docker/Podman
---

Do instalacji lokalnej w formie kontenerów wymagany jest program `git` oraz usługa kontenerów Docker lub Podman. Aby uruchomić aplikację, należy sklonować repozytorium i uruchomić kontenery Dockera, wykonując w CLI następujące polecenia:

    git clone https://github.com/TomaszGasior/RadioLista-v3.git
    cd RadioLista-v3
    sudo docker-compose up

W przypadku programu Podman można użyć polecenia `podman-compose up`. Alternatywnie można też uruchomić w CLI:

    git clone https://github.com/TomaszGasior/RadioLista-v3.git
    cd RadioLista-v3
    ./podman-setup
    podman pod start radiolista-v3

Po wybudowaniu kontenerów pierwsze uruchomienie aplikacji może **potrwać ponad minutę** — automatycznie zostaną pobrane zależności poprzez program `composer`, a baza danych MySQL zostanie wypełniona danymi przykładowymi.

Aplikacja uruchomi się pod adresem `http://127.0.0.1:2012`. Testowe dane logowania to login `radiolista` i hasło `radiolista`. Baza danych MySQL będzie dostępna pod adresem `127.0.0.1:2013`, a jej konfiguracja będzie przechowywana w folderze `var/mysql`.

Instalacja lokalna — wbudowany serwer PHP
---

Do lokalnej instalacji wymagane są: PHP w wersji 7.3 (wraz z rozszerzeniami `intl` i `pdo_sqlite`), `composer` oraz `git`. Aby uruchomić aplikację, należy sklonować repozytorium, przygotować bazę danych SQLite oraz uruchomić serwer WWW, wykonując w CLI następujące polecenia:

    git clone https://github.com/TomaszGasior/RadioLista-v3.git
    cd RadioLista-v3
    composer install
    bin/console doctrine:database:create
    bin/console doctrine:schema:create
    bin/console doctrine:fixtures:load -n
    php -S 127.0.0.1:2012 -t ./public

Aplikacja uruchomi się pod adresem podanym na ekranie — domyślnie `http://127.0.0.1:2012`. Testowe dane logowania to login `radiolista` i hasło `radiolista`.

### Te funkcje mogą nie działać

* Wyszukiwarka wykazów — ta funkcja wymaga bazy MySQL/MariaDB zamiast domyślnej SQLite.
* Eksport wykazu do pliku PDF — funkcja ta wymaga instalacji i konfiguracji programu `wkhtmltopdf`.

Testy
---

Aby uruchomić testy automatyczne, należy wykonać w CLI:

    bin/console doctrine:database:create --env test
    bin/console doctrine:schema:create --env test
    bin/console doctrine:fixtures:load -n --env test
    bin/phpunit
