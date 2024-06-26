<a href="whatsapp://send?text=Hello Sir&phone=+917678252209" class="float bounce" target="_blank">
<i class="fa-brands fa-whatsapp my-float"></i>
</a>
<div class="fixed-icons">
<a href="tel:+91 76782 52209" class="call">
    <i class="fa fa-phone"></i>
</a>
</div>
<div class="container-fluid bg-white text-black-50 footer wow fadeIn" data-wow-delay="0.1s" style="border-top: 1px solid #605BE5;">
    <div class="row mt-4">
        <div class="col-lg-5 col-md-6">
            <img src="img/footer-Logo.png" alt="Tankhwaa Logo" class="img-fluid mb-2" style="margin-left: -16px;">
            <p style="color:#4F5E64;">Cheers to personal and professional <br> growth on career path!</p>
            <span style="color:#4F5E64;">Get A Job in 24 Hours <br>
            <span style="color:#4F5E64;"></span>0% Commission | 100% Employment</span>
            <div class="d-flex pt-2">
                <!--<a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>-->
                <a class="btn btn-outline-light btn-social" href="https://www.facebook.com/Tankhwaa?mibextid=ZbWKwL" target="_blank">
                <i class="fab fa-facebook-f"></i></a>
                <a class="btn btn-outline-light btn-social" href="https://www.instagram.com/tankhwaa?igsh=MWxncGk0bjNibnc0bA==" target="_blank">
                <i class="fab fa-instagram"></i></a>
                <a class="btn btn-outline-light btn-social" href="https://www.linkedin.com/company/tankhwaa/" target="_blank">
                <i class="fab fa-linkedin-in"></i></a>
            </div>
            <!--<div class="position-relative mx-auto" style="max-width: 400px;">
                <input class="form-control bg-transparent w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
            </div>-->
        </div>


        <div class="col-lg-3 col-md-6">
            <h5 class="mb-4" style="color: #1D1D35;">Quick Links</h5>
            <a class="btn btn-link" href="aboutus" style="color:#4F5E64;" >About Us</a>
            <a class="btn btn-link" href="contact" style="color:#4F5E64;">Contact Us</a>
            <a class="btn btn-link" href="view-applicants" style="color:#4F5E64;">Candidates</a>
            <a class="btn btn-link" href="Job-opening" style="color:#4F5E64;">Job Opening</a>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="row">
                <h5 class="mb-4" style="color: #1D1D35;">Job Types</h5>

                <?php
                require_once "Admin/Models/Job_Types.php";
                $Job_Type = new Job_Types($con);
                $job_type_data = $Job_Type->get_job_types(0,1,1);
                if(isset($job_type_data) && count($job_type_data) != 0) {
                    echo '<div class="col-md-6">';
                    for ($i=0;$i<count($job_type_data) && $i<count($job_type_data);$i++) {
                        if($i %3 == 0 && $i !=0) {
                            echo '</div><div class="col-md-6">';
                        }
                        echo '<a class="btn btn-link" href="Job-opening?job='.encrypt($job_type_data[$i]["job_type_id"],"job23").'" style="color:#4F5E64;">'.$job_type_data[$i]["name"].'</a>';
                    }
                    echo '</div>';
                }
                ?>
<!--                <div class="col-md-6">-->
<!--                    <a class="btn btn-link" href="" style="color:#4F5E64;">Telecaller</a>-->
<!--                    <a class="btn btn-link" href="" style="color:#4F5E64;">Office Boy</a>-->
<!--                    <a class="btn btn-link" href="" style="color:#4F5E64;">Data Entry</a>-->
<!--                </div>-->
<!--                <div class="col-md-6">-->
<!--                    <a class="btn btn-link" href="" style="color:#4F5E64;">WFH Job</a>-->
<!--                    <a class="btn btn-link" href="" style="color:#4F5E64;">Inside Sales</a>-->
<!--                    <a class="btn btn-link" href="" style="color:#4F5E64;">Delivery Boy</a>-->
<!--                </div>-->
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="copyright">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-md-0">
                   <small style="color: #4F5E64;">Copyright &copy; 2024. Tankhwaa all right reserved</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <small><a href="Privacy-Policy">Privacy Policy</a>
                        <a href="Terms-and-conditions">Terms & Conditions</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->
<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>