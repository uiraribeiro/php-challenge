Bug: Subscription cancellation is not working as expected
==========================================================

When I try to cancel a subscription I expect the subscription to be marked as non-recurring, and the shipments to be canceled too, instead nothing happens.

Comment:
-------
Shame on us, most of the stuff is already in place, we simply forget to finish the feature, and now it became a bug.


Requirements:
-------------
* When a user wants to cancel a subscription he has two choices:
  1. at the end of the subscription period, then the only thing to do is set the recurring field to 0 and send a mail to our sales department with the reason he provided.
  2. before the end of the subscription period, in that case he needs to fill in the at_date field. Then again, the recurring field is set to 0 and all the outstanding shipments need to be canceled, and like with option 1, an email has to send to sales.

Task:
-----
- Add a validator to the SubscriptionCancellationRequestDto.
- Set recurring to 0.
- If subscription is canceled before its end, mark all shipments as canceled 
- Send out an email.

AC:
- All features are implemented.
- There are tests for all parts of the code

