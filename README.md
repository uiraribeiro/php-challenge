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
Note: the included docker-compose.yml is supposed to run on linux, if you're using a different host os, you will need to adapt it:

```  
php-challenge:
    build: .
    volumes:
        - .:/var/www/php-challenge
    ports:
        - 9080:9080
    # remove if you're using a mac or windows
    extra_hosts:    
        - "host.docker.internal:host-gateway"
``` 
You will also need to adapt xdebug.ini 
```  
max_execution_time=-1

; Uncomment to enable this extension.
zend_extension=xdebug.so
xdebug.remote_enable=on
xdebug.remote_host=host.docker.internal ; change to docker.for.mac.host.internal if its not working
xdebug.remote_port=9002
xdebug.idekey=PHPSTORM
``` 

The database should have been initialized automatically and its tables should hold fixture data.  

If you want to init it again you can either remove the database container `docker-compose rm db` or within the php container using:
``` 
bash-4.4# php app/console doctrine:database:drop
bash-4.4# php app/console doctrine:database:create
bash-4.4# php app/console doctrine:schema:create
bash-4.4# php app/console doctrine:fixtures:load
```


Site is available at: http://localhost:9080, xdebug is listening at port 9002, you find the API calls available at http://localhost:9080/api/docs 


To run the tests enter the container and run:

`bin/simple-phpunit -c app/phpunit.xml src/AppBundle`

The tests will create a code coverage which is available in the `build` folder. 

Tasks
-----
**[You find the tasks and their description here](src/AppBundle/Resources/doc/challenges/index.md)**

You should also read through the **[project description](src/AppBundle/Resources/doc/index.md)** for a small introduction what the test project is about.

After you finished please document your decisions. Your also free to add extra documentation
on how to run the project in case you make changes to its setup.


What are we looking for
------------------------

- Ability to spot code errors
- How do you improve code
- If you can dive into an old codebase
- What is your rationale to change code
- How you document previously undocumented project.
- How you add features without breaking existing functionalities 
