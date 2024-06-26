<?php
require_once "Admin/Helpers/config_helper.php";
require_once "Admin/Helpers/validation_helper.php";
require_once "Admin/Models/Employees.php";
require_once "Admin/Models/City.php";
require_once "Admin/Models/Job_Types.php";
require_once "helper.php";

$City = new City($con);
$Job_Types = new Job_Types($con);
$city_data = $City->get_city();
$job_type_data = $Job_Types->get_job_types();

$job_type_id = 0;
$city_id = 0;
//if(isset($_REQUEST["job"])) {
//    $job_type_id = decrypt($_REQUEST["job"], "job23");
//}

if(isset($_SESSION["job_type_id"])) {
    $job_type_id = $_SESSION["job_type_id"];
} else if (isset($_REQUEST["job_type_id"])) {
    $job_type_id = $_REQUEST["job_type_id"];
}

if(isset($_REQUEST["find_job"])) {
    if(isset($_REQUEST["job_type_id"])) {
        $job_type_id = $_REQUEST["job_type_id"];
    }

    if(isset($_REQUEST["city_id"])) {
        $city_id = $_REQUEST["city_id"];
    }
}

$Employee = new Employees($con);
$J = new Job_Types($con);
$Job_Type = $J->get_job_types($job_type_id);

if(count($Job_Type) == 0) {
    header("location:index");
}

$applicants = $Employee->get_employee_by_job_type($job_type_id,$city_id);
//var_dump($applicants);

function get_link($candidate_id)
{
    $link = "";
    if (check_employer()) {
        $link = 'hire?candidate='.$candidate_id.'&hire=true';
    } else {
        $link = "employer-login";
    }
    return $link;
}

function maskString1($input) {
    $length = strlen($input);

    if ($length < 2) {
        // If the string has less than 2 characters, return it as is
        return $input;
    }

    $firstLetter = $input[0];
    $lastLetter = $input[$length - 1];

    // Create a masked string with the first and last letters
    $maskedString = $firstLetter . str_repeat('*', $length - 2) . $lastLetter;

    return $maskedString;
}

function maskString2($input) {
    $length = strlen($input);

    if ($length < 2) {
        // If the string has less than 2 characters, return it as is
        return $input;
    }

    $firstLetter = $input[0];
    $lastLetter = $input[$length - 1];

    // Create a masked string with the first and last letters
    $maskedString = $firstLetter . str_repeat('X', $length - 2) . $lastLetter;

    return $maskedString;
}

$job_types = [];
foreach ($job_type_data as $job_type) {
    array_push($job_types,["label"=>$job_type["name"],"value"=>$job_type["name"],"id"=>$job_type["job_type_id"]]);
}

$_SESSION["rand"] = rand(0, 99999999);
$_SESSION["strrand"] = random_string(10);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('css.php'); ?>
    <style>
	.form-control:focus {
    color: #000;
    background-color: #E9E7EB;
    /* border-color: #fff; */
    outline: 0;
    box-shadow: none;
}
	</style>
</head>
<body>
	<div style="background-color: #F2F6FD;">
    	<?php require_once('menu.php'); ?>
    </div>        
    <div class="container-fluid my-3 mb-5">
    	<div class="col-md-12 mb-4">
        	<div class="searchboxfindjob mb-3">
<!--                <div class="row">-->
<!--                    <div class="col-md-5 mb-0">-->
<!--                    	<input type="text" class="form-control searchicon" placeholder="Search Job by  “ Skills”"-->
<!--                        style="border: none;background-color: #E9E7EB;">-->
<!--                    </div>-->
<!--                    <div class="col-md-4 mb-0">-->
<!--                        <select class="form-control mapicon" style="background-color: #E9E7EB;border: none;">-->
<!--                            <option>All Cities</option>-->
<!--                            <option>New Delhi</option>-->
<!--                            <option>Gurugan</option>-->
<!--                            <option>Faridabad</option>-->
<!--                            <option>Ghazoabad</option>-->
<!--                        </select>-->
<!--                    </div>-->
<!--                    <div class="col-md-3" align="right">-->
<!--                        <a href="find_jobes"><input type="button" class="btn btn-primary" ></a>-->
<!--                    </div>-->
<!--                </div>-->
                <form method="get" action="?key=<?= get_from_key("&*Ygbhukyq3rw76") ?>">
                <div class="row">
                    <!--<div class="col-lg-5 col-md-7" id="autocomplete">
                        <input id="job_type" type="text" class="form-control searchicon"
                                                                   placeholder="Search Job by  “ Skills”" style="border: none;background-color: #E9E7EB;">
                        <input type="hidden" name="job_type_id" value="" id="job_type_id">
                    </div>-->
                    <div class="col-lg-4 col-md-5">
                        <select name="city_id" class="form-control mapicon" style="background-color: #E9E7EB;border: none;">
                            <option value="0" selected>All Cities</option>
                            <?php
                            foreach ($city_data as $city) {
                                echo '<option value="'.$city["city_id"].'">'.$city["name"].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-12" ALIGN="right">
                        <input type="submit" name="find_job" class="btn btn-primary" value="find candidates" >
                    </div>
                </div>
                </form>
            </div>

            
            <b>Showing <span style="color:#605BE5;"><?= count($applicants) ?></span> Applicants based on your filter</b>
        </div>    
        <div class="row">
<!--            <div class="col-md-4 mb-2">-->
<!--                <div class="card find-job cat-item">-->
<!--                    <div class="card-body">-->
<!--                        <div class="col-sm-12 col-md-12 align-items-center">-->
<!--                            <div class="row mb-3">-->
<!--                            	<div class="col-md-6">-->
<!--                                    <h6>Ravi Kumar <small style="color:#8C8594;">(21yr)</small></h6>-->
<!--                                    <small class="text-truncate me-3">City : Noida</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6" align="right">-->
<!--                                	<img class="flex-shrink-0 img-fluid" src="img/Byjus-Logo.png" alt="Logo" style="height: 35px;width: 40px;">-->
<!--                                </div>-->
<!--                            </div>	   -->
<!--                            <h6>INSIDE SALES</h6>-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small style="color:#000;">Qualification:</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small class="text-truncate">Graduate</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small style="color:#000;">Experience:</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small class="text-truncate">Fresher</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small style="color:#000;">English:</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small class="text-truncate">Thodas</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <h6 class="mt-2">94XXXXXX10</h6>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="card-footer" align="center">-->
<!--                        <a href="javascript:void(0);" style="font-weight:500;">HIRE NOW</a>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

            <?php
                if(count($applicants) == 0) {
                    echo "<h1>No Candidate Found</h1>";
                } else
                foreach ($applicants as $candidate) {
                    echo '
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                        <div class="card find-job cat-item">
                            <div class="card-body">
                                <div class="col-sm-12 col-lg-12 align-items-center">
                                    <div class="row mb-3">
                                        <div class="col-md-6 col-6">
                                            <h6>'.$candidate["name"].'</h6>
                                            <small class="text-truncate me-3">City : '.$candidate["city"].'</small>
                                        </div>
                                        <div class="col-md-6 col-6" align="right">
                                            <img class="flex-shrink-0 img-fluid" src="img/user.png" alt="Logo" style="height: 35px;width: 40px;">
                                        </div>
                                    </div>	   
                                    <h6>Skills ------ </h6>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-4">
                                            <small style="color:#000;">Qualification:</small>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-8">
                                            <small class="text-truncate">'.$candidate["education"].'</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-4">
                                            <small style="color:#000;">Experience:</small>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-8">
                                            <small class="text-truncate">'.$candidate["experience"].'</small>
                                        </div>
                                    </div>
                                    <!--<div class="row">
                                        <div class="col-md-6 col">
                                            <small style="color:#000;">English:</small>
                                        </div>
                                        <div class="col-md-6 col">
                                            <small class="text-truncate">'.$candidate["education"].'</small>
                                        </div>
                                    </div>-->
                                    <h6 class="mt-2">'.maskString2($candidate["phone_no"]).'</h6>
                                </div>
                            </div>
                            <div class="card-footer" align="center">
                                <a href="'.get_link($candidate["employee_id"]).'" style="font-weight:500;">HIRE NOW</a>
                            </div>
                        </div>
                    </div>
                    ';
                }
            ?>

<!--            <div class="col-md-4 mb-2">-->
<!--                <div class="card find-job cat-item">-->
<!--                    <div class="card-body">-->
<!--                        <div class="col-sm-12 col-md-12 align-items-center">-->
<!--                            <div class="row mb-3">-->
<!--                            	<div class="col-md-6">-->
<!--                                    <h6>Ravi Kumar <small style="color:#8C8594;">(21yr)</small></h6>-->
<!--                                    <small class="text-truncate me-3">City : Noida</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6" align="right">-->
<!--                                	<img class="flex-shrink-0 img-fluid" src="img/Byjus-Logo.png" alt="Logo" style="height: 35px;width: 40px;">-->
<!--                                </div>-->
<!--                            </div>	   -->
<!--                            <h6>INSIDE SALES</h6>-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small style="color:#000;">Qualification:</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small class="text-truncate">Graduate</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small style="color:#000;">Experience:</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small class="text-truncate">Fresher</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small style="color:#000;">English:</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small class="text-truncate">Thodas</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <h6 class="mt-2">94XXXXXX10</h6>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="card-footer" align="center">-->
<!--                        <a href="javascript:void(0);" style="font-weight:500;">HIRE NOW</a>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
            
<!--            <div class="col-md-4 mb-2">-->
<!--                <div class="card find-job cat-item">-->
<!--                    <div class="card-body">-->
<!--                        <div class="col-sm-12 col-md-12 align-items-center">-->
<!--                            <div class="row mb-3">-->
<!--                            	<div class="col-md-6">-->
<!--                                    <h6>Ravi Kumar <small style="color:#8C8594;">(21yr)</small></h6>-->
<!--                                    <small class="text-truncate me-3">City : Noida</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6" align="right">-->
<!--                                	<img class="flex-shrink-0 img-fluid" src="img/Byjus-Logo.png" alt="Logo" style="height: 35px;width: 40px;">-->
<!--                                </div>-->
<!--                            </div>	   -->
<!--                            <h6>INSIDE SALES</h6>-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small style="color:#000;">Qualification:</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small class="text-truncate">Graduate</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small style="color:#000;">Experience:</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small class="text-truncate">Fresher</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small style="color:#000;">English:</small>-->
<!--                                </div>-->
<!--                                <div class="col-md-6 col">-->
<!--                                    <small class="text-truncate">Thodas</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <h6 class="mt-2">94XXXXXX10</h6>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="card-footer" align="center">-->
<!--                        <a href="javascript:void(0);" style="font-weight:500;">HIRE NOW</a>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>   -->
        </div>
    </div>
    <?php require_once('footer.php'); ?>
</body>
	<?php require_once('js.php'); ?>
<script>
    $(function () {
        var job_types = <?= json_encode($job_types) ?>;
        $("#job_type").autocomplete({
            source: job_types,
            select : function(event, ui) {
                $("#job_type_id").val(ui.item.id);
            }
        });
    })

    $("#job_type").on("change",(e)=> {
        if($("#job_type").val().trim() === "") {
            $("#job_type_id").val("");
        }
    })

    $("#cv").on("change",(e)=>{
        alert("Changed Content submit the form");
        $("#cv_form").submit();
    })
</script>
</html>