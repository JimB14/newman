<?php
namespace App\Controllers\Admin; // namespace is path from root for this file

/**
 * User admin controller
 *
 * PHP version 7.0
 */
class Users extends \Core\Controller
{
      /**
       * Before filter
       *
       * @return void
       */
      protected function before()
      {
        // Make sure an admin user is logged in
        //return false;
      }



      /**
       * Show the index page
       *
       * @return void
       */
      public function indexAction()
      {
          echo "Hello from the index function in the Admin/Users controller!";
      }

}
