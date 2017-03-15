<?php

namespace App\Models;

use PDO;


/**
 * UserPending Model
 *
 * PHP version 7.0
 */
class UserPending extends \Core\Model
{

    /**
     * adds new user to users_pending table
     *
     * @param string $token   Unique string
     *
     * @param integer $user_id  The user's ID
     */
    public static function addUserToUsersPending($token, $user_id)
    {
        // establish db connection
        $db = static::getDB();

        try
        {
            $sql = "INSERT INTO users_pending (token, user_id)
                    VALUES (:token, :user_id)";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':token' => $token,
                ':user_id' => $user_id
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


/**
 * verifies user account from email link
 *
 * @param  string $token   Unique string
 * @param  integer $user_id The user's ID
 *
 * @return boolean
 */
    public static function verifyNewUserAccount($token, $user_id)
    {
        // echo "Connected to verifyNewUserAccount in UserPending model<br><br>";
        // echo $token . "<br>";
        // echo $user_id . "<br>";
        //exit();

        // check for $token value in users_pending.token
        try {
            // establish db connection
            $db = static::getDB();

            $sql = "SELECT * FROM users_pending WHERE token = :token
                    AND user_id = :user_id";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':token' => $token,
                ':user_id' => $user_id
            ];
            $stmt->execute($parameters);
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            // echo "<pre>";
            // print_r($user);
            // echo "</pre>";
            // exit();

            if(empty($user))
            {
                echo "Unable to find match. Please re-register.";
                exit();
            }
            else
            {
                // activate user account
                try {
                    $sql = "UPDATE users SET
                            active = 1
                            WHERE id = :id";
                    $stmt = $db->prepare($sql);
                    $parameters = [
                        ':id' => $user_id
                    ];
                    $stmt->execute($parameters);
                }
                catch (PDOException $e)
                {
                    echo "Error adding new user.";
                    exit();
                }

                // delete pending user from table to disable verify email link
                try {
                    $sql = "DELETE FROM users_pending
                            WHERE token = :token";
                    $stmt =  $db->prepare($sql);
                    $parameters = [
                        ':token' => $token
                    ];
                    $stmt->execute($parameters);
                }
                catch(PDOException $e)
                {
                    echo "Error deleting users pending entry";
                    exit();
                }
            }
            return true;
        }
        catch(PDOException $e)
        {
            echo "Error retrieving data from users pending";
            exit();
        }
    }
}
