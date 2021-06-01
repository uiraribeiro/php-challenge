##Bug: Admin is able to "under" stock items
The stock up feature has two operation modes: replace or update. When using replace it can lead to the fact that items are becoming short, and we might not able to ship, but since apples are a rare good, we might have no other choice. The validation should cover that case.

###Task
1. extend the validation that it triggers an error when this happens
2. implement an additional switch in the form which overrides the check.
3. In case the item is left under stocked the sales team is informed

###A:
- the validation and the in input is adapted
- an email is sent out in case (3.)  

###Challenge
Create tasks (and user stories if required) for solving the issues (write them into this document).
Implement the changes and new features

* Note: you don't need to sent out a real formatted email, see `Lib\EventSubscriber\MailingSubscriber` for an example.
