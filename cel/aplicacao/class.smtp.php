<?php

/**
 * SMTP is rfc 821 compliant and implements all the rfc 821 SMTP
 * commands except TURN which will always return a not implemented
 * error. SMTP also provides some utility methods for sending mail
 * to an SMTP server.
 * @package PHPMailer
 * @author Chris Ryan
 */
class SMTP {

    //  @var int
    var $serverPortSmtp = 25;

    //  @var string
    var $endLine = "\r\n";

    //  @var bool
    var $setLevelDebug;

    // @access private
    var $networkSocketSmtp;
    var $errorOnLastCall;
    var $heloReply;

    // @access public
    // @return void
    function SMTP() {
        $this->networkSocketSmtp = 0;
        $this->errorOnLastCall = null;
        $this->heloreply = null;

        $this->setLevelDebug = 0;
    }

    /**************************************************************
     *                    CONNECTION FUNCTIONS                  *
     * ********************************************************* */

     /* SMTP CODE SUCCESS: 220
     * SMTP CODE FAILURE: 421
     * @access public
     * @return bool
     */
    function Connect($serverHost, $serverPort = 0, $timeToGiveUp = 30) {
        $this->errorOnLastCall = null;

        if ($this->connected()) {
            
            $this->errorOnLastCall =
                    array("error" => "Already connected to a server");
            
            return false;
        }

        if (empty($serverPort)) {
            $serverPort = $this->serverPortSmtp;
        }

        $this->networkSocketSmtp = 
                fsockopen($serverHost, $serverPort, $numericError,
                
                        $messageError, $timeToGiveUp);
        
        # verify we connected properly
        if (empty($this->networkSocketSmtp)) {
            $this->errorOnLastCall = array("error" => "Failed to connect to server",
                "errno" => $numericError,
                "errstr" => $messageError);
            
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": $messageError ($numericError)" . $this->endLine;
            }
            
            return false;
        }

        // Windows still does not have support for this timeout function
        if (substr(PHP_OS, 0, 3) != "WIN")
            socket_set_timeout($this->networkSocketSmtp, $timeToGiveUp, 0);

        $announce = $this->get_lines();

        //if(function_exists("socket_set_timeout"))
        //   socket_set_timeout($this->networkSocketSmtp, 0, 100000);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $announce;
        }

        return true;
    }

    // @access public
    // @return bool
    function Authenticate($username, $password) {

        fputs($this->networkSocketSmtp, "AUTH LOGIN" . $this->endLine);

        $reply = $this->get_lines();
        
        $code = substr($reply, 0, 3);

        if ($code != 334) {
            $this->errorOnLastCall =
                    array("error" => "AUTH not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($reply, 4));
            
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $reply . $this->endLine;
            }
            
            return false;
        }

        fputs($this->networkSocketSmtp, base64_encode($username) . $this->endLine);

        $reply = $this->get_lines();
        $code = substr($reply, 0, 3);

        if ($code != 334) {
            $this->errorOnLastCall =
                    array("error" => "Username not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($reply, 4));

            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $reply . $this->endLine;
            }
            
            return false;
        }

        fputs($this->networkSocketSmtp, base64_encode($password) . $this->endLine);

        $reply = $this->get_lines();
        $code = substr($reply, 0, 3);

        if ($code != 235) {
            $this->errorOnLastCall =
                    array("error" => "Password not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($reply, 4));
            
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $reply . $this->endLine;
            }
            
            return false;
        }

        return true;
    }

    // @access private
    // @return bool
    function Connected() {
        if (!empty($this->networkSocketSmtp)) {
            $sock_status = socket_get_status($this->networkSocketSmtp);
            if ($sock_status["eof"]) {

                if ($this->setLevelDebug >= 1) {
                    echo "SMTP -> NOTICE:" . $this->endLine .
                    "EOF caught while checking if connected";
                }
                
                $this->Close();
                return false;
            }
            
            return true;
        }
        
        return false;
    }

    // @access public
    // @return void
    function Close() {
        $this->errorOnLastCall = null;
        $this->heloreply = null;
        
        if (!empty($this->networkSocketSmtp)) {

            fclose($this->networkSocketSmtp);
            $this->networkSocketSmtp = 0;
        }
    }

    /* *************************************************************
                              SMTP COMMANDS                       
     * *********************************************************** */

    /**
     * SMTP CODE SUCCESS: 250
     * SMTP CODE FAILURE: 552,554,451,452
     * SMTP CODE FAILURE: 451,554
     * SMTP CODE ERROR  : 500,501,503,421
     * @access public
     * @return bool
     */
    function Data($msg_data) {
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called Data() without being connected");
            return false;
        }

        fputs($this->networkSocketSmtp, "DATA" . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 354) {
            $this->errorOnLastCall =
                    array("error" => "DATA command not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
      
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }

        # the server is ready to accept data!
        # according to rfc 821 we should not send more than 1000
        # including the endLine
        # characters on a single line so we will break the data up
        # into lines by \r and/or \n then if needed we will break
        # each of those into smaller lines to fit within the limit.
        # in addition we will be looking for lines that start with
        # a period '.' and append and additional period '.' to that
        # line. NOTE: this does not count towards are limit.
        # normalize the line breaks so we know the explode works
        $msg_data = str_replace("\r\n", "\n", $msg_data);
        $msg_data = str_replace("\r", "\n", $msg_data);
        $lines = explode("\n", $msg_data);

        $field = substr($lines[0], 0, strpos($lines[0], ":"));
        $in_headers = false;
        if (!empty($field) && !strstr($field, " ")) {
            $in_headers = true;
        }

        $max_line_length = 998;

        while (list(, $line) = @each($lines)) {
            $lines_out = null;
            if ($line == "" && $in_headers) {
                $in_headers = false;
            }
            
            while (strlen($line) > $max_line_length) {
                $pos = strrpos(substr($line, 0, $max_line_length), " ");

                # Patch to fix DOS attack
                if (!$pos) {
                    $pos = $max_line_length - 1;
                }

                $lines_out[] = substr($line, 0, $pos);
                $line = substr($line, $pos + 1);

                if ($in_headers) {
                    $line = "\t" . $line;
                }
            }
            $lines_out[] = $line;

            while (list(, $line_out) = @each($lines_out)) {
                if (strlen($line_out) > 0) {
                    if (substr($line_out, 0, 1) == ".") {
                        $line_out = "." . $line_out;
                    }
                }
                fputs($this->networkSocketSmtp, $line_out . $this->endLine);
            }
        }

        fputs($this->networkSocketSmtp, $this->endLine . "." . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 250) {
            $this->errorOnLastCall =
                    array("error" => "DATA not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }
        return true;
    }

    /**
     * Expand takes the name and asks the server to list all the
     * people who are members of the _list_. Expand will return
     * back and array of the result or false if an error occurs.
     * Each value in the array returned has the format of:
     *     [ <full-name> <sp> ] <path>
     * The definition of <path> is defined in rfc 821
     *
     * Implements rfc 821: EXPN <SP> <string> <endLine>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE FAILURE: 550
     * SMTP CODE ERROR  : 500,501,502,504,421
     * @access public
     * @return string array
     */
    function Expand($name) {
        $this->errorOnLastCall = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called Expand() without being connected");
            return false;
        }

        fputs($this->networkSocketSmtp, "EXPN " . $name . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 250) {
            $this->errorOnLastCall =
                    array("error" => "EXPN not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }

        $entries = explode($this->endLine, $rply);
        while (list(, $l) = @each($entries)) {
            $list[] = substr($l, 4);
        }

        return $list;
    }

    /**
     * Sends the HELO command to the smtp server.
     * This makes sure that we and the server are in
     * the same known state.
     *
     * Implements from rfc 821: HELO <SP> <domain> <endLine>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500, 501, 504, 421
     * @access public
     * @return bool
     */
    function Hello($serverHost = "") {
        $this->errorOnLastCall = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called Hello() without being connected");
            return false;
        }

        if (empty($serverHost)) {

            $serverHost = "localhost";
        }

        // Send extended hello first (RFC 2821)
        if (!$this->SendHello("EHLO", $serverHost)) {
            if (!$this->SendHello("HELO", $serverHost))
                return false;
        }

        return true;
    }

    /**
     * Sends a HELO/EHLO command.
     * @access private
     * @return bool
     */
    function SendHello($hello, $serverHost) {
        fputs($this->networkSocketSmtp, $hello . " " . $serverHost . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER: " . $this->endLine . $rply;
        }

        if ($code != 250) {
            $this->errorOnLastCall =
                    array("error" => $hello . " not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }

        $this->heloreply = $rply;

        return true;
    }

    /**
     * Gets help information on the keyword specified. If the keyword
     * is not specified then returns generic help, ussually contianing
     * A list of keywords that help is available on. This function
     * returns the results back to the user. It is up to the user to
     * handle the returned data. If an error occurs then false is
     * returned with $this->errorOnLastCall set appropiately.
     *
     * Implements rfc 821: HELP [ <SP> <string> ] <endLine>
     *
     * SMTP CODE SUCCESS: 211,214
     * SMTP CODE ERROR  : 500,501,502,504,421
     * @access public
     * @return string
     */
    function Help($keyword = "") {
        $this->errorOnLastCall = null; # to avoid confusion

        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called Help() without being connected");
            return false;
        }

        $extra = "";
        if (!empty($keyword)) {
            $extra = " " . $keyword;
        }

        fputs($this->networkSocketSmtp, "HELP" . $extra . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 211 && $code != 214) {
            $this->errorOnLastCall =
                    array("error" => "HELP not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }

        return $rply;
    }

    /**
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command.
     *
     * Implements rfc 821: MAIL <SP> FROM:<reverse-path> <endLine>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,421
     * @access public
     * @return bool
     */
    function Mail($from) {
        $this->errorOnLastCall = null; 
        
        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called Mail() without being connected");
            return false;
        }

        fputs($this->networkSocketSmtp, "MAIL FROM:<" . $from . ">" . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 250) {
            $this->errorOnLastCall =
                    array("error" => "MAIL not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }
        return true;
    }

    /**
     * Sends the command NOOP to the SMTP server.
     *
     * Implements from rfc 821: NOOP <endLine>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500, 421
     * @access public
     * @return bool
     */
    function Noop() {
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called Noop() without being connected");
            return false;
        }

        fputs($this->networkSocketSmtp, "NOOP" . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 250) {
            $this->errorOnLastCall =
                    array("error" => "NOOP not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }
        return true;
    }

    /**
     * Sends the quit command to the server and then closes the socket
     * if there is no error or the $close_on_error argument is true.
     *
     * Implements from rfc 821: QUIT <endLine>
     *
     * SMTP CODE SUCCESS: 221
     * SMTP CODE ERROR  : 500
     * @access public
     * @return bool
     */
    function Quit($close_on_error = true) {
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called Quit() without being connected");
            return false;
        }

        fputs($this->networkSocketSmtp, "quit" . $this->endLine);

 
        $$GoodByeMessage = $this->get_lines();

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $$GoodByeMessage;
        }

        $replyValue = true;
        $temporary = null;

        $code = substr($$GoodByeMessage, 0, 3);
        if ($code != 221) {
            
            $temporary = array("error" => "SMTP server rejected quit command",
                "smtp_code" => $code,
                "smtp_rply" => substr($$GoodByeMessage, 4));
            $replyValue = false;
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $temporary["error"] . ": " .
                $$GoodByeMessage . $this->endLine;
            }
        }

        if (empty($temporary) || $close_on_error) {
            $this->Close();
        }

        return $replyValue;
    }

    /**
     * Sends the command RCPT to the SMTP server with the TO: argument of $to.
     * Returns true if the recipient was accepted false if it was rejected.
     *
     * Implements from rfc 821: RCPT <SP> TO:<forward-path> <endLine>
     *
     * SMTP CODE SUCCESS: 250,251
     * SMTP CODE FAILURE: 550,551,552,553,450,451,452
     * SMTP CODE ERROR  : 500,501,503,421
     * @access public
     * @return bool
     */
    function Recipient($to) {
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called Recipient() without being connected");
            return false;
        }

        fputs($this->networkSocketSmtp, "RCPT TO:<" . $to . ">" . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 250 && $code != 251) {
            $this->errorOnLastCall =
                    array("error" => "RCPT not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }
        return true;
    }

    /**
     * Sends the RSET command to abort and transaction that is
     * currently in progress. Returns true if successful false
     * otherwise.
     *
     * Implements rfc 821: RSET <endLine>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500,501,504,421
     * @access public
     * @return bool
     */
    function Reset() {
        $this->errorOnLastCall = null;
        
        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called Reset() without being connected");
            return false;
        }

        fputs($this->networkSocketSmtp, "RSET" . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 250) {
            $this->errorOnLastCall =
                    array("error" => "RSET failed",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }

        return true;
    }

    /**
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command. This command
     * will send the message to the users terminal if they are logged
     * in.
     *
     * Implements rfc 821: SEND <SP> FROM:<reverse-path> <endLine>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,502,421
     * @access public
     * @return bool
     */
    function Send($from) {
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Send() without being connected");
            return false;
        }

        fputs($this->networkSocketSmtp, "SEND FROM:" . $from . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 250) {
            $this->errorOnLastCall =
                    array("error" => "SEND not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }
        return true;
    }

    /**
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command. This command
     * will send the message to the users terminal if they are logged
     * in and send them an email.
     *
     * Implements rfc 821: SAML <SP> FROM:<reverse-path> <endLine>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,502,421
     * @access public
     * @return bool
     */
    function SendAndMail($from) {
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called SendAndMail() without being connected");
            return false;
        }

        fputs($this->networkSocketSmtp, "SAML FROM:" . $from . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 250) {
            $this->errorOnLastCall =
                    array("error" => "SAML not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }
        return true;
    }

    /**
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command. This command
     * will send the message to the users terminal if they are logged
     * in or mail it to them if they are not.
     *
     * Implements rfc 821: SOML <SP> FROM:<reverse-path> <endLine>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,502,421
     * @access public
     * @return bool
     */
    function SendOrMail($from) {
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called SendOrMail() without being connected");
            return false;
        }

        fputs($this->networkSocketSmtp, "SOML FROM:" . $from . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 250) {
            $this->errorOnLastCall =
                    array("error" => "SOML not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }
        return true;
    }

    /**
     * This is an optional command for SMTP that this class does not
     * support. This method is here to make the RFC821 Definition
     * complete for this class and __may__ be implimented in the future
     *
     * Implements from rfc 821: TURN <endLine>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE FAILURE: 502
     * SMTP CODE ERROR  : 500, 503
     * @access public
     * @return bool
     */
    function Turn() {
        $this->errorOnLastCall = array("error" => "This method, TURN, of the SMTP " .
            "is not implemented");
        if ($this->setLevelDebug >= 1) {
            echo "SMTP -> NOTICE: " . $this->errorOnLastCall["error"] . $this->endLine;
        }
        return false;
    }

    /**
     * Verifies that the name is recognized by the server.
     * Returns false if the name could not be verified otherwise
     * the response from the server is returned.
     *
     * Implements rfc 821: VRFY <SP> <string> <endLine>
     *
     * SMTP CODE SUCCESS: 250,251
     * SMTP CODE FAILURE: 550,551,553
     * SMTP CODE ERROR  : 500,501,502,421
     * @access public
     * @return int
     */
    function Verify($name) {
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Verify() without being connected");
            return false;
        }

        fputs($this->networkSocketSmtp, "VRFY " . $name . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }

        if ($code != 250 && $code != 251) {
            $this->errorOnLastCall =
                    array("error" => "VRFY failed on name '$name'",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": " . $rply . $this->endLine;
            }
            return false;
        }
        return $rply;
    }

    /*     * *****************************************************************
     *                       INTERNAL FUNCTIONS                       *
     * **************************************************************** */

    /**
     * Read in as many lines as possible
     * either before eof or socket timeout occurs on the operation.
     * With SMTP we can tell if we have more lines to read if the
     * 4th character is '-' symbol. If it is a space then we don't
     * need to read anything else.
     * @access private
     * @return string
     */
    function get_lines() {
        $data = "";
        while ($str = fgets($this->networkSocketSmtp, 515)) {
            if ($this->setLevelDebug >= 4) {
                echo "SMTP -> get_lines(): \$data was \"$data\"" .
                $this->endLine;
                echo "SMTP -> get_lines(): \$str is \"$str\"" .
                $this->endLine;
            }
            $data .= $str;
            if ($this->setLevelDebug >= 4) {
                echo "SMTP -> get_lines(): \$data is \"$data\"" . $this->endLine;
            }

            if (substr($str, 3, 1) == " ") {
                break;
            }
        }
        return $data;
    }

}

?>
