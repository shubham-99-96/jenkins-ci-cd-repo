<?php
require_once "../Admin/Helpers/config_helper.php";
require_once "../Admin/Helpers/validation_helper.php";
require_once "../Admin/Models/Employees.php";
require_once "../Admin/Models/City.php";
require_once "../Admin/Models/Job_Types.php";
require_once "../Admin/Models/Jobs.php";
require_once "../helper.php";

$City = new City($con);
$Job_Types = new Job_Types($con);
$city_data = $City->get_city();
$job_type_data = $Job_Types->get_job_types();

$job_type_id = 0;
$city_id = 0;
//if(isset($_REQUEST["job"])) {
//    $job_type_id = decrypt($_REQUEST["job"], "job23");
//}

if (isset($_REQUEST["job_type_id"]) && !empty($_REQUEST["job_type_id"])) {
    $job_type_id = $_REQUEST["job_type_id"];
}

if(isset($_REQUEST["find_job"])) {
    if(isset($_REQUEST["city_id"]) && !empty($_REQUEST["city_id"])) {
        $city_id = $_REQUEST["city_id"];
    }
}

$Employee = new Employees($con);
$J = new Job_Types($con);
$Job_Type = $J->get_job_types($job_type_id);

if(count($Job_Type) == 0) {
    set_alert("danger","No jobs found");
    header("location:index");
    die();
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
                <form method="get" action="?key=<?= get_from_key("&*Ygbhukyq3rw76") ?>">
                <div class="row">
                    <div class="col-lg-5 col-md-7" id="autocomplete">
                        <input id="job_type" type="text" class="form-control searchicon"
                                                                   placeholder="Search Candidate by  “ Job Type ”" style="border: none;background-color: #E9E7EB;">
                        <input type="hidden" name="job_type_id" value="" id="job_type_id">
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="mapicon">
                            <select name="city_id" class="form-control" id="city_id" style="background-color: #E9E7EB;border: none;">
                                <option value="0" selected>All Cities</option>
                                <?php
                                foreach ($city_data as $city) {
                                    echo '<option value="'.$city["city_id"].'">'.$city["name"].'</option>';
                                }
                                ?>
                            </select>
                        </div>
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

            <?php
                if(count($applicants) == 0) {
                    echo "<h1>No Candidate Found</h1>";
                } else {
                    $i = 0;
                    $Jobs = new Jobs($con);
                    foreach ($applicants as $candidate) {
                        $skills = $Employee->get_candidate_skills($candidate["employee_id"], true);

                        $check = false;
                        if(check_employer_login() && check_employer_profile($con,$_SESSION["phone_no"])) {
                            $check = !$Jobs->check_hired_candidates($_SESSION["employer_id"],$candidate["employee_id"]);
                        }
                        echo '
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                        <div class="card find-job cat-item">
                            <div class="card-body">
                                <div class="col-sm-12 col-lg-12 align-items-center">
                                    <div class="row mb-3">
                                        <div class="col-md-6 col-6">
                                            <h6>' . $candidate["name"] . '</h6>
                                            <small class="text-truncate me-3">City : ' . $candidate["city"] . '</small>
                                        </div>
                                        <div class="col-md-6 col-6" align="right">
                                            <img class="flex-shrink-0 img-fluid" src="img/user.png" alt="Logo" style="height: 35px;width: 40px;">
                                        </div>
                                    </div>	   
                                    <h6  style="height: 57px;">Skills : ' . $skills . '</h6>
                                    <!--<div class="row">
                                        <div class="col-md-6 col-sm-6 col-4">
                                            <small style="color:#000;">Qualification:</small>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-8">
                                            <small class="text-truncate">' . $candidate["education"] . '</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-4">
                                            <small style="color:#000;">Experience:</small>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-8">
                                            <small class="text-truncate">' . $candidate["experience"] . '</small>
                                        </div>
                                    </div>-->
                                    <!--<div class="row">
                                        <div class="col-md-6 col">
                                            <small style="color:#000;">English:</small>
                                        </div>
                                        <div class="col-md-6 col">
                                            <small class="text-truncate">' . $candidate["education"] . '</small>
                                        </div>
                                    </div>-->
									
                                    <h6 class="mt-4"><i class="fa fa-phone" aria-hidden="true"></i>  <span id="phone_no_'.$i.'">'; if(!$check) { echo maskString2($candidate["phone_no"]);  } else { echo $candidate["phone_no"]; } echo '</span></h6>
                                </div>
                            </div>
                            <div class="card-footer" align="center">
                                <!--<a href="' . get_link($candidate["employee_id"]) . '" style="font-weight:500;">HIRE NOW</a>-->
                                ';
                                echo '<a href="javascript:void(0);" style="font-weight:500;" id="view_candidate_' . $i . '">View Candidate</a>';
                                echo '
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="view_candidate_modal_'.$i.'">
                        <div class="modal-dialog modal-md modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="row mb-3">
                                            <div class="col-md-8 col-8">
                                                <h6>' . $candidate["name"] . ' <small style="color:#8C8594;">( '.$candidate["gender"].' )</small></h6>
                                                <small class="text-truncate me-3">City : ' . $candidate["city"] . '</small>
                                            </div>
                                            <div class="col-md-4 col-4" align="right">
                                                <img class="flex-shrink-0 img-fluid" src="img/user.png" alt="Logo" style="height: 35px;width: 40px;">
                                            </div>
                                        </div>	   
                                        <h6>'.$skills.'</h6>
                                        <div class="row">
                                            <div class="col-md-6 col">
                                                <small style="color:#000;">Qualification:</small>
                                            </div>
                                            <div class="col-md-6 col">
                                                <small class="text-truncate">' . $candidate["education"] . '</small>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col">
                                                <small style="color:#000;">Experience:</small>
                                            </div>
                                            <div class="col-md-6 col">
                                                <small class="text-truncate">' . $candidate["experience"] . '</small>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col">
                                                <small style="color:#000;">English:</small>
                                            </div>
                                            <div class="col-md-6 col">
                                                <small class="text-truncate">' . $candidate["english"] . '</small>
                                            </div>
                                        </div>
                                        <h6 class="mt-2"><i class="fa fa-phone" aria-hidden="true"></i> &nbsp;<span id="mobile_no_'.$i.'">'; if(!$check) { echo maskString2($candidate["phone_no"]);  } else { echo $candidate["phone_no"]; } echo '</span></h6>
                                    </div>
                                    <hr>
                                    <div align="center">
                                        <!--class="close" id="close_candidate_modal_'.$i.'"-->';
                                    if($check) {
                                        echo '<a id="close_candidate_modal_' . $i . '" style="cursor:pointer;">Close</a>';
                                    } else {
                                        echo '<a onclick="return hire(' . $i . ',' . $candidate["employee_id"] . ');" style="font-weight:500;">HIRE NOW</a>';
                                    } echo '
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ';
                        $i++;
                    }
                }
            ?>
        </div>
    </div>

    <?php require_once('footer.php'); ?>
</body>
	<?php require_once('js.php'); ?>

<?php
    $i = 0;
    foreach ($applicants as $candidate) {
        echo '
        <script>
                    $(function () {
                        $("#view_candidate_'.$i.'").click(function () {
                            $("#view_candidate_modal_'.$i.'").modal("show");
                        });
                        $("#close_candidate_modal_'.$i.'").click(function () {
                            $("#view_candidate_modal_'.$i.'").modal("hide");
                        });
                    });
                    </script>
                  
        ';
        $i++;
    }
?>

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

    // $('#city_id').select2({
    //     theme: 'custom-theme', // Use a custom theme class
    //     dropdownCssClass: 'custom-dropdown', // Apply styles to the dropdown
    //     containerCssClass: 'custom-container'
    // });

    $("#job_type").on("change",(e)=> {
        if($("#job_type").val().trim() === "") {
            $("#job_type_id").val("");
        }
    })

    $("#cv").on("change",(e)=>{
        alert("Changed Content submit the form");
        $("#cv_form").submit();
    })

    async function hire(i,id) {
        let res = await fetch("hire?candidate="+id+"&hire=true").then(response => response.json()).catch(err=>{
            console.error(err);
            return false;
        })
        if(res && res.hasOwnProperty("status")) {
            if(res["status"] == 505) {
                window.location.href='index';
            } else if(res["status"] == 200) {
                alert(res["msg"]);
                $("#phone_no_"+i).text(res["phone_no"]);
                $("#mobile_no_"+i).text(res["phone_no"]);
            }  else if(res["status"] == 201) {
                $("#phone_no_"+i).text(res["phone_no"]);
                $("#mobile_no_"+i).text(res["phone_no"]);
            } else if(res["status"] == 500) {
                alert(res["msg"]);
            } else if(res["status"] == 501) {
                window.location.href='employer-login';
            }
        } else {
            alert("Something went wrong ... ");
        }
        // console.log(res);
    }
</script>
</html>