<?php

include './FileLogger.php';
include './WebLogger.php';

$fileLogger = new FileLogger();
$webLogger = new WebLogger();


/**
 * This function neither take FileLogger object nor the WebLogger object.
 * It takes an obj of clas that implements Logger interface as parameter.
 * if the recieved obj is of a class that implements the interface,
 * then error method can be called without hesitation.
 * the function doesn't care which class this obj belong to,
 * as long as it implement the Logger interface.
 */
function logError(Logger $logger){
    $logger->error('an error occoured!.');
}

// calling the logError with fileLogger will execute the error method on the FileLogger class.
logError($fileLogger);

// calling the logError with webLogger will execute the error method on the WebLogger class.
logError($webLogger);


// as we know FileLogger and WebLogger implements the Logger class,
// we can safely call error method on objec made from those.
$fileLogger->error('Log this to the file logger.');
$webLogger->error('Log it to to web.');