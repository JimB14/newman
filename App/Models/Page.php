<?php

namespace App\Models;

use PDO;

/**
 * Page Model
 *
 * PHP version 7.0
 */
class Page extends \Core\Model
{
    /**
     * retrieve about page data
     *
     * @return array  All columns for about page
     */
    public static function getHomePageData()
    {
        try
        {
            // get database connection
            $db = static::getDB();

            $sql = "SELECT * FROM pages
                    WHERE id = 7";
            $stmt = $db->query($sql);
            $content = $stmt->fetch(PDO::FETCH_ASSOC);

            return $content;
        }
        catch (PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }



    /**
     * retrieve about page data
     *
     * @return array  All columns for about page
     */
    public static function getAboutPageData()
    {
        try
        {
            // get database connection
            $db = static::getDB();

            $sql = "SELECT * FROM pages
                    WHERE id = 1";
            $stmt = $db->query($sql);
            $content = $stmt->fetch(PDO::FETCH_ASSOC);

            return $content;
        }
        catch (PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }


    /**
     * retrieve calendar page data
     *
     * @return array  All columns for about page
     */
    public static function getCalendarPageData()
    {
        try
        {
            // get database connection
            $db = static::getDB();

            $sql = "SELECT * FROM pages
                    WHERE id = 9";
            $stmt = $db->query($sql);
            $content = $stmt->fetch(PDO::FETCH_ASSOC);

            return $content;
        }
        catch (PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }



    /**
     * retrieve services page data
     *
     * @return array  All columns for services page
     */
    public static function getServicesPageData()
    {
        try
        {
            $db = static::getDB();

            $sql = "SELECT * FROM pages WHERE id = 6";
            $stmt = $db->query($sql);
            $content = $stmt->fetch(PDO::FETCH_ASSOC);

            return $content;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }



    /**
     * retrieve add-testimonial page data
     *
     * @return array  All columns for services page
     */
    public static function getAddTestimonialPageData()
    {
        try
        {
            $db = static::getDB();

            $sql = "SELECT * FROM pages
                    WHERE id = 10";
            $stmt = $db->query($sql);
            $content = $stmt->fetch(PDO::FETCH_ASSOC);

            return $content;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }



    public static function getContactPageData()
    {
        try
        {
            $db = static::getDB();

            $sql = "SELECT * FROM pages WHERE id = 2";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $content = $stmt->fetch(PDO::FETCH_OBJ);

            return $content;
        }
        catch (PDOException $e)
        {
          echo $e->getMessage();
          exit();
        }
    }



    /**
     * retrieves Get Quote data
     *
     * @return array The Get Quote data
     */
    public static function getGetQuoteData()
    {
        try
        {
            $db = static::getDB();

            $sql = "SELECT * FROM
                    pages WHERE id = 8";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $content = $stmt->fetch(PDO::FETCH_OBJ);

            return $content;
        }
        catch (PDOException $e)
        {
          echo $e->getMessage();
          exit();
        }
    }



    /**
     * retrieves Pay online data
     *
     * @return array The Get Quote data
     */
    public static function getPayonlinePageData()
    {
        try
        {
            $db = static::getDB();

            $sql = "SELECT * FROM
                    pages WHERE id = 11";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $content = $stmt->fetch(PDO::FETCH_OBJ);

            return $content;
        }
        catch (PDOException $e)
        {
          echo $e->getMessage();
          exit();
        }
    }





/* - - - - - - - - - update section - - - - - - - - - - - - - - */


    /**
     * updates about page in pages table
     *
     * @return boolean
     */
    public static function updateHomePage()
    {
        $page_id = $_REQUEST['page_id'];
        $page_content = $_REQUEST['thehomedata'];

        // echo $page_id . '<br>';
        // echo $page_content . "<br>";
        // exit();

        // check if page exists (new page will have page_id > 0)
        if($page_id > 0)
        {
            try
            {
                $db = static::getDB();

                $sql = "UPDATE pages SET
                        page_content = :page_content
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':id' => $page_id,
                    ':page_content' => $page_content
                ];

                return $stmt->execute($parameters);
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }
        }
    }



    /**
     * updates about page in pages table
     *
     * @return boolean
     */
    public static function updateAboutPage()
    {
        //echo "Connected to updateAboutPage method from Page model";exit();

        $page_id = $_REQUEST['page_id'];
        $page_content = $_REQUEST['theaboutdata'];

        // check if page exists (new page will have page_id > 0)
        if($page_id > 0)
        {
            try
            {
                $db = static::getDB();

                $sql = "UPDATE pages SET
                        page_content = :page_content
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':id' => $page_id,
                    ':page_content' => $page_content
                ];

                return $stmt->execute($parameters);
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }

        }

        /* code to create new page
        else
        {
            // create new instance of Page
            $page = new Page;

            // create new instance of Slugify
            $slugify = new Slugify;

            // retrieve post data and store in variable
            $browser_title = $_REQUEST['browser_title'];
            $page->browser_title = $browser_title;
            $page->slug = $slugify->slugify($browser_title);

            // check if slugify version of $browser_title is in DB; if found, store in $results
            $results = Page::where('slug', '=', $slugify->slugify($browser_title))->get();

            // loop will execute only if $results is not empty; if not empty, slug exists, so $okay = false;
            foreach($results as $result)
            {
                $okay = false;
            }

        }

        // store page_content in $page
        $page->page_content = $page_content;

        if($okay)
        {
            $page->save();
            echo "OK";
        }
        else
        {
            // reach here if $okay = false
            echo "Browser title is already in use. Please choose another title.";
        }
        */
    }



    /**
     * updates calendar page in pages table
     *
     * @return boolean
     */
    public static function updateCalendarPage()
    {
        //echo "Connected to updateAboutPage method from Page model";exit();

        $page_id = $_REQUEST['page_id'];
        $page_content = $_REQUEST['thecalendardata'];

        // check if page exists (new page will have page_id > 0)
        if($page_id > 0)
        {
            try
            {
                $db = static::getDB();

                $sql = "UPDATE pages SET
                        page_content = :page_content
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':id' => $page_id,
                    ':page_content' => $page_content
                ];

                return $stmt->execute($parameters);
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }

        }
    }




    /**
     * updates about page in pages table
     *
     * @return boolean
     */
    public static function updateServicesPage()
    {
        $page_id = $_REQUEST['page_id'];
        $page_content = $_REQUEST['theservicesdata'];

        // echo $page_id . '<br>';
        // echo $page_content . "<br>";
        // exit();

        // check if page exists (new page will have page_id > 0)
        if($page_id > 0)
        {
            try
            {
                $db = static::getDB();

                $sql = "UPDATE pages SET
                        page_content = :page_content
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':id' => $page_id,
                    ':page_content' => $page_content
                ];

                return $stmt->execute($parameters);
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }
        }
    }



    /**
     * updates about page in pages table
     *
     * @return boolean
     */
    public static function updateAddtestimonialPage()
    {
        $page_id = $_REQUEST['page_id'];
        $page_content = $_REQUEST['theaddtestimonialdata'];

        // echo $page_id . '<br>';
        // echo $page_content . "<br>";
        // exit();

        // check if page exists (new page will have page_id > 0)
        if($page_id > 0)
        {
            try
            {
                $db = static::getDB();

                $sql = "UPDATE pages SET
                        page_content = :page_content
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':id' => $page_id,
                    ':page_content' => $page_content
                ];

                return $stmt->execute($parameters);
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }
        }
    }




    /**
     * updates about page in pages table
     *
     * @return boolean
     */
    public static function updateContactPage()
    {
        $page_id = $_REQUEST['page_id'];
        $page_content = $_REQUEST['thecontactdata'];

        // check if page exists (new page will have page_id > 0)
        if($page_id > 0)
        {
            try
            {
                $db = static::getDB();

                $sql = "UPDATE pages SET
                        page_content = :page_content
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':id' => $page_id,
                    ':page_content' => $page_content
                ];

                return $stmt->execute($parameters);
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }
        }
    }



    /**
     * updates Get Quote page in pages table
     *
     * @return boolean
     */
    public static function updateGetQuotePage()
    {
        $page_id = $_REQUEST['page_id'];
        $page_content = $_REQUEST['thegetquotedata'];

        // check if page exists (new page will have page_id > 0)
        if($page_id > 0)
        {
            try
            {
                $db = static::getDB();

                $sql = "UPDATE pages SET
                        page_content = :page_content
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':id' => $page_id,
                    ':page_content' => $page_content
                ];

                return $stmt->execute($parameters);
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }
        }
    }



    /**
     * updates Pay online page in pages table
     *
     * @return boolean
     */
    public static function updatePayonlinePage()
    {
        $page_id = $_REQUEST['page_id'];
        $page_content = $_REQUEST['thepayonlinedata'];

        // check if page exists (new page will have page_id > 0)
        if($page_id > 0)
        {
            try
            {
                $db = static::getDB();

                $sql = "UPDATE pages SET
                        page_content = :page_content
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':id' => $page_id,
                    ':page_content' => $page_content
                ];

                return $stmt->execute($parameters);
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }
        }
    }





}
