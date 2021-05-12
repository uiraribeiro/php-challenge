Coding challenge
========================

Introduction
-------------

This app has two different endpoints: one to list products and other to create a new product.
Products have name, price (in cents) and a description.

How to run
-----------

docker-compose up -d


Tasks
-----

1. The POST endpoint has at least one problem and we need you to fix it.
   Discovering what the problems are is also part of the job
2. The prices to be displayed as a currency. ex: 100 == 1,00
3. When creating the product price should be accepted as a currency value. ex: 54,98
4. These are old API endpoints and people are using it.
5. Products need to save its billing cycle: "anual" or "monthly" (default "monthly") - 
   adding a field is considerable backwards compatible

After you finish please document your decisions. Your also free to add extra documentation
on how to run the project in case you make changes to its setup.


What are we looking for
------------------------

- Ability to spot code errors
- How do you improve code
- If you can dive into an old codebase
- What is your rationale to change code
- How you document previously undocumented project.

