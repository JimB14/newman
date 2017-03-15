<?php

namespace App\Models;

use PDO; 

/**
 * Post Model
 *
 * PHP version 7.0
 */
class Post extends \Core\Model
{
    /**
     * Get all posts as an associative array
     *
     * @return array
     */
    public static function getAll()
    {
        try
        {
            // call getDB() static method of abstract Core/Model class
            // to get connection to db
            $db = static::getDB();

            // query
            $sql = "SELECT id, title, content FROM posts ORDER BY created_at";
            $stmt = $db->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage;
            exit();
        }
    }
}
