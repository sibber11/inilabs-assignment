<?php

include_once './Logger.php';

class FileLogger implements Logger
{
    public function __construct(public string $fileName = 'log.txt')
    {
    }

    /**
     * implementation of the error function from the Logger interface.
     */
    public function error($text)
    {
        file_put_contents($this->fileName, "Error: $text\n", FILE_APPEND);
    }
}
