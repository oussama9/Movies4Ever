# Movies4Ever

Developed by **[Oussama Guezguez](https://github.com/oussama9)** as a test for an internship

## Run the project

Run `composer update` to install the needed bundles.<br/>
Configure your database URL in `.env` file.<br/>
Create your database by running `php bin/console doctrine:database:create`<br/>
Create the migration by running `php bin/console make:migration`<br/>
Execute your migration by running `php bin/console doctrine:migrations:migrate`<br/>
Run `php -S 127.0.0.1:8000 -t public` for a dev server. Navigate to `http://localhost:8000/`.
Import `Tripartie.postman_collection.json` in postman in order to explore the Apis.