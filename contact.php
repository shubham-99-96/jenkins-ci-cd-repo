<?php
require_once "Admin/Helpers/config_helper.php";
require_once "Admin/Helpers/validation_helper.php";
require_once "Admin/Models/Contact_Us.php";
require_once "helper.php";

if(isset($_POST["Contact_Us"])) {
    if(check_form_key("*Tfst8uj#90")) {
        if(check_params($_POST,["name","phone_no","email","message"])) {
            /*        document.getElementById('hdn_key').value=random_string(4)+MD5.hex('<?=$_SESSION["rand"]?>'+title.value+desc.value+city.value+type_of_job.value+gender.value+education.value+english.value+experience.value+'<?=$_SESSION["strrand"]?>')+random_string(4);*/

            if (check_hdn_key(["name", "email", "phone_no", "message"])) {

                $name = $_POST["name"];
                $phone_no = $_POST["phone_no"];
                $email = $_POST["email"];
                $message = $_POST["message"];

                $Contact = new Contact_us($con);
                if($Contact->create_contactus($name,$email,$phone_no,$message)) {
                    set_alert("success","Your query is submitted. we will get back to you soon .....");
                } else {
                    set_alert("danger","Failed .... ");
                }
                header("location:contact");
                die();
            } else {
                set_alert("danger","Invalid Request code=3");
                header("location:contact");
                die();
            }
        } else {
            set_alert("danger","Invalid Request code=2");
            header("location:contact");
            die();
        }
    } else {
        set_alert("danger","Invalid Request code=1");
        header("location:contact");
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
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <div style="background-color: #F2F6FD;">
        	<?php require_once('menu.php'); ?>
        </div> 
        <div class="container-xxl py-5">
            <div class="container">
                <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Contact For Any Query</h1>
                <div class="row g-4">
                    <div class="col-12">
                        <div class="row gy-4">
                            <div class="col-md-4 wow fadeIn" data-wow-delay="0.1s">
                                <div class="d-flex align-items-center bg-light rounded p-4">
                                    <div class="bg-white border rounded d-flex flex-shrink-0 align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                        <i class="fa fa-map-marker-alt text-primary"></i>
                                    </div>
                                    <span>Block B, New Ashok Nagar, Delhi</span>
                                </div>
                            </div>
                            <div class="col-md-4 wow fadeIn" data-wow-delay="0.3s">
                                <div class="d-flex align-items-center bg-light rounded p-4">
                                    <div class="bg-white border rounded d-flex flex-shrink-0 align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                        <i class="fa fa-envelope-open text-primary"></i>
                                    </div>
                                    <span><a href="mailto:tankhwaa@gmail.com">tankhwaa@gmail.com</a></span>
                                </div>
                            </div>
                            <div class="col-md-4 wow fadeIn" data-wow-delay="0.5s">
                                <div class="d-flex align-items-center bg-light rounded p-4">
                                    <div class="bg-white border rounded d-flex flex-shrink-0 align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                        <i class="fa fa-phone-alt text-primary"></i>
                                    </div>
                                    <span>+91 <a href="tel:7678252209">76782 52209</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    	<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d448399.44807139976!2d77.302159!3d28.596156!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce489a57f00db%3A0xd9c7b964b579b6e4!2sBlock%20B%2C%20New%20Ashok%20Nagar%2C%20Noida%2C%20Delhi%2C%20India!5e0!3m2!1sen!2sus!4v1709021105468!5m2!1sen!2sus" style="border:0;min-height: 380px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" width="100%"></iframe>
                    </div>
                    <div class="col-md-6">
                        <div class="wow fadeInUp" data-wow-delay="0.5s">
                            <p class="mb-4">Have questions or feedback? Reach out to Tankhwaa! Our dedicated support team is ready to assist you.
Connect with us for a seamless job-seeking experience.</p>
                            <form method="post" action="?key=<?= get_from_key("*Tfst8uj#90") ?>">
                                <div class="row g-3">
                                	<div class="col-12">
                                        <div class="form-group">
                                            <input type="text" name="name" class="form-control" id="name" placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control" id="email" placeholder="Email Address">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="phone_no" class="form-control" id="phone_no" placeholder="Phone Number">
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-group">
                                            <textarea name="message" class="form-control" placeholder="Enter your message..." id="message" rows="4"></textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" id="hdn_key" name="hdn_key" >
                                    <div class="col-12">
                                        <button name="Contact_Us" onclick="return validate();" class="btn btn-primary" type="submit">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Start -->
        <?php require_once('footer.php'); ?>
     </div>   
</body>
	<?php require_once('js.php'); ?>
    <script>
        function validateAddress(input) {
            var pattern = /^[0-9A-Za-z\s\.,#&\-]+$/;
            return pattern.test(input);
        }

        var phone_no=document.getElementById('phone_no');
        let name_2=document.getElementById('name');

        function allowDigitsOnly(event) {
            // Get the value of the input field
            const inputValue = event.target.value;

            // Check if the input value contains non-digit characters
            if (/[^0-9]/.test(inputValue)) {
                // Remove non-digit characters from the input value
                event.target.value = inputValue.replace(/[^0-9]/g, '');
            }
        }

        function allowCharOnly(event) {
            // Get the value of the input field
            const inputValue = event.target.value;

            // Check if the input value contains non-digit characters
            if (/[^A-Za-z\s]/g.test(inputValue)) {
                // Remove non-digit characters from the input value
                event.target.value = inputValue.replace(/[^A-Za-z\s]/g, '');
            }
        }

        // Get the input fiel

        // Attach the event listener to the input field
        phone_no.addEventListener('input', allowDigitsOnly);
        name_2.addEventListener('input', allowCharOnly);

        function validate()
        {
            var email=document.getElementById('email');
            var message=document.getElementById('message');
            let name_2=document.getElementById('name');

            if(name_2.value =="")
            {
                alert("Please enter name!!");
                name_2.focus();
                return false;
            }
            if(email.value=="")
            {
                alert("Please enter email!!");
                email.focus();
                return false;
            }
            if(phone_no.value=="")
            {
                alert("Please enter phone no!!");
                phone_no.focus();
                return false;
            }
            if(message == "" || !validateAddress(message.value)) {
                alert("Please choose type of job");
                message.focus();
                return false;
            }

            document.getElementById('hdn_key').value=random_string(4)+MD5.hex('<?=$_SESSION["rand"]?>'+name_2.value+email.value+phone_no.value+message.value+'<?=$_SESSION["strrand"]?>')+random_string(4);
            return true;
        }
    </script>
</html>