<?php

include_once './Logger.php';

class WebLogger implements Logger
{
    /**
     * implementation of the error function from the Logger interface.
     */
    public function error($text)
    {
        // log it direct to the web browser.
        echo "<cite style=\"color:red;\">$text<br>";
    }
}
