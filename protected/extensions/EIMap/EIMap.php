<?php

/**
 * EIMap.php
 *
 * This class allows you to access mailboxes through the IMAP php extension.
 *
 *
 * Requirements: imap php extension installed on server
 *
 * http://www.php.net/manual/en/intro.imap.php
 *
 * @author: antonio ramirez <antonio@clevertech.biz>
 * Date: 8/7/12
 * Time: 11:34 PM
 */
class EIMap extends CComponent {

    /**
     * Mime Types
     */
    const MIME_TEXT = 0;
    const MIME_MULTIPART = 1;
    const MIME_MESSAGE = 2;
    const MIME_APPLICATION = 3;
    const MIME_AUDIO = 4;
    const MIME_IMAGE = 5;
    const MIME_VIDEO = 6;
    const MIME_OTHER = 7;

    /**
     * Email Flags
     */
    const FLAG_SEEN = '\\Seen';
    const FLAG_ANSWERED = '\\Answered';
    const FLAG_FLAGGED = '\\Flagged';
    const FLAG_DELETED = '\\Deleted';
    const FLAG_DRAFT = '\\Draft';

    /**
     * Search Options
     */
    const SEARCH_ALL = 'ALL'; //return all messages matching the rest of the criteria
    const SEARCH_ANSWERED = 'ANSWERED'; //  match messages with the \\ANSWERED flag set
    const SEARCH_BCC = 'BCC'; // "string" - match messages with "string" in the Bcc: field
    const SEARCH_BEFORE = 'BEFORE'; // "date" - match messages with Date: before "date"
    const SEARCH_BODY = 'BODY'; // "string" - match messages with "string" in the body of the message
    const SEARCH_CC = 'CC'; // "string" - match messages with "string" in the Cc: field
    const SEARCH_DELETED = 'DELETED'; // - match deleted messages
    const SEARCH_FLAGGED = 'FLAGGED'; // - match messages with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
    const SEARCH_FROM = 'FROM'; // "string" - match messages with "string" in the From: field
    const SEARCH_KEYWORD = 'KEYWORD'; // "string" - match messages with "string" as a keyword
    const SEARCH_NEW = 'NEW'; // - match new messages
    const SEARCH_OLD = 'OLD'; //- match old messages
    const SEARCH_ON = 'ON'; // "date" - match messages with Date: matching "date"
    const SEARCH_RECENT = 'RECENT'; // - match messages with the \\RECENT flag set
    const SEARCH_SEEN = 'SEEN'; // - match messages that have been read (the \\SEEN flag is set)
    const SEARCH_SINCE = 'SINCE'; // "date" - match messages with Date: after "date"
    const SEARCH_SUBJECT = 'SUBJECT'; // "string" - match messages with "string" in the Subject:
    const SEARCH_TEXT = 'TEXT'; // "string" - match messages with text "string"
    const SEARCH_TO = 'TO'; // "string" - match messages with "string" in the To:
    const SEARCH_UNANSWERED = 'UNANSWERED'; // - match messages that have not been answered
    const SEARCH_UNDELETED = 'UNDELETED'; // - match messages that are not deleted
    const SEARCH_UNFLAGGED = 'UNFLAGGED'; // - match messages that are not flagged
    const SEARCH_UNKEYWORD = 'UNKEYWORD'; // "string" - match messages that do not have the keyword "string"
    const SEARCH_UNSEEN = 'UNSEEN'; // - match messages which have not been read yet

    protected $username;
    protected $password;
    protected $attachmentsDirectory;
    protected $mailbox;
    protected $stream;
    protected $serverEncoding;
    protected $mimes = array(
        self::MIME_TEXT => 'TEXT',
        self::MIME_MULTIPART => 'MULTIPART',
        self::MIME_MESSAGE => 'MESSAGE',
        self::MIME_APPLICATION => 'APPLICATION',
        self::MIME_AUDIO => 'AUDIO',
        self::MIME_IMAGE => 'IMAGE',
        self::MIME_VIDEO => 'VIDEO',
        self::MIME_OTHER => 'OTHER'
    );

    /**
     * Class Construct
     * @param $mailbox the path of the inbox to open. This can be any of the following:
     * - To connect to an IMAP server running on port 143 on the local machine,
     * - do the following: "{localhost:143}INBOX"
     * - To connect to a POP3 server on port 110 on the local server, use: "{localhost:110/pop3}INBOX"
     * - To connect to an SSL IMAP or POP3 server, add /ssl after the protocol
     * - specification: "{localhost:993/imap/ssl}INBOX"
     * - To connect to an SSL IMAP or POP3 server with a self-signed certificate,
     * - add /ssl/novalidate-cert after the protocol specification: "{localhost:995/pop3/ssl/novalidate-cert}"
     * - To connect to an NNTP server on port 119 on the local server, use: "{localhost:119/nntp}comp.test"
     * @param $username
     * @param $password
     * @param string $serverEncoding
     */
    public function __construct($mailbox, $username, $password, $serverEncoding = 'utf-8') {
        $this->mailbox = $mailbox;
        $this->username = $username;
        $this->password = $password;
        $this->serverEncoding = $serverEncoding;
    }

    /**
     * Destroy function of class
     */
    public function __destroy() {
        $this->close();
    }

    /**
     * Magic function __call to check whether we still connected or not. Servers a bridget between this class and
     * its parent::__call method so we keep CComponent wonders :)
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args) {
        if (strncasecmp($method, 'imap', 4) === 0) {
            $this->checkConnection();
            $result = call_user_func_array($method, $args);
            $this->checkErrors();
            return $result;
        }
        return parent::__call($method, $args);
    }

    /**
     * This event is raised before connection occurs.
     * @param EIMapEvent $event the event parameter
     */
    public function onBeforeConnect($event) {
        $this->raiseEvent('onBeforeConnect', $event);
    }

    /**
     * Event raised on reconnection
     * @param $event
     */
    public function onReconnect($event) {
        $this->raiseEvent('onReconnect', $event);
    }

    /**
     * $this event is raised after connection occurs.
     * @param  EIMapEvent $event
     */
    public function onAfterConnect($event) {
        $this->raiseEvent('onAfterConnect', $event);
    }

    /**
     * @param $event
     */
    public function onIMapError($event) {
        $this->raiseEvent('onIMapError', $event);
    }

    /**
     * Attribute to set server encoding
     * @param string $encoding
     */
    public function setServerEncoding($encoding = 'utf-8') {
        $this->serverEncoding = $encoding;
    }

    /**
     * @return string the server encoding
     */
    public function getServerEncoding() {
        return $this->serverEncoding;
    }

    /**
     * Attribute to specify the directory where to save the attachments
     * @param $directory
     * @return bool
     */
    public function setAttachmentsDirectory($directory) {
        if (is_dir($directory) && is_writable($directory)) {
            $this->attachmentsDirectory = $directory;
        }
        return false;
    }

    /**
     * Attribute attachmentsDirectory, a local directory where to save attachments. Runtime will be returned if none
     * is specified.
     * @return mixed
     */
    public function getAttachmentsDirectory() {
        if (null === $this->attachmentsDirectory) {
            $this->attachmentsDirectory = Yii::app()->getRuntimePath();
        }
        return $this->attachmentsDirectory;
    }

    /**
     * @see http://www.php.net/manual/en/function.imap-open.php
     * @param null $username
     * @param null $password
     * @param null $options
     * @param int $retries
     * @param array $params
     */
    public function connect($mailbox = null, $username = null, $password = null, $options = null, $retries = 0, $params = array()) {

        if (null !== $mailbox)
            $this->mailbox = $mailbox;
        if (null !== $username)
            $this->username = $username;
        if (null !== $password)
            $this->password = $password;

        $ev = new EIMapEvent();
        $ev->mailbox = $this->mailbox;
        $this->onBeforeConnect($ev);
        $ev->stream = $this->stream = @imap_open($this->mailbox, $this->username, $this->password, $options, $retries, $params);

        if (!$this->stream) {
            $ev->errorMessage = imap_last_error();
            die($ev->errorMessage);
            $this->onIMapError($ev);
            return false;
        }
        $this->onAfterConnect($ev);
        return true;
    }

    /**
     *  Closes connection
     */
    public function close() {
        if ($this->stream) {
            $this->checkErrors();
            imap_close($this->stream, CL_EXPUNGE);
        }
    }

    /**
     * Checks connection state and if not connected, reconnects
     */
    public function checkConnection() {
        if (!imap_ping($this->stream))
            $this->reconnect();
    }

    /**
     * Resets connection
     */
    public function reconnect() {
        $this->raiseEvent('onReconnect', new CEvent);

        $this->close();
        $this->connect();
    }

    /**
     * Check current mailbox
     * @return mixed the information in an object with following properties:
     *  Date - current system time formatted according to » RFC2822
     *  Driver - protocol used to access this mailbox: POP3, IMAP, NNTP
     *  Mailbox - the mailbox name
     *  Nmsgs - number of messages in the mailbox
     *  Recent - number of recent messages in the mailbox
     *  Returns FALSE on failure.
     */
    public function getCheck() {
        return $this->imap_check($this->stream);
    }

    /**
     * Returns the number of emails
     * @return int
     */
    public function getNumberOfMails() {
        return $this->imap_num_msg($this->stream);
    }

    /**
     * Returns the number of recent emails on the inbox
     * @return mixed
     */
    public function getNumberOfRecentMails() {
        return $this->imap_num_recent($this->stream);
    }

    /**
     * Deletes an email
     * @param $msgId
     */
    public function deleteMail($msgId) {
        return $this->imap_delete($this->stream, $msgId, FT_UID | CL_EXPUNGE) && imap_expunge($this->stream);
    }

    /**
     * Sets a message as seen
     * @param $msgId
     */
    public function markMailAsRead($msgId) {
        $this->setMailFlag($msgId, self::FLAG_SEEN);
    }

    /**
     * Causes a store to add the specified flag to the flags set for the messages in the specified sequence.
     * @param $msgId
     * @param $flag
     * @return mixed
     * @see http://www.php.net/manual/en/function.imap-setflag-full.php
     */
    public function setMailFlag($msgId, $flag) {
        return $this->imap_setflag_full($this->stream, $msgId, $flag);
    }

    /**
     * Returns headers of a specific mail
     * @param $msgId
     * @return bool|void
     */
    public function getMailHeader($msgId) {
        return $this->imap_header($this->stream, $msgId);
    }

    /**
     * Read an overview of the information in the headers of the given message.
     * In order to get a number of messages in the mailbox
     * @see getCheck()
     * @see http://www.php.net/manual/en/function.imap-fetch-overview.php
     * @param $secuence A message sequence description. You can enumerate desired messages with the X,Y syntax,
     *  or retrieve all messages within an interval with the X:Y syntax
     * @return mixed and array of objects with the following possible properties:
     *  subject - el sujeto del mensaje
     *  from - quién lo envió
     *  to - destinatario
     *  date - cuándo fue enviado
     *  message_id - ID del mensaje
     *  references - es una referencia a este id de mensaje
     *  in_reply_to - es una respueste a este id de mensaje
     *  size - tamaño en bytes
     *  uid - UID del mensaje que está en el buzón
     *  msgno - número de secuencia de mensaje en el buzón
     *  recent - este mensaje está marcado como reciente
     *  flagged - este mensaje está marcado
     *  answered - este mensaje está marcado como respondido
     *  deleted - este mensaje está marcado para su eliminación
     *  seen - este mensaje está marcado como ya leído
     *  draft - este mensaje está marcado como borrador
     */
    public function getMailboxOverview($secuence) {

        return $this->imap_fetch_overview($this->stream, $secuence);
    }

    /**
     * Gets sender information from a message id
     * @param $msgId
     * @return null|StdClass
     */
    public function getSender($msgId) {
        $headers = $this->getMailHeader($msgId);
        if ($headers) {
            $sender = new StdClass();
            $sender->fromEmail = $headers->from[0]->mailbox . '@' . $headers->from[0]->host;
            $sender->fromName = @$headers->from[0]->personal;
            $sender->replyToEmail = @$headers->reply_to[0]->mailbox . '@' . @$headers->reply_to[0]->host;
            $sender->replyToName = @$headers->reply_to[0]->personal;
            return $sender;
        }
        return null;
    }

    /**
     * Reads and email and returns it as EIMapMessage class
     * NOTE: Attachments are automatically saved to a writtable folder. If not specified then runtime folder will be
     * returned instead.
     *
     * @param $msgId
     * @return bool|EIMapMessage
     */
    public function getMail($msgId) {
        $headers = $this->imap_fetchheader($this->stream, $msgId, FT_UID);
        if (!$headers) {
            $ev = new EIMapEvent();
            $ev->errorMessage = 'Message with UID "' . $msgId . '" not found!';
            $this->onIMapError($ev);
            return false;
        }
        $head = imap_rfc822_parse_headers($headers);
        $msg = new EIMapMessage();
        $msg->UID = $msgId;
        $datetime = date('Y-m-d H:i:s', time());
        if (isset($head->date)) {
            $datetime = new DateTime($head->date);
            $datetime = $datetime->format('Y-m-d H:i:s');
        }
        $msg->date = $datetime;
        $msg->subject = $head->subject ? $this->decodeMimeString($head->subject) : '(sin asunto)';
        $msg->fromName = isset($head->from[0]->personal) ? $this->decodeMimeString($head->from[0]->personal) : null;
        $msg->fromAddress = strtolower($head->from[0]->mailbox . '@' . $head->from[0]->host);

        $toStrings = array();
        if (isset($head->to)) {
            foreach ($head->to as $to) {
                $toEmail = strtolower($to->mailbox . '@' . $to->host);
                $toName = isset($to->personal) ? $this->decodeMimeString($to->personal) : null;
                $toStrings[] = $toName ? "$toName <$toEmail>" : $toEmail;
                $msg->to[$toEmail] = $toName;
            }
        }
        $msg->toString = implode(', ', $toStrings);

        if (isset($head->cc)) {
            foreach ($head->cc as $cc) {
                $msg->cc[strtolower($cc->mailbox . '@' . $cc->host)] = isset($cc->personal) ? $this->decodeMimeString($cc->personal) : null;
            }
        }

        if (isset($head->reply_to)) {
            foreach ($head->reply_to as $replyTo) {
                $msg->replyTo[strtolower($replyTo->mailbox . '@' . $replyTo->host)] = isset($replyTo->personal) ? $this->decodeMimeString($replyTo->personal) : null;
            }
        }

        $struct = imap_fetchstructure($this->stream, $msgId, FT_UID);
        if (empty($struct->parts)) {
            $this->getMailPart($msg, $struct, 0);
        } else {
            foreach ($struct->parts as $partNum => $partStruct) {
                $this->getMailPart($msg, $partStruct, $partNum + 1);
            }
        }
        $msg->textHtmlOriginal = $msg->textHtml;
        $msg->attachments = $this->getAttachments($msg->UID);
        return $msg;
    }

    /**
     * Search through all emails
     * @param string $criteria
     * @return array
     */
    public function searchMails($criteria = self::SEARCH_ALL) {
        $ids = imap_search($this->stream, $criteria, SE_UID, $this->serverEncoding);
        return $ids ? $ids : array();
    }

    /**
     * Gets a list of the attachments and it saves them to application's runtime path
     * @param $msgId
     * @param $structure an structure returned by imap_fetchstructure
     * @param $save if true will save to writable directory
     * @param $return whether the attachment data should be returned in the resulting array
     * @see http://www.php.net/manual/en/function.imap-fetchstructure.php
     * @return array|bool
     */
    public function getAttachments($msgId, $structure = null, $save = true, $return = false) {

        if (!$this->stream)
            return false;

        if (null === $structure) {
            $structure = imap_fetchstructure($this->stream, $msgId, FT_UID);
        }
//        var_dump($structure);
        $attachments = array();

        $imgTypes = array(
            'gif',
            'jpg',
            'jpeg',
            'png',
            'vnd.openxmlformats-officedocument.wordprocessingml.document'
        );

        if (isset($structure->parts)) {
            $parts = $structure->parts;
            $section = 2;

            for ($i = 0, $idx = 0, $len = count($parts); $i < $len; $i++) {
                $part = $parts[$i];
//                if (($part->ifdisposition && strtolower($part->disposition) == 'attachment' && $part->ifdparameters)) {
//                if (($structure->ifdisposition && strtolower($part->disposition) == 'attachment' && $structure->ifdparameters) || (in_array(strtolower($part->subtype), $imgTypes))) {
                if (($structure->ifdisposition && strtolower($part->disposition) == 'attachment' && $structure->ifdparameters) || ($part->ifdisposition && strtolower($part->disposition) == 'attachment' && $part->ifdparameters)) {
                    $attachments[$idx] = $this->getAttachment($msgId, $i, $part, $section, $save, $return);
                    $attachments[$idx]['id'] = $structure->ifid ? trim($structure->id, " <>") : null;
                    $idx++;
                    $section++;
                } else {
                    
                }
            }
        }
        return $attachments;
    }

    /**
     * Returns attachment data and saves to runtime path
     * @param $msgId
     * @param $partNumber
     * @param $part
     * @param $section
     * @param $save if true will save to writable directory
     * @param $return whether the attachment data should be returned in the resulting array
     * @return array
     */
    private function getAttachment($msgId, $partNumber, $part, $section, $save = true, $return = false) {
        $types = array(
            TYPETEXT => 'text',
            TYPEMULTIPART => 'multipart',
            TYPEMESSAGE => 'message',
            TYPEAPPLICATION => 'application',
            TYPEAUDIO => 'audio',
            TYPEIMAGE => 'image',
            TYPEVIDEO => 'video',
            TYPEOTHER => 'other'
        );
        $attachment = array('pid' => $partNumber);
        $attachment['subtype'] = strtolower($part->subtype);
        $attachment['mimetype'] = $types[$part->type] . '/' . $attachment['subtype'];
        // give a unique filename
        $attachment['filename'] = (uniqid()) . '-';
        if (isset($part->dparameters)) {
            $attachment['filename'] = $attachment['filename'] . $part->dparameters[0]->value;
        }

        $body = imap_fetchbody($this->stream, $msgId, $section, FT_UID);
        $data = $this->decodeValue($body, $part->type);

        $path = realpath(Yii::app()->getBasePath() . "/../uploads/mail/") . "/";
        $attachment['filepath'] = $path . $attachment['filename'];
        $attachment['data'] = $return ? $data : null;

        if ($save) {
            $this->saveData($attachment['filepath'], $data);
            $publicPath = Yii::app()->getBaseUrl() . "/uploads/mail/" . $attachment['filename'];
            $attachment['filepath'] = $publicPath;
        }


        return $attachment;
    }

    /**
     * Returns part of a message
     * @param EIMapMessage $msg
     * @param $partStruct
     * @param $partNum
     */
    protected function getMailPart(EIMapMessage $msg, $partStruct, $partNum) {
        $data = $partNum ? $this->imap_fetchbody($this->stream, $msg->UID, $partNum, FT_UID) : $this->imap_body($this->stream, $msg->UID, FT_UID);
//        echo '------------------------------------------------------';
////        echo $data;
        if ($partStruct->encoding == ENC8BIT) {
            $data = $this->imap_utf8($data);
        } elseif ($partStruct->encoding == ENCBINARY) {
            $data = $this->imap_binary($data);
        } elseif ($partStruct->encoding == ENCBASE64) {
            $data = $this->imap_base64($data);
        } elseif ($partStruct->encoding == ENCQUOTEDPRINTABLE) {
            $data = $this->imap_qprint($data);
        }
        $data = trim($data);

        $params = array();
        if (!empty($partStruct->parameters)) {
            foreach ($partStruct->parameters as $param) {
                $params[strtolower($param->attribute)] = $param->value;
            }
        }
        if (!empty($partStruct->dparametersx)) {
            foreach ($partStruct->dparameters as $param) {
                $params[strtolower($param->attribute)] = $param->value;
            }
        }
        if (!empty($params['charset'])) {
            $data = iconv($params['charset'], $this->serverEncoding, $data);
        }

        if ($partStruct->type == TYPETEXT && $data) {
            if (strtolower($partStruct->subtype) == 'plain') {
                $msg->textPlain .= $data;
            } else {
                $msg->textHtml .= $data;
            }
        } elseif ($partStruct->type == TYPEMESSAGE && $data) {
            $msg->textPlain .= trim($data);
        }
        if (!empty($partStruct->parts)) {
            foreach ($partStruct->parts as $subpartNum => $subpartStruct) {
                $this->getMailPart($msg, $subpartStruct, $partNum . '.' . ($subpartNum + 1));
            }
        }
    }

    /**
     * Quotes an attachment filename
     * @param $filename
     * @return mixed
     */
    protected function quoteAttachmentFilename($filename) {
        $replace = array('/\s/' => '_', '/[^0-9a-zA-Z_\.]/' => '', '/_+/' => '_', '/(^_)|(_$)/' => '');

        return preg_replace(array_keys($replace), $replace, $filename);
    }

    /**
     * Decodes a string specific to the charset
     * @param $string
     * @param string $charset
     * @return string
     */
    protected function decodeMimeString($string, $charset = 'UTF-8') {
        $newString = '';
        $elements = $this->imap_mime_header_decode($string);
        for ($i = 0, $ln = count($elements); $i < $ln; $i++) {
            if ($elements[$i]->charset == 'default') {
                $elements[$i]->charset = 'iso-8859-1';
            }
            $newString .= iconv($elements[$i]->charset, $charset, $elements[$i]->text);
        }
        return $newString;
    }

    /**
     * checks for imap errors and if any raises an event with their messages
     */
    protected function checkErrors() {
        $errors = imap_errors();
        if ($errors) {
            $ev = new EIMapEvent();
            foreach ($errors as $e) {
                $ev->errorMessage .= $e . PHP_EOL;
            }
            $this->onIMapError($ev);
        }
    }

    /**
     * Saves a file to specified path
     * @param $filename
     * @param $data
     */
    protected function saveData($filename, $data) {
        $fp = fopen($filename, 'w');
        fputs($fp, $data);
        fclose($fp);
    }

    /**
     * Function to decode a part of the message
     * @param $msgPart the message part to decode
     * @param $encoding the encoding used
     * @return bool|string
     */
    protected function decodeValue($msgPart, $encoding) {
        switch ($encoding) {
            case ENC7BIT:
            case ENC8BIT:
                $msgPart = imap_8bit($msgPart);
                break;
            case ENCBINARY:
                $msgPart = imap_binary($msgPart);
                break;
            case ENCBASE64:
            case ENCOTHER:
                $msgPart = imap_base64($msgPart);
                break;
            case ENCQUOTEDPRINTABLE:
                $msgPart = imap_qprint($msgPart);
                break;
            default:
                return false;
        }

        return $msgPart;
    }

    /**
     * @param $structure object returned by imap_fetchstructure function
     * @see http://www.php.net/manual/en/function.imap-fetchstructure.php
     * @return string
     */
    private function getMimeType($structure) {
        if ($structure->subtype && @$this->mimes[(int) $structure->type])
            return @$this->mimes[(int) $structure->type] . '/' . $structure->subtype;

        return $this->mimes[self::MIME_TEXT] . '/PLAIN';
    }

    /**
     * Helper function to validate a server IP just in case we wish to use server's IP on mailbox paths
     * @param $ip  string the IP to validate
     * @param bool $includePrivate @see http://en.wikipedia.org/wiki/Private_network
     * @return mixed
     */
    public function validateIp($ip, $includePrivate = true) {
        return $includePrivate ?
                filter_var($ip, FILTER_VALIDATE_IP) !== false :
                filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

}

/**
 * EIMapEvent class
 *
 * Object passed to the events
 */
class EIMapEvent extends CEvent {

    public $mailbox; /* passed onBeforeConnect and onAfterConnect, onIMapError is null */
    public $stream; /* passed onAfterConnect, onAfterConnect and onIMapError is null */
    public $errorMessage = ''; /* passed onIMapError event, null onBeforeConnect and onAfterConnect */

}

/**
 * EIMapMessage class
 *
 * Helper class to be filled with the properties of a message.
 *
 *
 */
class EIMapMessage {

    /**
     * @var integer the UID of the message
     */
    public $UID;

    /**
     * @var string the date of the message
     */
    public $date;

    /**
     * @var string the subject
     */
    public $subject;

    /**
     * @var string the sender name
     */
    public $fromName;

    /**
     * @var string the sender email address
     */
    public $fromAddress;

    /**
     * @var array recipients
     */
    public $to = array();

    /**
     * @var string recipients
     */
    public $toString;

    /**
     * @var array CC
     */
    public $cc = array();

    /**
     * @var array reply_to addresses and names
     */
    public $replyTo = array();

    /**
     * @var string the body text
     */
    public $textPlain;

    /**
     * @var string the body html
     */
    public $textHtml;

    /**
     * @var holds a copy of the original body html
     */
    public $textHtmlOriginal;

    /**
     * @var array the attachments of the message
     * structure:
     * - id        <structure.id>
     * - filepath <where it was saved>
     * - filename <given filename>
     * - subtype <subtype i.e. jpeg, gif, zip>
     * - mimetype <type i.e. text/plain>
     */
    public $attachments;

    /**
     * Fetches internal message links so they have local references (where attachments were saved to)
     * @param $baseUrl
     */
    public function fetchMessageInternalLinks($baseUrl) {
        if ($this->textHtml) {
            foreach ($this->attachments as $attachment) {
                if (isset($attachment['id'])) {
                    $filename = basename($attachment['filepath']);
                    $this->textHtml = preg_replace('/(<img[^>]*?)src=["\']?ci?d:' . preg_quote($attachment['id']) . '["\']?/is', '\\1 src="' . $baseUrl . $filename . '"', $this->textHtml);
                }
            }
        }
    }

    /**
     * Cleans textHtml from unwanted html tags
     * @param array $stripTags the tags to remove (you can add those you do not wish)
     */
    public function fetchMessageHtmlTags($stripTags = array('html', 'body', 'head', 'meta')) {
        if ($this->textHtml) {
            foreach ($stripTags as $tag) {
                $this->textHtml = preg_replace('/<\/?' . $tag . '.*?>/is', '', $this->textHtml);
            }
            $this->textHtml = trim($this->textHtml, " \r\n");
        }
    }

}
