# Doctrine ORM + ODM quick start

## Introduction

We'll be writing a small application for a very simplified version of HelloFresh's domain.

As we write this application, we'll see how the Data Mapper and Repository patterns allow clean separation between domain and infrastructure. We'll use MySQL to store some entities and MongoDb to store others, and our business logic will not know or care.

We'll learn the basics of [Doctrine](http://www.doctrine-project.org/) along the way.

It's a good idea to refer to the official documentation from time to time:

Doctrine ORM (Relational databases):  
http://doctrine-orm.readthedocs.org/en/latest/tutorials/getting-started.html

Doctrine ODM (MongoDb):  
http://doctrine-mongodb-odm.readthedocs.org/en/latest/tutorials/getting-started.html


## Requirements

### Domain models

- Customers subscribe to Products
- Products have a different Menu each week
- Menus have Recipes
- The same Recipe can be in different menus
- Recipes have Steps

```
Customer
--------
string      name
string      email
Product[]   subscribedProducts

Product
-------
string      name

Menu
----
string      week                ex: 2015-W11
Product     product
Recipe[]    recipes

Recipe
------
string      name
Step[]      steps

Step
----
string      instructions
```


### Objectives

We'll be focusing on read operations for simplicity sake. We want to:

- Given a Customer, I want to list the products he's subscribed to.
- Given a Customer and a week, I want to see which Recipes he's getting.
- Given a week, I want to see all available Recipes.
- I want to get a Recipe by its ID. 


## Implementation

We can choose a variety of datastores to accomplish our goals. We can go fully relational, fully document-based, or mix both approaches. Because this is a tutorial, we're mixing both.

MySQL entities (they clearly have relations):

- Customer
- Product
- Subscription

MongoDB documents (they map well to an embedded document storage):

- Recipe
- Step


### Active Record vs Data Mapper

[Active Record](http://www.martinfowler.com/eaaCatalog/activeRecord.html) is great to get started quickly because it's so easy to reason about. Your Models know how to persist and retrieve themselves, which makes for great syntactic sugar and easy to read code for the most common operations:

```php
$customer = Customer::find(1);
$customer->isActive = true;
$customer->save();
```

Easy peasy! In contrast, [Data Mapper](http://martinfowler.com/eaaCatalog/dataMapper.html) requires another object (the Mapper) to perform these operations:

```php
$customer = $mapper->find('Customer', 1);
$customer->isActive = true;
$mapper->save($customer);
```

And if we go with the Repository pattern, we need to involve yet another object (the Repository):

```php
$customer = $customersRepository->find(1);
$customer->isActive = true;
$mapper->save($customer);
```

Or go with Repository all the way and have it use the mapper:

```php
// code.php
$customer = $customersRepository->find(1);
$customer->isActive = true;
$customersRepository->save($customer);

// CustomersRepository.php
class CustomersRepository implements CustomersRepositoryInterface
{
    /**
     * @{inheritdoc}
     */
    pubic function save(Customer $customer)
    {
        $this->mapper->save($customer);
    }
}
```


**What do we gain from involving more classes?** Put simply, we follow the *Single Responsibility Principle*. `CustomersRepository` is our persistence layer for `Customer`. It cares *only* about persistence. Now `Customer` is free from those considerations, and we're free to change the underlying storage mechanism.

**But can't we use repositories with Active Record too?** Yes, but we're still stuck with models who know about persistence. So changing the underlying data store still requires refactoring the models. Why should would we refactor our models unless our domain itself changes?

**Is that the only advantage of Data Mapper?** No. A big advantage is that our domain entities no longer have to map 1:1 to database tables. We're free from the constraints of the data store we selected. We can have domain entities constructed from partials that live across different tables, databases or even servers. A good Data Mapper and decent repositories let us do that more easily than Active Record.

**Why is that important?** For the same reasons SRP and decoupling are important. Explaining their importance is beyond the scope of this tutorial, but consider this: you may already have data that doesn't come from your own database. Maybe payment data from a 3rd party API. Maybe files stored in S3. All of that is data which belongs in your domain. Representing them as domain entities and delegating their persistence to a Repository assisted by a Data Mapper is a robust, consistent and flexible strategy.

**I'm not convinced.** That's okay. ActiveRecord vs DataMapper is an ongoing debate. The answer depends on your priorities. I leave you with a quote from someone who writes more clearly than me:

http://culttt.com/2014/06/18/whats-difference-active-record-data-mapper/

> If you are building an Minimum viable product application to test the waters of a new market, I think it makes more sense to use the Active Record pattern. At the outset you don’t know what business rules are going to be important and you never will if you obsess over the architecture of your application.
>
> On the other hand, if you have been brought into an existing business to build a new application from a legacy system, I think it usually makes more sense to use the Data Mapper pattern. An existing business will already have rules and procedures around how their business works. By using the Active Record pattern you will end up trying to force those business rules to play nicely with the “database mindset” of Active Record. The Data Mapper pattern will allow you to encapsulate the domain rules of the business so working with the application is clear and intuitive.


## Getting it done

So far we talked a lot and coded nothing. Let's change that. Here's what we need to do:

- Create databases
- Initialize a project
- Write the domain entities
- Write the entity mapping files
- Write the repositories

Let's get cracking!


### Create databases

We need a MySQL and a MongoDb database running. Not going to teach you how to do this. I'll just assume a mongo instance is running and the following details for MySQL:

```
host: localhost
username: user
password: pass
dbname: doctrine_tutorial
```


### Initialize a project

Create the following file structure. We're organizing classes according to what we image our [bounded contexts](http://martinfowler.com/bliki/BoundedContext.html) will be. It's a small application but it still pays to do it properly.

```
/
    /src
        /Customer
            Customer.php
        /Product
            Product.php
        /Menu
            Menu.php
            Recipe.php
            Step.php
    /tests
        /bootstrap.php
    composer.json
```

Write the following files:

- [composer.json](./composer.json)
- [phpunit.xml](./phpunit.xml)
- [tests/bootstrap.php](./tests/bootstrap.php)

Now use composer to install dependencies and off you go:
```
$ composer install
```


### Write the domain entities

Next up, let's code our entities. We're using accessors here instead of public properties for reasons which are beyond the scope of this tutorial. Notice how the entities have nothing in them to hint at the underlying storage mechanism. That's exactly how we want them.

We could go further and use value objects for properties, but let's save that for another time.

Also, there's a `Collection` and a `CollectionInterface` in there. They just extend `Doctrine\Common\Collections\Collection` and `Doctrine\Common\Collections\ArrayCollection`, respectively. We're just putting some sanity separation between ourselves and the library we're consuming because we might well need some other kind of collection later.

- [src/Customer/Customer.php](./src/Customer/Customer.php)
- [src/Menu/Menu.php](./src/Menu/Menu.php)
- [src/Menu/Recipe.php](./src/Menu/Recipe.php)
- [src/Menu/Step.php](./src/Menu/Step.php)
- [src/Product/Product.php](./src/Product/Product.php)


### Write the metadata files

Doctrine relies on metadata to know how to map our entities to the underlying storage mechanism. This can be done in the entity files themselves using docblock annotations, or in separate mapping files written in yaml or xml.

[Read the docs](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/basic-mapping.html)

Again, we don't want our entities to contain anything that hints at infrastructure. So we're going with external mapping files. Yaml in this case, simply because we like it better than xml. You can find all mapping files inside the `mapping` directory of each bounded context:

- [src/Customer/mapping](./src/Customer/mapping)
- [src/Menu/mapping](./src/Menu/mapping)
- [src/Product/mapping](./src/Product/mapping)

Let's take a look at the most interesting one:

```yaml
Hellofresh\DoctrineTutorial\Menu\Menu:
    type: entity # As opposed to "document"
    table: menus # Self explanatory

    # Also self explanatory
    indexes:
        index_id:
            columns: [ id ]

    # This lets us define the ID generation strategy.
    # This is the default one if you don't write anything.
    id:
        id:
            type: integer
            generator:
                strategy: AUTO

    # All the fields go here.
    # There are many options for their primitive types, lengths, etc.
    fields:
        week:
            type: string

    # We can have many kinds of associations:
    # - oneToOne
    # - oneToMany
    # - manyToOne
    # - manyToMany
    # 
    # This only works within the storage engine, though, which is why
    # we're only going to define the association with Product here.
    manyToOne:
        product:
            targetEntity: Hellofresh\DoctrineTutorial\Product\Product
```

If recipes were also stored in MySQL, we'd expect to add something like this
 to that file:

```yaml
    manyToMany:
        recipes:
            targetEntity: Hellofresh\DoctrineTutorial\Menu\Recipe
            joinTable:
                name: menus_recipes
                joinColumns:
                    menu_id:
                        referencedColumnName: id
                inverseJoinColumns:
                    recipe_id:
                        referencedColumnName: id
```

But we won't. Because they're stored in Mongo, we have to implement associations ourselves through the repositories.


### Write the repositories

Repositories are the meat and potatoes of the persistence layer. Before we dive in, let's consider a few points:

##### Always write your own repositories

This might seem like extra work, when the good people at the Doctrine Project already supplied you with generic `EntityRepository` and `DocumentRepository` classes which handle the most common use cases (find by id, find by property). But read on.

##### Don't extend from the generic repositories

Say your `CustomersRepositoryInterface` declares one method: `find($id)`. Nice and clean. Because the good people at the Doctrine Project already did that for you, you feel inclined to implement it like so:

```php
use Doctrine\ORM\EntityRepository;

class CustomersRepository extends EntityRepository implements CustomersRepositoryInterface
{
    // EntityRepository already has find($id).
    // We're done here. Productivity!
    // Everyone gets a raise!
}
```

Problem is, your class is now lying. It doesn't just implement your beautiful, terse interface, it also implements everything that comes from `EntityRepository`. Meaning that, when someone using PhpStorm is autocompleting, he's not going to see this:

```
- CustomersRepository::find($id)
```

Instead, he'll see this:

```
- CustomersRepository::find($id)
- CustomersRepository::findBy(...)
- CustomersRepository::findOneBy(...)
- CustomersRepository::findAll(...)
- CustomersRepository::findOneBy(...)
- CustomersRepository::createQueryBuilder(...)

and a bunch more
```

I can **guarantee** someone somewhere is going to write some production code that happily consumes methods not declared in the interface. Those methods cannot be stubbed in tests, meaning either this code will not be tested, or will be tested in a very slow, very ugly integration test that **you** will end up having to fix at 2am.

Don't let that happen.


##### Inject the object manager and proxy calls to it

Like so:

```php
use Doctrine\ORM\EntityManager;

class CustomersRepository implements CustomersRepositoryInterface
{
    protected $em;
    protected $entity = 'Hellofresh\DoctrineTutorial\Customer\Customer';

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function find($id)
    {
        // You have options here.
        // Can also use the generic repository or, better, do DQL.
        return $this
            ->em
            ->find($this->entity, $id)
        ;
    }
}
```

One drawback:

- You can no longer magically get this repository from the EntityManager:

```php
// Gets the generic repo, not your awesome one.
$em->getRepository('Hellofresh\DoctrineTutorial\Customer\Customer')
```

Two big advantages:

- Now you have full control over how to query (check the comment).
- You're now free to inject any dependencies the repository needs.


##### Let's TDD this thing

Because it's 2015 and we all love [Uncle Bob](https://twitter.com/unclebobmartin), we're going to TDD these bad boys. It's not that difficult, but it requires a bit more setup up front. The payoff is, of course, a nice safety net.

That being said, we won't go with unit tests. It's totally possible to unit test repositories, but, because of their nature, I find it more productive to test them in integration with the database.

Also, in a production project I'd retrieve repositories from a dependency injection container instead of instantiating them in the test itself. Reason being, that way I can keep the tests even if I change the repository implementations.

So, allowing for that couple of idiosyncrasies, let's get cracking.


#### Set the tests up to use Doctrine

We'll create traits to use in our tests, which give us nicely set up instances of `EntityManager` and `DocumentManager`:

- [tests/EntityManagerTestCase.php](./tests/EntityManagerTestCase.php)
- [tests/DocumentManagerTestCase.php](./tests/DocumentManagerTestCase.php)

**Review these carefully**. The code in these classes is essentially how you instantiate Doctrine's Entity and Document Managers.


#### Prepare some fixtures

Since these are database integration tests, it'd be nice to have a simple way of loading fixtures into it. Fortunately, there's a really nice library called [nelmio/alice](https://github.com/nelmio/alice) which does the job nicely: it allows defining fixtures in yaml, which is highly expressive and readable. They look like this:

```yaml
Nelmio\Entity\User:
    user{1..10}:
        username: <username()>
        fullname: <firstName()> <lastName()>
        birthDate: <date()>
        email: <email()>
        favoriteNumber: 50%? <numberBetween(1, 200)>
```

We'll put ours in [`tests/support/fixtures`](./tests/support/fixtures) and load them when needed like so:

```
$this->loadFixtures('customers');
```


#### Writing the first test

Let's start with the simplest of our objectives:

> - I want to get a Recipe by its ID.

We should test for the failure mode first. Why? Because any system starts out not working. Most failure mode tests don't require loading fixtures or anything, so they're fast. Also because most people only test for success cases, meaning their coverage isn't complete.

```php
// tests/Menu/Repository/RecipesRepositoryTest.php

namespace Tests\Hellofresh\DoctrineTutorial\Menu\Repository;

use Tests\Hellofresh\DoctrineTutorial\DocumentManagerTestCase;
use Hellofresh\DoctrineTutorial\Menu\Repository\RecipesRepository;

class RecipesRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use DocumentManagerTestCase;

    /**
     * @expectedException Hellofresh\DoctrineTutorial\Common\NotFoundException
     * @expectedExceptionMessage Recipe not found
     */
    public function testFindFailsWithNotFoundException()
    {
        $repository = new RecipesRepository();
        $repository->find(9999);
    }
}
```

We're looking for a `NotFoundException` to be thrown, with a message saying that recipe wasn't found. Running the test, it fails miserably:

```bash
➜  tutorial  bin/phpunit
PHPUnit 4.8.6 by Sebastian Bergmann and contributors.

PHP Fatal error:  Class 'Hellofresh\DoctrineTutorial\Menu\Repository\RecipesRepository' not found in /Users/pedrocarvalho/project/tutorial/tests/Menu/Repository/RecipesRepositoryTest.php on line 19

Fatal error: Class 'Hellofresh\DoctrineTutorial\Menu\Repository\RecipesRepository' not found in /Users/pedrocarvalho/project/tutorial/tests/Menu/Repository/RecipesRepositoryTest.php on line 19
```

But that's **okay**. This is TDD. Red, Green, Refactor. It always starts by failing, and errors are considered failures. Let's fix that. Add just enough code until this thing passes:

```php
// src/Menu/Repository/RecipesRepository.php

namespace Hellofresh\DoctrineTutorial\Menu\Repository;

use Hellofresh\DoctrineTutorial\Common\NotFoundException;
use Hellofresh\DoctrineTutorial\Menu\Recipe;

class RecipesRepository
{
    /**
     * @param  string $id
     * @return Recipe
     * @throws NotFoundException If no Recipe found
     */
    public function find($id)
    {
        throw new NotFoundException('Recipe not found');
    }
}
```

And it passes!

```bash
➜  tutorial  bin/phpunit
PHPUnit 4.8.6 by Sebastian Bergmann and contributors.

.

Time: 62 ms, Memory: 2.00Mb

OK (1 test, 2 assertions)
```

*Now* we test for success:

```php
// tests/Menu/Repository/RecipesRepositoryTest.php
...
class RecipesRepositoryTest extends \PHPUnit_Framework_TestCase
{
    ...
    public function testFindReturnsRecipe()
    {
        $repository = new RecipesRepository();
        $this->assertInstanceOf(
            'Hellofresh\DoctrineTutorial\Menu\Recipe',
            $repository->find('55f13bd332668a28390041a7')
        );
    }
}
```

This fails, of course. All the `find()` method ever does is throw exceptions. But that's okay. Let's add enough code to make this pass:

```php
...
class RecipesRepository
{
    ...
    public function find($id)
    {
        if ($id == 1) {
            return (new Recipe)->setId(1);
        }

        throw new NotFoundException('Recipe not found');
    }
}
```

"What the hell??" You're shouting. Don't worry. If it looks basic, it's because TDD starts very basic. This is actually a design stage. You're figuring out how your System Under Test should behave. In this case, that it should return a Recipe or throw an exception.

Now that we have green tests, we can start refactoring so that this looks more like what we want:

```php
// src/Menu/Repository/RecipesRepository.php
...
class RecipesRepository
{
    protected $recipes = [];

    public function __construct()
    {
        $this->recipes[1] = (new Recipe)->setId(1);
    }

    /**
     * @param  string $id
     * @return Recipe
     * @throws NotFoundException If no Recipe found
     */
    public function find($id)
    {
        if (!isset($this->recipes[$id])) {
            throw new NotFoundException('Recipe not found');
        }

        return $this->recipes[$id];
    }
}
```

Still green, and now it looks more like a repository. It's got an internal collection of Recipes and looks for the one with the given $id as index. See? It's a proper repository, albeit an in-memory one.

This is a good time to define our interface. [Add it](./src/Menu/Repository/RecipesRepositoryInterface.php), and refactor the code:

```php
// src/Menu/Repository/RecipesRepository.php
class RecipesRepository implements RecipesRepositoryInterface
```

Still green, because the interface is respected. Let's kick it up a notch, now. Let's load fixtures in the test and have the repository actually use a database.

For that, we're going to inject the `DocumentManager` into the repository and actually run a query on it:

```php
// src/Menu/Repository/RecipesRepository.php

use Doctrine\ODM\MongoDB\DocumentManager;
...
class RecipesRepository implements RecipesRepositoryInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function find($id)
    {
        $recipe = $this
            ->dm
            ->find('Hellofresh\DoctrineTutorial\Menu\Recipe', $id)
        ;

        if (!$recipe) {
            throw new NotFoundException("Recipe not found");
        }

        return $recipe;
    }
}
```

Tests now fail because they're broken. We need to respect the contract and refactor them so we always inject the DocumentManager into the repository:

```php
// tests/Menu/Repository/RecipesRepositoryTest.php
...
    public function testFindFailsWithNotFoundException()
    {
        $repository = new RecipesRepository($this->getDm());
        $repository->find(9999);
    }

    public function testFindReturnsRecipe()
    {
        $dm = $this->getDm();
        $this->loadFixtures('recipes', $dm);

        $repository = new RecipesRepository($dm);
        $this->assertInstanceOf(
            'Hellofresh\DoctrineTutorial\Menu\Recipe',
            $repository->find('55f13bd332668a28390041a7')
        );
    }
}
```

Tests are green. We just TDD'd our first repository!

## But Pedro, what about Listeners?

That's an advanced topic beyond the scope of this tutorial. Feel free to read up on them:

[Read the docs](http://doctrine-orm.readthedocs.org/en/latest/reference/events.html).


