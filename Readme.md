#Behat Additional Contexts

This package extends the basic contexts and step definitions provided with Mink and Drupal contexts.

You'll find these additional definitions:

###GeneralContext:
>take a screenshot

Takes a screenshot of the current session/page state

>I wait for "X" seconds

Wait "X" seconds before executing the next step

>I focus on "element"

Set the focus on the element, clicking on it. [requires javascript api]
```javascript
jQuery( ":contains('element')" ).click()
```

>I fill in "field" with "value" plus random data

It tries to create a unique value to be submitted, appending some random data to the specified value

>I select "option" from "select" with javascript
>I check "checkbox/label" with javascript

Selects/Checks elements with javascript, avoiding selenium2d errors about element not being available
and it's also form-elements-javascript-libraries friendly (Chosen.js, Bootstrap, customSelect, etc.) [requires javascript api]



DrupalContext:
>I am logged in as a user with the "role" role(s) on this site
>I am an anonymous user on this site
>I am not logged in on this site
>I am logged in as :name on this site

Due [this issue](https://github.com/jhedstrom/drupalextension/pull/131) if you don't have a Logout link on the homepage
Drupal Driver doens't understand if the user is loggedin or not. These definitions fix that problem giving you the possibility
to define the path where the driver can find the Logout link: define the constant BDD_DRUPAL_LOGGEDIN_PATH in order to set your custom path (i.e.: "/user")

>I am logged in as :name with password :pass

It logs in the specific user with his/her specific password

> I should see the "element" form element
> I should not see the "element" form element
Using Fields Dependencies or Form API states some elements are visible only after certain conditions.
These definitions check the visibility of the element. [requires javascript api]

