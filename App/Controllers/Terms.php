<?php

namespace App\Controllers;

use \App\Models\Page;
use \Core\View;

/**
 * Calendar controller
 *
 * PHP version 7.0
 */
class Terms extends \Core\Controller
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
        // call method of Page class/oject to retrieve About page data from
        // pages table & store in $content
        // $content = Page::getCalendarPageData();

        // display retrieved data in view
        View::renderTemplate('Terms/index.html', [
              'termsindex'  => 'true'
        ]);
    }

}
