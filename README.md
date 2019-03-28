RadioLista-v3
===

RadioLista to serwis internetowy umożliwiający użytkownikom publikację własnych bandscanów oraz wykazów radiowych i telewizyjnych. Serwis funkcjonuje pod adresem https://radiolista.pl. Więcej informacji: https://tomaszgasior.pl/pomysly/radiolista, https://radiolista.pl/o-stronie.

Zadaniem projektu RadioLista-v3 jest przeportowanie obecnej wersji serwisu do frameworka Symfony. Więcej informacji: https://github.com/TomaszGasior/RadioLista-v3/wiki.


Instalacja lokalna
---

Do lokalnej instalacji wymagane są: PHP w wersji 7.3, composer oraz git. Aby uruchomić aplikację RadioLista-v3, należy sklonować repozytorium, przygotować bazę danych SQLite oraz uruchomić serwer WWW, wykonując w CLI następujące polecenia:

    git clone https://github.com/TomaszGasior/RadioLista-v3.git
    cd RadioLista-v3
    composer install
    bin/console doctrine:database:create
    bin/console doctrine:schema:create
    bin/console doctrine:fixtures:load -n
    bin/console server:start

Aplikacja uruchomi się pod adresem podanym na ekranie — domyślnie `http://127.0.0.1:8000/`. Testowe dane logowania to login `radiolista` i hasło `radiolista`.

Testy
---

Aby uruchomić testy automatyczne, należy wykonać w CLI:

    bin/console doctrine:database:create --env test
    bin/console doctrine:schema:create --env test
    bin/console doctrine:fixtures:load -n --env test
    bin/phpunit
