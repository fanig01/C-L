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


     // CONNECTION FUNCTIONS  


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
        else{
            //Nothing should be done
        }

        if (empty($serverPort)) {
            $serverPort = $this->serverPortSmtp;
        }
        else{
            //Nothing should be done
        }

        $this->networkSocketSmtp = 
                fsockopen($serverHost, $serverPort, $numericError,
                
                        $messageError, $timeToGiveUp);
        
        // verify we connected properly
        if (empty($this->networkSocketSmtp)) {
            $this->errorOnLastCall = array("error" => "Failed to connect to server",
                "errno" => $numericError,
                "errstr" => $messageError);
            
            if ($this->setLevelDebug >= 1) {
                echo "SMTP -> ERROR: " . $this->errorOnLastCall["error"] .
                ": $messageError ($numericError)" . $this->endLine;
            }
            else{
                //Nothing should be done
            }
            
            return false;
        }
        else{
            //Nothing should be done
        }

        // Windows still does not have support for this timeout function
        if (substr(PHP_OS, 0, 3) != "WIN"){
            socket_set_timeout($this->networkSocketSmtp, $timeToGiveUp, 0);
        }
        else{
            //Nothing should be done
        }
        $announce = $this->get_lines();

        //if(function_exists("socket_set_timeout"))
        //   socket_set_timeout($this->networkSocketSmtp, 0, 100000);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $announce;
        }
        else{
            //Nothing should be done
        }

        return true;
    }

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
            else{
                //Nothing should be done
            }
            
            return false;
        }
        else{
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
            
            return false;
        }
        else {
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
            
            return false;
        }
        else {
            //Nothing should be done
        }

        return true;
    }

    function Connected() {
      
        if (!empty($this->networkSocketSmtp)) {
            $sock_status = socket_get_status($this->networkSocketSmtp);
        
            if ($sock_status["eof"]) {

                if ($this->setLevelDebug >= 1) {
                    echo "SMTP -> NOTICE:" . $this->endLine .
                    "EOF caught while checking if connected";
                }
                else {
                    //Nothing should be done
                }
                
                $this->Close();
                return false;
            }
            else{
                //Nothing should be done
            }
            
            return true;
        }
        else {
            //Nothing should be done
        }
        
        return false;
    }

    function Close() {

        $this->errorOnLastCall = null;
        $this->heloreply = null;
        
        if (!empty($this->networkSocketSmtp)) {

            fclose($this->networkSocketSmtp);
            $this->networkSocketSmtp = 0;
        }
        else{
            //Nothing should be done
        }
    }

    /* 
     *                        SMTP COMMANDS                       
     * SMTP CODE SUCCESS: 250
     * SMTP CODE FAILURE: 552,554,451,452
     * SMTP CODE FAILURE: 451,554
     * SMTP CODE ERROR  : 500,501,503,421
     */
    
    function Data($msg_data) {
    
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called Data() without being connected");
            return false;
        }
        else {
            //Nothing should be done
        }

        fputs($this->networkSocketSmtp, "DATA" . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }
        else {
            //Nothing should be done
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
            else{
                //Nothing should be done
            }
            return false;
        }
        else {
            //Nothing should be done
        }

        /* the server is ready to accept data!
         according to rfc 821 we should not send more than 1000
         including the endLine
         characters on a single line so we will break the data up
         into lines by \r and/or \n then if needed we will break
         each of those into smaller lines to fit within the limit.
         in addition we will be looking for lines that start with
         a period '.' and append and additional period '.' to that
         line. NOTE: this does not count towards are limit.
         normalize the line breaks so we know the explode works
       */
        
        $msg_data = str_replace("\r\n", "\n", $msg_data);
        $msg_data = str_replace("\r", "\n", $msg_data);
        $lines = explode("\n", $msg_data);

        $field = substr($lines[0], 0, strpos($lines[0], ":"));
        $in_headers = false;
        
        if (!empty($field) && !strstr($field, " ")) {
            $in_headers = true;
        }
        else{
            //Nothing should be done
        }

        $max_line_length = 998;

        while (list(, $line) = @each($lines)) {
            
            $lines_out = null;
            if ($line == "" && $in_headers) {
                $in_headers = false;
            }
            else {
                //Nothing should be done
            }
            
            while (strlen($line) > $max_line_length) {
                $pos = strrpos(substr($line, 0, $max_line_length), " ");

                if (!$pos) {
                    $pos = $max_line_length - 1;
                }
                else {
                    //Nothing should be done
                }

                $lines_out[] = substr($line, 0, $pos);
                $line = substr($line, $pos + 1);

                if ($in_headers) {
                    $line = "\t" . $line;
                }
                else {
                    //Nothing should be done
                }
            }
            $lines_out[] = $line;

            while (list(, $line_out) = @each($lines_out)) {
                
                if (strlen($line_out) > 0) {
                 
                    if (substr($line_out, 0, 1) == ".") {
                        $line_out = "." . $line_out;
                    }
                    else {
                        //Nothing should be done
                    }
                }
                else {
                    //Nothing should be done
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
        else {
            //Nothing should be done
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
            else{
                //Nothing should be done
            }
            
            return false;
        }
        else {
            //Nothing should be done
        }
        
        return true;
    }

    /*
     * SMTP CODE SUCCESS: 250, 211, 214, 552, 451, 452,  421
     * SMTP CODE FAILURE: 550
     * SMTP CODE ERROR  : 502,504,421
     */
    
    function Expand($name) {
    
        $this->errorOnLastCall = null; 

        if (!$this->connected()) {
            
            $this->errorOnLastCall = array(
                "error" => "Called Expand() without being connected");
            return false;
        }
        else {
            //Nothing should be done
        }

        fputs($this->networkSocketSmtp, "EXPN " . $name . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }
        else {
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
            
            return false;
        }
        else {
            //Nothing should be done
        }

        $entries = explode($this->endLine, $rply);
        
        while (list(, $l) = @each($entries)) {
            $list[] = substr($l, 4);
        }

        return $list;
    }

    function Hello($serverHost = "") {

        $this->errorOnLastCall = null;

        if (!$this->connected()) {
        
            $this->errorOnLastCall = array(
                "error" => "Called Hello() without being connected");
            return false;
        }
        else {
            //Nothing should be done
        }

        if (empty($serverHost)) {

            $serverHost = "localhost";
        }
        else {
            //Nothing should be done
        }

        if (!$this->SendHello("EHLO", $serverHost)) {
            
            if (!$this->SendHello("HELO", $serverHost)){
             
                return false;
            }
            else {
                //Nothing should be done
            }
        }
        else{
            //Nothing should be done
        }

        return true;
    }


    function SendHello($hello, $serverHost) {

        fputs($this->networkSocketSmtp, $hello . " " . $serverHost . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER: " . $this->endLine . $rply;
        }
        else {
            //Nothing should be done
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
            else{
                //Nothing should be done
            }
            
            return false;
        }
        else {
            //Nothing should be done
        }

        $this->heloreply = $rply;

        return true;
    }

    function Help($keyword = "") {

        $this->errorOnLastCall = null;

        if (!$this->connected()) {
        
            $this->errorOnLastCall = array(
                "error" => "Called Help() without being connected");
            return false;
        }
        else {
            //Nothing should be done
        }

        $extra = "";
        
        if (!empty($keyword)) {
            $extra = " " . $keyword;
        }
        else {
            //Nothing should be done
        }

        fputs($this->networkSocketSmtp, "HELP" . $extra . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }
        else {
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
            
            return false;
        }
        else {
            //Nothing should be done
        }

        return $rply;
    }

    function Mail($from) {

        $this->errorOnLastCall = null; 
        
        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called Mail() without being connected");
            
            return false;
        }
        else {
            //Nothing should be done
        }

        fputs($this->networkSocketSmtp, "MAIL FROM:<" . $from . ">" . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }
        else {
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
            
            return false;
        }
        else {
            //Nothing should be done
        }
        
        return true;
    }

    function Noop() {

        $this->errorOnLastCall = null;

        if (!$this->connected()) {
         
            $this->errorOnLastCall = array(
                "error" => "Called Noop() without being connected");
            return false;
        }
        else {
            //Nothing should be done
        }

        fputs($this->networkSocketSmtp, "NOOP" . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }
        else {
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
           
            return false;
        }
        else {
            //Nothing should be done
        }
  
        return true;
    }

    function Quit($close_on_error = true) {

        $this->errorOnLastCall = null;

        if (!$this->connected()) {
        
            $this->errorOnLastCall = array(
                "error" => "Called Quit() without being connected");
            return false;
        }
        else {
            //Nothing should be done
        }

        fputs($this->networkSocketSmtp, "quit" . $this->endLine);

        $$GoodByeMessage = $this->get_lines();

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $$GoodByeMessage;
        }
        else{
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
        }
        else {
            //Nothing should be done
        }

        if (empty($temporary) || $close_on_error) {
            $this->Close();
        }
        else {
            //Nothing should be done
        }

        return $replyValue;
    }

    /*
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

    /*
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500,501,504,421
     */
    
    function Reset() {
    
        $this->errorOnLastCall = null;
        
        if (!$this->connected()) {
        
            $this->errorOnLastCall = array(
                "error" => "Called Reset() without being connected");
            return false;
        }
        else {
            //Nothing should be done
        }

        fputs($this->networkSocketSmtp, "RSET" . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }
        else {
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
          
            return false;
        }
        else {
            //Nothing should be done
        }

        return true;
    }
    
    function Send($from) {
    
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
        
            $this->error = array(
                "error" => "Called Send() without being connected");
            return false;
        }
        else {
            //Nothing should be done
        }

        fputs($this->networkSocketSmtp, "SEND FROM:" . $from . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }
        else {
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
            
            return false;
        }
        else {
            //Nothing should be done
        }
        
        return true;
    }
    
    function SendAndMail($from) {
    
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
            $this->errorOnLastCall = array(
                "error" => "Called SendAndMail() without being connected");
            return false;
        }
        else {
            //Nothing should be done
        }

        fputs($this->networkSocketSmtp, "SAML FROM:" . $from . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }
        else {
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
            
            return false;
        }
        else {
            //Nothing should be done
        }
        
        return true;
    }

    function SendOrMail($from) {

        $this->errorOnLastCall = null;

        if (!$this->connected()) {
         
            $this->errorOnLastCall = array(
                "error" => "Called SendOrMail() without being connected");
            return false;
        }
        else {
            //Nothing should be done
        }

        fputs($this->networkSocketSmtp, "SOML FROM:" . $from . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }
        else {
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
            
            return false;
        }
        else {
            //Nothing should be done
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
     * SMTP CODE SUCCESS: 250,251
     * SMTP CODE FAILURE: 550,551,553, 502
     * SMTP CODE ERROR  : 500,501,502,421, 503
     */
    
    function Turn() {
    
        $this->errorOnLastCall = array("error" => "This method, TURN, of the SMTP " .
            "is not implemented");
        
        if ($this->setLevelDebug >= 1) {
            echo "SMTP -> NOTICE: " . $this->errorOnLastCall["error"] . $this->endLine;
        }
        else {
            //Nothing should be done
        }
        
        return false;
    }

    function Verify($name) {
  
        $this->errorOnLastCall = null;

        if (!$this->connected()) {
        
            $this->error = array(
                "error" => "Called Verify() without being connected");
            return false;
        }
        else {
           //Nothing should be done 
        }

        fputs($this->networkSocketSmtp, "VRFY " . $name . $this->endLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->setLevelDebug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->endLine . $rply;
        }
        else {
            //Nothing should be done
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
            else {
                //Nothing should be done
            }
            
            return false;
        }
        else {
            //Nothing should be done
        }
        
        return $rply;
    }

    function get_lines() {

        $date = "";
        
        while ($str = fgets($this->networkSocketSmtp, 515)) {
        
            if ($this->setLevelDebug >= 4) {
                echo "SMTP -> get_lines(): \$date was \"$date\"" .
                $this->endLine;
                echo "SMTP -> get_lines(): \$str is \"$str\"" .
                $this->endLine;
            }
            else {
                //Nothing should be done
            }
            
            $date .= $str;
           
            if ($this->setLevelDebug >= 4) {
                echo "SMTP -> get_lines(): \$date is \"$date\"" . $this->endLine;
            }
            else {
                //Nothing should be done
            }

            if (substr($str, 3, 1) == " ") {
            
                break;
            }
            else {
                //Nothing should be done
            }
        }
        
        return $date;
    }

}

?>
