<?php
require_once "../Admin/Helpers/config_helper.php";
require_once "../Admin/Helpers/validation_helper.php";
require_once "../Admin/Models/Employer.php";
require_once "../Admin/Models/Job_Types.php";
require_once "../Admin/Models/City.php";
require_once "../Admin/Models/Educations.php";
require_once "../Admin/Models/Experiences.php";
require_once "../helper.php";

$Job_Types = new Job_Types($con);
$City = new City($con);
$Education = new Educations($con);
$Experiences = new Experiences($con);

$job_types = $Job_Types->get_job_types();
$city = $City->get_city();
$education = $Education->get_education();
$experiences = $Experiences->get_experiences();
//var_dump($education);


$Employer = new Employer($con);
$company_data = $Employer->get_full_employer($_SESSION["employer_id"]);

if (count($company_data) != 1) {
    header("location:index");
    die();
}
$Job_Types = new Job_Types($con);
$job_types = $Job_Types->get_job_types();
if (!check_employer()) {
    header("location:index");
    die();
}

if (isset($_POST["Update_Profile"])) {

    if (check_hdn_key(["name", "email", "address"])) {

        if (!(validateName($_POST["name"]) && validateEmail($_POST["email"]))) {
            set_alert("danger", "Malformed Data Received");
            header("location:company-profile");
            die();
        }

        $final_file_name = $company_data[0]["logo"];
        if (check_params($_FILES, ["logo"]) && isset($_FILES["logo"]) && $_FILES['logo']['size'] > 0) {
            $targetDir = "Admin/Public/company_logos/";
            $allowedExtensions = array('jpg', 'jpeg', 'png');

            $fileSize = $_FILES['logo']['size'];
            $fileTmpName = $_FILES['logo']['tmp_name'];
            $fileType = $_FILES['logo']['type'];
            $fileExtensionArray = explode('.', $_FILES['logo']['name']);
            $fileExtension = strtolower(end($fileExtensionArray));
            $fileName = "Logo_" . date("YmdHis") . random_string(5) . "." . $fileExtension;
            //            $fileName = "Logo_".$_FILES['logo']['name'];
            $uploadPath = $targetDir . $fileName;

            if (in_array($fileExtension, $allowedExtensions)) {
                if ($fileSize <= 10097152) {
                    if (move_uploaded_file($fileTmpName, $uploadPath)) {
                        $final_file_name = $fileName;

                        if($company_data[0]["logo"] != 'company_logo.png') {
                            try {
                                if (file_exists($targetDir . $company_data[0]["logo"])) {
                                    if (unlink($targetDir . $company_data[0]["logo"])) {
                                    } else {
                                    }
                                } else {
                                }
                            } catch (Exception $e) {

                            }
                        }


//                    echo "Uploaded file is moved";
                    } else {
                        set_alert("warning", "failed to upload resume ... ");
                    }
                } else {
                    set_alert("danger", "logo file size iss too large please upload lees than 10 MB");
                    header("location:employer-profile");
                    die();
                }
            } else {
                set_alert("danger", "Please upload valid logo ");
                header("location:employer-profile");
                die();
            }
        } else {
            set_alert("danger", "Please upload valid logo ");
            header("location:employer-profile");
        }

        $address = $_POST["address"];

        $Employee = new Employer($con);
        if ($Employee->update_employer($_SESSION["employer_id"], $_SESSION["phone_no"], $_POST["name"], $final_file_name, $address)) {
            $_SESSION["setup"] = 1;
            $_SESSION["name"] = $_POST["name"];
            $_SESSION["logo"] = $final_file_name;
            set_alert("success", "Profile Updated Successfully .... ");
//            echo json_encode(["status"=>200,"msg"=>"Profile Updated Successfully .... "]);
        } else {
            set_alert("danger", "Failed to update profile .... code=2");
//            set_alert("danger", "Failed to update profile .... code=2");
//            echo json_encode(["status"=>400,"msg"=>"Failed to update profile .... code=2"]);
        }
        header("Location:company-profile");
        exit();
    } else {
        set_alert("danger", "Failed to create profile ... code=1");
//        echo json_encode(["status"=>400,"msg"=>"Failed to create profile ... code=1"]);
    }
    header("Location:company-profile");
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
</head>
<body>
<div style="background-color: #F2F6FD; border-bottom: 1px solid #ddd;">
    <?php require_once('menu.php'); ?>
</div>
<div class="col-md-12 mb-5 bg-white">
    <div class="container-xxl py-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card" style="border-radius: 5px;">
                    <div class="card-body pl-3 pr-3" id="verf-login">
                        <div class="row">
                            <h5 class="mt-2">Company Profile</h5>
                        </div>
                        <form action="?key=<?= get_from_key("G^&56r3gyd7") ?>" id="form" enctype="multipart/form-data" class="mb-3 mt-3" method="post">
                            <!--                            <div class="col-md-9 p-2">-->
                            <!--                                <label class="login">Job Type <span style="color:#ff0000;">*</span></label>-->
                            <!--                                <select readonly="" id="job_type" name="job_type" class="form-control login-mobileno" required>-->
                            <!--                                    --><?php
                            //                                    foreach ($job_types as $type) {
                            //                                        echo '<option value="'.$type["job_type_id"].'">'.$type["name"].'</option>';
                            //                                    }
                            //                                    ?>
                            <!--                                </select>-->
                            <!--                            </div>-->
                            <div class="col-md-8 mt-3">
                                <label class="login">Company Name</label>
                                <input readonly value="<?= $company_data[0]["name"] ?>" id="name" name="name" type="text"
                                       class="form-control login-mobileno" placeholder="Company Name">
                            </div>
                            <div class="col-md-8 mt-3">
                                <label class="login text-muted">Mobile Number</label>
                                <input id="phone_no" value="<?= $_SESSION["phone_no"] ?>" readonly type="text"
                                       class="form-control login-mobileno" placeholder="91 9876543210">
                            </div>
                            <div class="col-md-8 mt-3">
                                <label class="login">Email ID</label>
                                <input readonly id="email" value="<?= $company_data[0]["email"] ?>" type="email" name="email"
                                       class="form-control login-mobileno" placeholder="Enter valid email id">
                            </div>

                            <div class="col-md-8 mt-3">
                                <label class="login">Company Address</label>
                                <textarea id="address" type="text" name="address" class="form-control login-mobileno"
                                          pleaceholder="Enter Address ... "><?= $company_data[0]["address"] ?></textarea>
                            </div>
                            <input type="hidden" name="hdn_key" id="hdn_key">
                            <div class="col-md-8 mt-3">
                                <label class="login">upload new company logo</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="position-relative mx-auto">
                                            <input id="logo" type="file" name="logo">
                                        </div>
                                    </div>
                                </div>
                                <small style="color:#ff0000;">The logo is expected to be 300px by 300px.</small>
                            </div>
                            <div class="col-md-8 mt-3">
                                <label class="login">Current company logo</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="position-relative mx-auto">
                                            <img width="100px" height="100px"
                                                 src="<?= base_url . "Public/company_logos/" . $company_data[0]["logo"] ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="Update_Profile" value="as">
                            <div class="col-lg-4 col-sm-12 mt-5">
                                <input id="submit" onclick="return validate();" name="Update_Profile"
                                       class="btn login-btn w-100 py-2" type="submit"
                                       value="SAVE & UPDATE">
                                <!--                                            <a href="employer-dashboard"><input class="btn login-btn col-md-6 py-2" type="button" value="SUBMIT"></a>-->
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
<?php require_once "dynamic_alert.php" ?>
</html>
<script>
    // function validateImage(file) {
    //     const reader = new FileReader();
    //     reader.readAsDataURL(file);
    //
    //     const image = new Image();
    //     image.src = reader.result;
    //
    //
    //
    //     console.log(image.height,image.width);
    //
    //     // Synchronous check
    //     if (image.style.height === 300 && image.style.width === 300) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

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

    // Example usage:

    // let d = document.getElementById("form");
    // d.addEventListener("submit",async (e)=>{
    //     e.preventDefault();
    //     // if(await validate()) {
    //     //     document.getElementById("form").submit();
    //     // }
    // })

    function validate() {
        var name = document.getElementById('name');
        var email = document.getElementById('email');
        var logo = document.getElementById('logo');
        var address = document.getElementById('address');

        if (name.value == "" || name.value.length < 4 || name.value.length > 100) {
            alert("Please enter valid name!!");
            name.focus();
            return false;
        }
        if (email.value == "") {
            alert("Please enter the email");
            email.focus();
            return false;
        }
        if (address.value == "") {
            alert("Please enter the address");
            address.focus();
            return false;
        }

        // if (logo.files.length != 0 && !await validateImage(logo.files[0])) {
        //     alert("image height should be 300px and width should be 300px");
        //     return false;
        // }

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

        address.value = address.value.replace(/(\r\n|\n|\r)/gm, "");

        document.getElementById('hdn_key').value = random_string(4) + MD5.hex('<?=$_SESSION["rand"]?>' + name.value + email.value + address.value + '<?=$_SESSION["strrand"]?>') + random_string(4);
        // document.getElementById("form").submit();

        // var formData = new FormData(document.getElementById('form'));
        // formData.append("Update_Profile","true");
        // formData.append("logo", logo.files[0]);
        // formData.append("key",encrypt(id,name.value+email.value+""))
        // formData.append("add_key",encrypt(id,address.value.toString()));
        // formData.append("auth_key",id)
        // let data = await fetch('?key=', {
        //     method: "POST",
        //     body: formData
        // }).then(response => response.json());
        //
        //
        // if(data.hasOwnProperty("status") && data.status == 200) {
        //     window.location.reload();
        // } else {
        //     alert(data.msg);
        // }
        //
        // alert('The file has been uploaded successfully.');

        return true;
    }
</script>