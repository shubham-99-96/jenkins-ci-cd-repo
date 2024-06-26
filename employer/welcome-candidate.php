<?php
require_once "../Admin/Helpers/config_helper.php";
require_once "../Admin/Helpers/validation_helper.php";
require_once "../helper.php";
require_once "../Admin/Models/Jobs.php";
$Jobs = new Jobs($con);
if (!check_employer()) {
    header("location:index");
}

$job_id = decrypt($_GET["job"], "Job45");
//var_dump($job_id);

if (!$Jobs->check_employer_job($_SESSION["employer_id"], $job_id)) {
    header("location:employer-dashboard");
    die();
}

if (isset($_POST["Delete_Job"])) {
    if (check_params($_POST, ["key", "job_id"])) {
        if (check_form_key("&*B^#QGstf87gsU", $_POST["key"])) {
            $id = decrypt($_POST["job_id"], "job23");

            if ($Jobs->job_op($id, 3, "", $_SESSION["employer_id"])) {
                set_alert("success", "Job Deleted Successfully");
            } else {
                set_alert("failed", "Failed to delete job");
            }
            header("location:employer-dashboard");
            die();
//            echo $id;
//            var_dump($_POST);
            exit();
        }
    }
}

if (isset($_POST["Edit_Job"])) {
    if (check_params($_POST, ["key", "job_id", "desc"])) {
        if (check_form_key("&*B^#QGstf87gsU", $_POST["key"])) {

            $desc = $_POST["desc"];
            if (!empty($desc) && validate_address($desc) && strlen($desc) > 3000) {
                set_alert("danger", "Job description not valid .... ");
                header("location:welcome-candidate?job=".$_POST["job_id"]);
                die();
            }

            $id = decrypt($_POST["job_id"], "job23");
            $desc = $_POST["desc"];
            if($Jobs->edit_job_desc($id,$desc,$_SESSION["employer_id"])) {
                set_alert("success","Successfully Edited description");
                header("location:welcome-candidate?job=".$_POST["job_id"]);
            } else {
                set_alert("danger","Failed to edit job .. ");
                header("location:welcome-candidate?job=".$_POST["job_id"]);
            }
            die();
//            echo $id;
//            var_dump($_POST);
//            exit();
//            if ($Jobs->job_op($id, 3, "", $_SESSION["employer_id"])) {
//                set_alert("success", "Job Deleted Successfully");
//            } else {
//                set_alert("failed", "Failed to delete job");
//            }
//            header("location:employer-dashboard");
//            die();
////            echo $id;
////            var_dump($_POST);
//            exit();
        }
    }
}

$job_details = $Jobs->get_jobs(0, $job_id);
$candidates = $Jobs->get_job_candidates($job_id);

if (isset($_POST["reject"])) {
    if (check_form_key("&*#J7s8d^&Tbg") && check_params($_POST, ["employee_id"]) && check_params($_GET, ["job"])) {
        $employee_id = decrypt($_POST["employee_id"], "cand74");

        if ($Jobs->job_employee_op($employee_id, $job_id, 2, "", $_SESSION["employer_id"])) {
            set_alert("success", "Rejected ... ");
        } else {
            set_alert("danger", "Failed to reject ... ");
        }
    } else {
        set_alert("danged", "Access Denied ... ");
    }
    header("location:welcome-candidate?job=" . $_GET["job"]);
    die();
} else {
    $_SESSION["rand"] = rand(0, 99999999);
    $_SESSION["strrand"] = random_string(10);
}
//var_dump($candidates);
function get_link($candidate_id)
{
    $link = "";
    if (check_employer()) {
        $link = 'hire?candidate=' . $candidate_id . '&hire=true';
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
            font-weight: normal;
        }

        .btn-outline-danger:hover {
            color: #fff !important;
        }
    </style>
</head>
<body>
<div style="background-color: #F2F6FD; border-bottom: 1px solid #ddd;">
    <?php require_once('menu.php'); ?>
</div>
<div class="container-fluid py-5">
    <div class="tab-class  wow fadeInUp" data-wow-delay="0.3s">
        <ul class="nav nav-pills d-inline-flex justify-content-center mb-5">
            <li class="nav-item mb-3">
                <a class="d-flex align-items-center text-start mx-3 ms-0 pb-3 active" data-bs-toggle="pill"
                   href="#tab-1">
                    <h6 class="mt-n1 mb-0">Applied Candidates (<?= count($candidates) ?>)</h6>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="d-flex align-items-center text-start mx-3 pb-3" data-bs-toggle="pill" href="#tab-2">
                    <h6 class="mt-n1 mb-0">Hire Similar Candidates</h6>
                </a>
            </li>
            <li class="nav-item">
                <a class="d-flex align-items-center text-start mx-3 pb-3" data-bs-toggle="pill" href="#tab-3">
                    <h6 class="mt-n1 mb-0">Job Details</h6>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="job-item">
                    <?php
                    if (count($candidates) == 0) {
                        echo "<div align='center' class='py-4'><img src='img/no-bag.gif' width='80'>
										<h4 class='mt-3'>No Candidates</h4></div>";
                    } else
                        foreach ($candidates as $candidate) {
                            echo '
								<div class="card find-job mb-2">
									<div class="card-body ps-4">
										<div class="row">
											<div class="col-sm-12 col-md-12 d-flex align-items-center">
												<span class="emploginicon" style="width: 45px;height: 45px;text-align: center;">' . $candidate["name"][0] . '</span>
												<div class="text-start ps-4">
													<h5>' . $candidate["name"] . '</h5>
													<span class="text-truncate me-3">' . $candidate["gender"] . '</span>
													<span class="text-truncate me-3">' . $candidate["city"] . '</span>
													<span class="text-truncate me-3">' . $job_details[0]["job_type"] . '</span>
												</div>
											</div>
										</div>   
										<div class="row mt-4">
											<div class="col-md-3 me-3"><h6><img src="img/open-book.png" alt="open book" width="20px;" class="img-fluid" 
											style="margin-right:10px;" />Education: ' . $candidate["education"] . '</h6></div>
											<div class="col-md-4 me-3"><h6><img src="img/suitcase.png" alt="suitcase" width="20px;" class="img-fluid" 
											style="margin-right:10px;" />Work Experience: ' . $candidate["experience"] . '</h6></div>
											<div class="col-md-4 me-3"><h6><img src="img/language.png" alt="language" width="20px;" class="img-fluid" 
											style="margin-right:10px;" />Language: ' . $candidate["language"] . '</h6></div>
										</div>
										<div class="row mt-4">
											<h6>Contact Details: <a href="tel:+91' . $candidate["phone_no"] . '"> +91 ' . $candidate["phone_no"] . ' </a></h6>
										</div>
									</div>
									<div class="card-footer">
										<div class="row">
											<div class="col-md-9 col mt-2">
												<span class="text-truncate">Applied on: ' . date("d/m/Y", strtotime($candidate["date"])) . ' </span>
											</div>
											<div class="col-md-3 col">
												<div style="display:flex;">
												';
                            if (!empty($candidate["resume"])) {
                                echo '<a href="view_my_resume?job=true&id=' . $_GET["job"] . '&employee_id=' . encrypt($candidate["employee_id"], "cand74") . '">
													<button type="button" class="btn btn-sm btn-primary applynow" style="margin-right: 10px !important;">View Resume</button></a>';
                            }
                            echo '						
														<form method="post" action="?job=' . $_GET["job"] . '&key=' . get_from_key("&*#J7s8d^&Tbg") . '"  style="float:right;">
												<input type="hidden" name="employee_id" value="' . encrypt($candidate["employee_id"], "cand74") . '">
												<!--<button type="submit" name="reject" href="candidate-login" class="nav-link btn btn-outline-danger"><i class="fa fa-times"></i> Reject</button>-->
												</form>
												</div>
											</div>
										</div>
									</div>
								</div>';

                        }
                    ?>
                </div>
            </div>
            <div id="tab-2" class="tab-pane fade show p-0">
                <div class="job-item">
                    <div class="row justify-content-center">
                        <?php
                        $Employee = new Employees();
                        $similar_candidates = $Employee->get_employee_by_job_type(0, 0, $job_details[0]["title_id"]);
                        //                            var_dump($job_details);
                        $i = 0;

                        if (count($similar_candidates) == 0) {
                            echo "<div align='center' class='py-4'><img src='img/no-bag.gif' width='80'>
										<h4 class='mt-3'>No Similar Candidates Found</h4></div>";
                        } else
                            foreach ($similar_candidates as $candidate) {
                                $skills = $Employee->get_candidate_skills($candidate["employee_id"], true);

                                $check = false;
                                if (check_employer()) {
                                    $check = !$Jobs->check_hired_candidates($_SESSION["employer_id"], $candidate["employee_id"]);
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
                                            <h6>Skills : ' . $skills . '</h6>
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
                                            <h6 class="mt-2"><i class="fa fa-phone" aria-hidden="true"></i>  <span id="phone_no_' . $i . '">';
                                if (!$check) {
                                    echo maskString2($candidate["phone_no"]);
                                } else {
                                    echo '<a href="tel:+91'.$candidate["phone_no"].'">'.$candidate["phone_no"].'</a>';
                                }
                                echo '</span></h6>
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
                            <div class="modal fade" id="view_candidate_modal_' . $i . '">
                                <div class="modal-dialog modal-md modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="col-sm-12 col-md-12">
                                                <div class="row mb-3">
                                                    <div class="col-md-8">
                                                        <h6>' . $candidate["name"] . ' <small style="color:#8C8594;">( ' . $candidate["gender"] . ' )</small></h6>
                                                        <small class="text-truncate me-3">City : ' . $candidate["city"] . '</small>
                                                    </div>
                                                    <div class="col-md-4" align="right">
                                                        <img class="flex-shrink-0 img-fluid" src="img/user.png" alt="Logo" style="height: 35px;width: 40px;">
                                                    </div>
                                                </div>	   
                                                <h6>' . $skills . '</h6>
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
                                                <h6 class="mt-2"><i class="fa fa-phone" aria-hidden="true"></i><span id="mobile_no_' . $i . '">';
                                if (!$check) {
                                    echo maskString2($candidate["phone_no"]);
                                } else {
                                    echo '<a href="tel:+91'.$candidate["phone_no"].'">'.$candidate["phone_no"].'</a>';
                                }
                                echo '</span></h6>
                                            </div>
                                            <hr>
                                            <div align="center">
                                                <!--class="close" id="close_candidate_modal_' . $i . '"-->';
                                if ($check) {
                                    echo '<a id="close_candidate_modal_' . $i . '">Close</a>';
                                } else {
                                    echo '<a onclick="return hire(' . $i . ',' . $candidate["employee_id"] . ');" style="font-weight:500;">HIRE NOW</a>';
                                }
                                echo '
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            ';
                                $i++;
                            }
                        //                            var_dump();
                        ?>
                    </div>
                </div>
            </div>
            <div id="tab-3" class="tab-pane fade show p-0">
                <div class="job-item">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card" style="border-radius: 5px;">
                                <div class="card-body pl-3 pr-3">
                                    <div class="row">
                                        <div class="col-lg-6 col">
                                            <h5 class="mt-2">Job Details </h5>
                                        </div>
                                        <?php
                                        if($job_details[0]["status"] == 1 || $job_details[0]["status"] == 0) {
                                            ?>
                                            <div class="col-md-6 col" align="right">
                                                <button type="button" data-toggle="modal" data-target="#exampleModalCenter"
                                                        class="nav-link btn btn-outline-danger" id="deletejob">
                                                    Delete Job &nbsp;&nbsp;<i class="fa fa-trash"></i></button>
                                            </div>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="col-md-6 col" align="right">
                                                <button disabled type="button" data-toggle="modal" data-target="#exampleModalCenter"
                                                        class="nav-link btn btn-outline-danger" id="deletejob">
                                                    Delete Job &nbsp;&nbsp;<i class="fa fa-trash"></i></button>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-4 col-6">
                                            <label>Job Title/ Designation</label>
                                        </div>
                                        <div class="col-md-4 col-5">
                                            <label class="login"><?= $job_details[0]["title"] ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-6">
                                            <label>Job Location</label>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <label class="login"><?= $job_details[0]["city"] ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-6">
                                            <label>Job Description</label>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <label class="login" style="width: 100%;"><?= $job_details[0]["desc"] ?></label>
                                            <?php if($job_details[0]["status"] == 1 || $job_details[0]["status"] == 0) { ?>
                                                <button type="button" data-toggle="modal" data-target="#editModalCenter"
                                                        class="nav-link btn btn-outline-success mb-2" id="editjob"><i class="fa fa-pencil"></i></button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-6">
                                            <label>Type Of Job</label>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <label class="login"><?= $job_details[0]["type_of_job"] ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-6">
                                            <label>Gender </label>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <label class="login"><?= $job_details[0]["gender"] ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-6">
                                            <label>Education</label>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <label class="login"><?= $job_details[0]["education"] ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-6">
                                            <label>Language </label>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <label class="login"><?= $job_details[0]["proficiency"] ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-6">
                                            <label>Experience </label>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <label class="login"><?= $job_details[0]["experience"] ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-6">
                                            <label>Work Mode </label>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <label class="login"><?= $job_details[0]["work_type"] ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-6">
                                            <label>Salary Range</label>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <label class="login"><?= $job_details[0]["salary_from"] . " - " . $job_details[0]["salary_to"] ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post">
                <div class="modal-body py-4" align="center">
                    <div class="col-md-12">
                        <h5>Are you sure you want to delete job</h5>
                    </div>
                    <div class="col-md-12 mt-4">
                        <button type="button" class="btn btn-primary" id="no">No</button>
                        <input type="hidden" name="key" value="<?= get_from_key("&*B^#QGstf87gsU") ?>">
                        <input type="hidden" name="job_id" value="<?= encrypt($job_id, "job23") ?>">
                        <button type="submit" name="Delete_Job" class="btn btn-outline-danger"
                                style="padding-right: 1.5rem !important;padding-left: 1.5rem !important;">Yes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModalCenter" tabindex="-1" role="dialog" aria-labelledby="editModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post">
                <div class="modal-body py-4" align="center">
                    <div class="col-md-12">
                        <h5>Edit Job Description.</h5>
                    </div>
                    <div class="col-md-12 mt-4">
                        <textarea name="desc" id="edit_job_desc" class="form-control" placeholder="Edit Description."><?= $job_details[0]["desc"] ?></textarea>
                        <input type="hidden" name="key" value="<?= get_from_key("&*B^#QGstf87gsU") ?>">
                        <input type="hidden" name="job_id" value="<?= encrypt($job_id, "job23") ?>">
                        <button onclick="return validate();" type="submit" name="Edit_Job" class="m-2 btn btn-outline-success"
                                style="padding-right: 1.5rem !important;padding-left: 1.5rem !important;">Submit
                        </button>
                        <button type="button" id="close_edit" class="m-2 btn btn-outline-danger"
                                style="padding-right: 1.5rem !important;padding-left: 1.5rem !important;">Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once('footer.php'); ?>
</body>
<?php require_once('js.php'); ?>
<script>

    function validate() {
        let desc = document.getElementById("edit_job_desc");

        // if (desc.value != "" && desc.value.length < 80) {
        //     alert("Job description should be at least 80-100 characters long");
        //     return false;
        // }

        if (desc.value != "" && desc.value.length > 3000) {
            alert("Please enter job description less than 3000 characters.... ");
            return false;
        }
        return true;
    }

    $(document).ready(function () {
        $('#deletejob').click(function () {
            $('#exampleModalCenter').modal('show');
        });
        $('#no').click(function () {
            $('#exampleModalCenter').modal('hide');
        });
        $('#editjob').click(function () {
            $('#editModalCenter').modal('show');
        });
        $('#close_edit').click(function () {
            $('#editModalCenter').modal('hide');
        });
    });

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
            }
        } else {
            alert("Something went wrong ... ");
        }
        // console.log(res);
    }
</script>
<?php
$i = 0;
foreach ($similar_candidates as $candidate) {
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
</html>