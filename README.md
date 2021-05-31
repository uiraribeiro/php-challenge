Coding challenge
========================

Introduction
-------------

This app is about an API handling the products, customers and subscriptions of an online shop.

How to run
-----------

```  
docker-compose up -d
``` 

After that you need to init the database and load the fixture data, to do so enter the `php-challenge` container and run:
``` 
php app/console doctrine:schema:create --force
```

``` 
php app/console doctrine:fixtures:load
```


Site is available at: http://localhost:9080, xdebug is listening at port 9002, you find the API calls available at http://localhost:9080/api/docs 


To run the tests enter the container and run:

`bin/simple-phpunit -c app/phpunit.xml src/AppBundle`

The tests will create a code coverage which is available in the `build` folder. 

Tasks
-----
You find the tasks and their description

After you finish please document your decisions. Your also free to add extra documentation
on how to run the project in case you make changes to its setup.


What are we looking for
------------------------

- Ability to spot code errors
- How do you improve code
- If you can dive into an old codebase
- What is your rationale to change code
- How you document previously undocumented project.
- How you add features without breaking existing functionalities 
