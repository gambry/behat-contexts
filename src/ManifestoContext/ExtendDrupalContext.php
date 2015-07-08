<?php
/**
 * Extends The DrupalContext step-definitions
 *
 * @author Gabriele Maira
 * @version 1.0
 */

namespace ManifestoContext;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;

use Behat\Behat\Definition\Call;

class ExtendDrupalContext extends RawDrupalContext implements SnippetAcceptingContext {


  /**
   * @var string custom Loggedin path (default /)
   */
  protected $loggedin_path;


  public function __construct() {

    //redefining where the DrupalContext will check if the user is loggedin
    $this->loggedin_path = (defined('BDD_DRUPAL_LOGGEDIN_PATH'))
      ? BDD_DRUPAL_LOGGEDIN_PATH
      : '/';
  }



  /**
  * Check logged in status.
  *
  * @override RawDrupalContext::loggedIn().
  * @see https://github.com/jhedstrom/drupalextension/pull/131
  */
  public function loggedIn() {
    $session = $this->getSession();
    $session->visit($this->locatePath($this->loggedin_path));


    // If a logout link is found, we are logged in. While not perfect, this is
    // how Drupal SimpleTests currently work as well.
    $element = $session->getPage();
    return $element->findLink($this->getDrupalText('log_out'));
  }

  /**
  * Creates and authenticates a user with the given role(s).
  *
  * This extends the basic DrupalContext step/method due the redirect
  * on Diabetes UK appending the words "on this site"
  *
  * @extends DrupalContext::assertAuthenticatedByRole()
  *
  * @Given I am logged in as a user with the :role role(s) on this site
  */
  public function assertAuthenticatedByRoleOnThisSite($role) {
    // Check if a user with this role is already logged in.
    if (!$this->loggedInWithRole($role)) {
      // Create user (and project)
      $user = (object) array(
        'name' => $this->getRandom()->name(8),
        'pass' => $this->getRandom()->name(16),
        'role' => $role,
      );
      $user->mail = "{$user->name}@example.com";

      $this->userCreate($user);

      $roles = explode(',', $role);
      $roles = array_map('trim', $roles);
      foreach ($roles as $role) {
        if (!in_array(strtolower($role), array('authenticated', 'authenticated user'))) {
          // Only add roles other than 'authenticated user'.
          $this->getDriver()->userAddRole($user, $role);
        }
      }

    // Login.
    $this->login();
    }
  }

  /**
  *
  * @override RawDrupalContext::assertAnonymousUser() with better logged in check.
  *
  * @Given I am an anonymous user on this site
  * @Given I am not logged in on this site
  */
  public function assertAnonymousUserOnThisSite() {
    // Verify the user is logged out.
    if ($this->loggedIn()) {
      $this->logout();
    }
  }

  /**
  * @Given I am logged in as :name with password :pass
  */
  public function assertLoggedInByNameAndPass($name,$pass,$role = 'authenticated user') {

    $this->user = new \stdClass();
    //username
    $this->user->name = $name;
    //pass
    $this->user->pass = $pass;

    // Login.
    $this->login();
  }


  /**
  * @Given I am logged in as :name on this site
  */
  public function assertLoggedInByName($name) {
    if (!isset($this->users[$name])) {
      throw new \Exception(sprintf('No user with %s name is registered with the driver. %s', $name,print_r($this->user,1)));
    }

    // Change internal current user.
    $this->user = $this->users[$name];

    // Login.
    $this->login();
  }

  /**
   * Checks if form element is (CSS-)visible
   *
   * @Then /^(?:|I )should see the "(?P<label>[^"]*)" form element$/
   */
  public function assertFormElementIsVisible($label) {
    $element = $this->getSession()->getPage();
    $nodes = $element->findAll('css', '.form-item label');

    foreach ($nodes as $node) {
      if (preg_replace("/\s*\*/","",$node->getText()) === $label) {
        if ($node->isVisible()) {
          return;
        }
        else {
          throw new \Exception("Form item with label \"$label\" not visible.");
        }
      }
    }

    throw new \Behat\Mink\Exception\ElementNotFoundException($this->getSession(), 'form item', 'label', $label);
  }

  /**
   * Checks if form element is not (CSS-)visible
   *
   * @Then /^(?:|I )should not see the "(?P<label>[^"]*)" form element$/
   */
  public function assertFormElementNotOnPage($label) {
    $element = $this->getSession()->getPage();
    $nodes = $element->findAll('css', '.form-item label');

    foreach ($nodes as $node) {

      if (preg_replace("/\s*\*/","",$node->getText()) === $label && $node->isVisible()) {
        throw new \Exception();
      }
    }

  }


}
