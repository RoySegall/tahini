# Tahini
[![Build Status](https://travis-ci.com/RoySegall/tahini.svg?branch=master)](
https://travis-ci.com/RoySegall/tahini
)

Tahini was a a project I created in a startup I worked for. The startup has some
finance struggles and had to fire me. The CTO was kind enough and gave me the 
option to use the new API I created for them as my own project. 

## Set up.

First install:
```bash
composer install
```

In the `.env` file add the DB settings:

```
DEFAULT_DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```

After you got the DB settings up and running
```bash
bin/console doctrine:database:create
bin/console doctrine:schema:create
```

When you have changes in the entity you can do
```bash
bin/console doctrine:schema:update --force
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
DBs. Go to the one of the entity class and add the new field or change the name 
of the field.

### Second, generate the migration
Now, we need generate the new command which apply our change. You can do 
something like this:
```bash
# No need for default be let's keep the environment so we would know which 
# environment will be affected.
bin/console doctrine:migrations:diff
```

We will get:

```bash
Generated new migration class to "/Applications/MAMP/htdocs/tahini/src/Migrations/Version20180716084436.php" from schema differences.
```

Cool!

### Third and last, running the migration
Very easy:
```bash
bin/console doctrine:migrations:migrate
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

## Plugins

Plugins are small peaces of code which combine together big logic. For example,
if we need to send info from the system we can do this in two ways - sendgrid,
custom SMTP server or sms. If we would have a class for that our class will be 
big and un-easy to maintain. In addition, if we want to add more functionality,
like a push notification, the class will grow in huge sizes.

Instead of that, we can split our logic to small files and with a plugin manager
we can negotiate between the plugins and use the most matching plugin.

### Defining a plugin's annotation.
First, we need to set up an annotation. Annotation is a stylish way to describe 
the plugin. It's based on doctrine annotation mechanism. The annotation will be
place in `src/Plugins/Annotations`. Let's take for example the Authentication 
annotation:
```php
<?php

namespace App\Plugins\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Authentication {

  public $id;

  public $name;

}
```

### Defining a plugin manager
Now, that we have an annotation, let's set a plugin manager that will handle for
us all the managing of the plugins. A plugin manager will be set in the 
`src/Plugins` library. The plugin manager need to define three elements:

* The namespace of the plugin annotation

* The namespace of the plugins

* The negotiation will give the instance of the best matching plugin for the 
task.

Let's have a look on the authentication plugin:

```php
<?php

namespace App\Plugins;
use App\Plugins\Authentication\AuthenticationPluginBase;

/**
 * {@inheritdoc}
 */
class Authentication extends PluginManagerBase {

  /**
   * {@inheritdoc}
   */
  public function getNamespace() : string {
    return 'App\Plugins\Authentication';
  }

  /**
   * {@inheritdoc}
   */
  public function getAnnotationHandler() : string {
    return 'App\Plugins\Annotations\Authentication';
  }

  /**
   * {@inheritdoc}
   */
  public function negotiate() : PluginBase {
    $plugins = array_keys($this->getPlugins());

    foreach ($plugins as $id) {
      /** @var AuthenticationPluginBase $plugin */
      $plugin = $this->getPlugin($id);

      if ($plugin->validateUser()) {
        return $plugin;
      }
    }
  }

}
```

You can see that the method `getNamespace` return the namespace of the where all
the plugins sits.

The method `getAnnotationHandler` returns the reference for the annotation we 
just created in the previous step.

The method `negotiate` get all the plugins and check what's the best plugin we
can use for our task. In this case, the first plugin that returned something
is the best one for the task and we will get an instance of the plugin.

### Writing a plugin
Now, that we set all the basic for the plugins, let's set up a plugin. Since we
define the namespace of the plugins in `App\Plugins\Authentication` our plugin
will be in `src/Plugins/Authentication`. Let's have a look on two plugins to see
how the plugins need to be define.

`AccessToken.php`:

```php
<?php

namespace App\Plugins\Authentication;

use App\Plugins\Annotations\Authentication;

/**
 * @Authentication(
 *   id = "access_token",
 *   name = "Access Token",
 * )
 */
class AccessToken extends AuthenticationPluginBase {

  /**
   * Making sure the user is valid.
   */
  function validateUser() {
    return true;
  }

}
```

`Cookie.php`:
```php
<?php

namespace App\Plugins\Authentication;

use App\Plugins\Annotations\Authentication;

/**
 * @Authentication(
 *   id = "cookie",
 *   name = "Cookie",
 * )
 */
class Cookie extends AuthenticationPluginBase {

  /**
   * Making sure the user is valid.
   */
  function validateUser() {
  }

}
```

So, what we got exactly? Each class got an annotation:
```
// Cookie.php:
/**
 * @Authentication(
 *   id = "cookie",
 *   name = "Cookie",
 * )
 */

// AccessToken.php:
/**
 * @Authentication(
 *   id = "access_token",
 *   name = "Access Token",
 * )
 */
``` 

Every annotation starts with `@Authentication`, the `Authentication` is the name 
of the annotation class. After that we have braces and inside that we have 
properties. We can use only properties we defined in the annotation 
class(remember the first section?).

That's it.

### More methods

We have extra methods from the plugin manager we can use:

* `getPlugins` - Get all the plugins available.

* `getPlugin` - Get a single plugin. Just pass the ID of the plugin.

* `convertNamespaceToPath` - Convert a namespace to a path in the system. Not 
very useful outside the plugin manager but might be useful sometime. 

### One more thing

* Since the plugins and the plugin manager defined inside the src directory, 
they are in fact a service. You can pass them as dependency injection, or get 
them from the container in tests:
    
    ```php
    <?php
    
    namespace App\Tests\Controller;
    
    use App\Plugins\Authentication;
    use App\Tests\TahiniBaseWebTestCase;
    
    class SomeClassForTest extends TahiniBaseWebTestCase {
    
      /**
       * Get the authentication service.
       *
       * @return Authentication
       *  The authentication service.
       */
      public function getAuthenticationService() : Authentication {
        return $this->getContainer()->get('App\Plugins\Authentication');
      }
      
    }
    ``` 

* When using an annotation your IDE might mark their namespace as un-used. Don't
bother.

## Commands

We have a couple of commands which helps us to maintain and develop. The 
commands uses Symfony Console thus ensure some nice tricks and effects.

### Creating
Creating a command is very easy:
```bash
bin/console make:command
```

The command will ask you a couple of questions and will set a skeleton of the 
command.

### Sandndbox command

The sandbox command designed for running peace of code so we could test them and
see the effects of the code.

```bash
bin/console app:sandbox
```

### Creating user

If you need to create a dummy user you can do this by:
```bash
bin/console user:create-user
```

### Creating auth for access token requiring
An access token is generated by the `api/user/login` endpoint, but in order to
acquire the access token you need an auth. The auth string is a decoded base64
string which combine from the date of today, username and password.

You don't to do every time to a site to generate the string or something like
that. Just do:
```php
bin/console user:get-auth
```

### Creating an access token
In a brief text, this command will create an access token in the system for a 
user. In case the token is invalid it will generate a new one.

```bash
bin/console user:generate-access-token
```

After creating an app for 3rd party apps, we should send the refresh token,
access token, and expires to the developers.

### Remove old tokens
Although a token is being check if valid or not when loading it from the system,
the system need to go over the tokens and remove them.

```bash
bin/console user:user:prune-tokens
```

Set this one in a cron task for every day. Like that:
```bash
00 00 * * * PATH/TO/PHP/ bin/console user:user:prune-tokens
```

