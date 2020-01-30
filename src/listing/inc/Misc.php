<?php

declare (strict_types = 1);

namespace App\listing\inc;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Misc
{
    private $logger;

    public function __construct()
    {
        // Create the logger
        $log_file_name = date('dmY') . '_logger';
        $this->logger = new Logger($log_file_name);

        // Now add some handlers
		$this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/' . $log_file_name . '.log', 
		Logger::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());

        $this->set_env();
    }

    /**
     * generate random string
     */
    public function rand_string($length)
    {
        $str = "";
        $chars = "subinsblogabcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_@#$";
        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }
        return $str;
    }

    /**
     * generate random number
     */
    public function rand_number($length)
    {
        $str = "";
        $chars = "0123456789";
        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }
        return $str;
    }

    //For backward compatibility with the hash_equals function.
    //The hash_equals function was released in PHP 5.6.0.
    //It allows us to perform a timing attack safe string comparison.
    public function hash_equals($str1, $str2)
    {
        if (strlen($str1) != strlen($str2)) {
            return false;
        } else {
            $res = $str1 ^ $str2;
            $ret = 0;
            for ($i = strlen($res) - 1; $i >= 0; $i--) {
                $ret |= ord($res[$i]);
            }
            return !$ret;
        }
    }

    /**
     * this function will set all .env values as a getenv
     */
    public function set_env()
    {

        $file = fopen("../src/.env", "r");
        while (!feof($file)) {
            $line = trim(fgets($file));
            if ($line != '') {
                $line = explode('=', $line);
                $line[1] = str_replace("'", "", $line[1]);
                putenv("$line[0]=$line[1]");
                if (!defined($line[0])) {
                    define($line[0], $line[1]);
                }
            }
        }
        fclose($file);

        return true;
    }

    /**
     * this will write into error log
     */
    public function log($from, $exception)
    {

        $this->logger->info('From: ' . $from, array('exception' => $exception));
    }

}
