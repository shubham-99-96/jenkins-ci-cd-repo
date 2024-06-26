<?php
require_once "Admin/Helpers/config_helper.php";
require_once "Admin/Helpers/validation_helper.php";
require_once "Admin/Models/Employees.php";
require_once "Admin/Models/Job_Types.php";
require_once "Admin/Models/City.php";
require_once "Admin/Models/Educations.php";
require_once "Admin/Models/Experiences.php";
require_once "Admin/Models/Site.php";
require_once "helper.php";

if(!check_employee()) {
    header("location:index");
}

$Job_Types = new Job_Types($con);
$City = new City($con);
$Education = new Educations($con);
$Experiences = new Experiences($con);
$Site = new Site($con);

$job_types = $Job_Types->get_job_types();
$city = $City->get_city();
$education = $Education->get_education();
$experiences = $Experiences->get_experiences();

$genders = $Site->get_genders();
$languages = $Site->get_language_proficiency();

$Employees = new Employees($con);
$profile_data = $Employees->get_full_employee($_SESSION["employee_id"]);
$candidate_skills = $Employees->get_candidate_skills($_SESSION["employee_id"]);

//var_dump($candidate_skills);

if(isset($_POST["Update_Profile"])) {
    if(check_hdn_key(["name","city","job_type","education","experience", "language","gender"])) {

        $language = $_POST["language"];

        if(!(validateName($_POST["name"]) && validateDigitOnly($_POST["city"]) && validateDigitOnly($_POST["job_type"]) && validateDigitOnly($_POST["education"]) && validateDigitOnly($_POST["experience"]) && validateDigitOnly($_POST["gender"]) && validateDigitOnly($language))) {
            set_alert("danger","Malformed Data .... ");
            header("location:my-profile");
            die();
        }

        $final_file_name = $profile_data[0]["resume"];

        if(check_params($_FILES,["cv"]) && isset($_FILES["cv"]) && $_FILES['cv']['size']>0) {
            $targetDir = "Admin/Public/candidate_resume/";
            $allowedExtensions = array('jpg', 'jpeg', 'png', 'pdf');

            $fileSize = $_FILES['cv']['size'];
            $fileTmpName = $_FILES['cv']['tmp_name'];
            $fileType = $_FILES['cv']['type'];
            $fileExtensionArray = explode('.', $_FILES['cv']['name']);
            $fileExtension = strtolower(end($fileExtensionArray));
            $fileName = "Resume_".date("YmdHis").random_string(5).".".$fileExtension;
            //            $fileName = "Logo_".$_FILES['logo']['name'];
            $uploadPath = $targetDir . $fileName;

            if (in_array($fileExtension, $allowedExtensions)) {
                if ($fileSize <= 10097152) {
                    if (move_uploaded_file($fileTmpName, $uploadPath)) {
                        $final_file_name = $fileName;
                        try {
                            if (file_exists($targetDir.$profile_data[0]["resume"])) {
                                if (unlink($targetDir.$profile_data[0]["resume"])) {
                                } else {
                                }
                            } else {
                            }
                        } catch (Exception $e) {

                        }

                    } else {
                        set_alert("warning","failed to upload resume ... ");
                    }
                } else {
                    set_alert("danger","resume file size iss too large please upload lees than 10 MB");
                    header("location:my-profile");
                    die();
                }
            } else {
                set_alert("danger","Please upload valid resume in PDF form ");
                header("location:my-profile");
                die();
            }
        }

        $Employee= new Employees($con);
        if($Employee->update_employee($_SESSION["employee_id"],$_POST["name"],$_POST["job_type"],$_POST["city"],$_POST["education"],$_POST["experience"],$language,$final_file_name,$_POST["gender"])) {
            $_SESSION["setup"] = 1;
            $_SESSION["name"] = $_POST["name"];
            set_alert("success","Profile Updated Successfully .... ");
        } else {
            set_alert("danger","Failed to update profile .... ");
        }
        header("Location:my-profile");
        die();
    } else {
        set_alert("danger","Failed to update profile ... ");
    }
    header("Location:my-profile");
    die();
} else {
    $_SESSION["rand"] = rand(0, 99999999);
    $_SESSION["strrand"] = random_string(10);
}


//var_dump($profile_data);
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
    <div class="container-xxl py-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body" id="verf-login">
                                <h5 class="mt-3 mb-4">
                                <img src="img/myprofile.png" alt="My Profile" width="30px;" class="img-fluid" style="margin-right:10px;" /> My Profile</h5>
                                <form method="post" enctype="multipart/form-data" class="mb-3">
                                    <div class="col-md-8 p-2">
                                        <label class="login text-muted">Enter Name</label>
                                        <input readonly value="<?= $profile_data[0]["name"] ?>" type="text" id="name" name="name" class="form-control login-mobileno" placeholder="Full Name">
                                    </div>
                                    <div class="col-md-8 p-2">
                                        <label class="login text-muted">Mobile Number</label>
                                        <input type="text" class="form-control login-mobileno" readonly value="<?= $_SESSION["phone_no"] ?>" placeholder="84445465465">
                                    </div>
                                    <div class="col-md-8 p-2">
                                        <label class="login">Job Title</label>
                                        <select id="job_type" name="job_type" class="form-control login-mobileno">
                                            <option>Choose job title</option>
                                            <?php
                                            foreach ($job_types as $job_type) {
                                                $selected = "";
                                                if($job_type["job_type_id"] == $profile_data[0]["job_type_id"]) {
                                                    $selected = "selected";
                                                }

                                                echo "<option ".$selected." value='".$job_type["job_type_id"]."'>".$job_type["name"]."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div id="added_skills"></div>
                                    <div class="col-md-8 p-2">
                                        <label class="login">Location</label>
                                        <select id="city_id" name="city" class="form-control login-mobileno">
                                            <option value="">Choose Location</option>
                                            <?php
                                            foreach ($city as $c) {

                                                $selected = "";
                                                if($c["city_id"] == $profile_data[0]["city_id"]) {
                                                    $selected = "selected";
                                                }

                                                echo "<option ".$selected." value='".$c["city_id"]."'>".$c["name"]."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12 p-2">
                                        <label class="login">Education</label>
                                        <div class="row">

                                            <?php
                                            $i = 0;
                                            foreach ($education as $e) {

                                                $selected = "";
                                                if($e["education_id"] == $profile_data[0]["education_id"]) {
                                                    $selected = "checked";
                                                }

                                                echo '<div class="col-md-4 mb-1">
                                                            <input  '.$selected.' type="radio" id="secondary'.$i.'" name="education" value="'.$e["education_id"].'">
                                                            <label for="secondary'.$i.'" class="rediobutton">'.$e["name"].'</label>
                                                        </div>';
                                                $i++;
                                            }
                                            ?>


                                            <!--                                                <div class="col-md-4">-->
                                            <!--                                                    <input type="radio" id="higher_secondary" name="brand" value="higher_secondary">-->
                                            <!--                                                    <label for="higher_secondary" class="rediobutton">Higher Secondary</label>-->
                                            <!--                                                </div>-->
                                            <!--                                                <div class="col-md-2">-->
                                            <!--                                                    <input type="radio" id="graduate" name="brand" value="graduate">-->
                                            <!--                                                    <label for="graduate" class="rediobutton">Graduate</label>-->
                                            <!--                                                </div>-->
                                            <!--                                                <div class="col-md-3">-->
                                            <!--                                                    <input type="radio" id="post_graduate" name="brand" value="post_graduate">-->
                                            <!--                                                    <label for="post_graduate" class="rediobutton">Post Graduate</label>-->
                                            <!--                                                </div>-->
                                        </div>
                                    </div>
                                    <div class="col-md-12 p-2">
                                        <label class="login">Experience</label>
                                        <div class="row">

                                            <?php
                                            $i = 0;
                                            foreach ($experiences as $e) {

                                                $selected = "";
                                                if($e["experience_id"] == $profile_data[0]["experience_id"]) {
                                                    $selected = "checked";
                                                }

                                                echo '<div class="col-md-4">
                                                            <input '.$selected.' type="radio" id="experience'.$i.'" name="experience" value="'.$e["experience_id"].'">
                                                            <label for="experience'.$i.'" class="rediobutton">'.$e["name"].'</label>
                                                        </div>';
                                                $i++;
                                            }
                                            ?>

                                            <!--                                                <div class="col-md-3">-->
                                            <!--                                                    <input type="radio" id="secondary" name="brand" value="secondary">-->
                                            <!--                                                    <label for="secondary" class="rediobutton">Fresher</label>-->
                                            <!--                                                </div>-->
                                            <!--                                                <div class="col-md-4">-->
                                            <!--                                                    <input type="radio" id="higher_secondary" name="brand" value="higher_secondary">-->
                                            <!--                                                    <label for="higher_secondary" class="rediobutton">Experience (1-5 Yr)</label>-->
                                            <!--                                                </div>-->
                                        </div>
                                    </div>
                                    <div class="col-md-12 p-2">
                                        <label class="login">Language <span style="color:#ff0000;">*</span></label>
                                        <div class="row">

                                            <?php
                                            $i = 0;
                                            foreach ($languages as $l) {
                                                $selected = "";
                                                if($l["id"] == $profile_data[0]["language_id"]) {
                                                    $selected = "checked";
                                                }

                                                echo '<div class="col-md-4 col-6 mb-2">
                                                            <input '.$selected.' type="radio" id="language'.$i.'" name="language" value="'.$l["id"].'">
                                                            <label for="language'.$i.'" class="rediobutton">'.$l["name"].'</label>
                                                        </div>';
                                                $i++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12 p-2">
                                        <label class="login">Gender</label>
                                        <div class="row">

                                            <?php
                                            $i = 0;
                                            foreach ($genders as $g) {

                                                if($g["id"] == 0) {
                                                    continue;
                                                }

                                                $selected = "";
                                                if($g["id"] == $profile_data[0]["gender_id"]) {
                                                    $selected = "checked";
                                                }

                                                echo '<div class="col-md-4">
                                                            <input  '.$selected.' type="radio" id="gender'.$i.'" name="gender" value="'.$g["id"].'">
                                                            <label for="gender'.$i.'" class="rediobutton">'.$g["name"].'</label>
                                                        </div>';
                                                $i++;
                                            }
                                            ?>


                                            <!--                                                <div class="col-md-4">-->
                                            <!--                                                    <input type="radio" id="higher_secondary" name="brand" value="higher_secondary">-->
                                            <!--                                                    <label for="higher_secondary" class="rediobutton">Higher Secondary</label>-->
                                            <!--                                                </div>-->
                                            <!--                                                <div class="col-md-2">-->
                                            <!--                                                    <input type="radio" id="graduate" name="brand" value="graduate">-->
                                            <!--                                                    <label for="graduate" class="rediobutton">Graduate</label>-->
                                            <!--                                                </div>-->
                                            <!--                                                <div class="col-md-3">-->
                                            <!--                                                    <input type="radio" id="post_graduate" name="brand" value="post_graduate">-->
                                            <!--                                                    <label for="post_graduate" class="rediobutton">Post Graduate</label>-->
                                            <!--                                                </div>-->
                                        </div>
                                    </div>
                                    <input type="hidden" name="hdn_key" id="hdn_key">
                                    <div class="col-md-12 p-2">
                                        <label class="login">Upload Your Resume (Optional)</label>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="position-relative mx-auto">
                                                    <input type="file" name="cv">
                                                </div>
                                            </div>
                                            <?php
                                            if(!empty($profile_data[0]["resume"])) {
                                            ?>
                                            <div class="col-md-4"><a href="view_my_resume"><button class="btn login-btn col-md-6 py-2" type="button">View</button></a></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-12 mt-5">
                                        <button onclick="return validate();" name="Update_Profile" class="btn login-btn w-100 py-2" type="submit" value="SUBMIT">SAVE & UPDATE</button>
                                    </div>
                                </form>
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
    window.skills = 0;
    window.skills_selected = [];
    function validate()
    {
        var name=document.getElementById('name');
        var job_type=document.getElementById('job_type');
        var city=document.getElementById('city_id');
        var education=document.querySelector('input[name = "education"]:checked');
        var experience=document.querySelector('input[name = "experience"]:checked');
        var gender=document.querySelector('input[name = "gender"]:checked');
        var language=document.querySelector('input[name = "language"]:checked');

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

        if(language == null) {
            alert("Please choose language");
            // experience.focus();
            return false;
        }

        if(gender == null) {
            alert("Please choose gender");
            // experience.focus();
            return false;
        }

        document.getElementById('hdn_key').value=random_string(4)+MD5.hex('<?=$_SESSION["rand"]?>'+name.value+city.value+job_type.value+education.value+experience.value+language.value+gender.value+'<?=$_SESSION["strrand"]?>')+random_string(4);
        return true;
    }
    function add_skill(id,name) {
        if (window.skills < 3) {
            if (window.skills_selected.includes(id.toString())) {
                alert('Skill added alredy');
                return false;
            }
            let skill = `<div>
    <div class="mb-2 alert alert-success alert-dismissible fade show w-100 skill"
         role="alert">
        <h6 class="mb-0" style="max-width: fit-content;">` + name + `</h6>
        <input type="hidden" name="skills[]" value="` + id + `" />
        <button onclick="remove_skill(` + id + `);" type="button" class="btn close delete_skill"
                data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
</div>`;
            window.skills_selected.push(id.toString());
            $('#added_skills').append(skill);
            window.skills++;
        }
    }
</script>

</html>