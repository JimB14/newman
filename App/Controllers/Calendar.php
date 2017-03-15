<?php

namespace App\Controllers;

use \App\Models\Page;
use \Core\View;

/**
 * Calendar controller
 *
 * PHP version 7.0
 */
class Calendar extends \Core\Controller
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
        $content = Page::getCalendarPageData();

        // display retrieved data in view
        View::renderTemplate('Calendar/index.html', [
              'content'     => $content,
              'aboutindex'  => 'true'
        ]);
    }



    /**
     * updates About page in pages table
     *
     * @return void
     */
    public function updateCalendar()
    {
        // update about page
        Page::updateCalendarPage();
    }
}
