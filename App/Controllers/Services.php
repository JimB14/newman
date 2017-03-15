<?php

namespace App\Controllers;

use \App\Models\Page;
use \Core\View;

/**
 * Services controller
 *
 * PHP 7.0
 */
class Services extends \Core\Controller
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
     * show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        // call method of Page model to retrieve data
        $content = Page::getServicesPageData();

        // display retrieved data in view
        View::renderTemplate('Services/index.html', [
            'content'       => $content,
            'servicesindex' => 'true'
        ]);
    }




    /**
     * updates About page in pages table
     *
     * @return void
     */
    public function updateServices()
    {
        // update services page
        Page::updateServicesPage();
    }

}
