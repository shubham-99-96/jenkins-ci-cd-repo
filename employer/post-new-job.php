<?php
require_once "../Admin/Helpers/config_helper.php";
require_once "../Admin/Helpers/validation_helper.php";
require_once "../Admin/Models/Employer.php";
require_once "../Admin/Models/Site.php";
require_once "../Admin/Models/Job_Types.php";
require_once "../Admin/Models/City.php";
require_once "../Admin/Models/Educations.php";
require_once "../Admin/Models/Experiences.php";
require_once "../Admin/Models/Jobs.php";
require_once "../helper.php";

if (!check_employer_profile($con,$_SESSION["phone_no"])) {
    set_alert("warning","Please complete your profile first");
    header("location:index");
    die();
}

if(!check_employer_login()) {
    set_alert("danger","Please login ...");
    header("location:employer-login");
    die();
}

$Job_Types = new Job_Types($con);
$City = new City($con);
$Site = new Site($con);
$Education = new Educations($con);
$Experiences = new Experiences($con);
$Employer = new Employer($con);

$data = $Employer->get_full_employer($_SESSION["employer_id"]);

//$job_types = $Job_Types->get_job_types($data[0]["job_type_id"]);
$job_types = $Job_Types->get_job_types();
$city = $City->get_operational_city();
$education = $Education->get_education();
$experiences = $Experiences->get_experiences();
$genders = $Site->get_genders();
$type_of_job = $Site->get_type_of_job();

$language_proficiency = $Site->get_language_proficiency();

$work_type = $Site->get_work_type();
//var_dump($education);

$Employer = new Employer($con);
//$company_data = $Employer->get_full_employer($_SESSION["employer_id"]);
//
//if(count($company_data) != 1) {
//    header("location:index");
//    die();
//}


if (isset($_POST["Create_Job"])) {
    if (check_form_key("K#@*eo*h0890")) {
        if (check_params($_POST, ["title","title_id", "city", "type_of_job", "gender", "education", "experience", "salary_from", "salary_to"]) && isset($_POST["language"]) && isset($_POST["language"][1])) {
            /*        document.getElementById('hdn_key').value=random_string(4)+MD5.hex('<?=$_SESSION["rand"]?>'+title.value+desc.value+city.value+type_of_job.value+gender.value+education.value+english.value+experience.value+'<?=$_SESSION["strrand"]?>')+random_string(4);*/

            $_POST["language"] = $_POST["language"][1];

            if (check_hdn_key(["job_type_id","title","title_id", "city", "type_of_job", "gender", "education", "language", "experience", "work_type"])) {

                $job_type_id = $_POST["job_type_id"];
                $title = $_POST["title"];
                $title_id = $_POST["title_id"];
                $desc = "";

                if (isset($_POST["desc"])) {
                    $desc = $_POST["desc"];
                }

                $city = $_POST["city"];
                $type_of_job = $_POST["type_of_job"];
                $gender = $_POST["gender"];
                $education = $_POST["education"];
                $experience = $_POST["experience"];
                $language = 1;
//                $proficiency = $_POST["language"];
                $salary_from = $_POST["salary_from"];
                $salary_to = $_POST["salary_to"];
                $work_type = $_POST["work_type"];


                if (!(validateDigitOnly($city) && validateDigitOnly($type_of_job) && validateDigitOnly($gender) && validateDigitOnly($education) && validateDigitOnly($experience) && validateDigitOnly($language) && validateDigitOnly($salary_from) && validateDigitOnly($salary_to))) {
                    set_alert("danger", "Malformed Data .... ");
                    header("location:post-new-job");
                    die();
                }

//                if (!empty($desc) && validate_address($desc) && strlen($desc) < 80) {
//                    set_alert("danger", "Job description should be at least 80-100 characters long");
//                    header("location:post-new-job");
//                    die();
//                }

                if (!empty($desc) && strlen($desc)>3000) {
                    set_alert("danger", "Job description should be less than 3000 characters .... ");
                    header("location:post-new-job");
                    die();
                }

                if($title_id == 0) {
                    $title_id = $Job_Types->create_job_title($job_type_id,$title,$_SESSION["employer_id"]);
                }

                $Jobs = new Jobs($con);
                if ($Jobs->create_job($_SESSION["employer_id"],$title_id, $job_type_id, $desc, $city, $type_of_job, $gender, $education, $experience, 1, $language, $salary_from, $salary_to, $work_type, $_SESSION["employer_id"])) {
                    set_alert("success", "Job Created Successfully");
                    header("location:employer-dashboard");
                    die();
                } else {
                    set_alert("danger", "Failed to create job");
                    header("location:post-new-job");
                    die();
                }
            } else {
                set_alert("danger", "Invalid Request code=3");
                header("location:post-new-job");
                die();
            }
        } else {
            set_alert("danger", "Invalid Request code=2");
            header("location:post-new-job");
            die();
        }
    } else {
        set_alert("danger", "Invalid Request code=1");
        header("location:post-new-job");
        die();
    }
} else {
    $_SESSION["rand"] = rand(0, 99999999);
    $_SESSION["strrand"] = random_string(10);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('css.php'); ?>
    <style>
        /* Remove borders from Select2 container */
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
<div style="background-color: #F2F6FD; border-bottom: 1px solid #ddd;">
    <?php require_once('menu.php'); ?>
</div>
<div class="col-md-12 mb-5 bg-white">
    <div class="container-xxl py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card" style="border-radius: 5px;">
                    <div class="card-body pl-3 pr-3">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="mt-2">Post New Job <small class="text-muted" style="font-weight:normal;">
                                        (Enter Job Details)</small></h5>
                            </div>
                            <div class="col-md-6" align="right">
                                <small style="color:#ff0000;">*Marked Fields are mandatory</small>
                            </div>
                        </div>
                        <form method="post" class="mb-3 mt-3" action="?key=<?= get_from_key("K#@*eo*h0890") ?>">
                            <!--                <div class="col-md-4 mb-2">-->
                            <!--                    <label class="login">Company Name</label>-->
                            <!--                    <input type="text" class="form-control login-mobileno" placeholder="Company Name">-->
                            <!--                </div>-->
                            <div class="row">
                                <label class="login">Job Title/ Designation <span style="color:#ff0000;">*</span></label>
                                <div class="col-md-4 mb-3">
                                    <input autocomplete="on" class="form-control login-mobileno" type="text" name="title" id="title"
                                           placeholder="Please enter job title"/>
                                    <input type="hidden" name="title_id" id="title_id" value="0">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="login">Job Type <span style="color:#ff0000;">*</span></label>
                                    <div class="login-mobileno border">
                                        <select name="job_type_id" id="job_type_id" class="form-control login-mobileno">
                                            <!--                                        <option value="">Choose Job Title</option>-->
                                            <?php
                                            foreach ($job_types as $job_type) {
                                                echo "<option value='" . $job_type["job_type_id"] . "'>" . $job_type["name"] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="login">Job Location <span style="color:#ff0000;">*</span></label>
                                    <select id="city" name="city" class="form-control login-mobileno">
                                        <option value="">Choose Location</option>
                                        <?php
                                        foreach ($city as $c) {
                                            echo "<option value='" . $c["city_id"] . "'>" . $c["name"] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8  mb-3">
                                <label class="login">Job Description</label>
                                <!--                                <textarea id="desc" name="desc" type="text" role="3" class="form-control login-mobileno"-->
                                <!--                                          placeholder="Job Description  (Job description should be at least 80-100 characters long) "></textarea>-->

                                <textarea id="desc" name="desc" type="text" role="3" class="form-control login-mobileno" placeholder="Job Description"></textarea>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="login">Type Of Job <span style="color:#ff0000;">*</span></label>
                                <div class="row">

                                    <?php
                                    $i = 0;
                                    foreach ($type_of_job as $t) {
                                        echo '<div class="col-md-3 col-6">
                                                       <input type="radio" id="type_of_job' . $i . '" name="type_of_job" value="' . $t["id"] . '">
                                                       <label for="type_of_job' . $i . '" class="rediobutton">' . $t["name"] . '</label>
                                                  </div>';
                                        $i++;
                                    }
                                    ?>

                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="login">Who can apply? <span style="color:#ff0000;">*</span></label>
                                <div class="row">
                                    <?php
                                    $i = 0;
                                    foreach ($genders as $g) {
                                        echo '<div class="col-md-3 col-6 mb-2">
                                                       <input type="radio" id="gender' . $i . '" name="gender" value="' . $g["id"] . '">
                                                       <label for="gender' . $i . '" class="rediobutton">' . $g["name"] . '</label>
                                                  </div>';
                                        $i++;
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="login">Minimal educational qualification<span style="color:#ff0000;">*</span></label>
                                <div class="row">

                                    <?php
                                    $i = 0;
                                    foreach ($education as $e) {
                                        echo '<div class="col-lg-3 col-md-3 col-sm-12 mb-2">
                                                    <input type="radio" id="secondary' . $i . '" name="education" value="' . $e["education_id"] . '">
                                                    <label for="secondary' . $i . '" class="rediobutton">' . $e["name"] . '</label>
                                                </div>';
                                        $i++;
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="login">Language proficiency required<span style="color:#ff0000;">*</span></label>
                                <div class="row">
                                    <?php
                                    $i = 0;
                                    foreach ($language_proficiency as $l) {
                                        echo '<div class="col-md-3 col-6 mb-2">
                                                       <input type="radio" id="lgp' . $i . '" name="language[1]" value="' . $l["id"] . '">
                                                       <label for="lgp' . $i . '" class="rediobutton">' . $l["name"] . '</label>
                                                  </div>';
                                        $i++;
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="login">Total experience needed <span style="color:#ff0000;">*</span></label>
                                <div class="row">
                                    <?php
                                    $i = 0;
                                    foreach ($experiences as $e) {
                                        echo '<div class="col-lg-3 col-md-3 col-sm-12 mb-2">
                                                    <input type="radio" id="experience' . $i . '" name="experience" value="' . $e["experience_id"] . '">
                                                    <label for="experience' . $i . '" class="rediobutton">' . $e["name"] . '</label>
                                                </div>';
                                        $i++;
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="login">Work Mode <span style="color:#ff0000;">*</span></label>
                                <div class="row">
                                    <?php
                                    $i = 0;
                                    foreach ($work_type as $w) {
                                        echo '<div class="col-md-3 col-12 mb-2">
                                                    <input type="radio" id="work_type' . $i . '" name="work_type" value="' . $w["id"] . '">
                                                    <label for="work_type' . $i . '" class="rediobutton">' . $w["name"] . '</label>
                                                </div>';
                                        $i++;
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <label class="login">Salary Range <span style="color:#ff0000;">*</span></label>
                                <div class="col-md-4 col">
                                    <input id="salary_from" name="salary_from" placeholder="Salary From"
                                           class="form-control login-mobileno"
                                           style="padding-left: 25px;background: url('img/rsbox.png') no-repeat left;background-size:15px;"/>
                                </div>
                                <div class="col-md-4 col">
                                    <input id="salary_to" name="salary_to" placeholder="Salary To"
                                           class="form-control login-mobileno"
                                           style="padding-left: 25px;background: url('img/rsbox.png') no-repeat left;background-size:15px;"/>
                                </div>
                            </div>
                            <input type="hidden" id="hdn_key" name="hdn_key">
                            <div class="col-lg-4 col-sm-12 mt-5">
                                <!--                    <a href="employer-dashboard"><input class="btn login-btn col-md-2 py-2" type="button" value="POST JOB"></a>-->
                                <input class="btn login-btn w-100 py-2" type="submit" onclick="return validate();"
                                       name="Create_Job" value="POST JOB">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once('footer.php'); ?>
</body>
<?php require_once('js.php'); ?>
<script>
    var salary_from = document.getElementById('salary_from');
    var salary_to = document.getElementById('salary_to');
    var category = document.getElementById('category_id');

    // function validateAddress(input) {
    //     // var pattern = /^[0-9A-Za-z\s\.,#&\-\n]+$/;
    //     var pattern = /^\s*(\d{1,4}[\w\s,-]+)\s*,?\s*(\w[\w\s,-]+)\s*,?\s*(\w[\w\s,-]+)\s*,?\s*(\d{5}(-\d{4})?)\s*$/;
    //     // input = input.replace(/\s+/g, ' ').trim();
    //     return pattern.test(input);
    // }


    function validate() {
        var title = document.getElementById('title');
        var job_type_id = document.getElementById('job_type_id');
        var title_id = document.getElementById('title_id');
        var desc = document.getElementById('desc');
        var city = document.getElementById('city');
        var type_of_job = document.querySelector('input[name = "type_of_job"]:checked');
        var gender = document.querySelector('input[name = "gender"]:checked');
        var education = document.querySelector('input[name = "education"]:checked');
        var english = document.querySelector('input[name = "language[1]"]:checked');
        var experience = document.querySelector('input[name = "experience"]:checked');
        var work_type = document.querySelector('input[name = "work_type"]:checked');
        var salary_from = document.getElementById('salary_from');
        var salary_to = document.getElementById('salary_to');

        if(job_type_id.value =="")
        {
            alert("Please select job type!!");
            job_type_id.focus();
            return false;
        }
        if (city.value == "") {
            alert("Please select the city");
            city.focus();
            return false;
        }

        // if (desc.value != "" && !validateAddress(desc.value)) {
        //     alert("Please enter valid job description");
        //     desc.focus();
        //     return false;
        // }

        // if (desc.value != "" && desc.value.length > 3000) {
        //     alert("Job description should be less than 3000 characters");
        //     return false;
        // }

        if (type_of_job == null) {
            alert("Please choose type of job");
            return false;
        }
        if (gender == null) {
            alert("Please choose gender");
            return false;
        }
        if (work_type == null) {
            alert("Please choose work mode");
            return false;
        }
        if (education == null) {
            alert("Please choose education");
            return false;
        }
        if (english == null) {
            alert("Please choose english proficiency");
            return false;
        }
        if (experience == null) {
            alert("Please choose experience ");
            return false;
        }
        if (salary_from.value == "") {
            alert("Please enter salary range");
            salary_from.focus();
            return false;
        }
        if (salary_to.value == "") {
            alert("Please enter salary range");
            salary_to.focus();
            return false;
        }
        if(parseInt(salary_from.value) > parseInt(salary_to.value)) {
            alert("Please enter valid salary range");
            salary_from.focus();
            return false;
        } else {
            console.log(salary_from.value,">",salary_to.value);
            console.log(salary_from.value>salary_to.value)
        }
        // return false;
        document.getElementById('hdn_key').value = random_string(4) + MD5.hex('<?=$_SESSION["rand"]?>' +job_type_id.value+title.value+title_id.value+ city.value + type_of_job.value + gender.value + education.value + english.value + experience.value + work_type.value + '<?=$_SESSION["strrand"]?>') + random_string(4);
        return true;
    }


    function allowDigitsOnly(event) {
        // Get the value of the input field
        const inputValue = event.target.value;

        // Check if the input value contains non-digit characters
        if (/[^0-9]/.test(inputValue)) {
            // Remove non-digit characters from the input value
            event.target.value = inputValue.replace(/[^0-9]/g, '');
        }
    }

    // Get the input fiel

    // Attach the event listener to the input field
    salary_to.addEventListener('input', allowDigitsOnly);
    salary_from.addEventListener('input', allowDigitsOnly);

    function get_job_titles(e) {
        let id = Math.floor((Math.random() * 5) + 1);
        let job_type_id = document.getElementById("job_type_id").value;
        //if (job_category_id.toString().trim() != "") {
        //    $.ajax({
        //        type: "POST",
        //        url: "<?php //= base_url ?>//Public/API/get_job_titles.php?key=" + encrypt(id, "DW%^35g2d778" + id), // Replace with your server endpoint
        //        data: {
        //            key: encrypt(id, "TW%fu3vtyufa5dh" + id + job_category_id),
        //            job_type_id: job_category_id,
        //            auth_key: id
        //        },
        //        success: function (response) {
        //            // Handle the successful response here
        //            // console.log("Response: " , response);
        //            //var job_types = <?php ////= json_encode($job_types) ?>////;
        //
        //            let final_json = [];
        //
        //            let data = JSON.parse(response);
        //            if (data["status"] == 200) {
        //                // var job_types = JSON.parse(response["data"]);
        //                // $("#job_type").autocomplete({
        //                //     source: job_types,
        //                //     select: function (event, ui) {
        //                //         $("#job_type_id").val(ui.item.id);
        //                //     }
        //                // });
        //                let d = data["data"];
        //                for (let i in d) {
        //                    final_json.push({label: d[i]["title"], value: d[i]["title"], id: d[i]["job_type_id"]});
        //                }
        //                $("#job_type").autocomplete({
        //                    source: final_json,
        //                    select: function (event, ui) {
        //                        $("#job_type_id").val(ui.item.id);
        //                    }
        //                });
        //                console.log(data, final_json);
        //            } else {
        //                console.error("No data found");
        //            }
        //        },
        //        error: function (xhr, status, error) {
        //            // Handle errors
        //            console.error("Error: " + error);
        //        }
        //    });
        //}
        if (job_type_id.toString().trim() !== "") {
            $.ajax({
                type: "POST",
                url: "<?= base_url ?>Public/API/get_job_titles.php?key=" + encrypt(id, "DW%^35g2d778" + id), // Check URL
                data: {
                    key: encrypt(id, "TW%fu3vtyufa5dh" + id + job_type_id),
                    job_type_id: job_type_id,
                    auth_key: id
                },
                success: function (response) {
                    let final_json = [];
                    let data = JSON.parse(response);
                    if (data["status"] === 200) {
                        let d = data["data"];
                        for (let i in d) {
                            final_json.push({ label: d[i]["title"], value: d[i]["title"], id: d[i]["title_id"],job_type : d[i]["job_type_id"] });
                        }
                        $("#title").autocomplete({
                            source: final_json,
                            select: function (event, ui) {
                                if (ui.item) {
                                    $("#title_id").val(ui.item.id); // Set ID if item is selected
                                    // $("#job_type_id").val(ui.item.job_type);
                                    $('#job_type_id').val(ui.item.job_type).trigger('change');
                                } else {
                                    $("#title_id").val(0); // Set ID to 0 if no item is selected
                                }
                            },
                            change : function (event,ui) {
                                if (ui.item) {
                                    $("#title_id").val(ui.item.id); // Set ID if item is selected
                                    // $("#job_type_id").val(ui.item.job_type);
                                    $('#job_type_id').val(ui.item.job_type).trigger('change');
                                } else {
                                    $("#title_id").val(0); // Set ID to 0 if no item is selected
                                }
                            }
                        });
                        console.log(data, final_json);
                    } else {
                        console.error("No data found");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error: " + error);
                }
            });
        }

    }



    // document.getElementById("job_type_id").addEventListener("input", get_job_titles)
    get_job_titles();
</script>
<script>
    $(document).ready(function() {
        $('#job_type_id').select2({
            placeholder: 'Choose Job Title', // Optional placeholder text
            style : 'color:red;'
        });
    });
</script>
</html>