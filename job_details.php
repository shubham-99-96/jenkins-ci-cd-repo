<?php
require_once "Admin/Helpers/config_helper.php";
require_once "Admin/Helpers/validation_helper.php";
require_once "helper.php";
require_once "Admin/Models/Jobs.php";
$Jobs = new Jobs($con);
$job_id = decrypt($_GET["job"],"Job23");
if(isset($_POST["apply_job"])) {
    if(isset($_POST["key"])) {
        if (check_form_key("N#&*I^FGaujdh34", $_POST["key"])) {
            if(check_employee()) {
                if(!$Jobs->check_applied($_SESSION["employee_id"],$job_id)) {
                    if($Jobs->apply_job($_SESSION["employee_id"],$job_id)) {
                        echo json_encode(["status"=>200,"msg"=>"Successfully applied to job ... "]);
                    } else {
                        echo json_encode(["status"=>400,"msg"=>"Something went wrong ... "]);
                    }
                } else {
                    echo json_encode(["status"=>201,"msg"=>"Already Applied ... "]);
                }
            } else {
                echo json_encode(["status"=>401,"msg"=>"Invalid Request code=3"]);
            }
        } else {
            echo json_encode(["status"=>400,"msg"=>"Invalid Request code=2"]);
        }
    } else {
        echo json_encode(["status"=>400,"msg"=>"Invalid Request code=1"]);
    }
    exit();
} else {
    $_SESSION["rand"] = rand(0, 99999999);
    $_SESSION["strrand"] = random_string(10);
}
if(!isset($_GET["job"])) {
    header("locaion:index");
}

$job_data = $Jobs->get_active_job($job_id);
if(count($job_data) != 1) {
    header("location:index");
}
$job = $job_data[0];
$applied = false;
if(check_employee()) {
    $applied = $Jobs->check_applied($_SESSION["employee_id"], $job_id);
}

//var_dump($job_data);

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
.applynow {
    border-radius: 10px;
    padding: 10px 0px;
    background-color: #292C73;
    color: #fff;
    box-shadow: 0px 4px 4px 0px #00000026;
    border: none;
	font-weight: normal;
}
	</style>
</head>
<body>
	<div style="background-color: #F2F6FD;">
    	<?php require_once('menu.php'); ?>
    </div>        
    <div class="mb-5">
    	<div class="container-fluid py-4" style="background-image:url('img/jobdetails.png');background-position: center;background-size: cover;">
        	<div class="row">
                <div class="col-lg-6">
                    <div class="col-sm-12 col-md-12 align-items-center">
                        <div class="d-flex mb-2">
                            <img class="flex-shrink-0 img-fluid" src=" <?= base_url.'/Public/company_logos/'.$job["company_logo"] ?>" alt="Logo" style="height: 35px;width: 40px;">
                            <div class="text-start ps-3">
                                <h5 class="text-white"><?= $job["title"] ?></h5>
                                <span class="text-truncate me-3 text-white"><?= $job["company_name"] ?></span>
                            </div>
                        </div>	   
                        <div class="text-truncate text-white"><img src="img/jobdetailsmaps.png" class="img-fluid me-3" style="width: 15px;"><?= $job["city"] ?></div>
                        <div class="text-truncate text-white"><img src="img/jobdetailswallet.png" class="img-fluid me-3" style="width: 15px;"><?= '₹ '.$job["salary_from"].' - ₹ '.$job["salary_to"] ?></div>
                        <div class="row mt-3" style="color:#292C73;">
                            <div class="col-md-4 col-6">
                                <div class="joblistcontent">
                                    <img src="img/work_type/<?= strtolower($job["type_of_job"][0]).".png" ?>" class="img-fluid me-2" style="width: 20px;"><?= $job["type_of_job"] ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="joblistcontent">
                                    <img src="img/company.png" class="img-fluid me-2" style="width: 20px;"><?= $job["work_type"] ?>
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

                <div class="col-lg-6 mt-5 pt-5 d-none d-lg-block" align="right">
                    <?php
                        if(isset($_SESSION["employee"]) && isset($_SESSION["setup"]) && $_SESSION["setup"] == 1) {
                    ?>
                    <input type="button" class="btn btn-primary applynow" <?php if(!$applied) { echo ' value="apply now" id="apply_now" '; } else { echo 'value="Already Applied"'; } ?>>
                    <?php
                        } else if(isset($_SESSION["employer"])) {

                        } else {
                    ?>
                            <a href="candidate-login"><input type="button" class="btn btn-primary applynow" value="apply now"></a>
                    <?php } ?>
                </div>
            </div>    
        </div>
        <div class="container-fluid py-2" style="background-color:#F2F6FD;">
        	<div class="row" style="color: #48485F;">
                <div class="col-md-3 col">
                	<i class="fa fa-xs fa-circle" aria-hidden="true" style="margin-right:10px;"></i><small> Posted on : <?= date("d-M-Y",strtotime($job["date"])) ?> </small>
                </div>
<!--                <div class="col-md-3 col">-->
<!--                	<i class="fa fa-xs fa-circle" aria-hidden="true" style="margin-right:10px;"></i><small> Expires on : --><?php //= date("d-M-Y",strtotime($job["expires_at"])) ?><!--</small>-->
<!--                </div>-->
            </div>
        </div>
        <div class="container-fluid">
        	<div class="row mt-4">
                <div class="col-md-8">
                	<h4 class="mb-3">Job Details</h4>
                	<div class="row mb-2">
                    	<div class="col-md-6">
                        	<p class="mb-0" style="color: #8C8594;">Education</p>
                            <p style="color: #1D1D35;font-weight: 600;"><?= $job["education"] ?></p>
                        </div>
                        <div class="col-md-6">
                        	<p class="mb-0" style="color: #8C8594;">English Level</p>
                            <p style="color: #1D1D35;font-weight: 600;"><?= $job["proficiency"] ?> </p>
                        </div>
                    </div>
                    <div class="row mb-2">
                    	<div class="col-md-6">
                        	<p class="mb-0" style="color: #8C8594;">Experience</p>
                            <p style="color: #1D1D35;font-weight: 600;"><?= $job["experience"] ?></p>
                        </div>
                        <div class="col-md-6">
                        	<p class="mb-0" style="color: #8C8594;">Gender</p>
                            <p style="color: #1D1D35;font-weight: 600;"><?= $job["gender"] ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-0" style="color: #8C8594;">Work Type</p>
                            <p style="color: #1D1D35;font-weight: 600;"><?= $job["work_type"] ?></p>
                        </div>
                    	<div class="col-md-6">
                        	<p class="mb-0" style="color: #8C8594;">Address</p>
                            <p style="color: #1D1D35;font-weight: 600;"><?= $job["address"] ?></p>
                        </div>
                    </div>
                    <h4 class="mb-3">Job Description</h4>
                    <p><?= $job["desc"] ?></p>
                </div>
            </div>
        </div>    
    </div>
    
    <!------------APPLY POPUP------------->
    <div class="modal fade" id="applynowmodel">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body" align="center">
                    <i class="fa fa-times close" aria-hidden="true" style="float:right;cursor:pointer;"></i>
                    <p><img src="img/hurray.gif" class="img-fluid" alt="Hurray" width="80" /></p>
                    <h5 style="color:#605BE5;">HURRAY!</h5>
                    <h5 style="font-weight: 600;">You have successfully<br>applied for a job.</h5>
                    <hr>
                    <div>
                    	<h6 class="text-muted">Call Recruiter</h6>
                        <h5>
                        <a href="tel:+91<?= $job_data[0]["company_phone"] ?>">
                            <button type="button" class="btn btn-primary mt-2" style="height: auto; font-size: 16px; width: 100%;"> 
                            	<i class="fa fa-phone-square" aria-hidden="true" style="margin-right:10px;"></i> +91<?= $job_data[0]["company_phone"] ?>
                            </button>
                        </a>
                        </h5>
                    </div>
                    <!--<input type="button" class="btn btn-primary applynow mt-3 col-md-5 close" value="OKAY">-->
                </div>
            </div>
        </div>
    </div>
    
    <div class="pt-4 pb-3 Proceed" align="right">
        <?php
			if(isset($_SESSION["employee"]) && isset($_SESSION["setup"]) && $_SESSION["setup"] == 1) {
		?>
		<input type="button" style="width: 50%;" class="btn btn-primary applynow" <?php if(!$applied) { echo ' value="apply now" id="apply_now1" '; } else { echo 'value="Already Applied"'; } ?>>
		<?php
			} else if(isset($_SESSION["employer"])) {

			} else {
		?>
				<a href="candidate-login"><input type="button" class="btn btn-primary applynow" style="width: 50%;" value="apply now"></a>
		<?php } ?>
    </div>
    <?php require_once('footer.php'); ?>
</body>
	<?php require_once('js.php'); ?>
<script>
    $(document).ready(function(){
        $("#apply_now").click(function(){
            let data = {apply_job:true,key:'<?= get_from_key("N#&*I^FGaujdh34") ?>'};
            $.ajax({
                type: "POST",
                data: data,
                success: function (response) {
                    response = JSON.parse(response);
                    if(response.status == 200) {
                        $("#applynowmodel").modal('show');
                        $("#apply_now").val('Already Applied');
                        $("#apply_now").disabled();
                    } else if(response.status == 201) {
                        alert("Already applied to this job ... ")
                    } else if (response.status == 401) {
                        window.location.href = 'candidate-profile';
                    } else {
                        alert(response.msg);
                    }
                }
            });
        });
        $("#apply_now1").click(function(){
            let data = {apply_job:true,key:'<?= get_from_key("N#&*I^FGaujdh34") ?>'};
            $.ajax({
                type: "POST",
                data: data,
                success: function (response) {
                    response = JSON.parse(response);
                    if(response.status == 200) {
                        $("#applynowmodel").modal('show');
                        $("#apply_now1").val('Already Applied');
                        $("#apply_now1").disabled();
                    } else if(response.status == 201) {
                        alert("Already applied to this job ... ")
                    } else if (response.status == 401) {
                        window.location.href = 'candidate-profile';
                    } else {
                        alert(response.msg);
                    }
                }
            });
        });
		$(".close").click(function (){
			$('#applynowmodel').modal('hide');
		})
    });


</script>
</html>