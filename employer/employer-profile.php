<?php
require_once "../Admin/Helpers/config_helper.php";
require_once "../Admin/Helpers/validation_helper.php";
require_once "../Admin/Models/Employer.php";
require_once "../Admin/Models/Job_Types.php";
require_once "../Admin/Models/City.php";
require_once "../Admin/Models/Educations.php";
require_once "../Admin/Models/Experiences.php";
if(!(isset($_SESSION["employer"]) && $_SESSION["employer"] == 1)) {
    header("location:index");
}
if (isset($_SESSION["setup"]) && $_SESSION["setup"] == 1) {
    header("Location:index");
}
if(isset($_POST["Complete_Profile"])) {
//    echo "Oksaks";
    if(check_hdn_key(["name","email","address"])) {

        if(!(validateName($_POST["name"]) && validateEmail($_POST["email"]))) {
            set_alert("danger","Malformed Data Received");
            header("location:employer-profile");
            die();
        }

        $final_file_name = "";

        if(check_params($_FILES,["logo"]) && isset($_FILES["logo"]) && $_FILES['logo']['size']>0) {
            $targetDir = "Admin/Public/company_logos/";
            $allowedExtensions = array('jpg', 'jpeg', 'png');

            $fileSize = $_FILES['logo']['size'];
            $fileTmpName = $_FILES['logo']['tmp_name'];
            $fileType = $_FILES['logo']['type'];
            $fileExtensionArray = explode('.', $_FILES['logo']['name']);
            $fileExtension = strtolower(end($fileExtensionArray));
            $fileName = "Logo_".date("YmdHis").random_string(5).".".$fileExtension;
            //            $fileName = "Logo_".$_FILES['logo']['name'];
            $uploadPath = $targetDir . $fileName;

            if (in_array($fileExtension, $allowedExtensions)) {
                if ($fileSize <= 10097152) {
                    if (move_uploaded_file($fileTmpName, $uploadPath)) {
                        $final_file_name = $fileName;
//                    echo "Uploaded file is moved";
                    } else {
                        set_alert("warning","failed to upload resume ... ");
                    }
                } else {
                    set_alert("danger","logo file size iss too large please upload lees than 10 MB");
                    header("location:employer-profile");
                    die();
                }
            } else {
                set_alert("danger","Please upload valid logo ");
                header("location:employer-profile");
                die();
            }
        } else {
//            set_alert("danger","Please upload valid logo ");
//            header("location:employer-profile");
//            die();
            $final_file_name = "company_logo.png";
        }

        $Employee= new Employer($con);

        $employee_id = $Employee->create_employer($_SESSION["phone_no"],$_POST["name"],$_POST["email"],$final_file_name,$_POST["address"]);
        if($employee_id) {
            $_SESSION["setup"] = 1;
            $_SESSION["name"] = $_POST["name"];
            $_SESSION["employer_id"] = $employee_id;
            set_alert("success","Profile Created Successfully .... ");
        } else {
            set_alert("danger","Failed to create profile .... code=2");
        }
//        header("Location:employer-dashboard");
        header("Location:post-new-job");
        exit();
    } else {
        set_alert("danger","Failed to create profile ... code=1");
    }
    header("Location:index");
    die();
} else {
    $_SESSION["rand"] = rand(0, 99999999);
    $_SESSION["strrand"] = random_string(10);
}
$Job_Types = new Job_Types($con);
$job_types = $Job_Types->get_job_types();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('css.php'); ?>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css'>
    <style>
        .card-view{padding-right: 0px;}
    </style>
</head>
<body style="background-color:#F2F6FD;">
<img src="img/loginbag.png" alt="Logo" class="img-fluid mobile-view" width="100%" />
<div class="py-3">
    <div class="container-xxl py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <img src="img/Tankhwaa-Logo.png" alt="Logo" class="img-fluid mb-3" width="140px" />
                <div class="card">
                    <div class="row">
                        <div class="col-md-9 card-view">
                            <div class="card-header bg-white p-4">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="login1">
                                            <div class="image" style="float: left;">
                                                <img src="img/profile.gif" alt="Logo" class="img-fluid" width="40" />
                                            </div>
                                            <div class="text" style="float: left;">
                                                <h6 class="mb-0">Complete your profile</h6>
                                                <p class="text-muted mb-0">Company Info</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" id="verf-login">
                                <form  method="post" enctype="multipart/form-data" class="mb-3">
                                    <div class="col-md-12 form-group">
                                        <label class="login">Company Name <span style="color:#ff0000;">*</span></label>
                                        <input id="name" name="name" type="text" class="form-control login-mobileno" placeholder="Company Name">
                                    </div>
                                    <!--<div class="col-md-12 form-group">
                                            <label class="login">Mobile Number <span style="color:#ff0000;">*</span></label>
                                            <input id="phone_no" value="<?= $_SESSION["phone_no"] ?>" readonly type="text" class="form-control login-mobileno" placeholder="91 9876543210">
                                        </div>-->
                                    <div class="col-md-12 form-group">
                                        <label class="login">Email ID <span style="color:#ff0000;">*</span></label>
                                        <input id="email" type="email" name="email" class="form-control login-mobileno" placeholder="Enter valid email id">
                                    </div>

                                    <!--<div class="col-md-12 form-group">
                                            <label class="login">Job Type <span style="color:#ff0000;">*</span></label>
                                            <select id="job_type" name="job_type" class="form-control login-mobileno" required>
                                                <option value="">Select Job Type</option>
                                                <?php
                                    foreach ($job_types as $type) {
                                        echo '<option value="'.$type["job_type_id"].'">'.$type["name"].'</option>';
                                    }
                                    ?>
                                            </select>
                                            <span style="text-size:20px;" class="text-danger">It can't be changed or modified </span>
                                        </div>-->

                                    <div class="col-md-12 form-group">
                                        <label class="login">Company Address <span style="color:#ff0000;">*</span></label>
                                        <textarea id="address" type="text" name="address" class="form-control login-mobileno" pleaceholder="Enter Address ... "></textarea>
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <label class="login">Company Logo <small class="text-muted">(Optional)</small></label>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="position-relative mx-auto">
                                                    <input id="logo" type="file" name="logo">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="hdn_key" id="hdn_key">

                                    <div class="col-lg-12 col-sm-12 mt-3">
                                        <input name="Complete_Profile" class="btn login-btn w-100 py-2 btn-next" type="submit" onclick="return validate();" value="Submit & Post New Job">
                                        <!--                                            <a href="employer-dashboard"><input class="btn login-btn col-md-6 py-2" type="button" value="SUBMIT"></a>-->
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-3" id="yourprofile">
                            <style>
                                #yourprofile{background-color:#605BE5;}
                            </style>
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
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js'></script>
</html>
<script>
    document.getElementById("logo").addEventListener("input", validateImage);

    function validateImage(e) {

        if (e.target.files.length == 0) {
            alert("please choose logo");
            return false;
        }

        let file = e.target.files[0];

        const image = new Image();

        image.onload = function () {
            // Asynchronous check
            if (image.height === 300 && image.width === 300) {
                window.image = true;
            } else {
                alert("image height should be 300px and width should be 300px");
                e.target.focus();
                window.image = false;
            }
        };

        image.onerror = function (error) {
            alert("Error occured in image");
        };

        image.src = URL.createObjectURL(file);
    }

    function validate()
    {
        var name=document.getElementById('name');
        var email=document.getElementById('email');
        var logo=document.getElementById('logo');
        var address = document.getElementById('address');

        if(name.value =="" || name.value.length<4 || name.value.length>100)
        {
            alert("Please enter valid name!!");
            name.focus();
            return false;
        }
        if(email.value=="")
        {
            alert("Please enter the email");
            email.focus();
            return false;
        }
        if(address.value=="")
        {
            alert("Please enter the address");
            address.focus();
            return false;
        }

        if (logo.files && logo.files.length != 0 && window.image != true ) {
            alert("image height should be 300px and width should be 300px");
            return false;
        } else if (logo.files && logo.files.length != 0) {
            const fileSizeInBytes = logo.files[0].size;
            const maxSizeInBytes = 10 * 1024 * 1024;

            if (fileSizeInBytes > maxSizeInBytes) {
                alert('File size exceeds the limit. Please choose a image smaller than 10MB.');
                return false;
            }
        }

        // if(logo.value =="") {
        //     alert("Please choose logo ");
        //     logo.focus();
        //     return false;
        // }

        document.getElementById('hdn_key').value=random_string(4)+MD5.hex('<?=$_SESSION["rand"]?>'+name.value+email.value+address.value+'<?=$_SESSION["strrand"]?>')+random_string(4);
        return true;
    }
</script>