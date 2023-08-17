<?php

class Employee
{

    public string $name;
    public int $age;
    
    /** 
     * visibility modifier set to private. so this properties cannot be accessed directly.
     * here we store the salary as cents.
     * 1 dollar will be 100 cents.
     * direct access to this property may result in wrong data.
     * storing values as int is a good practice.
    */
    private int $salary;

    /**
     * Here we can set the salary value without giving access to the $salary property.
     */
    public function setSalary(float $inputSalary): void{
        // here we can set the salary securely in cent.
        $this->salary = round($inputSalary, 2) * 100;
    }

    /**
     * Here we can get the salary value without giving access to the $salary property.
     */
    public function getSalary() : float{
        // here we can return the salary as int.
        return $this->salary / 100;
    }

    /**
     * return all the info of the employee
     */
    public function getInfo() : string{
        return "Name: $this->name, Age: $this->age, Salary: " . $this->getSalary();
    }
}
