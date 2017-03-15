<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Mail;


/**
 * Login controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller
{
    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        if(isset($_SESSION['user']))
        {
            echo "<p>Error. You are logged in.<br>You can manage your password
            in &quot;My Account&quot; in the Admin Panel.</p>";
            exit();
        }
    }


    protected function after()
    {
        //echo " (after)";

    }


    public function indexAction()
    {
        View::renderTemplate('Login/index.html', [
          'loginindex'  => 'true'
        ]);
    }


    /**
     * logs in user if matching credentials found
     *
     * @return user object or null
     */
    public function loginUser()
    {
        // retrieve form values
        $email = ( isset($_REQUEST['email'])  ) ? filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL) : '';
        $password = ( isset($_REQUEST['password'])  ) ? filter_var($_REQUEST['password'], FILTER_SANITIZE_EMAIL) : '';

        // validate user & find if in database; store user data in $user object
        $user = User::validateLoginCredentials($email, $password);

        if($user)
        {
            // log user in
            // create unique id & store in SESSION variable
            $uniqId = md5($user->id);
            $_SESSION['user'] = $uniqId;
            $_SESSION['loggedIn'] = true;

            // assign user ID & access_level & full_name to SESSION variables
            $_SESSION['user_id'] = $user->id;
            $_SESSION['access_level'] = $user->access_level;
            $_SESSION['first_name'] = $user->first_name;
            $_SESSION['full_name'] = $user->first_name . ' ' . $user->last_name;

            // echo $_SESSION['user'] . "<br>";
            // echo $_SESSION['loggedIn'] . "<br>";
            // echo $_SESSION['user_id'] . "<br>";
            // echo $_SESSION['access_level'] . "<br>";
            // echo $_SESSION['full_name'] . "<br>";
            //exit();

            // send login notification email
            $result = Mail::loginNotification($user);

            // send to home page
            header("Location: /");
            exit();
        }
    }


    /**
     * displays view
     * @return view
     */
    public function forgotPassword()
    {
        View::renderTemplate('Login/get-new-password.html', []);
    }



    /**
     * processes new pasword
     * @return [type] [description]
     */
    public static function getNewPassword()
    {
        // Verify that email exists in `users` table
        $email = ( isset($_POST['email_address']) ) ? htmlspecialchars($_POST['email_address']) : '';

        // verify user exists; return $user object
        $user = User::doesUserExist($email);

        // test
        // echo '<pre>';
        // print_r($user);
        // echo '</pre>';
        // exit();

        if ($user)
        {
            // create temp password for next step
            $tmp_pass = bin2hex(openssl_random_pseudo_bytes(4));

            // insert temporary password
            $result = User::insertTempPassword($user->id, $tmp_pass);

            if($result)
            {
                // send email to user; pass $user object & $tmp_pass
                $result = Mail::sendTempPassword($user, $tmp_pass);

                if($result)
                {
                    $message = "A temporary password was sent to your email address.
                      Please use it to log in and reset your password.";

                    View::renderTemplate('Success/index.html', [
                        'message' => $message
                    ]);
                }
                else
                {
                    echo "Unable to send a temporary password. Pleas try again";
                    exit();
                }
            }
            else
            {
                echo "Error occurred. Please try again.";
                exit();
            }
        }
        else
        {
            echo "User not found.";
            exit();
        }
    }




    public function tempPassLogin()
    {
        View::renderTemplate('Login/temp-password-login.html', []);
    }





    public function loginUserWithTempPassword()
    {
        // retrieve form values
        $email = ( isset($_REQUEST['email'])  ) ? filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL) : '';
        $tmp_pass = ( isset($_REQUEST['tmppassword'])  ) ? filter_var($_REQUEST['tmppassword'], FILTER_SANITIZE_STRING) : '';

        // log in user
        $user = User::loginUserWithTempPassword($email,$tmp_pass);

        if($user)
        {
            // delete tmp_pass from users table
            $result = User::deleteTempPassword($user->id);

            if($result)
            {
                // log user in
                // create unique id & store in SESSION variable
                $uniqId = md5($user->id);
                $_SESSION['user'] = $uniqId;
                $_SESSION['loggedIn'] = true;

                // assign user ID & access_level & full_name to SESSION variables
                $_SESSION['user_id'] = $user->id;
                $_SESSION['access_level'] = $user->access_level;
                $_SESSION['full_name'] = $user->first_name . ' ' . $user->last_name;

                // test
                // echo $_SESSION['user'] . "<br>";
                // echo $_SESSION['loggedIn'] . "<br>";
                // echo $_SESSION['user_id'] . "<br>";
                // echo $_SESSION['access_level'] . "<br>";
                // echo $_SESSION['full_name'] . "<br>";
                // exit();

                header("Location: /");
                exit();
            }
        }
    }



    /**
     * gets visitor's IP address
     * @return [type] [description]
     */
    public function getUserIP()
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }
}
