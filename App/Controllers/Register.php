<?php

namespace App\Controllers;

use \App\Models\User;
use \Core\View;
use \App\Models\UserPending;
use \App\Mail;


/**
 * Register controller
 *
 * PHP version 7.0
 */
class Register extends \Core\Controller
{
    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        //echo "(before) ";
        //return false;  // prevents originally called method from executing
    }


    protected function after()
    {
        //echo " (after)";

    }


/**
 * show registration form
 *
 * @return void
 */
    public function indexAction()
    {
        //echo "Connected to indexAction method in Register controller!";

        // display form in view
        View::renderTemplate('Register/index.html', [
          'registerindex' => 'true'
        ]);
    }



    public function checkIfEmailAvailableAction()
    {
        // store POST variable from Ajax script
        $email = $_POST['email'];

        // check for email using User model method
        $response = User::checkIfAvailable($email);

        // return $response value ('available' or 'not available') to
        // Ajax method for processing
        echo $response;
    }


    /**
     * adds new user to users table, users_pending table & sends verification email
     *
     * @return void
     */
    public function registerNewUser()
    {
        //echo "Connected to registerNewUser method in Register controller!<br>"; exit();

        // add new user to users table; get user data & store in $results array
        $results = User::addNewUser();

        // echo '<pre>';
        // print_r($results);
        // echo '</pre>';
        // echo "<br><br>";

        // assign values to variables
        $token = $results['token'];
        $user_id = $results['user_id'];

        // get all fields of new user data (need email & first name)
        $user = User::getUser($user_id);

        // assign values to variables
        $email = $user->email;
        $first_name = $user->first_name;

        // echo '<pre>';
        // print_r($user);
        // echo '</pre>';
        // echo '<br><br>';
        //exit();

        // add new user to users_pending table & pass $token & $user_id
        $results = $this->addToUsersPending($token, $user_id);

        if($results)
        {
            // send verification email to new user's email address
            $result = Mail::sendAccountVerificationEmail($token, $user_id, $email, $first_name);

            if($result)
            {
              // define message
              $message = "You have successfully registered! Please check your
                          email for our verification email. If you do not see it,
                          be sure to check your junk mail, and then white-list
                          Newman's Ground Care in your email client.";

              View::renderTemplate('Success/index.html', [
                  'message' => $message
              ]);
            }
            else
            {
                echo "Error. Verification email not sent";
                exit();
            }
        }
    }



    /**
     * adds new user to users_pending table
     *
     * @param string $token   Unique string
     * @param integer $user_id  The new user's ID
     */
    public function addToUsersPending($token, $user_id)
    {
        //echo "<br>Connected to addToUsersPending method in Register controller!<br><br>";

        $results = UserPending::addUserToUsersPending($token, $user_id);

        return $results;
    }



    public function verifyAccount()
    {
        //echo "Connected to verifyAccount method in register controller<br><br>"; //exit();

        // retrieve token in order to pass to verifyNewUserAccount method below
        $token = isset($_REQUEST['token']) ? filter_var($_REQUEST['token'], FILTER_SANITIZE_STRING) : '';
        $user_id = isset($_REQUEST['user_id']) ? filter_var($_REQUEST['user_id'], FILTER_SANITIZE_STRING) : '';

        // verify that user exists by matching stored token value with $token
        $result = UserPending::verifyNewUserAccount($token, $user_id);

        //echo '$result = '  . $result .  "<br>"; exit();

        // display success in view
        if($result)
        {
            // define message
            $message = "Congratulations! Your account has been verified. You can now login.";

            View::renderTemplate('Success/index.html', [
                'message' => $message
            ]);
        }
        else
        {
            echo "An error occurred while verifying your account. Please try again.";
            exit();
        }
    }
}
