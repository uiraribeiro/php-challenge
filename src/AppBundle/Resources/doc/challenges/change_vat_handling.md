# Epic: Change VAT handling

###Current Situation
At the moment, the respective VAT rate is stored directly on the product, which makes it difficult to react to changes of the respective tax authorities. Another shortcoming is that, at the moment, errors occur due to direct user input.

###Goals
A solution should be found that makes it possible to react flexibly to changes. In addition, incorrect user entries should be avoided.

####Challenge
- Develop a concept to implement the requirements.
- Creation of the necessary user stories.
- Break down the tasks  
- Implementation of the necessary features and adaptation of the existing code.
- Changes to the database should be made through migrations and previous data should be converted.
- Creation of the documentation.

Note: it is not required to create an API for the management of tax rates. Please add user stories to this document. 