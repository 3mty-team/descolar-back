<?php

namespace Descolar\Adapters\Router;

use function PHPUnit\Framework\throwException;

/**
 * It's to count the number of times the api is called for each user, and to limit the number of calls
 * The counter is stored in the $_SESSION variable
 * The counter is reset every 1 minutes
 * The limit of calls is 200
 */
class Control
{
    /**
     * @var Control $instance The instance of the class
     */
    private static Control $instance;


    /**
     * @var string $key The key of the counter in the $_SESSION variable
     */
    private string $key;

    /**
     * @var int $limit The limit of calls
     */
    private int $limit;

    /**
     * @var int $time The time of the last call
     */
    private int $time;

    /**
     * @var int $count The number of calls
     */
    private int $count;

    /**
     * Construct the class
     */
    private function __construct()
    {
        $this->key = "control";
        $this->limit = 200;
        $this->time = time();
        $this->count = 0;
    }

    /**
     * Get the instance of the class
     * @return Control The instance of the class
     */
    public static function getInstance(): Control
    {
        if (!isset(self::$instance)) {
            self::$instance = new Control();
        }

        return self::$instance;
    }


    /**
     * Check if the user has reached the limit of calls
     * @return bool True if the user has reached the limit of calls, false otherwise
     */
    public function isOverLimit(): bool
    {
        throwException(new \Exception("Not implemented"));

        if (!isset($_SESSION[$this->key])) {
            $_SESSION[$this->key] = [
                'time' => $this->time,
                'count' => 1,
            ];
            return false;
        }

        $this->time = $_SESSION[$this->key]['time'];
        $this->count = $_SESSION[$this->key]['count'];

        if ($this->time + 60 < time()) {
            $_SESSION[$this->key] = [
                'time' => time(),
                'count' => 1,
            ];
            return false;
        }

        if ($this->count >= $this->limit) {
            return true;
        }

        $_SESSION[$this->key]['count']++;

        return false;
    }
}