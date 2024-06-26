<?php
require_once "Admin/Helpers/config_helper.php";
require_once "Admin/Helpers/validation_helper.php";
require_once "Admin/Models/Employees.php";
require_once "Admin/Models/Job_Types.php";
require_once "Admin/Models/City.php";
require_once "Admin/Models/Educations.php";
require_once "Admin/Models/Experiences.php";
require_once "Admin/Models/Site.php";

$Job_Types = new Job_Types($con);
$City = new City($con);
$Education = new Educations($con);
$Experiences = new Experiences($con);
$Site = new Site($con);
$genders = $Site->get_genders();
$job_titles = $Job_Types->get_titles();
$city = $City->get_city();
$education = $Education->get_education();
$experiences = $Experiences->get_experiences();
$languages = $Site->get_language_proficiency();
//var_dump($education);

if (!(isset($_SESSION["employee"]) && $_SESSION["employee"] == 1)) {
    header("location:index");
}

if (isset($_POST["Complete_Profile"])) {
    if (check_hdn_key(["name", "city", "education", "experience", "language", "gender"])) {

        $language = $_POST["language"];

        if (!(validateName($_POST["name"]) && validateDigitOnly($_POST["city"]) && validateDigitOnly($_POST["education"]) && validateDigitOnly($_POST["experience"]) && validateDigitOnly($_POST["gender"]) && validateDigitOnly($language))) {
            set_alert("danger", "Malformed Data .... ");
            header("location:my-profile");
            die();
        }

        if(isset($_POST["skills"])) {
            $skills = $_POST["skills"];
            for($i=0;$i<count($skills);$i++) {
                if(!validateDigitOnly($skills[$i])) {
                    set_alert("danger", "Malformed Data 2.... ");
                    header("location:my-profile");
                    die();
                }
            }
        }

        $final_file_name = "";

        if (check_params($_FILES, ["cv"]) && isset($_FILES["cv"]) && $_FILES['cv']['size'] > 0) {
            $targetDir = "Admin/Public/candidate_resume/";
            $allowedExtensions = array('jpg', 'jpeg', 'png', 'pdf');

            $fileSize = $_FILES['cv']['size'];
            $fileTmpName = $_FILES['cv']['tmp_name'];
            $fileType = $_FILES['cv']['type'];
            $fileExtensionArray = explode('.', $_FILES['cv']['name']);
            $fileExtension = strtolower(end($fileExtensionArray));
            $fileName = "Resume_" . date("YmdHis") . random_string(5) . "." . $fileExtension;
            //            $fileName = "Logo_".$_FILES['logo']['name'];
            $uploadPath = $targetDir . $fileName;

            if (in_array($fileExtension, $allowedExtensions)) {
                if ($fileSize <= 10097152) {
                    if (move_uploaded_file($fileTmpName, $uploadPath)) {
                        $final_file_name = $fileName;
//                    echo "Uploaded file is moved";
                    } else {
                        set_alert("warning", "failed to upload resume ... ");
                    }
                } else {
                    set_alert("danger", "resume file size iss too large please upload lees than 10 MB");
                    header("location:candidate-profile");
                    die();
                }
            } else {
                set_alert("danger", "Please upload valid resume in PDF form");
                header("location:candidate-profile");
                die();
            }
        }

        $Employee = new Employees($con);

//        echo "Okay";
//        var_dump($_POST);
//        exit();
        $employee_id = $Employee->create_employee($_SESSION["phone_no"], $_POST["name"], $_POST["job_type"], $_POST["city"], $_POST["education"], $_POST["experience"], $language, $final_file_name, $_POST["gender"]);
        if ($employee_id) {

            if(isset($_POST["skills"])) {
                $skills = $_POST["skills"];
                for($i=0;$i<count($skills);$i++) {
                    $Employee->create_skill($employee_id,$skills[$i]);
                }
            }

            $_SESSION["setup"] = 1;
            $_SESSION["name"] = $_POST["name"];
            $_SESSION["employee_id"] = $employee_id;
            set_alert("success", "Profile Created Successfully .... ");
        } else {
            set_alert("danger", "Failed to create profile .... code=1");
        }
        header("Location:index");
        exit();
    } else {
        var_dump($_POST);
        exit();
        set_alert("danger", "Failed to create profile ... code=2");
    }
    header("Location:index");
    die();
} else {
    $_SESSION["rand"] = rand(0, 99999999);
    $_SESSION["strrand"] = random_string(10);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('css.php'); ?>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css'>
    <style>
        .login1 {
            display: flex;
            align-items: center;
        }

        .nav-pills.custom li {
            background: transparent;
            z-index: 2;
        }

        .nav-link.active.custom {
            background-color: transparent;
            color: #007bff;
        }

        .nav-link.custom span.icon {
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 50px;
            width: 30px;
            height: 30px;
            margin: 0 auto;
        }

        .nav-link.custom.active span.icon {
            background-color: #007bff;
            color: #fff;
        }

        .nav-link.custom i {
            font-size: 28px;
        }

        @media screen and (max-width: 992px) {
            .card-view {
                padding-right: 15px !important;
            }

            .nav-pills {
                float: left !important;
            }

            .alert-dismissible .close {
                margin: -7px -4px !important;
            }
        }

        .card-view {
            padding-right: 0px;
        }

        .nav-pills .nav-item .active {
            border-bottom: none;
        }

        .nav-link {
            display: block;
            padding: 1px 3px 1px 1px !important;
        }

        .nav-pills {
            float: right;
        }

        .skill {
            background-color: #F4F4F4;
            color: #5C5C5C;
            border-radius: 50px;
            padding: 5px 34px 7px 20px;
            font-weight: 600;
            margin-bottom: 0px;
        }

        .alert-dismissible .close {
            padding: 2px 10px !important;
        }

        .flex-container {
            display: ruby-text;
        }
    </style>
</head>
<body style="background-color:#F2F6FD;">
<img src="img/loginbag.png" alt="Logo" class="img-fluid mobile-view" width="100%"/>
<div class="py-3">
    <div class="container-xxl py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <img src="img/Tankhwaa-Logo.png" alt="Logo" class="img-fluid mb-3" width="140px"/>
                <div class="card">
                    <div class="row">
                        <div class="col-md-9 card-view">
                            <div class="card-header bg-white p-4">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="login1">
                                            <div class="image" style="float: left;">
                                                <img src="img/profile.gif" alt="Logo" class="img-fluid" width="40"/>
                                            </div>
                                            <div class="text" style="float: left;">
                                                <h6 class="mb-0">Complete your profile</h6>
                                                <p class="text-muted mb-0" id="title">Personal Info</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 d-none">
                                        <ul class="nav nav-pills text-center custom mt-2" id="pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active custom" id="pills-1-tab" data-toggle="pill"
                                                   href="#pills-1" role="tab"
                                                   aria-controls="pills-1" aria-selected="true">
                                                    <span class="icon">1</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a onclick="return validate1()" class="nav-link custom" id="pills-2-tab" data-toggle="pill"
                                                   href="#pills-2" role="tab"
                                                   aria-controls="pills-2" aria-selected="false">
                                                    <span class="icon">2</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a onclick="return validate2()" class="nav-link custom" id="pills-3-tab" data-toggle="pill"
                                                   href="#pills-3" role="tab"
                                                   aria-controls="pills-3" aria-selected="false">
                                                    <span class="icon">3</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <ul class="nav nav-pills text-center custom mt-2" id="pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a id="tab1" class="nav-link active custom" role="tab"
                                                   aria-controls="pills-1" aria-selected="true">
                                                    <span class="icon">1</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a id="tab2" class="nav-link custom"
                                                   role="tab"
                                                   aria-controls="pills-2" aria-selected="false">
                                                    <span class="icon">2</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a id="tab3" class="nav-link custom"
                                                   role="tab"
                                                   aria-controls="pills-3" aria-selected="false">
                                                    <span class="icon">3</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" id="verf-login">
                                <form enctype="multipart/form-data" method="post" class="mb-3">
                                    <div class="tab-content custom" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-1" role="tabpanel"
                                             aria-labelledby="pills-home-tab">
                                            <div class="col-md-12 form-group">
                                                <label class="login">Enter Name <span
                                                            style="color:#ff0000;">*</span></label>
                                                <input type="text" id="name" name="name"
                                                       class="form-control login-mobileno" placeholder="Full Name">
                                            </div>

                                            <input type="hidden" name="hdn_key" id="hdn_key">
                                            <div class="col-md-12 form-group">
                                                <label class="login">Email id (Optional)</label>
                                                <input type="email" id="email" class="form-control login-mobileno"
                                                       name="email" placeholder="Email">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label class="login">Location (City) <span
                                                            style="color:#ff0000;">*</span></label>
                                                <div class="login-mobileno"
                                                     style="border-top: 1px solid #dee2e6 !important;border-left: 1px solid #dee2e6 !important;border-right: 1px solid #dee2e6 !important;">
                                                    <select id="city_id" name="city"
                                                            class="form-control login-mobileno">
                                                        <option value="">Choose Location</option>
                                                        <?php
                                                        foreach ($city as $c) {
                                                            echo "<option value='" . $c["city_id"] . "'>" . $c["name"] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <label class="login">Gender <span
                                                            style="color:#ff0000;">*</span></label>
                                                <div class="row">

                                                    <?php
                                                    $i = 0;
                                                    foreach ($genders as $g) {

                                                        if ($g["id"] == 0) {
                                                            continue;
                                                        }

                                                        echo '<div class="col-md-6 col-6">
                                                                    <input type="radio" id="gender' . $i . '" name="gender" value="' . $g["id"] . '">
                                                                    <label for="gender' . $i . '" class="rediobutton">' . $g["name"] . '</label>
                                                                </div>';
                                                        $i++;
                                                    }
                                                    ?>

                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <button type="button" class="btn login-btn w-100 py-2 btn-next"
                                                        validate="1" next="2" data-to="#pills-2-tab" >Next
                                                </button>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pills-2" role="tabpanel"
                                             aria-labelledby="pills-profile-tab">
                                            <div class="col-md-12 form-group">
                                                <a onclick="set_active(2,1)" class="btn-link btn-prev mb-4" data-to="#pills-1-tab"
                                                   style="cursor:pointer;float:right;">
                                                    <i class="fa fa-angle-left mr-2" aria-hidden="true"></i>Back</a>
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label class="login">Highest Education Level<span
                                                            style="color:#ff0000;">*</span></label>
                                                <div class="row">

                                                    <?php
                                                    $i = 0;
                                                    foreach ($education as $e) {
                                                        echo '<div class="col-md-6 col-12">
                                                                    <input type="radio" id="secondary' . $i . '" name="education" value="' . $e["education_id"] . '">
                                                                    <label for="secondary' . $i . '" class="rediobutton">' . $e["name"] . '</label>
                                                                </div>';
                                                        $i++;
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label class="login"> Work Experience <span
                                                            style="color:#ff0000;">*</span></label>
                                                <div class="row">

                                                    <?php
                                                    $i = 0;
                                                    foreach ($experiences as $e) {
                                                        echo '<div class="col-md-6 col-12">
                                                                    <input type="radio" id="experience' . $i . '" name="experience" value="' . $e["experience_id"] . '">
                                                                    <label for="experience' . $i . '" class="rediobutton">' . $e["name"] . '</label>
                                                                </div>';
                                                        $i++;
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label class="login">Language Proficiency <span
                                                            style="color:#ff0000;">*</span></label>
                                                <div class="row">

                                                    <?php
                                                    $i = 0;
                                                    foreach ($languages as $l) {
                                                        echo '<div class="col-md-6 col-12">
                                                                    <input type="radio" id="language' . $i . '" name="language" value="' . $l["id"] . '">
                                                                    <label for="language' . $i . '" class="rediobutton">' . $l["name"] . '</label>
                                                                </div>';
                                                        $i++;
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <button type="button" class="btn login-btn w-100 py-2 btn-next"
                                                        validate="2" next="3" data-to="#pills-3-tab">Next
                                                </button>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pills-3" role="tabpanel"
                                             aria-labelledby="pills-contact-tab">
                                            <div class="col-md-12 form-group">
                                                <a onclick="set_active(3,2);" class="btn-link btn-prev mb-4" data-to="#pills-2-tab"
                                                   style="cursor:pointer;float:right;">
                                                    <i class="fa fa-angle-left mr-2" aria-hidden="true"></i>Back</a>
                                            </div>
                                            <div class="col-md-12 form-group" id="skills">
                                                <label class="login">Add Role / skills <span
                                                            style="color:#ff0000;">*</span></label>
                                                <div type="text"
                                                     class="form-control login-mobileno" placeholder="Eg. Accountant">
                                                    <select class="form-control login-mobileno w-100" id="add-skills" name="add-skills">
                                                        <option value="">Choose Skills</option>
                                                        <?php
                                                            foreach ($job_titles as $skill) {
                                                                echo '<option value="'.$skill["title_id"].'">'.$skill["title"].'</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                                <h6 class="text-muted mb-3 mt-2">You can add upto 3 Skills</h6>
                                            </div>
                                            <div class="pl-3 flex-container" id="added_skills">


<!--                                                <div>-->
<!--                                                    <div class="mb-2 alert alert-success alert-dismissible fade show w-100 skill"-->
<!--                                                         role="alert">-->
<!--                                                        <h6 class="mb-0" style="max-width: fit-content;">Accountant-->
<!--                                                            Accountant Accountant</h6>-->
<!--                                                        <button type="button" class="btn close"-->
<!--                                                                data-dismiss="alert" aria-label="Close">-->
<!--                                                            <span aria-hidden="true">×</span>-->
<!--                                                        </button>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                                <div class="mb-2">-->
<!--                                                    <div class="alert alert-success alert-dismissible fade show w-100 skill"-->
<!--                                                         role="alert">-->
<!--                                                        <h6 class="mb-0" style="max-width: fit-content;">Accountant</h6>-->
<!--                                                        <button type="button" class="btn close"-->
<!--                                                                data-dismiss="alert" aria-label="Close">-->
<!--                                                            <span aria-hidden="true">×</span>-->
<!--                                                        </button>-->
<!--                                                    </div>-->
<!--                                                </div>-->
                                            </div>
                                            <div class="col-md-12 form-group mt-3">
                                                <label class="login">Upload Your Resume <small class="text-muted">(Optional)</small></label>
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <div class="position-relative mx-auto">
                                                            <input type="file" name="cv">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <button onclick="return validate();" name="Complete_Profile"
                                                        class="btn login-btn w-100 py-2" type="submit" value="SUBMIT">
                                                    DONE
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-3" id="yourprofile">
                            <style>
                                #yourprofile {
                                    background-color: #605BE5;
                                }
                            </style>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<?php require_once('js.php'); ?>
<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js'></script>
<?php require_once "dynamic_alert.php"; ?>
<script>
    var candidate_name = document.getElementById('name');
    var job_type = document.getElementById('job_type');
    var city = document.getElementById('city_id');
    var education = document.querySelector('input[name = "education"]:checked');
    var experience = document.querySelector('input[name = "experience"]:checked');
    var gender = document.querySelector('input[name = "gender"]:checked');
    var language = document.querySelector('input[name = "language"]:checked');
    function validate1() {
        gender = document.querySelector('input[name = "gender"]:checked');
        if (candidate_name.value == "" || candidate_name.value.length < 4 || candidate_name.value.length > 100) {
            alert("Please enter valid name!!");
            candidate_name.focus();
            return false;
        }
        if (city.value == "") {
            alert("Please choose the location");
            city.focus();
            return false;
        }

        if (gender == null) {
            alert("Please choose gender");
            // experience.focus();
            return false;
        }
        return true;
    }

    function validate2() {
        education = document.querySelector('input[name = "education"]:checked');
        experience = document.querySelector('input[name = "experience"]:checked');
        language = document.querySelector('input[name = "language"]:checked');

        if (education == null) {
            alert("Please choose education");
            // education.focus();
            return false;
        }

        if (experience == null) {
            alert("Please choose experience");
            // experience.focus();
            return false;
        }

        if (language == null) {
            alert("Please choose language");
            // experience.focus();
            return false;
        }

        return true;
    }

    function validate() {

        if(window.skills == 0) {
            alert("You need to choose at least one skill");
            return false;
        }
        console.log('<?=$_SESSION["rand"]?>',candidate_name.value +","+ city.value +","+ education.value +","+ experience.value +","+ language.value +","+ gender.value,'<?=$_SESSION["strrand"]?>');
        document.getElementById('hdn_key').value = random_string(4) + MD5.hex('<?=$_SESSION["rand"]?>' + candidate_name.value + city.value + education.value + experience.value + language.value + gender.value + '<?=$_SESSION["strrand"]?>') + random_string(4);
        return true;
    }

    // JQUERY
    $(document).ready(function () {
        // Action next
        $('.btn-next').on('click', function () {
            // Get value from data-to in button next
            if($(this).attr("validate") && $(this).attr("validate") == 1) {
                if(validate1()) {
                    const n = $(this).attr('data-to');
                    // Action trigger click for tag a with id in value n
                    $(n).trigger('click');
                    set_active($(this).attr("validate"),$(this).attr("next"));
                }
            } else if ($(this).attr("validate") == 2) {
                if(validate2()) {
                    const n = $(this).attr('data-to');
                    $(n).trigger('click');
                    set_active($(this).attr("validate"),$(this).attr("next"));
                }
            }

        });
        // Action back
        $('.btn-prev').on('click', function () {
            // Get value from data-to in button prev
            const n = $(this).attr('data-to');
            // Action trigger click for tag a with id in value n
            $(n).trigger('click');
        });
    });
</script>
<script>
    window.skills = 0;
    window.skills_selected = [];
    $(document).ready(function () {
        $('#city_id').select2({
            placeholder: 'Choose Location', // Optional placeholder text
            style: 'color:red;'
        });

        $('#add-skills').select2({
            placeholder: 'Choose Skills',
            label : 'Choose Skills',// Optional placeholder text
            width : '100%',
            templateSelection: function (data, container) {
                // Add custom attributes to the <option> tag for the selected option
                // let selected = $('#mySelect2').val();
                // $('#add-skills option[value="' + data.id + '"]').remove();
                // $('#add-skills').trigger('change');
                // console.log(data.text);
                if(window.skills < 3) {
                    if(!data.id || data.id == undefined || data.id == null)
                        return false;
                if(window.skills_selected.includes(data.id.toString())) {
                    alert('Skill added alredy');
                    return false;
                }
                    let skill = `<div>
    <div class="mb-2 alert alert-success alert-dismissible fade show w-100 skill"
         role="alert">
        <h6 class="mb-0" style="max-width: fit-content;">` + data.text + `</h6>
        <input type="hidden" name="skills[]" value="` + data.id + `" />
        <button onclick="remove_skill(` + data.id + `);" type="button" class="btn close delete_skill"
                data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
</div>`;
                    window.skills_selected.push(data.id);
                    $('#added_skills').append(skill);
                    window.skills++;
                }
                if(window.skills == 3){
                    $('#skills').hide();
                }
            }
        }).val(null).trigger('change');

    });

    function remove_skill(id) {
        window.skills--;
        if(window.skills < 3) {
            $('#skills').show();
        }
        window.skills_selected = window.skills_selected.filter(item => item !== id.toString());
    }

    function set_active(current,next) {
        $('#tab'+current).removeClass('active');
        $('#tab'+next).addClass('active');
        if(next == 1) {
            $('#title').text('Personal Information');
        } else if(next == 2) {
            $('#title').text('Education & Work Experience');
        } else if (next == 3) {
            $('#title').text('Skills');
        }
    }

</script>
</html>
