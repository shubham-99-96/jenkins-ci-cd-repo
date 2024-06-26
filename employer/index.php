<?php
require_once "../Admin/Helpers/config_helper.php";
require_once "../Admin/Helpers/validation_helper.php";
require_once "../helper.php";
require_once "../Admin/Models/Employer.php";
require_once "../Admin/Models/Users.php";
//
//if(isset($_SESSION["setup"]) && $_SESSION["setup"] == 0) {
//    if($_SESSION["employer"] == 1) {
//        header("Location:employer-profile");
//    } else {
//        header("Location:index");
//    }
//    die();
//} else if (isset($_SESSION["setup"]) && $_SESSION["setup"] == 1) {
//    header("Location:index");
//}
//
//if(isset($_POST["send_otp"])) {
//    if(check_params($_POST,["auth_key","key","phone_no"]) && check_params($_GET,["key"])) {
//        if(check_key($_POST["auth_key"], ["$#W&YGys463e7",$_POST["phone_no"],$_POST["auth_key"]],$_POST["key"]) && check_key($_POST["auth_key"],["@EH7478fh78y",$_POST["auth_key"]],$_GET["key"])) {
//            $phone_no = $_POST["phone_no"];
//
//            if(!validateDigitOnly($phone_no)) {
//                echo json_encode(["status"=>400,"msg"=>"Invalid Mobile No.."]);
//                exit();
//            }
//
//            $Employees = new Employees($con);
//            $otp = $Employees->send_otp($phone_no);
//            if($otp) {
//                echo json_encode(["status"=>200,"msg"=>"Done..........."]);
//            } else {
//                echo json_encode(["status"=>400,"msg"=>"Invalid Request code=3"]);
//            }
//        } else {
//            echo json_encode(["status"=>400,"msg"=>"Invalid Request code=2 .... !"]);
//        }
//    } else {
//        echo json_encode(["status"=>400,"msg"=>"Invalid Request code=1 .... !"]);
//    }
//    exit();
//}
//
//if(isset($_POST["verify_otp"])) {
//    if(check_params($_POST,["auth_key","key","phone_no","otp"]) && check_params($_GET,["key"])) {
//        if(check_key($_POST["auth_key"], ["S&*^#$&df63e7",$_POST["phone_no"],$_POST["otp"],$_POST["auth_key"]],$_POST["key"]) && check_key($_POST["auth_key"],["@#ewbih78s#h78y",$_POST["auth_key"]],$_GET["key"])) {
//            $phone_no = $_POST["phone_no"];
//            $otp = $_POST["otp"];
//            $Employer = new Employer($con);
//            $otp = $Employer->verify_otp($phone_no,$otp);
//            if($otp) {
//
//                $_SESSION["phone_no"] = $phone_no;
//                $_SESSION["verified"] = 1;
//                $_SESSION["employer"] = 1;
//                if(check_employer_profile($con,$phone_no)) {
//                    $details = $Employer->get_employer_by_phone_no($phone_no);
//                    if(count($details) == 1) {
//                        $_SESSION["employer_id"] = $details[0]["employee_id"];
//                        $_SESSION["name"] = $details[0]["name"];
//                        $_SESSION["logo"] = $details[0]["logo"];
//                        $_SESSION["job_type_id"] = $details[0]["job_type_id"];
//                        echo json_encode(["status" => 200, "msg" => "Done..........."]);
//                        set_alert("success","Logged in successfully .... ");
//                    } else {
//                        echo json_encode(["status" => 200, "msg" => "Done..........."]);
//                    }
//                } else {
//                    echo json_encode(["status"=>201,"msg"=>"Done..........."]);
//                }
//
////                echo json_encode(["status"=>200,"msg"=>"Otp Verified Successfully ..........."]);
//            } else {
//                echo json_encode(["status"=>400,"msg"=>"Wrong Otp ....... "]);
//            }
//        } else {
//            echo json_encode(["status"=>400,"msg"=>"Invalid Request code=2 .... !"]);
//        }
//    } else {
//        echo json_encode(["status"=>400,"msg"=>"Invalid Request code=1 .... !"]);
//    }
//    exit();
//}
if(check_employer()) {
    header("location:employer-dashboard");;
} else {
    header("location:employer-login");
}
?>