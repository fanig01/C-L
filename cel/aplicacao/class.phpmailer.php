<?php

/**
 * PHPMailer - PHP email transport class
 * @package PHPMailer
 * @author Brent R. Matzelle
 * @copyright 2001 - 2003 Brent R. Matzelle
 */
class PHPMailer {
    // PUBLIC VARIABLES

    /**
     * Email priority (1 = High, 3 = Normal, 5 = low).
     * @var int
     */
    var $Priority = 3;

    /**
     * @var string
     */
    var $CharSet = "iso-8859-1";

    /**
     * @var string
     */
    var $ContentType = "text/plain";

    /**
     * Options for this are "8bit",
     * "7bit", "binary", "base64", and "quoted-printable".
     * @var string
     */
    var $Encoding = "8bit";

    /**
     * @var string
     */
    var $ErrorInfo = "";

    /**
     * @var string
     */
    var $From = "root@localhost";

    /**
     * @var string
     */
    var $FromName = "Root User";

    /**
     * If not empty, will be sent via -f to sendmail or as 'MAIL FROM' in smtp mode.
     * @var string
     */
    var $Sender = "";

    /**
     * @var string
     */
    var $Subject = "";

    /**
     * If HTML then run IsHTML(true).
     * @var string
     */
    var $Body = "";

    /**
     * Sets the text-only body of the message.  This automatically sets the
     * email to multipart/alternative.  This body can be read by mail
     * clients that do not have HTML email capability such as mutt. Clients
     * that can read HTML will view the normal Body.
     * @var string
     */
    var $AltBody = "";

    /**
     * @var int
     */
    var $WordWrap = 0;

    /**
     * @var string
     */
    var $Mailer = "mail";

    /**
     * @var string
     */
    var $Sendmail = "/usr/sbin/sendmail";

    /**
     * @var string
     */
    var $PluginDir = "";

    /**
     *  @var string
     */
    var $Version = "1.73";

    /**
     * @var string
     */
    var $ConfirmReadingTo = "";

    /**
     *  If empty, the value returned by SERVER_NAME is used or 
     *  'localhost.localdomain'.
     *  @var string
     */
    var $Hostname = "";

    // SMTP VARIABLES

    /**
     *  Sets the SMTP hosts.  All hosts must be separated by a
     *  semicolon.  You can also specify a different port
     *  for each host by using this format: [hostname:port]
     *  (e.g. "smtp1.example.com:25;smtp2.example.com").
     *  Hosts will be tried in order.
     *  @var string
     */
    var $Host = "localhost";

    /**
     *  @var int
     */
    var $Port = 25;

    /**
     *  @var string
     */
    var $Helo = "";

    /**
     *  @var bool
     */
    var $SMTPAuth = false;

    /**
     *  @var string
     */
    var $Username = "";

    /**
     *  @var string
     */
    var $Password = "";

    /**
     *  @var int
     */
    var $Timeout = 10;

    /**
     *  @var bool
     */
    var $SMTPDebug = false;

    /** 
     * @var bool
     */
    var $SMTPKeepAlive = false;

    /*     * #@+
     * @access private
     */
    var $smtp = NULL;
    var $to = array();
    var $cc = array();
    var $bcc = array();
    var $ReplyTo = array();
    var $attachment = array();
    var $CustomHeader = array();
    var $message_type = "";
    var $boundary = array();
    var $language = array();
    var $error_count = 0;
    var $LE = "\n";

    /*     * #@- */

    // VARIABLE METHODS

    /**
     * @param bool $bool
     * @return void
     */
    function IsHTML($bool) {
        
        if ($bool == true) {
            
            $this->ContentType = "text/html";
            
        } else {
            
            $this->ContentType = "text/plain";
            
        }
    }

    /**
     * @return void
     */
    function IsSMTP() {
        
        $this->Mailer = "smtp";
        
    }

    /**
     * @return void
     */
    function IsMail() {
        
        $this->Mailer = "mail";
        
    }

    /**
     * @return void
     */
    function IsSendmail() {
        
        $this->Mailer = "sendmail";
        
    }

    /**
     * @return void
     */
    function IsQmail() {
        
        $this->Sendmail = "/var/qmail/bin/sendmail";
        $this->Mailer = "sendmail";
        
    }

    // RECIPIENT METHODS

    /**
     * @param string $address
     * @param string $name
     * @return void
     */
    function AddAddress($address, $name = "") {
        
        $cur = count($this->to);
        $this->to[$cur][0] = trim($address);
        $this->to[$cur][1] = $name;
        
    }

    /**
     * @param string $address
     * @param string $name
     * @return void
     */
    function AddCC($address, $name = "") {
        
        $cur = count($this->cc);
        $this->cc[$cur][0] = trim($address);
        $this->cc[$cur][1] = $name;
        
    }

    /**
     * @param string $address
     * @param string $name
     * @return void
     */
    function AddBCC($address, $name = "") {
        
        $cur = count($this->bcc);
        $this->bcc[$cur][0] = trim($address);
        $this->bcc[$cur][1] = $name;
        
    }

    /** 
     * @param string $address
     * @param string $name
     * @return void
     */
    function AddReplyTo($address, $name = "") {
        
        $cur = count($this->ReplyTo);
        $this->ReplyTo[$cur][0] = trim($address);
        $this->ReplyTo[$cur][1] = $name;
        
    }

    // MAIL SENDING METHODS

    /**
     * @return bool
     */
    function Send() {
        
        $header = "";
        $body = "";
        $result = true;

        if ((count($this->to) + count($this->cc) + count($this->bcc)) < 1) {
            
            $this->SetError($this->Lang("provide_address"));
            return false;
            
        } else {
            // Not should be done
        }

        // Set whether the message is multipart/alternative
        if (!empty($this->AltBody)) {
            
            $this->ContentType = "multipart/alternative";
            
        } else {
            // Not should be done
        }

        $this->error_count = 0;
        $this->SetMessageType();
        $header .= $this->CreateHeader();
        $body = $this->CreateBody();

        if ($body == "") {
            
            return false;
            
        } else {
            // Not should be done
        }

        switch ($this->Mailer) {
            
            case "sendmail":
                $result = $this->SendmailSend($header, $body);
                break;
            
            case "mail":
                $result = $this->MailSend($header, $body);
                break;
            
            case "smtp":
                $result = $this->SmtpSend($header, $body);
                break;
            
            default:
                $this->SetError($this->Mailer . $this->Lang("mailer_not_supported"));
                $result = false;
                break;
        }

        return $result;
    }

    /**  
     * @access private
     * @return bool
     */
    function SendmailSend($header, $body) {
        
        if ($this->Sender != "") {
            
            $sendmail = sprintf("%s -oi -f %s -t", $this->Sendmail, $this->Sender);
            
        } else {
            
            $sendmail = sprintf("%s -oi -t", $this->Sendmail);
        }

        if (!@$mail = popen($sendmail, "w")) {
            
            $this->SetError($this->Lang("execute") . $this->Sendmail);
            return false;
            
        } else {
            // Not should be done
        }

        fputs($mail, $header);
        fputs($mail, $body);

        $result = pclose($mail) >> 8 & 0xFF;
        
        if ($result != 0) {
            
            $this->SetError($this->Lang("execute") . $this->Sendmail);
            return false;
            
        } else {
            // Not should be done
        }
        
        return true;
    }

    /**
     * @access private
     * @return bool
     */
    function MailSend($header, $body) {
        
        $to = "";
        
        for ($i = 0; $i < count($this->to); $i++) {
            
            if ($i != 0) {
                
                $to .= ", ";
                
            } else {
                // Not should be done
            }
            
            $to .= $this->to[$i][0];
        }

        if ($this->Sender != "" && strlen(ini_get("safe_mode")) < 1) {
            
            $old_from = ini_get("sendmail_from");
            ini_set("sendmail_from", $this->Sender);
            $params = sprintf("-oi -f %s", $this->Sender);
            $rt = @mail($to, $this->EncodeHeader($this->Subject), $body, $header, $params);
            
        } else {
        
            $rt = @mail($to, $this->EncodeHeader($this->Subject), $body, $header);
        }

        if (isset($old_from)) {
            ini_set("sendmail_from", $old_from);
            
        } else {
            // Not should be done
        }

        if (!$rt) {
            
            $this->SetError($this->Lang("instantiate"));
            return false;
            
        } else {
            // Not should be done
        }

        return true;
    }

    /**
     * @access private
     * @return bool
     */
    function SmtpSend($header, $body) {
        
        include_once($this->PluginDir . "class.smtp.php");
        $error = "";
        $bad_rcpt = array();

        if (!$this->SmtpConnect()) {
            return false;
            
        } else {
            // Not should be done
        }

        $smtp_from = ($this->Sender == "") ? $this->From : $this->Sender;
        
        if (!$this->smtp->Mail($smtp_from)) {
            $error = $this->Lang("from_failed") . $smtp_from;
            $this->SetError($error);
            $this->smtp->Reset();
            
            return false;
            
        } else {
            // Not should be done
            
        }

        for ($i = 0; $i < count($this->to); $i++) {
            
            if (!$this->smtp->Recipient($this->to[$i][0])) {
                $bad_rcpt[] = $this->to[$i][0];
                
            } else {
                // Not should be done
            }
        }
        
        for ($i = 0; $i < count($this->cc); $i++) {
            
            if (!$this->smtp->Recipient($this->cc[$i][0])) {
                $bad_rcpt[] = $this->cc[$i][0];
                
            } else {
                // Not should be done
            }
        }
        
        for ($i = 0; $i < count($this->bcc); $i++) {
            
            if (!$this->smtp->Recipient($this->bcc[$i][0])) {
                $bad_rcpt[] = $this->bcc[$i][0];
                
            } else {
                // Not should be done
            }
        }

        if (count($bad_rcpt) > 0) {
            
            for ($i = 0; $i < count($bad_rcpt); $i++) {
                
                if ($i != 0) {
                    $error .= ", ";
                    
                } else {
                    // Not should be done
                }
                
                $error .= $bad_rcpt[$i];
            }
            
            $error = $this->Lang("recipients_failed") . $error;
            $this->SetError($error);
            $this->smtp->Reset();
            
            return false;
        }

        if (!$this->smtp->Data($header . $body)) {
            
            $this->SetError($this->Lang("data_not_accepted"));
            $this->smtp->Reset();
            
            return false;
            
        } else {
            // Not should be done
        }
        
        if ($this->SMTPKeepAlive == true) {
            $this->smtp->Reset();
            
        } else {
            $this->SmtpClose();
        }

        return true;
    }

    /**
     * @access private
     * @return bool
     */
    function SmtpConnect() {
        if ($this->smtp == NULL) {
            $this->smtp = new SMTP();
            
        } else {
            // Not should be done
        }

        $this->smtp->do_debug = $this->SMTPDebug;
        $hosts = explode(";", $this->Host);
        $index = 0;
        $connection = ($this->smtp->Connected());

        while ($index < count($hosts) && $connection == false) {
            
            if (strstr($hosts[$index], ":")) {
                
                list($host, $port) = explode(":", $hosts[$index]);
                
            } else {
                $host = $hosts[$index];
                $port = $this->Port;
            }

            if ($this->smtp->Connect($host, $port, $this->Timeout)) {
                
                if ($this->Helo != '') {
                    $this->smtp->Hello($this->Helo);
                    
                } else {
                    
                    $this->smtp->Hello($this->ServerHostname());
                }

                if ($this->SMTPAuth) {
                    
                    if (!$this->smtp->Authenticate($this->Username, $this->Password)) {
                        
                        $this->SetError($this->Lang("authenticate"));
                        $this->smtp->Reset();
                        $connection = false;
                        
                    } else {
                        // not should be done
                    }
                    
                } else {
                    // Not should be done
                }
                
                $connection = true;
            } else {
                // Not should be done
            }
            
            $index++;
        }
        
        if (!$connection) {
            $this->SetError($this->Lang("connect_host"));
        } else {
            // not should be done
        }

        return $connection;
    }

    /**
     * @return void
     */
    function SmtpClose() {
        
        if ($this->smtp != NULL) {
            
            if ($this->smtp->Connected()) {
                
                $this->smtp->Quit();
                $this->smtp->Close();
                
            } else {
                // not should be done
            }
            
        } else {
            // Not should be done
        }
    }

    /**
     * @param string $lang_type Type of language (e.g. Portuguese: "br")
     * @param string $lang_path Path to the language file directory
     * @access public
     * @return bool
     */
    function SetLanguage($lang_type, $lang_path = "language/") {
        
        if (file_exists($lang_path . 'phpmailer.lang-' . $lang_type . '.php')) {
            
            include($lang_path . 'phpmailer.lang-' . $lang_type . '.php');
            
        } else if (file_exists($lang_path . 'phpmailer.lang-en.php')) {
            
            include($lang_path . 'phpmailer.lang-en.php');
            
        } else {
            
            $this->SetError("Could not load language file");
        }
            return false;
    
        $this->language = $PHPMAILER_LANG;

        return true;
    }

    // MESSAGE CREATION METHODS

    /**
     * @access private
     * @return string
     */
    function AddrAppend($type, $addr) {
        
        $addr_str = $type . ": ";
        $addr_str .= $this->AddrFormat($addr[0]);
        
        if (count($addr) > 1) {
            
            for ($i = 1; $i < count($addr); $i++) {
                
                $addr_str .= ", " . $this->AddrFormat($addr[$i]);
            }
            
        } else {
            // Not should be done
        }
        
        $addr_str .= $this->LE;

        return $addr_str;
    }

    /**
     * @access private
     * @return string
     */
    function AddrFormat($addr) {
        
        if (empty($addr[1])) {
            
            $formatted = $addr[0];
            
        } else {
            
            $formatted = $this->EncodeHeader($addr[1], 'phrase') . " <" .
                    $addr[0] . ">";
        }

        return $formatted;
    }

    /**
     * @access private
     * @return string
     */
    function WrapText($message, $length, $qp_mode = false) {
        $soft_break = ($qp_mode) ? sprintf(" =%s", $this->LE) : $this->LE;
        $message = $this->FixEOL($message);
        
        if (substr($message, -1) == $this->LE) {
            $message = substr($message, 0, -1);
            
        } else {
            // Not should be done
        }

        $line = explode($this->LE, $message);
        $message = "";
        
        for ($i = 0; $i < count($line); $i++) {
            
            $line_part = explode(" ", $line[$i]);
            $buf = "";
            
            for ($e = 0; $e < count($line_part); $e++) {
                
                $word = $line_part[$e];
                
                if ($qp_mode and (strlen($word) > $length)) {
                    
                    $space_left = $length - strlen($buf) - 1;
                    
                    if ($e != 0) {
                        
                        if ($space_left > 20) {
                            
                            $len = $space_left;
                            
                            if (substr($word, $len - 1, 1) == "=") {
                                
                                $len--;
                                
                            } else if (substr($word, $len - 2, 1) == "=") {
                                
                                $len -= 2;
                                
                            } else {
                                // not should be done
                            }
                            
                            $part = substr($word, 0, $len);
                            $word = substr($word, $len);
                            $buf .= " " . $part;
                            $message .= $buf . sprintf("=%s", $this->LE);
                            
                        } else {
                            
                            $message .= $buf . $soft_break;
                        }
                        
                        $buf = "";
                        
                    } else {
                        // Not should be done
                    }
                    
                    while (strlen($word) > 0) {
                        
                        $len = $length;
                        
                        if (substr($word, $len - 1, 1) == "=") {
                            
                            $len--;
                            
                        } else if (substr($word, $len - 2, 1) == "=") {
                            
                            $len -= 2;
                            
                        } else {
                            // Not should be done
                        }
                        
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);

                        if (strlen($word) > 0) {
                            
                            $message .= $part . sprintf("=%s", $this->LE);
                            
                        } else {
                            
                            $buf = $part;
                        }
                    }
                    
                } else {
                    
                    $buf_o = $buf;
                    $buf .= ($e == 0) ? $word : (" " . $word);

                    if (strlen($buf) > $length and $buf_o != "") {
                        
                        $message .= $buf_o . $soft_break;
                        $buf = $word;
                        
                    } else {
                        // Not should be done
                    }
                }
            }
            $message .= $buf . $this->LE;
        }

        return $message;
    }

    /**
     * @access private
     * @return void
     */
    function SetWordWrap() {
        
        if ($this->WordWrap < 1) {
            
            return;
            
        } else {
            // Not should be done
        }

        switch ($this->message_type) {
            
            case "alt":
                // fall through
                
            case "alt_attachments":
                $this->AltBody = $this->WrapText($this->AltBody, $this->WordWrap);
                break;
            
            default:
                $this->Body = $this->WrapText($this->Body, $this->WordWrap);
                break;
        }
    }

    /**
     * @access private
     * @return string
     */
    function CreateHeader() {
        $result = "";

        $uniq_id = md5(uniqid(time()));
        $this->boundary[1] = "b1_" . $uniq_id;
        $this->boundary[2] = "b2_" . $uniq_id;

        $result .= $this->HeaderLine("Date", $this->RFCDate());
        
        if ($this->Sender == "") {
            
            $result .= $this->HeaderLine("Return-Path", trim($this->From));
            
        } else {
            
            $result .= $this->HeaderLine("Return-Path", trim($this->Sender));
        }

        if ($this->Mailer != "mail") {
            
            if (count($this->to) > 0) {
                
                $result .= $this->AddrAppend("To", $this->to);
                
            } else if (count($this->cc) == 0) {
                
                $result .= $this->HeaderLine("To", "undisclosed-recipients:;");
                
            } else {
                // not should be done
            }
            
            if (count($this->cc) > 0) {
                
                $result .= $this->AddrAppend("Cc", $this->cc);
                
            } else {
                // not should be done
            }
            
        } else {
                // not should be done
        }

        $from = array();
        $from[0][0] = trim($this->From);
        $from[0][1] = $this->FromName;
        $result .= $this->AddrAppend("From", $from);

        if ((($this->Mailer == "sendmail") || ($this->Mailer == "mail")) && 
                (count($this->bcc) > 0)) {
            
            $result .= $this->AddrAppend("Bcc", $this->bcc);
            
        } else {
                // not should be done
        }

        if (count($this->ReplyTo) > 0) {
            
            $result .= $this->AddrAppend("Reply-to", $this->ReplyTo);
            
        } else {
                // not should be done
        }

        if ($this->Mailer != "mail") {
            
            $result .= $this->HeaderLine("Subject", $this->EncodeHeader(trim($this->Subject)));
            
        } else {
                // not should be done
        }

        $result .= sprintf("Message-ID: <%s@%s>%s", $uniq_id, $this->ServerHostname(), $this->LE);
        $result .= $this->HeaderLine("X-Priority", $this->Priority);
        $result .= $this->HeaderLine("X-Mailer", "PHPMailer [version " . $this->Version . "]");

        if ($this->ConfirmReadingTo != "") {
            
            $result .= $this->HeaderLine("Disposition-Notification-To", "<" . trim($this->ConfirmReadingTo) . ">");
            
        } else {
                // not should be done
        }

        for ($index = 0; $index < count($this->CustomHeader); $index++) {
            
            $result .= $this->HeaderLine(trim($this->CustomHeader[$index][0]), $this->EncodeHeader(trim($this->CustomHeader[$index][1])));
        }
        
        $result .= $this->HeaderLine("MIME-Version", "1.0");

        switch ($this->message_type) {
            
            case "plain":
                $result .= $this->HeaderLine("Content-Transfer-Encoding", $this->Encoding);
                $result .= sprintf("Content-Type: %s; charset=\"%s\"", $this->ContentType, $this->CharSet);
                break;
            
            case "attachments":
            // fall through
                
            case "alt_attachments":
                if ($this->InlineImageExists()) {
                    
                    $result .= sprintf("Content-Type: %s;%s\ttype=\"text/html\";%s\tboundary=\"%s\"%s", 
                            "multipart/related", $this->LE, $this->LE, $this->boundary[1], $this->LE);
                    
                } else {
                    
                    $result .= $this->HeaderLine("Content-Type", "multipart/mixed;");
                    $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
                }
                
                break;
                
            case "alt":
                $result .= $this->HeaderLine("Content-Type", "multipart/alternative;");
                $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
        }

        if ($this->Mailer != "mail") {
            
            $result .= $this->LE . $this->LE;
            
        } else {
                // not should be done
        }

        return $result;
    }

    /**
     * @access private
     * @return string
     */
    function CreateBody() {
        $result = "";

        $this->SetWordWrap();

        switch ($this->message_type) {
            
            case "alt":
                $result .= $this->GetBoundary($this->boundary[1], "", "text/plain", "");
                $result .= $this->EncodeString($this->AltBody, $this->Encoding);
                $result .= $this->LE . $this->LE;
                $result .= $this->GetBoundary($this->boundary[1], "", "text/html", "");

                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE . $this->LE;

                $result .= $this->EndBoundary($this->boundary[1]);
                break;
            
            case "plain":
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                break;
            
            case "attachments":
                $result .= $this->GetBoundary($this->boundary[1], "", "", "");
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE;

                $result .= $this->AttachAll();
                break;
            
            case "alt_attachments":
                $result .= sprintf("--%s%s", $this->boundary[1], $this->LE);
                $result .= sprintf("Content-Type: %s;%s" .
                        "\tboundary=\"%s\"%s", "multipart/alternative", $this->LE, $this->boundary[2], $this->LE . $this->LE);

                $result .= $this->GetBoundary($this->boundary[2], "", "text/plain", "") . $this->LE;
                $result .= $this->EncodeString($this->AltBody, $this->Encoding);
                $result .= $this->LE . $this->LE;

                $result .= $this->GetBoundary($this->boundary[2], "", "text/html", "") . $this->LE;
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE . $this->LE;

                $result .= $this->EndBoundary($this->boundary[2]);
                $result .= $this->AttachAll();
                break;
            
        }
        
        if ($this->IsError()) {
            $result = "";
            
        } else {
                // not should be done
        }

        return $result;
    }

    /**
     * @access private
     */
    function GetBoundary($boundary, $charSet, $contentType, $encoding) {
        
        $result = "";
        
        if ($charSet == "") {
            $charSet = $this->CharSet;
            
        } else {
                // not should be done
        }
        
        if ($contentType == "") {
            $contentType = $this->ContentType;
            
        } else {
                // not should be done
        }
        
        if ($encoding == "") {
            $encoding = $this->Encoding;
            
        } else {
                // not should be done
        }

        $result .= $this->TextLine("--" . $boundary);
        $result .= sprintf("Content-Type: %s; charset = \"%s\"", $contentType, $charSet);
        $result .= $this->LE;
        $result .= $this->HeaderLine("Content-Transfer-Encoding", $encoding);
        $result .= $this->LE;

        return $result;
    }

    /**
     * @access private
     */
    function EndBoundary($boundary) {
        return $this->LE . "--" . $boundary . "--" . $this->LE;
        
    }

    /**
     * @access private
     * @return void
     */
    function SetMessageType() {
        if (count($this->attachment) < 1 && strlen($this->AltBody) < 1) {
            $this->message_type = "plain";
            
        } else {
            if (count($this->attachment) > 0) {
                $this->message_type = "attachments";
                
            } else if (strlen($this->AltBody) > 0 && count($this->attachment) < 1) {
                $this->message_type = "alt";
                
            } else if (strlen($this->AltBody) > 0 && count($this->attachment) > 0) {
                $this->message_type = "alt_attachments";
                
            } else {
                // not should be done
            }
        }
    }

    /**
     * @access private
     * @return string
     */
    function HeaderLine($name, $value) {
        return $name . ": " . $value . $this->LE;
        
    }

    /**
     * @access private
     * @return string
     */
    function TextLine($value) {
        return $value . $this->LE;
        
    }

    // ATTACHMENT METHODS

    /**
     * @param string $path Path to the attachment.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @return bool
     */
    function AddAttachment($path, $name = "", $encoding = "base64", $type = "application/octet-stream") {
        if (!@is_file($path)) {
            $this->SetError($this->Lang("file_access") . $path);
            return false;
            
        } else {
            // Not should be done
            
        }

        $filename = basename($path);
        if ($name == "") {
            $name = $filename;
            
        } else {
            // Not should be done
            
        }

        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false; // isStringAttachment
        $this->attachment[$cur][6] = "attachment";
        $this->attachment[$cur][7] = 0;

        return true;
    }

    /**
     * @access private
     * @return string
     */
    function AttachAll() {

        $mime = array();

        for ($i = 0; $i < count($this->attachment); $i++) {

            $bString = $this->attachment[$i][5];
            
            if ($bString) {
                $string = $this->attachment[$i][0];
                
            } else {
                $path = $this->attachment[$i][0];
                
            }

            $filename = $this->attachment[$i][1];
            $name = $this->attachment[$i][2];
            $encoding = $this->attachment[$i][3];
            $type = $this->attachment[$i][4];
            $disposition = $this->attachment[$i][6];
            $cid = $this->attachment[$i][7];

            $mime[] = sprintf("--%s%s", $this->boundary[1], $this->LE);
            $mime[] = sprintf("Content-Type: %s; name=\"%s\"%s", $type, $name, $this->LE);
            $mime[] = sprintf("Content-Transfer-Encoding: %s%s", $encoding, $this->LE);

            if ($disposition == "inline") {
                $mime[] = sprintf("Content-ID: <%s>%s", $cid, $this->LE);
                
            } else {
                // Not should be done
            }

            $mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", 
                              $disposition, $name, $this->LE . $this->LE);

            if ($bString) {
                $mime[] = $this->EncodeString($string, $encoding);
                
                if ($this->IsError()) {
                    return "";
                    
                } else {
                    // Not should be done
                }
                
                $mime[] = $this->LE . $this->LE;
                
            } else {
                $mime[] = $this->EncodeFile($path, $encoding);
                
                if ($this->IsError()) {
                    return "";
                    
                } else {
                    // Not should be done
                }
                
                $mime[] = $this->LE . $this->LE;
            }
        }

        $mime[] = sprintf("--%s--%s", $this->boundary[1], $this->LE);

        return join("", $mime);
    }

    /**
     * @access private
     * @return string
     */
    function EncodeFile($path, $encoding = "base64") {
        
        if (!@$fd = fopen($path, "rb")) {
            $this->SetError($this->Lang("file_open") . $path);
            return "";
            
        } else {
            // Not should be done
        }
        
        $magic_quotes = get_magic_quotes_runtime();
        set_magic_quotes_runtime(0);
        $file_buffer = fread($fd, filesize($path));
        $file_buffer = $this->EncodeString($file_buffer, $encoding);
        fclose($fd);
        set_magic_quotes_runtime($magic_quotes);

        return $file_buffer;
    }

    /**
     * @access private
     * @return string
     */
    function EncodeString($str, $encoding = "base64") {
        $encoded = "";
        
        switch (strtolower($encoding)) {
            
            case "base64":
                $encoded = chunk_split(base64_encode($str), 76, $this->LE);
                break;
            
            case "7bit":
                
            case "8bit":
                $encoded = $this->FixEOL($str);
                
                if (substr($encoded, -(strlen($this->LE))) != $this->LE) {
                    $encoded .= $this->LE;
                    
                } else {
                    // Not should be done
                }
                
                break;
                
            case "binary":
                $encoded = $str;
                break;
            
            case "quoted-printable":
                $encoded = $this->EncodeQP($str);
                break;
            
            default:
                $this->SetError($this->Lang("encoding") . $encoding);
                break;
        }
        
        return $encoded;
    }

    /**
     * @access private
     * @return string
     */
    function EncodeHeader($str, $position = 'text') {
        $x = 0;

        switch (strtolower($position)) {
            
            case 'phrase':
                if (!preg_match('/[\200-\377]/', $str)) {
                   
                    $encoded = addcslashes($str, "\0..\37\177\\\"");

                    if (($str == $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str)) {
                        return ($encoded);
                        
                    } else {
                        return ("\"$encoded\"");
                        
                    }
                }
                
                $x = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
                break;
                
            case 'comment':
                $x = preg_match_all('/[()"]/', $str, $matches);
                
            case 'text':
                
            default:
                $x += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
                break;
        }

        if ($x == 0) {
            return ($str);
        } else {
            // Not should be done
        }

        $maxlen = 75 - 7 - strlen($this->CharSet);

        if (strlen($str) / 3 < $x) {
            $encoding = 'B';
            $encoded = base64_encode($str);
            $maxlen -= $maxlen % 4;
            $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
            
        } else {
            $encoding = 'Q';
            $encoded = $this->EncodeQ($str, $position);
            $encoded = $this->WrapText($encoded, $maxlen, true);
            $encoded = str_replace("=" . $this->LE, "\n", trim($encoded));
            
        }

        $encoded = preg_replace('/^(.*)$/m', " =?" . $this->CharSet . "?$encoding?\\1?=", $encoded);
        $encoded = trim(str_replace("\n", $this->LE, $encoded));

        return $encoded;
    }

    /**  
     * @access private
     * @return string
     */
    function EncodeQP($str) {
        $encoded = $this->FixEOL($str);
        
        if (substr($encoded, -(strlen($this->LE))) != $this->LE) {
            $encoded .= $this->LE;
            
        } else {
            // Not should be done
            
        }

        $encoded = preg_replace('/([\000-\010\013\014\016-\037\075\177-\377])/e', 
                                "'='.sprintf('%02X', ord('\\1'))", $encoded);

        $encoded = preg_replace("/([\011\040])" . $this->LE . "/e", 
                                "'='.sprintf('%02X', ord('\\1')).'" . $this->LE . "'", $encoded);

        $encoded = $this->WrapText($encoded, 74, true);

        return $encoded;
    }

    /**
     * @access private
     * @return string
     */
    function EncodeQ($str, $position = "text") {

        $encoded = preg_replace("[\r\n]", "", $str);

        switch (strtolower($position)) {
            case "phrase":
                $encoded = preg_replace("/([^A-Za-z0-9!*+\/ -])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
                break;
            
            case "comment":
                $encoded = preg_replace("/([\(\)\"])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
                
            case "text":
                
            default:
                $encoded = preg_replace('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/e', 
                                        "'='.sprintf('%02X', ord('\\1'))", $encoded);
                break;
        }

        $encoded = str_replace(" ", "_", $encoded);

        return $encoded;
    }

    /**
     * @param string $string String attachment data.
     * @param string $filename Name of the attachment.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @return void
     */
    function AddStringAttachment($string, $filename, $encoding = "base64", $type = "application/octet-stream") {

        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $string;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $filename;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = true; // isString
        $this->attachment[$cur][6] = "attachment";
        $this->attachment[$cur][7] = 0;
    }

    /**
     * @param string $path Path to the attachment.
     * @param string $cid Content ID of the attachment.  Use this to identify 
     *        the Id for accessing the image in an HTML form.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.  
     * @return bool
     */
    function AddEmbeddedImage($path, $cid, $name = "", $encoding = "base64", $type = "application/octet-stream") {

        if (!@is_file($path)) {
            $this->SetError($this->Lang("file_access") . $path);
            return false;
            
        } else {
            // Not should be done
        }

        $filename = basename($path);
        
        if ($name == "") {
            $name = $filename;
            
        } else {
            // Not should be done
        }

        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false; // isStringAttachment
        $this->attachment[$cur][6] = "inline";
        $this->attachment[$cur][7] = $cid;

        return true;
    }

    /**
     * @access private
     * @return bool
     */
    function InlineImageExists() {
        $result = false;
        
        for ($i = 0; $i < count($this->attachment); $i++) {
            if ($this->attachment[$i][6] == "inline") {
                $result = true;
                break;
                
            } else {
                // Not should be done
            }
        }

        return $result;
    }

    // MESSAGE RESET METHODS

    /**
     * @return void
     */
    function ClearAddresses() {
        $this->to = array();
    }

    /**
     * @return void
     */
    function ClearCCs() {
        $this->cc = array();
    }

    /**
     * @return void
     */
    function ClearBCCs() {
        $this->bcc = array();
    }

    /**
     * @return void
     */
    function ClearReplyTos() {
        $this->ReplyTo = array();
    }

    /**
     * @return void
     */
    function ClearAllRecipients() {
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();
    }

    /**
     * @return void
     */
    function ClearAttachments() {
        $this->attachment = array();
    }

    /**
     * @return void
     */
    function ClearCustomHeaders() {
        $this->CustomHeader = array();
    }

    // MISCELLANEOUS METHODS

    /**
     * @access private
     * @return void
     */
    function SetError($msg) {
        $this->error_count++;
        $this->ErrorInfo = $msg;
    }

    /**
     * @access private
     * @return string
     */
    function RFCDate() {
        $tz = date("Z");
        $tzs = ($tz < 0) ? "-" : "+";
        $tz = abs($tz);
        
        $tz = ($tz / 3600) * 100 + ($tz % 3600) / 60;
        $result = sprintf("%s %s%04d", date("D, j M Y H:i:s"), $tzs, $tz);

        return $result;
    }

    /**
     * @access private
     * @return mixed
     */
    function ServerVar($varName) {
        global $HTTP_SERVER_VARS;
        global $HTTP_ENV_VARS;

        if (!isset($_SERVER)) {
            $_SERVER = $HTTP_SERVER_VARS;
            
            if (!isset($_SERVER["REMOTE_ADDR"])) {
                $_SERVER = $HTTP_ENV_VARS;
                
            } else {
                // Not should be done
            }
            
        } else {
            // Not should be done
        }

        if (isset($_SERVER[$varName])) {
            return $_SERVER[$varName];
            
        } else {
            return "";
        }
    }

    /**
     * @access private
     * @return string
     */
    function ServerHostname() {
        if ($this->Hostname != "") {
            $result = $this->Hostname;
            
        } else if ($this->ServerVar('SERVER_NAME') != "") {
            $result = $this->ServerVar('SERVER_NAME');
            
        } else {
            $result = "localhost.localdomain";
        }

        return $result;
    }

    /**
     * @access private
     * @return string
     */
    function Lang($key) {
        if (count($this->language) < 1) {
            $this->SetLanguage("en");
            
        } else {
            // Not should be done
        }

        if (isset($this->language[$key])) {
            return $this->language[$key];
            
        } else {
            return "Language string failed to load: " . $key;
        }
    }

    /**
     * @return bool
     */
    function IsError() {
        return ($this->error_count > 0);
    }

    /** 
     * @access private
     * @return string
     */
    function FixEOL($str) {
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("\r", "\n", $str);
        $str = str_replace("\n", $this->LE, $str);
        return $str;
    }

    /**
     * @return void
     */
    function AddCustomHeader($custom_header) {
        $this->CustomHeader[] = explode(":", $custom_header, 2);
    }

}

?>