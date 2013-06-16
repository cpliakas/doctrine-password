# Doctrine Password Type

This project provides a password type for [Doctrine](http://www.doctrine-project.org/) that
automatically hashes passwords using the [PHP Password Library](https://github.com/rchouinard/phpass)
and provides a helper method to compare them to raw data submitted by end users. The primary goal
is to make it stupid-simple to store hashed passwords in a database and check if passwords
submitted by end users are valid.

## Installation

This library can be installed with [Composer](http://gtcomposer.org). Define the following
requirement in your project's `composer.json` file:

``` json
{
    "require": {
        "cpliakas/doctrine-password": "*"
    }
}
```

Then follow Composer's [Installation / Usage](https://github.com/composer/composer#installation--usage)
guide to install this library.

## Usage

This library assumes that the developer is familiar with [Doctrine ORM](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html),
otherwise the code snippets below won't make much sense.

First, define your entity. Use the "password" type for the column storing passwords:

``` php
<?php
// src/User.php

/** @Entity **/
class User
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /** @Column(length=255, unique=true, nullable=false) **/
    private $email;

    /** @Column(type="password", nullable=false) **/
    private $password;

    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
    }
    
    public function getPassword()
    {
        $return this->password;
    }
}

```

Then write your code to [obtain the EntityManager](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/getting-started.html#obtaining-the-entitymanager),
and register the password type:

``` php
<?php
// bootstrap.php

use Doctrine\DBAL\Types\Type;

require_once 'vendor/autoload.php';

// .. (code to obtain the entity manager, refer to the Doctrine docs)

Type::addType('password', 'Cpliakas\Password\Doctrine\PasswordType');

```

Next, [configure the command line tool](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/configuration.html#setting-up-the-commandline-tool)
and use it to create your schema:

```
php vendor/bin/doctrine orm:schema-tool:create
```

Now you are ready to add a user to the system. In the example below we will set the
raw password, and the library will automatically hash it when written to the database.

``` php
<?php

// Replace with your own project's bootstrap file.
require_once 'bootstrap.php';

// Replace with your project's mechanism to retrieve the EntityManager.
$em = GetEntityManager();

$user = new User();
$user
    ->setEmail('myuser@example.com')
    ->setPassword('mypassword')
;

$em->persist($user);
$em->flush();

```

The password is now stored as a hash in the database. When retrieving the user from the database,
the password is returned as an object that contains a helper method to compare raw passwords
submitted by end users:

``` php
<?php

// Replace with your own project's bootstrap file.
require_once 'bootstrap.php';

// Replace with your project's mechanism to retrieve the EntityManager.
$em = GetEntityManager();

$repository = $em->getRepository('User');
$user = $repository->findOneBy(array('email' => 'myuser@example.com'));

// Returns true.
$user->getPassword()->match('mypassword');

// Returns false.
$user->getPassword()->match('badpassword');

```
