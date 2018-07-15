# Tahini
Welcome to Tahini. A temporary repo for Taliaz API server.

Tahini stands for: **Ta**liaz **H**elath **In**itiative no. **I**.

## Set up.

First install:
```bash
composer install
```

In the `.env` file add the DB settings:

```
DEFAULT_DATABASE_URL=mysql://root:root@localhost:3306/tahini
PERSONAL_DATABASE_URL=mysql://root:root@localhost:3306/tahini_personal
```

After you got the DB settings up and running
```bash
bin/console doctrine:database:create
bin/console doctrine:database:create --connection=personal
```

When you have changes in the entity you can do
```bash
bin/console doctrine:schema:update --force
bin/console doctrine:schema:update --connection=personal --force
```

## Running the web server in development
```bash
bin/console server:run
```

## Tests
Of course we are using tests. In order to execute the tests just use
```bash
php bin/phpunit
```
