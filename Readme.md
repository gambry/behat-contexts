This package extends the basic contexts and step definitions provided with Mink and Drupal contexts.

You'll find these additional definitions:

GeneralContext:
take a screenshot
I wait for "X" seconds
I focus on :element
I fill in :field with :value plus random data
I select "<option>" from "<select>" with javascript
I check "<checkbox>/<label>" with javascript


DrupalContext:
I am logged in as a user with the "role" role(s) on this site
I am an anonymous user on this site
I am not logged in on this site
I am logged in as :name on this site
I am logged in as :name with password :pass
I should see the "element" form element
I should not see the "element" form element

