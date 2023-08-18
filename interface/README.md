# inilabs-assignment
## Interface Task-2
Interface is showed using the `Logger` interface.

The interface has abstract method `error` which is implemented in the classes that implements it.
The `FileLogger` class implements the `error` method by writing the error message to a file.
The `WebLogger` class implements the `error` method by writing the error message to the browser.

There is a function `logError` which takes a `Logger` object and a message as arguments.
The function calls the `error` method of the `Logger` object with the message as argument.


