<?php
require_once "Admin/Helpers/config_helper.php";
require_once "Admin/Helpers/validation_helper.php";
require_once "Admin/Models/Jobs.php";
require_once "Admin/Models/City.php";
require_once "Admin/Models/Job_Types.php";
require_once "Admin/Models/Experiences.php";
require_once "Admin/Models/Educations.php";
require_once "Admin/Models/Site.php";

require_once "helper.php";
employee_only();

$City = new City($con);
$Job_Types = new Job_Types($con);
$city_data = $City->get_city();
$job_type_data = $Job_Types->get_job_types();

$job_types = [];
foreach ($job_type_data as $job_type) {
    array_push($job_types,["label"=>$job_type["name"],"value"=>$job_type["name"],"id"=>$job_type["job_type_id"]]);
}
$Jobs = new Jobs($con);

$job_type_id = 0;
if(isset($_REQUEST["job_type_id"])) {
//    $job_type_id = decrypt($_REQUEST["job_type_id"],"job23");
    $job_type_id = $_REQUEST["job_type_id"];
}

$salary_from = 0;$salary_to = 0;
if(isset($_REQUEST["salary"])) {
    $salary_from = explode("-",$_REQUEST["salary"])[0];
    $salary_to = explode("-",$_REQUEST["salary"])[1];
}
$experience = 0;
if(isset($_REQUEST["experience"])) {
    $experience = $_REQUEST["experience"];
}
$education = 0;
if(isset($_REQUEST["education"])) {
    $education = $_REQUEST["education"];
}
$work_mode = 0;
if(isset($_REQUEST["work_mode"])) {
    $work_mode = $_REQUEST["work_mode"];
}

$city_id = 0;
if(isset($_REQUEST["city_id"])) {
    $city_id = $_REQUEST["city_id"];
}

$jobs_data = $Jobs->get_active_job(0,$job_type_id,$experience,$education,0,$salary_from,$salary_to,$city_id,$work_mode);

$Experiences = new Experiences($con);
$Education = new Educations($con);
$experiences = $Experiences->get_experiences();
$educations = $Education->get_education();
$Site = new Site();
$work_modes = $Site->get_work_type();
//echo $salary_from." ".$salary_to;


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
            <form method="get">
                    <div class="row">
                        <div class="col-md-5" id="autocomplete">
                            <input type="text" class="form-control searchicon" id="job_type" placeholder="Search Job by  “ Skills”"
                                   style="border: none;background-color: #E9E7EB;">
                            <input type="hidden" name="job_type_id" value="0" id="job_type_id">
                        </div>
                        <div class="col-md-3">
                            <select name="city_id" class="form-control mapicon" style="background-color: #E9E7EB;border: none;">
                                <option value="0" selected>All Cities</option>
                                <?php
                                foreach ($city_data as $city) {
                                    echo '<option value="'.$city["city_id"].'">'.$city["name"].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4" align="right">
                            <input name="find_job" type="submit" class="btn btn-primary" value="find Jobs">
                        </div>
                    </div>
                </form>
            </div>
            
            <b>Showing <span style="color:#605BE5;"><?= count($jobs_data) ?></span> jobs based on your filter</b>
            <form id="find_job" method="get">
            <div class="row mt-3">
                <?php
                    if(isset($job_type_id)) {
                        echo '<input type="hidden" name="job_type_id" value="'.$job_type_id.'">';
                    }
                ?>
                <div class="col-md-2 col-6 mb-1">
                    <select name="salary" class="form-control find" style="background-color: #fff;border-radius: 50px;">
                        <option value="0-0">Salary</option>
                        <option <?php if(isset($_REQUEST["salary"]) && $_REQUEST["salary"] == "1-20000") { echo "selected"; } ?> value="1-20000">0-20000</option>
                        <option <?php if(isset($_REQUEST["salary"]) && $_REQUEST["salary"] == "20000-40000") { echo "selected"; } ?> value="20000-40000">20000-40000</option>
                        <option <?php if(isset($_REQUEST["salary"]) && $_REQUEST["salary"] == "40000-80000") { echo "selected"; } ?> value="40000-80000">40000-80000</option>
                    </select>
                </div>
                <div class="col-md-2 col-6 mb-1">
                    <select name="experience" class="form-control find" style="background-color: #fff;border-radius: 50px;">
                        <option value="0">Experience</option>
                        <?php
                        foreach ($experiences as $e) {
                            $selected = "";
                            if($e["experience_id"] == $experience) {
                                $selected = "selected";
                            }
                            echo '<option '.$selected.' value="'.$e["experience_id"].'">'.$e["name"].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 col-6 mb-1">
                    <select name="education" class="form-control find" style="background-color: #fff;border-radius: 50px;">
                        <option value="0">Education</option>
                        <?php
                        foreach ($educations as $e) {
                            $selected = "";
                            if($e["education_id"] == $education) {
                                $selected = "selected";
                            }
                            echo '<option '.$selected.' value="'.$e["education_id"].'">'.$e["name"].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 col-6 mb-1">
                    <select name="work_mode" class="form-control find" style="background-color: #fff;border-radius: 50px;">
                        <option value="0">Work Mode</option>
                        <?php
                            foreach ($work_modes as $w) {
                                $selected = "";
                                if($w["id"] == $work_mode) {
                                    $selected = "selected";
                                }
                                echo '<option '.$selected.' value="'.$w["id"].'">'.$w["name"].'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            </form>
        </div>    
        <div class="row">

            <?php
                if(count($jobs_data) == 0) {
                    echo "<div align='center'><img src='img/no-bag.gif' width='90'>
					<h3 class='mt-3'>Sorry we couldn’t find any matches</h3></div>";
                } else
                foreach ($jobs_data as $job) {
                    $type_of_job = strtolower($job["type_of_job"][0]).".png";
                    echo '
                        <div class="col-md-6 mb-2">
                            <a href="job_details?job='.encrypt($job["job_id"],"Job23").'" >
                                <div class="card find-job">
                                    <div class="card-body">
                                        <div class="col-sm-12 col-md-12 align-items-center">
                                            <div class="d-flex mb-2">
                                                <img class="flex-shrink-0 img-fluid" src="'.base_url.'/Public/company_logos/'.$job["company_logo"].'" alt="Logo" style="height: 35px;width: 40px;">
                                                <div class="text-start ps-3">
                                                    <h6>'.$job["title"].'</h6>
                                                    <span class="text-truncate me-3">'.$job["company_name"].'</span>
                                                </div>
                                            </div>
                                            <div class="text-truncate"><i class="fa-solid fa-location-dot"></i> &nbsp;&nbsp;&nbsp; '.$job["city"].'</div>
                                            <div class="text-truncate"><i class="fa-solid fa-wallet"></i> &nbsp;&nbsp; ₹ '.$job["salary_from"].' - ₹ '.$job["salary_to"].'</div>
                                            <div class="row mt-3" style="color:#292C73;">
                                                <div class="col-md-4 col-6">
                                                    <div class="joblistcontent">
                                                        <img src="img/work_type/'.$type_of_job.'" class="img-fluid me-2" style="width: 20px;">'.$job["type_of_job"].'
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <div class="joblistcontent">
                                                        <img src="img/company.png" class="img-fluid me-2" style="width: 20px;">'.$job["work_type"].'
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <div class="joblistcontent">
                                                        <img src="img/notest.png" class="img-fluid me-2" style="width: 20px;">No Test Required
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                             </a>
                        </div>
                    ';
                }
            ?>
            
        </div>
    </div>
    <?php require_once('footer.php'); ?>
</body>
	<?php require_once('js.php'); ?>
<script>
    $(function () {
        var job_types = <?= json_encode($job_types) ?>;
        console.log(job_types);
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

    let q = document.querySelectorAll(".find");
    q.forEach(p=>{
        p.addEventListener('change',(e)=>{
            let d = document.getElementById("find_job");
            d.submit();
        })
    })
</script>
</html>