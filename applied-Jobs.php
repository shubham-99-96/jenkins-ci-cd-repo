<?php
require_once "Admin/Helpers/config_helper.php";
require_once "Admin/Helpers/validation_helper.php";
require_once "helper.php";
require_once "Admin/Models/Jobs.php";
if(!check_employee()) {
    set_alert("success","Please login .... ");
    header("location:candidate-login");
    die();
}

$Jobs = new Jobs($con);
$appliend_jobs = $Jobs->get_applied_jobs($_SESSION["employee_id"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('css.php'); ?>
</head>
<body>
    <div class="" style="background-color: #F2F6FD;border-bottom: 1px solid #ddd;">
    	<?php require_once('menu.php'); ?>
    </div>
    <div class="container-xxl my-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body" id="verf-login">
                                <h5 class="mt-3 mb-4">
                                <img src="img/applyjob.png" alt="apply job" width="30px;" class="img-fluid" style="margin-right:10px;" /> Recent Applied Jobs
                                </h5>
                                <?php
                                    if(count($appliend_jobs) == 0) {
                                        echo "<div align='center' class='py-4'><img src='img/no-bag.gif' width='80'>
										<h4 class='mt-3'>No Jobs Found</h4></div>";
                                    } else {
                                        foreach ($appliend_jobs as $job) {
                                            echo '
                                            <div class="card find-job mb-2">
                                                <div class="card-header">
                                                    <i class="fa fa-xs fa-circle" aria-hidden="true" style="margin-right:10px;"></i> Applied on : '.date("d-M-Y",strtotime($job["date"])).'
                                                </div>
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
                                                        <div class="text-truncate"><i class="fa-solid fa-phone"></i><a href="tel:+91'.$job["company_phone"].'"> &nbsp;&nbsp; ₹ +91'.$job["company_phone"].'</a></div>
                                                        <div class="row mt-3">
                                                            <div class="col-md-4 col-5">
                                                                <div class="joblistcontent">
                                                                    <img src="https://tankhwa.smsipl.com/img/work_type/'.strtolower($job["type_of_job"][0]).'.png" class="img-fluid me-2" style="width: 20px;">'.$job["type_of_job"].'
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-7">
                                                                <div class="joblistcontent">
                                                                    <img src="img/company.png" class="img-fluid me-2" style="width: 20px;">'.$job["work_type"].'
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-7">
                                                                <div class="joblistcontent">
                                                                    <img src="img/notest.png" class="img-fluid me-2" style="width: 20px;">No Test Required
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                                        }
                                    }
                                ?>
                                
<!--                                <div class="card find-job mb-2">-->
<!--                                    <div class="card-header">-->
<!--                                        <i class="fa fa-xs fa-circle" aria-hidden="true" style="margin-right:10px;"></i> Applied on : 15-Jan-2024-->
<!--                                    </div>-->
<!--                                    <div class="card-body">-->
<!--                                        <div class="col-sm-12 col-md-12 align-items-center">-->
<!--                                            <div class="d-flex mb-2">-->
<!--                                                <img class="flex-shrink-0 img-fluid" src="img/Byjus-Logo.png" alt="Logo" style="height: 35px;width: 40px;">-->
<!--                                                <div class="text-start ps-3">-->
<!--                                                    <h6>Software Engineer</h6>-->
<!--                                                    <span class="text-truncate me-3">byJu’s </span>-->
<!--                                                </div>-->
<!--                                            </div>	   -->
<!--                                            <div class="text-truncate"><img src="img/maps-and-flags.png" class="img-fluid me-3" style="width: 15px;">Inside Sales</div>-->
<!--                                            <div class="text-truncate"><img src="img/wallet.png" class="img-fluid me-3" style="width: 15px;">₹ 10,000 - ₹ 11,000</div>-->
<!--                                            <div class="row mt-3">-->
<!--                                                <div class="col-md-4 col">-->
<!--                                                    <div class="joblistcontent">-->
<!--                                                        <img src="img/company.png" class="img-fluid me-2" style="width: 20px;">Work from office-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                                <div class="col-md-3 col">-->
<!--                                                    <div class="joblistcontent">-->
<!--                                                        <img src="img/fulltime.png" class="img-fluid me-2" style="width: 20px;">Full time-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                                <div class="col-md-4">-->
<!--                                                    <div class="joblistcontent">-->
<!--                                                        <img src="img/notest.png" class="img-fluid me-2" style="width: 20px;">No test required-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
                 
                            </div>
                        </div>   
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('footer.php'); ?>
</body>
	<?php require_once('js.php'); ?>
</html>