##Bug: User is able to create a subscription which exceeds stock
When a user creates a new subscription, the validation does not check the items that are available in stock, the could lead to overcommitment and problems whe delivering items.

###Task:
* extend the current validator and add check for items in stock.
* depending on the subscription time you should check at least the available items for the next three month.
* depending on the start date of the subscription the check should act differently:
  * if the start date is in the current or next month, the subscription should be rejected
  * if the start date is two or more month onwards, the error can be ignored. In that case an email should be sent to our sales staff*
    
###AC
* the validator is tested
* an email is sent to the sales team


###Challenge
Create tasks (and user stories if required) for solving the issues (write them into this document).
Implement the changes and new features

* Note: you don't need to sent out a real formatted email, see `Lib\EventSubscriber\MailingSubscriber` for an example.


