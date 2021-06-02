The Project
===========

It's a small B2B Business where we are selling fruits to our customers on a subscription base. The following is a small introduction into the structure of the business.

Items
-----
This is the stuff we sell. (Mostly apples at the moment)

Products
--------
Our products are based on the items, one item can have as many products as you want or see fit. Each product is available in a single country, where wthey have a price, a VAT rate, individual shipping costs and a minimal quantity and since we are selling on a subscription base a minimal subscription time. Each product is only available in a time frame. 

Stock
-----
This is where we're storing our items, it contains information how many items are available per country and month. Since the people prefer fresh fruits, the stock is cleared at the end of each month.   

Customers
---------
Well, our customers.

Stores
-------
Each of our customers can have as many stores as they want. Each store is located in a country. All orders are going to that store. A store can only order products which are available in its country. 

Subscriptions
-------------
Every subscription is linked to a product, and to a store, it has a start date, an end date, and a quantity. Subscription can be marked as recurring, when this is the case it is automatically extended if the product is still available.

Shipment
--------
This is the place where we plan our shipments, whenever a customers creates a new subscription the shipments are planned here by adding a record for each month of the subscription time. If a customer cancel his subscription before the end of his subscription period, the outstanding subscriptions are marked as canceled.

The API
-------
The API is available at http://localhost:9080/api/doc
