<?php

/**
 * AntiSpam by hxii
 * Fairly basic honeypot anti-spam measure
 * https://0xff.nu
 * https://git.sr.ht/~hxii/AntiSpam
 */

class formSpam {

    public static $logfile = 'log.txt';
    public static $rulefile = 'rules.txt';

    /**
     * Check if current visitor is banned
     *
     * @return bool|string false if not banned, reference ID if banned
     */
    public static function check() {
        $id = self::getID();
        return self::checkDB($id);
    }

    /**
     * Ban current visitor by value
     *
     * @param string $banBy can be 'ip', 'ua' (user-agent), 'ref' (referer) or 'sig' (see getID())
     * @return void
     */
    public static function ban($banBy = 'sig') {
        $id = self::getID($banBy);
        $fh = fopen(self::$rulefile,'a+');
        fwrite($fh, $id.PHP_EOL);
        fclose($fh);
    }

    
    /**
     * Get current visitor details: IP, User-Agent, Referer and Signature which is a hash of the IP and User-Agent
     *
     * @param string $get empty ('') to get all details or 'ip', 'ua', 'ref', 'sig' or 'payload'
     * @return void
     */
    public static function getID($get = '') {
        $ip = $_SERVER['REMOTE_ADDR'];
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $ref = @$_SERVER['HTTP_REFERER'];
        $payload = json_encode($_POST);
        $sig = md5(base64_encode($ip.$ua));
        if (!empty($get)) {
            return $$get;
        }
        return "ip:{$ip}\tua:{$ua}\tref:{$ref}\tsig:{$sig}\tpayload:{$payload}";
    }
    
    /**
     * Check current visitor against the ban rules.
     * Since preg_match is used, rules can be strings or regex rules
     *
     * @param string $id The user's details that should be checked
     * @return bool|string returns false if visitor is not banned, or logs and returns the infraction in case the visitor is banned
     */
    private static function checkDB(string $id) {
        $fh = fopen(self::$rulefile,'r');
        $return = false;
        $rule = 0;
        while (!feof($fh)) {
            $line = trim(fgets($fh, 1024));
            if (empty($line)) continue;
            $rule++;
            $match = preg_match("/$line/", $id);
            if ($match) {
                $return = self::logInfraction($id, $rule);
                break;
            }
        }
        fclose($fh);
        return $return;
    }

    /**
     * Log the infraction in case the visitor is banned.
     * $refid is the rule and an MD5 hash of the current time and visitor details string.
     * E.g. r1-2ce395ddd006e537d738a64aa87d06e9
     *
     * @param string $id Visitor details that should be logged.
     * @param int $rule The rule line our of the ban rules file.
     * @return string reference ID that you can trace in the log.
     */
    private static function logInfraction($id, $rule) {
        $fh = fopen(self::$logfile, 'a+');
        $date = date(DATE_RFC3339, microtime(true));
        $refid = "r$rule-".md5(base64_encode($date.$id));
        $payload = json_encode($_POST);
        fwrite($fh, "$date ($refid) - $id - $payload".PHP_EOL);
        return $refid;
    }

    /**
     * Say goodbye to the user with a reference ID.
     *
     * @param string $refid Reference ID that you can search to remove the ban rule.
     * @return void
     */
    public static function sendToOblivion($refid) {
        exit('You\'ve been naughty, so you\'re not allowed to use the contact form.<br> If you believe this is a mistake, contact me at paul[dot]0xff[at]glushak[dot]net and provide this ID: <code>' . $refid .'</code>');
    }
}
