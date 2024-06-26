<?php
require_once "../Admin/Helpers/config_helper.php";
require_once "../Admin/Helpers/validation_helper.php";
require_once "../helper.php";
require_once "../Admin/Models/Jobs.php";
require_once "../Admin/Models/Employer.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
if(!check_employer_login()) {
    echo json_encode(["status"=>501,"msg"=>"Please login ... "]);
    exit();
//    header("location:employer-login");
}
?>

<?php
    if(isset($_REQUEST["hire"]) && isset($_REQUEST["candidate"])) {
        $Jobs = new Jobs($con);
        if($Jobs->get_employer_job_count($_SESSION["employer_id"]) == 0) {
//            set_alert("warning","You must have one active job to hire candidate");
//            header("location:view-applicants");
//            die();
            echo json_encode(["status"=>201,"msg"=>"You must have one active job to hire candidate"]);
            exit();
        }

        $candidate_id = $_REQUEST["candidate"];
        $query = "SELECT COUNT(`EMPLOYER_CANDIDATE_ID`) AS CNT FROM `EMPLOYER_CANDIDATE_ACCESS` WHERE `EMPLOYER_ID`='".$_SESSION["employer_id"]."' AND `STATUS`='1'";
        $result = mysqli_query($con,$query);
        $hired_count = 0;
        if(mysqli_num_rows($result) != 0) {
            $hired_count = mysqli_fetch_assoc($result)["CNT"];
        }
        $query = "SELECT EMPLOYEE_VIEW_COUNT FROM EMPLOYER_DETAILS WHERE `EMPLOYER_ID`='".$_SESSION["employer_id"]."' AND `STATUS`='1'";
        $result = mysqli_query($con,$query);
        $available_count = 0;
        if(mysqli_num_rows($result) != 0) {
            $available_count = mysqli_fetch_assoc($result)["EMPLOYEE_VIEW_COUNT"];
        }

        if($hired_count<$available_count) {
            $Candidates = new Employees($con);
            $phone_no = '';
            $candidate = $Candidates->get_full_employee($candidate_id);
            if(count($candidate) != 0) {
                $phone_no = $candidate[0]["phone_no"];
                if($Jobs->check_hired_candidates($_SESSION["employer_id"],$candidate_id)) {
                    if ($Jobs->hire_candidate($candidate_id, $_SESSION["employer_id"])) {
//                    set_alert("success", "We hired candidate ..... ");

                        echo json_encode(["status"=>200,"msg"=>"You successfully hired candidate ... ","phone_no"=>$phone_no]);
                    } else {
                        echo json_encode(["status"=>400,"msg"=>"failed to hire"]);
//                    set_alert("warning", "failed to hire ..... ");
                    }
//                exit();
                } else {
//                set_alert("success", "Already hired ..... ");
//                header("location:view-applicants?job=".encrypt($_SESSION["job_type_id"],'job23'));
//                die();
                    echo json_encode(["status"=>201,"msg"=>"Already hired .... ","phone_no"=>$phone_no]);
                }
                exit();
            } else {
                echo json_encode(["status"=>500,"msg"=>"Something went wrong ..... "]);
            }

        } else {
//            set_alert("warning","Your hiring limit is reached ..... ");
//            header("location:view-applicants");
//            die();
            echo json_encode(["status"=>500,"msg"=>"Your hiring limit is reached ..... "]);
            exit();
        }
    } else {
        echo json_encode(["status"=>500,"msg"=>""]);
        exit();
//        header("location:view-applicants");
    }
?>