# Liliana's Trade

Site web codé par Juliette Debono

> Thème : Les Cartes Magic

## Utilisateurs

3 utilisateurs :
* Ayris
  * ayris2001@gmail.com
  * 1234

* Skyfred 
  * skyfred@gmail.com
  * skyfredthebest

* Wolffair
  * juliette.debono2002@gmail.com
  * password

## Déploiement Symfony

Mise en place classique de la VM (git, …)

### Installation de PHP8.1

    sudo apt-get update
    sudo apt-get install ca-certificates apt-transport-https software-properties-common wget curl lsb-release
    curl -sSL https://packages.sury.org/php/README.txt | sudo bash -x
    sudo apt-get update
    sudo apt-get install php8.1
    sudo apt-get install libapache2-mod-php8.1
    sudo systemctl restart apache2
    php -v

### Installations des dépendances

    sudo apt update
    sudo apt full-upgrade
    sudo apt install php8.1-cli
    sudo apt install php8.1-xml php8.1-sqlite3 php8.1-intl php8.1-mbstring
    php -m | grep "xml\|sqlite\|intl"
        intl
        libxml
        pdo_sqlite
        sqlite3
        xml
        xmlreader
        xmlwriter
    curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
    sudo apt install symfony-cli
    sudo apt install sqlite3
    symfony check:requirements
    symfony composer -V

### Fichier config

    nano /etc/apache2/sites-available/lilianastrade.conf

    <VirtualHost *:80>
        ServerName 157.159.195.109
        DocumentRoot /var/www/lilianastrade/public

        <Directory /var/www/lilianastrade/public>
            AllowOverride None
            Require all granted
            Allow from All

            FallbackResource /index.php
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
    </VirtualHost>

### Enfin

Puis on lance sur la VM dans le dossier racine du projet :

    rm -fr composer.lock symfony.lock var/cache/ vendor/
    symfony composer update
    symfony composer install
    sudo chmod 777 /var/www/lilianastrade/var/log/dev.log
    sudo chown www-data:www-data /var/www/lilianastrade/var/log/dev.log
    symfony console doctrine:database:drop --force
    symfony console doctrine:database:create
    symfony console doctrine:schema:create
    symfony console doctrine:fixtures:load -n
    sudo systemctl restart apache2