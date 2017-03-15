<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Page;
use \App\Mail;


/**
 * Contact controller
 *
 * PHP version 7.0
 */
class Contact extends \Core\Controller
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



    public function indexAction()
    {
       $content = Page::getContactPageData();

       // retrieve query string data
       $question = (isset($_REQUEST['question'])) ?  filter_var($_REQUEST['question'], FILTER_SANITIZE_STRING) : '';

       View::renderTemplate('Contact/index.html', [
          'content'       => $content,
          'contactindex'  => 'true',
          'question'      => $question
       ]);
    }


    public function submitContact()
    {
        //echo "Connected to submitContact method in Contact controller!<br>";

        unset($_SESSION['contacterror']);

        // set gate-keeper
        $okay = true;

        // retrieve data
        $question = (isset($_REQUEST['question'])) ?  filter_var($_REQUEST['question'], FILTER_SANITIZE_STRING) : '';
        $first_name = (isset($_REQUEST['first_name'])) ?  filter_var($_REQUEST['first_name'], FILTER_SANITIZE_STRING) : '';
        $last_name = (isset($_REQUEST['last_name'])) ?  filter_var($_REQUEST['last_name'], FILTER_SANITIZE_STRING) : '';
        $telephone = (isset($_REQUEST['telephone'])) ?  filter_var($_REQUEST['telephone'], FILTER_SANITIZE_NUMBER_INT) : '';
        $email = (isset($_REQUEST['email'])) ?  filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL) : '';
        $message = (isset($_REQUEST['message'])) ?  filter_var($_REQUEST['message'], FILTER_SANITIZE_STRING) : '';

        $honeypot = (isset($_REQUEST['honeypot'])) ?  filter_var($_REQUEST['honeypot'], FILTER_SANITIZE_STRING) : '';

        if($honeypot != '')
        {
           header("Location: /contact");
           exit();
        }

        // check for empty fields
        if($first_name === '' || $last_name === '' || $telephone === '' || $email === '' || $message === '')
        {
            $_SESSION['contacterror'] = "All fields are required";
            $okay = false;
            header("Location: /contact");
            exit();
        }

        if(filter_var($email, FILTER_SANITIZE_EMAIL === false))
        {
            $_SESSION['contacterror'] = "Please enter valid email address";
            $okay = false;
            header("Location: /contact");
            exit();
        }

        // test
        // echo $question . "<br>";
        // echo $first_name . "<br>";
        // echo $last_name . "<br>";
        // echo $telephone . "<br>";
        // echo $email . "<br>";
        // echo $message . "<br>";
        // exit();

        if($okay)
        {
            // call mailContactFormData method of Mail class & store boolean in $result
            $result = Mail::mailContactFormData($question, $first_name, $last_name, $telephone, $email, $message);

            // if successful display success message in view
            if ($result)
            {
                // display success message in view
                if($question == 'career')
                {
                    $message = "Your information was sent. Thank you for your
                    interest in a career with Newman's Ground Care. Afer reviewing
                    your qualifications, if we are interested, we will contact
                    you regarding the next step.";
                }
                else
                {
                    $message = "Your information was sent. We will contact you as soon
                    as possible";
                }


                View::renderTemplate('Success/index.html', [
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'message'    => $message
                ]);
            }
            else
            {
                echo 'Mailer error';
                exit();
            }
        }
    }




    public function updateContact()
    {
        // update contact page
        Page::updateContactPage();
    }



    public function getQuoteAction()
    {
      $content = Page::getGetQuoteData();

      View::renderTemplate('Contact/get-quote.html', [
         'content'       => $content,
         'contactindex'  => 'true'
      ]);
    }



    public function updateGetquote()
    {
        // update get quote page
        Page::updateGetQuotePage();
    }



    public function submitGetQuote()
    {
        //echo "Connected to submitContact method in Contact controller!<br>";

        unset($_SESSION['contacterror']);

        // set gate-keeper
        $okay = true;

        // retrieve data
        $first_name = (isset($_REQUEST['first_name'])) ?  filter_var($_REQUEST['first_name'], FILTER_SANITIZE_STRING) : '';
        $last_name = (isset($_REQUEST['last_name'])) ?  filter_var($_REQUEST['last_name'], FILTER_SANITIZE_STRING) : '';
        $telephone = (isset($_REQUEST['telephone'])) ?  filter_var($_REQUEST['telephone'], FILTER_SANITIZE_NUMBER_INT) : '';
        $email = (isset($_REQUEST['email'])) ?  filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL) : '';
        $message = (isset($_REQUEST['message'])) ?  filter_var($_REQUEST['message'], FILTER_SANITIZE_STRING) : '';

        $honeypot = (isset($_REQUEST['honeypot'])) ?  filter_var($_REQUEST['honeypot'], FILTER_SANITIZE_STRING) : '';

        if($honeypot != '')
        {
           header("Location: /contact/get-quote");
           exit();
        }

        // check for empty fields
        if($first_name === '' || $last_name === '' || $telephone === '' || $email === '' || $message === '')
        {
            $_SESSION['contacterror'] = "All fields are required";
            $okay = false;
            header("Location: /contact");
            exit();
        }

        if(filter_var($email, FILTER_SANITIZE_EMAIL === false))
        {
            $_SESSION['contacterror'] = "Please enter valid email address";
            $okay = false;
            header("Location: /contact");
            exit();
        }

        // echo $first_name . "<br>";
        // echo $last_name . "<br>";
        // echo $telephone . "<br>";
        // echo $email . "<br>";
        // echo $message . "<br>";
        // exit();

        if($okay)
        {
            // call mailContactFormData method of Mail class & store boolean in $result
            $result = Mail::mailGetQuoteFormData($first_name, $last_name, $telephone, $email, $message);

            // if successful display success message in view
            if ($result)
            {
                // display success message in view
                $message = "Your information was sent. We will contact you as soon
                as possible";

                View::renderTemplate('Success/index.html', [
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'message'    => $message
                ]);
            }
            else
            {
                echo 'Mailer error';
                exit();
            }
        }
    }
}
