<?php
require_once "Admin/Helpers/config_helper.php";
require_once "Admin/Helpers/validation_helper.php";
require_once "Admin/Models/Employees.php";
require_once "Admin/Models/Jobs.php";
require_once "helper.php";


$Employees = new Employees($con);
$Jobs = new Jobs($con);

$profile_data = [];

if(check_employee()) {
    $profile_data = $Employees->get_full_employee($_SESSION["employee_id"]);
} else if(check_employer()) {
    if(check_params($_GET,["job","id","employee_id"])) {
        $id = decrypt($_GET["id"],"Job45");
        $employee_id = decrypt($_GET["employee_id"],"cand74");
        if($Jobs->check_employer_job($_SESSION["employer_id"],$id) && $Jobs->check_applied($employee_id,$id)) {
            $profile_data = $Employees->get_full_employee($employee_id);
        } else {
            set_alert("danger","Access Denied ..... ");
            header("location:index");
            die();
        }
    } else {
        set_alert("danger","Access Denied ..... ");
        header("location:index");
        die();
    }
} else {
    header("location:index");
    die();
}

if(isset($_POST["Download_Resume"])) {
    if(count($profile_data) == 1) {
        $filePath = __DIR__ . "/Admin/Public/candidate_resume/" . $profile_data[0]["resume"];
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filePath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        // Read the file and output it to the browser
        readfile($filePath);;
    }
}

//var_dump($profile_data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('css.php'); ?>
</head>
<body>
<div style="background-color: #F2F6FD; border-bottom: 1px solid #ddd;">
	<?php require_once('menu.php'); ?>
</div>
<div class="container-xxl my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-body" id="verf-login">
                            <h5 class="mt-3 mb-4">
                             <img src="img/myprofile.png" alt="My Profile" width="30px;" class="img-fluid" style="margin-right:10px;" /> My Profile</h5>
                            <form method="post" enctype="multipart/form-data" class="mb-3">
                                <?php

                                $fileType = pathinfo($profile_data[0]["resume"], PATHINFO_EXTENSION);

                                // Display images
                                if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                                    echo "<div class='col-md-12 mb-4'>";
                                    echo "<div class='card'>";
                                    echo "<img class='card-img-top' src='".base_url."/Public/candidate_resume/".$profile_data[0]["resume"]."' alt='Image'>";
                                    echo "<div class='card-body'>";
                                    echo "<h5 class='card-title'>Image</h5>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "</div>";
                                }

                                // Display PDFs
                                elseif ($fileType == 'pdf') {
                                    echo "<div class='col-md-12 mb-4'>";
                                    echo "<div class='card'>";
                                    echo "<embed class='card-img-top' src='".base_url."/Public/candidate_resume/".$profile_data[0]["resume"]."' type='application/pdf' width='100%' height='300px'>";
                                    echo "<div class='card-body'>";
                                    echo "<h5 class='card-title'>PDF</h5>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                                ?>
                                
                            </form>
                            <div class="col-md-12 mt-3">
                            	<div class="row">
                                	<div class="col-md-6 col" align="center">
                                        <a href="my-profile">
                                            <button name="Update_Profile" class="btn login-btn" type="button" value="SUBMIT" style="padding-right:1.5rem !important;padding-left: 1.5rem !important;">Back</button>
                                        </a>
                                    </div>
                                    <div class="col-md-6 col" align="center">  
                                        <a href="my-profile">
                                            <form method="post">
                                                <button name="Download_Resume" class="btn btn-primary" type="submit" value="SUBMIT">Download</button>
                                            </form> 
                                        </a>
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
<?php require_once('footer.php'); ?>
</body>
<?php require_once('js.php'); ?>
<?php require_once "dynamic_alert.php"; ?>
<script>
    function validate()
    {
        var name=document.getElementById('name');
        var job_type=document.getElementById('job_type');
        var city=document.getElementById('city_id');
        var education=document.querySelector('input[name = "education"]:checked');
        var experience=document.querySelector('input[name = "experience"]:checked');

        if(name.value =="" || name.value.length<4 || name.value.length>100)
        {
            alert("Please enter valid name!!");
            name.focus();
            return false;
        }
        if(city.value=="")
        {
            alert("Please choose the location");
            city.focus();
            return false;
        }
        if(job_type.value =="") {
            alert("Please enter phone no");
            job_type.focus();
            return false;
        }

        if(education == null) {
            alert("Please choose education");
            // education.focus();
            return false;
        }

        if(experience == null) {
            alert("Please choose experience");
            // experience.focus();
            return false;
        }

        document.getElementById('hdn_key').value=random_string(4)+MD5.hex('<?=$_SESSION["rand"]?>'+name.value+city.value+job_type.value+education.value+experience.value+'<?=$_SESSION["strrand"]?>')+random_string(4);
        return true;
    }
</script>
</html>