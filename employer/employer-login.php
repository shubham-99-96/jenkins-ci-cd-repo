<?php
require_once "../Admin/Helpers/config_helper.php";
require_once "../Admin/Helpers/validation_helper.php";
require_once "../helper.php";
require_once "../Admin/Models/Employer.php";
require_once "../Admin/Models/Users.php";

if(isset($_SESSION["setup"]) && $_SESSION["setup"] == 0) {
    if(!isset($_SESSION["employer"])) {
        header("location:logout");
        die();
    }
    if($_SESSION["employer"] == 1) {
        header("Location:employer-profile");
    } else {
        header("Location:index");
    }
    die();
} else if (isset($_SESSION["setup"]) && $_SESSION["setup"] == 1) {
    header("Location:index");
}

if(isset($_POST["send_otp"])) {
    if(check_params($_POST,["auth_key","key","phone_no"]) && check_params($_GET,["key"])) {
        if(check_key($_POST["auth_key"], ["$#W&YGys463e7",$_POST["phone_no"],$_POST["auth_key"]],$_POST["key"]) && check_key($_POST["auth_key"],["@EH7478fh78y",$_POST["auth_key"]],$_GET["key"])) {
            $phone_no = $_POST["phone_no"];

            if(!validateDigitOnly($phone_no)) {
                echo json_encode(["status"=>400,"msg"=>"Invalid Mobile No.."]);
                exit();
            }
            $Employer = new Employer($con);
            $_SESSION["phone_no"] = $phone_no;
            $_SESSION["verified"] = 1;
            $_SESSION["employer"] = 1;
            if(check_employer_profile($con,$phone_no)) {
                $details = $Employer->get_employer_by_phone_no($phone_no);
                if(count($details) == 1) {
                    $_SESSION["employer_id"] = $details[0]["employee_id"];
                    $_SESSION["name"] = $details[0]["name"];
                    $_SESSION["logo"] = $details[0]["logo"];
                    echo json_encode(["status" => 200, "msg" => "Done..........."]);
                    set_alert("success","Logged in successfully .... ");
                } else {
                    echo json_encode(["status" => 200, "msg" => "Done..........."]);
                }
            } else {
                echo json_encode(["status"=>201,"msg"=>"Done..........."]);
            }
//            $Employees = new Employees($con);
//            $otp = $Employees->send_otp($phone_no);
//            if($otp) {
//                echo json_encode(["status"=>200,"msg"=>"Done..........."]);
//            } else {
//                echo json_encode(["status"=>400,"msg"=>"Invalid Request code=3"]);
//            }
        } else {
            echo json_encode(["status"=>400,"msg"=>"Invalid Request code=2 .... !"]);
        }
    } else {
        echo json_encode(["status"=>400,"msg"=>"Invalid Request code=1 .... !"]);
    }
    exit();
}

//if(isset($_POST["verify_otp"])) {
//    if(check_params($_POST,["auth_key","key","phone_no","otp"]) && check_params($_GET,["key"])) {
//        if(check_key($_POST["auth_key"], ["S&*^#$&df63e7",$_POST["phone_no"],$_POST["otp"],$_POST["auth_key"]],$_POST["key"]) && check_key($_POST["auth_key"],["@#ewbih78s#h78y",$_POST["auth_key"]],$_GET["key"])) {
//            $phone_no = $_POST["phone_no"];
//            $otp = $_POST["otp"];
//            $Employer = new Employer($con);
//            $otp = $Employer->verify_otp($phone_no,$otp);
//            if($otp) {
//
//                $_SESSION["phone_no"] = $phone_no;
//                $_SESSION["verified"] = 1;
//                $_SESSION["employer"] = 1;
//                if(check_employer_profile($con,$phone_no)) {
//                    $details = $Employer->get_employer_by_phone_no($phone_no);
//                    if(count($details) == 1) {
//                        $_SESSION["employer_id"] = $details[0]["employee_id"];
//                        $_SESSION["name"] = $details[0]["name"];
//                        $_SESSION["logo"] = $details[0]["logo"];
//                        $_SESSION["job_type_id"] = $details[0]["job_type_id"];
//                        echo json_encode(["status" => 200, "msg" => "Done..........."]);
//                        set_alert("success","Logged in successfully .... ");
//                    } else {
//                        echo json_encode(["status" => 200, "msg" => "Done..........."]);
//                    }
//                } else {
//                    echo json_encode(["status"=>201,"msg"=>"Done..........."]);
//                }
//
////                echo json_encode(["status"=>200,"msg"=>"Otp Verified Successfully ..........."]);
//            } else {
//                echo json_encode(["status"=>400,"msg"=>"Wrong Otp ....... "]);
//            }
//        } else {
//            echo json_encode(["status"=>400,"msg"=>"Invalid Request code=2 .... !"]);
//        }
//    } else {
//        echo json_encode(["status"=>400,"msg"=>"Invalid Request code=1 .... !"]);
//    }
//    exit();
//}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('css.php'); ?>
    <style>
		.resend{color:#4F5E64;cursor:pointer;}
		.resend:hover{color:#605BE5;cursor:pointer;}
	</style>
</head>
<body style="background-color:#F2F6FD;">
	<img src="img/loginbag.png" alt="Logo" class="img-fluid mobile-view" width="100%" />
    <div class="py-3">
        <div class="container-xxl py-5">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card mt-3">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card-body" id="verf-login">
                                    <div class="col-md-12">
                                        <img src="img/Tankhwaa-Logo.png" alt="Logo" class="img-fluid mb-4" width="140px" />
                                        <p class="login"><img src="img/handshake.gif" alt="Logo" class="img-fluid" width="40" /> 
                                        HEY, <span style="color:#605BE5;">LOGIN</span> WITH  TANKHWAA</p>
                                        <form class="mb-3">
                                            <div class="col-md-12 p-2">
                                                <label class="login">Enter your mobile number</label>
                                                <input type="text" id="phone_no" class="form-control mb-4 login-mobileno" placeholder="For eg. 9876543210">
                                               
                                                <div class="col-12">
                                                    <!--<input onclick="return send_otp();" class="btn login-btn w-100 py-2" type="button" value="NEXT">-->
                                                    <button id="btn_send_otp" class="btn login-btn w-100 py-2" type="button" onclick="return send_otp();">
                                                         <span style="margin-right: 20px;">NEXT</span> 

                                                        <span id="send_otp_loader" class="spinner-border d-none" style="width:20px;height:20px;"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        <p class="login-term">By continuing, you agree to the Tankhwaa’s <br>
                                            <a href="Terms-and-conditions">Terms and conditions</a> and <a href="Privacy-Policy">Privacy Policy</a></p>
                                    </div>      
                                </div>
                                
                                <div class="card-body" id="otplogin" style="display:none;">
                                    <div class="col-md-12">
                                        <img src="img/Tankhwaa-Logo.png" alt="Logo" class="img-fluid mb-3" width="140px" />
                                        <p class="login"><img src="img/phone-contact.gif" alt="Logo" class="img-fluid" width="60" />
                                        <p>We have sent an OTP on-<span id="dis_phone_no">8545465454</span></p>
                                        <h5>Enter OTP</h5>
                                        <form class="mb-3">
                                            <div class="row p-2">
                                            	<div class="col-md-2 col">
                                                	<input maxlength="1" type="number" class="form-control mb-4 login-mobileno lgotp" style="text-align: center;">
                                                </div>
                                                <div class="col-md-2 col">
                                                	<input maxlength="1" type="number" class="form-control mb-4 login-mobileno lgotp" style="text-align: center;">
                                                </div>
                                                <div class="col-md-2 col">
                                                	<input maxlength="1" type="number" class="form-control mb-4 login-mobileno lgotp" style="text-align: center;">
                                                </div>
                                                <div class="col-md-2 col">
                                                	<input maxlength="1" type="number" class="form-control mb-4 login-mobileno lgotp" style="text-align: center;">
                                                </div>
                                                <div class="col-md-2 col">
                                                	<input maxlength="1" type="number" class="form-control mb-4 login-mobileno lgotp" style="text-align: center;">
                                                </div>
                                                <div class="col-md-2 col">
                                                	<input maxlength="1" type="number" class="form-control mb-4 login-mobileno lgotp" style="text-align: center;">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <a><input id="btn_verify" onclick="return verify_otp()" class="btn login-btn w-100 py-2" type="button" value="NEXT"></a>
                                            </div>
                                        </form>
                                        <p class="login-term">Didn't receive OTP?  <button onclick="return resend_otp();" id="resend_button" class="resend btn btn-link">RESEND</button></p>
                                    </div>      
                                </div>
                            </div>    
                            <div class="col-md-6 login-img">
                                <img src="img/loginimg.png" alt="login img" class="img-fluid d-sm-none d-lg-block" />
                            </div>
                        </div>
                    </div>
                </div>
                <b align="center" class="mt-2"> <a href="<?= public_url ?>">Looking for a Jobs?</a></b>
            </div>
        </div>
    </div> 
</body>
	<?php require_once('js.php'); ?>
<script>
    let id = Math.floor((Math.random() * 5) + 1);

    // $('.login-btn').click(function (){
    // 	$('#verf-login').css("display", "none");
    // 	$('#otplogin').css("display", "block");
    // });

    function send_otp() {
        let phone = document.getElementById("phone_no");
        phone.setAttribute("readonly","true");
        let btn = document.getElementById("btn_send_otp");

        let loader = document.getElementById("send_otp_loader");
        loader.classList.remove("d-none");

        if(phone.value == "" || phone.value.toString().length != 10) {
            alert("Please Enter valid mobile no ... ");
            phone.removeAttribute("readonly");
            loader.classList.add("d-none");
            return false;
        }

        btn.setAttribute("disabled","true");

        $.ajax({
            type: "POST",
            url: "?key=" + encrypt(id, "@EH7478fh78y" + id), // Replace with your server endpoint
            data: {key: encrypt(id, "$#W&YGys463e7"+phone.value+id), auth_key: id,phone_no:phone.value,send_otp:true},
            success: function (response) {
                // Handle the successful response her
                response = JSON.parse(response);
                // if(response.status == 200) {
                //     // if(resend) {
                //     //     $("#resent").removeClass("d-none");
                //     // } else {
                //     //     $("#dis_email").text(email);
                //     //     $("#MyModal").modal('show');
                //     // }
                //     $('#dis_phone_no').text(phone.value);
                //     $('#verf-login').css("display", "none");
                //     $('#otplogin').css("display", "block");
                //     loader.classList.add("d-none");
                // } else {
                //     alert("Failed to send otp");
                //     btn.removeAttribute("disabled");
                //     phone.removeAttribute("readonly");
                //     loader.classList.add("d-none");
                // }

                if(response.status == 200) {
                    // if(resend) {
                    //     $("#resent").removeClass("d-none");
                    // } else {
                    //     $("#dis_email").text(email);
                    //     $("#MyModal").modal('show');
                    // }
                    // $('#dis_phone_no').text(phone.value);
                    // $('#verf-login').css("display", "none");
                    // $('#otplogin').css("display", "block");
                    window.location.href = 'employer-dashboard';
                } else if(response.status == 201) {
                    window.location.href = 'employer-profile';
                } else {
                    alert(response.msg);
                }

                $('#spinner').removeClass('show');
                // console.log(response);
            },
            error: function (xhr, status, error) {
                // Handle errors
                console.error("Error: " + error);
            }
        });

        // return false;
    }

    function resend_otp() {
        let phone = document.getElementById("phone_no");

        if(phone.value == "" && phone.toString().length != 10) {
            alert("Please Enter valid mobile no ... ");
            return false;
        }
        let loader = document.getElementById("send_otp_loader");
        loader.classList.remove("d-none");
        let btn = document.getElementById("resend_button");
        btn.setAttribute("disabled","true");
        $.ajax({
            type: "POST",
            url: "?key=" + encrypt(id, "@EH7478fh78y" + id), // Replace with your server endpoint
            data: {key: encrypt(id, "$#W&YGys463e7"+phone.value+id), auth_key: id,phone_no:phone.value,send_otp:true},
            success: function (response) {
                // Handle the successful response her
                response = JSON.parse(response);
                if(response.status == 200) {
                    // if(resend) {
                    //     $("#resent").removeClass("d-none");
                    // } else {
                    //     $("#dis_email").text(email);
                    //     $("#MyModal").modal('show');
                    // }
                    $('#dis_phone_no').text(phone.value);
                    $('#verf-login').css("display", "none");
                    $('#otplogin').css("display", "block");
                    alert("Otp sent successfully");
                } else {
                    alert("Failed to send otp");
                    btn.removeAttribute("disabled");
                    alert("failed to send otp");
                }
                btn.removeAttribute("disabled");
                loader.classList.add("d-none");

                $('#spinner').removeClass('show');
                // console.log(response);
            },
            error: function (xhr, status, error) {
                // Handle errors
                console.error("Error: " + error);
                btn.removeAttribute("disabled");
            }
        });

        // return false;
    }

    function verify_otp() {
        let phone = document.getElementById("phone_no");

        let inputs = document.querySelectorAll('.lgotp');
        let otp = '';

        inputs.forEach(input => {
            otp += input.value;
        });

        if(phone.value == "" && phone.toString().length != 10) {
            alert("Please Enter valid mobile no ... ");
            return false;
        }

        if(otp.toString().length != 6) {
            alert("Please Enter valid OTP ... ");
            return false;
        }

        let o = MD5.hex(""+otp+"#hjg&6^!jdsHsd");

        $.ajax({
            type: "POST",
            url: "?key=" + encrypt(id, "@#ewbih78s#h78y" + id), // Replace with your server endpoint
            data: {key: encrypt(id, "S&*^#$&df63e7"+phone.value+o+id), auth_key: id,phone_no:phone.value,otp:o,verify_otp:true},
            success: function (response) {
                response = JSON.parse(response);
                if(response.status == 200) {
                    // if(resend) {
                    //     $("#resent").removeClass("d-none");
                    // } else {
                    //     $("#dis_email").text(email);
                    //     $("#MyModal").modal('show');
                    // }
                    // $('#dis_phone_no').text(phone.value);
                    // $('#verf-login').css("display", "none");
                    // $('#otplogin').css("display", "block");
                    window.location.href = 'employer-dashboard';
                } else if(response.status == 201) {
                    window.location.href = 'employer-profile';
                } else {
                    alert(response.msg);
                }

                $('#spinner').removeClass('show');
                // console.log(response);
            },
            error: function (xhr, status, error) {
                // Handle errors
                console.error("Error: " + error);
            }
        });
    }

    let phone = document.getElementById("phone_no");
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
    phone.addEventListener('input', allowDigitsOnly);

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('.lgotp');
        inputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                if (e.key === "Backspace") {
                    // On backspace, if current input is empty, move to previous input
                    if (input.value === '' && index > 0) {
                        inputs[index - 1].focus();
                    }
                } else if (input.value.length === 1) {
                    // On input, if the input length is 1, move to next input
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
            });
        });
    });
</script>
</html>