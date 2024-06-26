<?php
require_once "Admin/Helpers/config_helper.php";
require_once "Admin/Helpers/validation_helper.php";
require_once "Admin/Models/Employees.php";
function check_employee_profile($con, $phone_no)
{
    $Employees = new Employees($con);
    if ($Employees->check_phone_no($phone_no)) {
        $_SESSION["setup"] = 1;
        return true;
    } else {
        $_SESSION["setup"] = 0;
        return false;
    }
}

function check_employer_profile($con, $phone_no)
{
    $Employer = new Employer($con);
    if ($Employer->check_phone_no($phone_no)) {
        $_SESSION["setup"] = 1;
        return true;
    } else {
        $_SESSION["setup"] = 0;
        return false;
    }
}

function check_employer()
{
    return isset($_SESSION["employer"]);
}

function check_employee()
{
    return (isset($_SESSION["employee"]) && isset($_SESSION["setup"]) && $_SESSION["setup"] == 1);
}

function employee_only()
{
    if (isset($_SESSION["employer"])) {
        header("location:employer-dashboard");
    }
}

?>