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
DEFAULT_DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
PERSONAL_DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name_personal
```

After you got the DB settings up and running
```bash
bin/console doctrine:database:create
bin/console doctrine:database:create --connection=personal
```

When you have changes in the entity you can do
```bash
bin/console doctrine:schema:update --force
bin/console doctrine:schema:update --em=personal --force
```

## Running the web server in development
```bash
bin/console server:run
```

## Database migrations
In the previous API version we used phinx for that. The problem was that phinx 
was not aware of the different environments(default, personal, genetic) and we 
needed to pass a lot of parameters to the command. Now, we have a better way.

### First, apply your changes
Go to `src/Entity` and then go to the sub-folders which represent the various 
DBs. The entity `job_process` is located under `Main` while the `user` entity is
located under the `Personal` folder. Go to the one of the entity class and add
the new field or change the name of the field.

### Second, generate the migration
Now, we need generate the new command which apply our change. You can do 
something like this:
```bash
# No need for default be let's keep the environment so we would know which 
# environment will be affected.
bin/console doctrine:migrations:diff --em=default
```

or:
```bash
bin/console doctrine:migrations:diff --em=personal
```

We will get:

```bash
Generated new migration class to "/Applications/MAMP/htdocs/tahini/src/Migrations/Version20180716084436.php" from schema differences.
```

Cool!

### Third and last, running the migration
Very easy:
```bash
bin/console doctrine:migrations:migrate --em=personal
```

The result will amaze you as well:
```bash
WARNING! You are about to execute a database migration that could result in schema changes and data loss. Are you sure you wish to continue? (y/n)y
Migrating up to 20180716084436 from 0

  ++ migrating 20180716084436

     -> ALTER TABLE user ADD name VARCHAR(255) NOT NULL

  ++ migrated (0.22s)

  ------------------------

  ++ finished in 0.22s
  ++ 1 migrations executed
  ++ 1 sql queries
```

### Bonus, rollback
Yap, something went wrong and we need to rollback. One way is the create another 
migration thus keep our track of the changes(no need to be a shame of mistakes).
Any way, you can rollback by

```bash
bin/console doctrine:migrations:execute 20180716084436 --down --em=personal
```

And the results are:
```bash
WARNING! You are about to execute a database migration that could result in schema changes and data lost. Are you sure you wish to continue? (y/n)y

  -- reverting 20180716084436

     -> ALTER TABLE user DROP name

  -- reverted (0.11s)
```

## Tests
Of course we are using tests. In order to execute the tests just use
```bash
php bin/phpunit
```
