<?php

namespace A3plus;

class Email
{

    /**
     * ****************************************************
     * ***************    Dokumentation    ****************
     * ****************************************************
     *
     * Include Email Class :
     * require_once ("your/path/email.php");
     *
     *     Include Email Class :
     * require_once ("your/path/email.php");
     *
     * Work with Namespaces:
     * use A3plus\Email as Email;
     *
     * Initialize new Email:
     * $mail = new Email( "Sender E-Mail Address", "Sender Name", "Sender Message", "$_POST["a3SpamProtection"]");
     *
     * To show Errors use
     * $mail->getError
     *
     * Integrating the following field for spam hedging
     * <input type="hidden" name="a3SpamProtection" >
     */

    /**
     * ****************************************************
     * ***********    Variables / Settings    *************
     * ****************************************************
     */

    // E-Mail Address of Recipient
    protected $senderMail;

    // Name Address of Recipient
    protected $senderName;

    // Message Address of Recipient
    protected $senderMessage;

    // Field to check out Bots
    protected $spamField;

    //Mail of E-Mail Sender
    protected $recipientMail = "f.breuer@a3plus.de";

    //Short Mail Subject
    protected $mailSubject = "Kontaktformular Website";

    //Spamtimeout in Seconds
    protected $Spamtimeout = 60;

    //Error Handling
    protected $error;

    //Error Message for Spamfilter
    protected $errorSpamFilterMessage = "Spamfilter !!!";

    //Error Message for non Validate Sender Email
    protected $errorSenderMailMessage = "Sender E-Mail Adresse ist nicht valide";

    //Error Message for non Validate Sender Recipient
    protected $errorRecipientMailMessage = "Empfänger E-Mail Adresse ist nicht valide";

    // Other Settings

    //Wordwrap after number of Characters
    protected $characterPerLine = 70;

    /*
     * ****************************************************
     * *****************    Functions    ******************
     * ****************************************************
     */

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
        return filter_var($email, FILTER_VALIDATE_EMAIL) === TRUE;
    }


    public function isSpam()
    {
        if(strlen($this->spamField) > 0) {
            return TRUE;
        }

        if (isset($_SESSION['lastSendMail']) && $_SESSION['lastSendMail'] + $this->Spamtimeout <= time()) {
            return true;
        }
        $_SESSION['lastSendMail'] = time();

        return false;
    }

    public function mailBuilder()
    {
        if ($this->validateEmail($this->senderMail) === FALSE) {
            //Error Recipient Mail is no valid Email
            $this->setError($this->errorRecipientMailMessage);
            return;
        }

        if ($this->validateEmail($this->recipientMail) === FALSE) {
            //Error Recipient Mail is no valid Email
            $this->setError($this->errorSenderMailMessage);
            return;
        }

        if ($this->isSpam() === TRUE) {
            //Error Spamfilter
            $this->setError($this->errorSpamFilterMessage);
            return;
        }

        // Vaild Email, Ready to Send
        $this->sendMail();
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
