# SoigneMoi APIRest

![Static Badge](https://img.shields.io/badge/Symfony-Symfony?logo=symfony)


This application serves as the backend for SoigneMoi project, acting as the intermediary between the database and the frontend apps.

## Prerequisites

Make sure you have the following installed before getting started:

- PHP 8 or higher
- Composer [Get Composer](https://getcomposer.org/doc/00-intro.md)
- Symfony CLI - [Installation Guide](https://symfony.com/download)

## Installation

1. Clone the repository to your local machine:

    ```bash
    git clone https://github.com/Q2jatte/soignemoi-api.git
    ```

2. Navigate to the project directory:

    ```bash
    cd soignemoi-api
    ```

3. Install dependances using Composer :

    ```bash
    composer install
    ```
    
4. Create a environement file .env into the root directory. This project need 4 values :

    ```bash
    ### Data base access ###
    DATABASE_URL="mysql://root:@127.0.0.1:3306/db-soignemoi"

    ###> lexik/jwt-authentication-bundle ###
    JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
    JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
    JWT_PASSPHRASE=passphrase
    
    ###> BUSINESS : Maximum visit per day for a doctor ###
    MAX_STAYS_PER_DAY=5

    ###> nelmio/cors-bundle ###
    CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
    
    ###> Google Recaptcha ###
    GOOGLE_RECAPTCHA_SECRET="aaaabbbbccccddddeeeeffffgggg"
    ```
    Configure your database connection, JWT and other environnement

    How to generate JWT private and public keys ?

    ```bash
    openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
    ```


5. Update schema database

    ```bash
    php bin/console doctrine:schema:update --force
    ```

6. Add admin user and test data

    Before running the fixtures you can modify the admin password  in 'src/Datafixtures/AppFixtures.php'

    ```bash
    php bin/console doctrine:fixtures:load
    ```


5. Run server

    ```bash
    symfony server:start
    ```
    
6. Access the web app by navigating to the URL provided in the command prompt.

    Visit http://localhost:8000 in your browser to see the application.

    Visit http://localhost/admin in your browser to see the admin dashboard.


## Contribution

The project was entirely created by Eric Terrisson as part of the preparation for his Bachelor's degree in Application Designer and Developer.

## License

MIT License.
