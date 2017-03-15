<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Page;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
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
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {

        // get home page data
        $content = Page::getHomePageData();

        View::renderTemplate('Home/index.html', [
            'content' => $content
        ]);
    }



    /**
     * updates About page in pages table
     *
     * @return void
     */
    public function updateHome()
    {
        // update about page
        Page::updateHomePage();
    }
}
