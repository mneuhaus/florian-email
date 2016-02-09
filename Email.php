<?php

namespace A3plus;

class Email
{


    // **************************************************** //
    // ***************    Dokumentation    **************** //
    // **************************************************** //

    // Include Email Class :
    //require_once ("your/path/email.php");

    // Work with Namespaces:
    //use A3plus\Email as Email;

    // Initialize new Email:
    //$mail = new Email( "Sender E-Mail Address", "Sender Name", "Sender Message", "$_POST["a3SpamProtection"]");

    // To show Errors use
    // $mail->getError

    // Integrating the following field for spam hedging
    // <input type="hidden" name="a3SpamProtection" >


    // **************************************************** //
    // ***********    Variables / Settings    ************* //
    // **************************************************** //


    // E-Mail Address of Recipient
    private $senderMail;

    // Name Address of Recipient
    private $senderName;

    // Message Address of Recipient
    private $senderMessage;

    // Field to check out Bots
    private $spamField;

    //Mail of E-Mail Sender
    private $recipientMail = "f.breuer@a3plus.de";

    //Short Mail Subject
    private $mailSubject = "Kontaktformular Website";

    //Spamtimeout in Seconds
    private $Spamtimeout = 60;

    //Error Handling
    private $error;

    //Error Message for Spamfilter
    private $errorSpamFilterMessage = "Spamfilter !!!";

    //Error Message for non Validate Sender Email
    private $errorSenderMailMessage = "Sender E-Mail Adresse ist nicht valide";

    //Error Message for non Validate Sender Recipient
    private $errorRecipientMailMessage = "Empfänger E-Mail Adresse ist nicht valide";

    // Other Settings

    //Wordwrap after number of Characters
    private $characterPerLine = 70;



    // **************************************************** //
    // *****************    Functions    ****************** //
    // **************************************************** //


    public function __construct($email, $name, $message, $spamField)
    {
        $this->senderMail    = htmlspecialchars($email);
        $this->senderName    = htmlspecialchars($name);
        $this->senderMessage = wordwrap(htmlspecialchars($message), $this->characterPerLine, "<br />\n");
        $this->spamField     = htmlspecialchars($spamField);
        $this->mailBuilder();
    }

    public function validateEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }else{
            return false;
        }
    }


    public function spamFilter()
    {
        if($this->spamField == "") {
            if (!isset($_SESSION['lastSendMail']) || $_SESSION['lastSendMail'] + $this->Spamtimeout <= time()) {
                $_SESSION['lastSendMail'] = time();
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }

    public function mailBuilder()
    {
        if($this->validateEmail($this->senderMail)){

            if($this->validateEmail($this->recipientMail)){

                if($this->spamFilter()){
                    // Vaild Email, Ready to Send
                    $this->sendMail();
                }else{
                    //Error Spamfilter
                    $this->setError($this->errorSpamFilterMessage);
                }

            }else{
                //Error Recipient Mail is no valid Email
                $this->setError($this->errorRecipientMailMessage);
            }

        }else {
            //Error Sender Mail is no valid Email
            $this->setError($this->errorSenderMailMessage);
        }
    }


    // Error Handling
    public function getError()
    {
        echo $this->error;
    }

    public function setError($errorMessage)
    {
        $this->error = $errorMessage;
    }


    public function sendMail ()
    {

        $header  = 'MIME-Version: 1.0' . "\r\n";
        $header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $header .= 'From:' . $this->senderMail . "\r\n";
        $header .= 'Reply-To:' . $this->recipientMail . "\r\n";

        // Add Sender Name to Message
        $this->senderMessage = $this->senderName .":" . "<br />\n <br />\n" .  $this->senderMessage;

        mail($this->recipientMail, $this->mailSubject, $this->senderMessage, $header);

    }
}
?>
