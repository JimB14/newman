<?php

namespace App\Models;

use PDO;
use \App\Controllers\Register;

/**
 * User Model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{
    /**
     * checks if email is in users table
     *
     * @param  string   $email  The user's email address
     *
     * @return string           The answer
     */
    public static function checkIfAvailable($email)
    {
        if($email == '' || strlen($email) < 3)
        {
          echo "Invalid email address";
          exit();
        }

        try
        {
            // establish db connection
            $db = static::getDB();

            $sql = "SELECT * FROM users
                    WHERE email = :email
                    LIMIT 1";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':email' => $email
            ];
            $stmt->execute($parameters);
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);

            $count = $stmt->rowCount();

            if ($count < 1)
            {
              return 'available';
            }
            else
            {
              return 'not available';
            }
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }



    /**
     * register new user
     *
     * @return [type] [description]
     */
    public static function addNewUser()
    {
        //echo "<br>Connected to addNewUser method of User model!<br><br>"; //exit();

        // unset SESSION variable
        unset($_SESSION['registererror']);

        // create gatekeeper variable
        $okay = true;

        // retrieve post data from form
        $first_name = isset($_REQUEST['first_name']) ? filter_var($_REQUEST['first_name'], FILTER_SANITIZE_STRING) : '';
        $last_name = isset($_REQUEST['last_name']) ? filter_var($_REQUEST['last_name'], FILTER_SANITIZE_STRING) : '';
        $email = isset($_REQUEST['email']) ? filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL) : '';
        $user_ip = $_SERVER['REMOTE_ADDR'];

        if($first_name === '' || $last_name === '' || $email === '')
        {
            $_SESSION['registererror'] = '*All fields are required.';
            $okay = false;
            header("Location: /register");
            exit();
        }

        // check if data passing
        // echo '<pre>';
        // print_r($_REQUEST);
        // echo '</pre>';
        // exit();

        // validate email
        if(filter_var($email, FILTER_SANITIZE_EMAIL === false))
        {
            $_SESSION['registererror'] = '*Please enter a valid email address.';
            $okay = false;
            header("Location: /register");
            exit();
        }

        $verify_email = isset($_REQUEST['verify_email']) ? filter_var($_REQUEST['verify_email'], FILTER_SANITIZE_EMAIL) : '';

         // check if emails match
         if($verify_email != $email)
         {
             $okay = false;
             $_SESSION['registererror'] = '*Emails do not match.';
             $okay = false;
             header("Location: /register");
             exit();
         }

        $password = isset($_REQUEST['password']) ? filter_var($_REQUEST['password'], FILTER_SANITIZE_STRING) : '';
        $verify_password = isset($_REQUEST['verify_password']) ? filter_var($_REQUEST['verify_password'], FILTER_SANITIZE_STRING) : '';

        // check if passwords match
        if($verify_password != $password)
        {
            $_SESSION['registererror'] = '*Passwords do not match';
            $okay = false;
            header("Location: /register");
            exit();
        }

        // hash password
        $pass = password_hash($password, PASSWORD_DEFAULT);

        // echo $first_name . '<br>';
        // echo $last_name . '<br>';
        // echo $email . '<br>';
        // echo $pass . '<br>';
        // echo $okay . '<br>';
        // exit();

        if($okay == true)
        {
            $db = static::getDB();

            // insert user data into users table
            try
            {
                $sql = "INSERT INTO users (first_name, last_name, email, password, user_ip)
                        VALUES (:first_name, :last_name, :email, :password, :user_ip)";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':first_name' => $first_name,
                    ':last_name'  => $last_name,
                    ':email'      => $email,
                    ':password'   => $pass,
                    ':user_ip'    => $user_ip
                ];
                $result = $stmt->execute($parameters);

                // get new user's id
                $user_id = $db->lastInsertId();

                // create token for validation email
                $token = md5(uniqid(rand(), true)) . md5(uniqid(rand(), true));

                // store new user ID & token in array & return to Register controller
                $results = [
                    'result' => $result,
                    'user_id' => $user_id,
                    'token' => $token
                ];

                // echo '<pre>';
                // print_r($results);
                // echo '</pre>';
                // exit();

                return $results;
            }
            catch (PDOException $e)
            {
                $_SESSION['registererror'] = "Error adding user to database " . $e->getMessage();
                header("Location: /register");
                exit();
            }
        }
        else
        {
            $_SESSION['registererror'] = "Error during registration. Please try again.";
            header("Location: /register");
            exit();
        }
    }



    /**
     * gets User data
     *
     * @param  integer $user_id The user ID
     *
     * @return Object           The user data
     */
    public static function getUser($user_id)
    {
        $db = static::getDB();

        try
        {
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':id' => $user_id
            ];
            $stmt->execute($parameters);
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            return $user;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }



    /**
     * validates user credentials
     *
     * @param  string $email     The user's email
     * @param  string $password  The user's password
     *
     * @return boolean
     */
    public static function validateLoginCredentials($email, $password)
    {
        // clear variable for new values
        unset($_SESSION['loginerror']);

        // set gate-keeper to true
        $okay = true;

        // check if fields have length
        if($email == "" || $password == "")
        {
            $_SESSION['loginerror'] = 'Please enter login email and password.';
            $okay = false;
            header("Location: /login/index");
            exit();
        }

        // validate email
        if(filter_var($email, FILTER_SANITIZE_EMAIL === false))
        {
            $_SESSION['loginerror'] = 'Please enter a valid email address';
            $okay = false;
            header("Location: /login/index");
            exit();
        }

        if($okay)
        {
            // check if email exists & retrieve password
            try
            {
                // establish db connection
                $db = static::getDB();

                $sql = "SELECT * FROM users
                        WHERE email = :email
                        AND active = 1";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':email' => $email
                ];
                $stmt->execute($parameters);
                $user = $stmt->fetch(PDO::FETCH_OBJ);
            }
            catch (PDOException $e)
            {
                $_SESSION['loginerror'] = "Error checking credentials";
                header("Location: /login/index");
                exit();
            }
        }

        // check if email & active match found
        if(empty($user))
        {
            $_SESSION['loginerror'] = "User not found";
            header("Location: /login");
            exit();
        }

        if( (!empty($user)) && (password_verify($password, $user->password)) )
        {
            return $user;
        }
        else
        {
            $_SESSION['loginerror'] = "Matching credentials not found.
            Please verify and try again.";
            header("Location: /login");
            exit();
        }
    }






    public static function doesUserExist($email)
    {
        // Server side validation (HTML5 validation 'required' on input tag)
        if($email === '' || strlen($email) < 6){
            echo 'Please provide a valid email address';
            exit();
        }

        // check if email is in `users` table
        try
        {
            // establish db connection
            $db = static::getDB();

            $sql = "SELECT * FROM users
                    WHERE email = :email
                    AND active = 1";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':email' => $email
            ];
            $stmt->execute($parameters);
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            // return user object to Login Controller
            return $user;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }




    public static function insertTempPassword($id, $tmp_pass)
    {
        try
        {
            // establish db connection
            $db = static::getDB();

            $sql = "UPDATE users SET tmp_pass = :tmp_pass
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':tmp_pass' => $tmp_pass,
                ':id'       => $id
            ];
            $result = $stmt->execute($parameters);

            return $result;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }




    public static function loginUserWithTempPassword($email,$tmp_pass)
    {
        if($email == '' || $tmp_pass == '')
        {
            echo "Submitted data is invalid.";
            exit();
        }

        try
        {
            // establish db connection
            $db = static::getDB();

            $sql = "SELECT * FROM users
                    WHERE email = :email
                    AND tmp_pass = :tmp_pass";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':email'    => $email,
                ':tmp_pass' => $tmp_pass
            ];
            $stmt->execute($parameters);
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            return $user;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }




    public static function deleteTempPassword($id)
    {
        try
        {
            // establish db connection
            $db = static::getDB();

            $sql = "UPDATE users SET tmp_pass = null
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':id' => $id
            ];
            $result = $stmt->execute($parameters);

            return $result;
        }
        catch(PDOException $e)
        {
            $e->getMessage();
            exit();
        }
    }
}
