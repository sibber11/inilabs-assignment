<?php

include './Employee.php';

// constructing a object of Employee
$employee = new Employee();
$employee->name = 'Samuel';
$employee->age = 18;

// $employee->salary = 200;
// the line above will raise an error.

// we can set the salary using a setter method securely.
$employee->setSalary(50.56);

// we can get the salary using the getter method securely.

echo 'Salary set to ' . $employee->getSalary() . '$<br>';

echo $employee->getInfo();