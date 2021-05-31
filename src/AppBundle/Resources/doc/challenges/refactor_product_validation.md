#User Story: As a developer I want to refactor the product validation
As a developer 
I want to refactor the product validation
So that it is easier to understand I can reduce technical dept

###Current situation and motivation
Currently the validation is part of the class, it works but it is getting a bit messy and it is far from perfect.

Furthermore we have to constantly answer questions from user why they can't change property xy. 

So that we decided it's time to move the whole validation into an independent validator and on to add external documentation.

AC:
- There is an independent validator in place (in Lib/Product/Validator) which is attached to the product entity
- The validator is tested.  
- There is an external page in the doc folder which describes the constraints for creating and updating products. 