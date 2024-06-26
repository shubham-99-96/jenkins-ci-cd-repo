<?php
require_once "../Admin/Helpers/config_helper.php";
require_once "../Admin/Helpers/validation_helper.php";
require_once "../helper.php";
require_once "../Admin/Models/Jobs.php";

if(!check_employer()) {
    header("location:employer-login");
}

$Jobs = new Jobs($con);
if(!isset($_SESSION["employer_id"])) {
    //header("location:employer-profile");
    $my_jobs = [];
    $hired_candidates = [];

} else {
    $my_jobs = $Jobs->get_jobs($_SESSION["employer_id"]);
    $hired_candidates = $Jobs->get_hired_candidates($_SESSION["employer_id"]);

}


//var_dump($my_jobs);
//set_alert("danger","test");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('css.php'); ?>
    <style>
	.applynow {
    border-radius: 10px;
    padding: 10px 0px;
    background-color: #292C73;
    color: #fff;
    box-shadow: 0px 4px 4px 0px #00000026;
    border: none;
	font-weight: normal;text-transform: capitalize;
	}
	</style>
</head>
<body>
    <div style="background-color: #F2F6FD; border-bottom: 1px solid #ddd;">
    	<?php require_once('menu.php'); ?>
    </div>
        <div class="container-fluid py-5">
        	<div class="row mb-3">
                <div class="col-md-6 col">
                    <h5 class="mt-2">All Jobs (<?= count($my_jobs) ?>)</h5>
                </div>
                <div class="col-md-6 col" align="right">
                	<a href="post-new-job">
                    	<button type="button" class="btn btn-primary applynow">Post a New Job &nbsp;&nbsp; <i class="fa fa-share-square"></i></button>
                    </a>     
                </div>
            </div>
<!--            <a href="welcome-candidate">-->
<!--                <div class="card find-job mb-2" style="border-left: 10px solid #BFBFBF;">-->
<!--                    <div class="card-body">-->
<!--                        <div class="row">-->
<!--                            <div class="col-sm-12 col-md-9 d-flex align-items-center">-->
<!--                                <div class="text-start ps-4">-->
<!--                                    <h5 class="mb-3">Sales Executive <i class="fa fa-xs fa-circle me-2 text-muted" -->
<!--                                    style="margin-left:10px;font-size: 12px;"></i><small class="activejob"> Active</small></h5>-->
<!--                                    <span class="text-truncate me-3">Job Location : Noida</span>-->
<!--                                    <span class="text-truncate me-3">Posted on: 04 Feb 2024</span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-sm-12 col-md-3 d-flex flex-column align-items-start  justify-content-center">-->
<!--                                <b>107</b>-->
<!--                                <span class="text-truncate me-3">Candidates Applied</span>-->
<!--                            </div>-->
<!--                        </div>   -->
<!--                    </div>-->
<!--                </div>-->
<!--            </a>    -->
<!--            <div class="card find-job mb-2" style="border-left: 10px solid #BFBFBF;">-->
<!--                <div class="card-body">-->
<!--                    <div class="row">-->
<!--                        <div class="col-sm-12 col-md-9 d-flex align-items-center">-->
<!--                            <div class="text-start ps-4">-->
<!--                                <h5 class="mb-3">Sales Executive <i class="fa fa-xs fa-circle me-2 text-muted" -->
<!--                                style="margin-left:10px;font-size: 12px;"></i><small class="expiredjob"> Expired</small></h5>-->
<!--                                <span class="text-truncate me-3">Job Location : Noida</span>-->
<!--                                <span class="text-truncate me-3">Posted on: 04 Feb 2024</span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-sm-12 col-md-3 d-flex flex-column align-items-start  justify-content-center">-->
<!--                            <b>107</b>-->
<!--                            <span class="text-truncate me-3">Candidates Applied</span>-->
<!--                        </div>-->
<!--                    </div>   -->
<!--                </div>-->
<!--            </div>-->

            <?php
                if(count($my_jobs) == 0) {
					echo "<div align='center' class='py-4'><img src='img/no-bag.gif' width='80'>
					<h4 class='mt-3'>No job created! Please post your job</h4></div>";
                } else {
                    foreach ($my_jobs as $job) {
                        $job_id = encrypt($job["job_id"],"Job45");

                        $count = $Jobs->get_job_candidates_count($job["job_id"]);

                        echo '
                        <a href="welcome-candidate?job='.$job_id.'">
                        <div class="card find-job mb-2" style="border-left: 10px solid #BFBFBF;">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12 col-md-9 d-flex align-items-center">
                                        <div class="text-start">
                                            <h5 class="mb-3">'.$job["title"].' <i class="fa fa-xs fa-circle me-2 text-muted" style="margin-left:10px;font-size: 12px;"></i>
                                                ';
                                                if($job["status"] == 0) {
                                                    echo '<small class="pendingjob"> Approval Pending</small>';
                                                } else if($job["status"] == 1) {
                                                    if(strtotime(date("Y-m-d H:i:s")) < strtotime($job["expires_at"])) {
                                                        echo '<small class="activejob"> Active </small>';
                                                    } else {
                                                        echo '<small class="expiredjob"> Expired </small>';
                                                    }
                                                } else if($job["status"] == 2) {
                                                    echo '<small class="expiredjob"> Rejected </small>';
                                                } else if ($job["status"] == 3) {
                                                    echo '<small class="expiredjob"> Deleted </small>';
                                                }
                                                echo '
                                            </h5>
                                            <!--<span class="text-truncate me-3">Job Location : '.$job["city"].'</span>-->
                                            <span class="text-truncate me-3">Posted on: '.date("d M Y",strtotime($job["date"])).'</span>
                                            <span class="text-truncate me-3">Expires on: '.date("d M Y",strtotime($job["expires_at"])).'</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-3 d-flex flex-column align-items-start  justify-content-center">
                                        <b>'.$count.'</b>
                                        <span class="text-truncate me-3">Candidates Applied</span>
                                    </div>
                                </div>   
                            </div>
                        </div>
                        </a>
                        ';
                    }
                }
            ?>
        </div>
        <div class="container-fluid py-1">
        <hr>
        </div>
        <div class="container-fluid py-5">
            <div class="row mb-3">
                <div class="col-md-6 col">
                    <h5 class="mt-2">Hired Candidates (<?= count($hired_candidates) ?>)</h5>
                </div>
                <div class="col-md-6 col" align="right">
                    <a href="view-applicants">
                        <button type="button" class="btn btn-primary applynow">Hire Candidates&nbsp;&nbsp; <i class="fa fa-share-square"></i></button>
                    </a>
                </div>
            </div>
            <?php
            if(count($hired_candidates) == 0) {
                echo "<div align='center' class='py-4'><img src='img/no-bag.gif' width='80'>
					<h4 class='mt-3'>No Candidates Hired ... </h4></div>";
            } else {
                echo '<div class="row">';
//                foreach ($hired_candidates as $candidate) {
//
//                    echo '
//
//                        <div class="card find-job mb-2" style="border-left: 10px solid #BFBFBF;">
//                            <div class="card-body">
//                                <div class="row">
//                                    <div class="col-sm-12 col-md-9 d-flex align-items-center">
//                                        <div class="text-start">
//                                            <h5 class="mb-3">'.$candidate["name"].' <i class="fa fa-xs fa-circle me-2 text-muted" style="margin-left:10px;font-size: 12px;">'.$candidate["city"].'</i>
//
//                                            </h5>
//                                            <span class="text-truncate me-3">Education : '.$candidate["education"].'</span>
//                                            <span class="text-truncate me-3">Experience : '.$candidate["experience"].'</span>
//                                            <span class="text-truncate me-3">Mobile No : '.$candidate["phone_no"].'</span>
//                                        </div>
//                                    </div>
//                                    <!--<div class="col-sm-12 col-md-3 d-flex flex-column align-items-start  justify-content-center">
//                                        <b>0</b>
//                                        <span class="text-truncate me-3">Candidates Applied</span>
//                                    </div>-->
//                                </div>
//                            </div>
//                        </div>
//                        ';
//                }
                $Employee = new Employees();
                foreach ($hired_candidates as $candidate) {
                    $skills = $Employee->get_candidate_skills($candidate["employee_id"], true);
                    echo '
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                        <div class="card find-job cat-item">
                            <div class="card-body">
                                <div class="col-sm-12 col-lg-12 align-items-center">
                                    <div class="row mb-3">
                                        <div class="col-md-6 col-6">
                                            <h6>' . $candidate["name"] . ' <small style="color:#8C8594;">( '.$candidate["gender"].' )</small></h6>
                                            <small class="text-truncate me-3">City : ' . $candidate["city"] . '</small>
                                        </div>
                                        <div class="col-md-6 col-6" align="right">
                                            <img class="flex-shrink-0 img-fluid" src="img/user.png" alt="Logo" style="height: 35px;width: 40px;">
                                        </div>
                                    </div>
                                    Skills : '.$skills.'
                                    <div class="row">
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
                                    </div>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-4">
                                                <small style="color:#000;">English:</small>
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-8">
                                                <small class="text-truncate">' . $candidate["language"] . '</small>
                                            </div>
                                        </div>
                                    <!--<div class="row">
                                        <div class="col-md-6 col">
                                            <small style="color:#000;">English:</small>
                                        </div>
                                        <div class="col-md-6 col">
                                            <small class="text-truncate">' . $candidate["education"] . '</small>
                                        </div>
                                    </div>-->
                                    ';
                    if (!empty($candidate["resume"])) {
                        echo '<a href="view_my_resume?hire=true&employee_id=' . encrypt($candidate["employee_id"], "cand74") . '">
													<button type="button" class="btn btn-sm btn-primary applynow" style="margin-right: 10px !important;">View Resume</button></a>';
                    }
                    echo '				
                                    <h6 class="mt-2">
                                    Contact Details: <a href="tel:+91' . $candidate["phone_no"] . '"> +91 ' . $candidate["phone_no"] . ' </a>
                                    </h6>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    ';
                }
                echo '</div>';
            }
            ?>


<!--            <div class="card find-job mb-2" style="border-left: 10px solid #BFBFBF;">-->
<!--                <div class="card-body">-->
<!--                    <div class="row">-->
<!--                        <div class="col-sm-12 col-md-9 d-flex align-items-center">-->
<!--                            <div class="text-start ps-4">-->
<!--                                <h5 class="mb-3">Sales Executive <i class="fa fa-xs fa-circle me-2 text-muted" -->
<!--                                style="margin-left:10px;font-size: 12px;"></i><small class="expiredjob"> Expired</small></h5>-->
<!--                                <span class="text-truncate me-3">Job Location : Noida</span>-->
<!--                                <span class="text-truncate me-3">Posted on: 04 Feb 2024</span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-sm-12 col-md-3 d-flex flex-column align-items-start  justify-content-center">-->
<!--                            <b>107</b>-->
<!--                            <span class="text-truncate me-3">Candidates Applied</span>-->
<!--                        </div>-->
<!--                    </div>   -->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="card find-job mb-2" style="border-left: 10px solid #BFBFBF;">-->
<!--                <div class="card-body">-->
<!--                    <div class="row">-->
<!--                        <div class="col-sm-12 col-md-9 d-flex align-items-center">-->
<!--                            <div class="text-start ps-4">-->
<!--                                <h5 class="mb-3">Sales Executive <i class="fa fa-xs fa-circle me-2 text-muted" -->
<!--                                style="margin-left:10px;font-size: 12px;"></i><small class="activejob"> Active</small></h5>-->
<!--                                <span class="text-truncate me-3">Job Location : Noida</span>-->
<!--                                <span class="text-truncate me-3">Posted on: 04 Feb 2024</span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-sm-12 col-md-3 d-flex flex-column align-items-start  justify-content-center">-->
<!--                            <b>107</b>-->
<!--                            <span class="text-truncate me-3">Candidates Applied</span>-->
<!--                        </div>-->
<!--                    </div>   -->
<!--                </div>-->
<!--            </div>-->
        </div>
    <?php require_once('footer.php'); ?>
</body>
	<?php require_once('js.php'); ?>
</html>