<nav class="navbar navbar-expand-md main-menu sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="https://test.tankhwaa.com">
            <img src="img/Tankhwaa-Logo.png" alt="Logo" class="img-fluid logotankhwa" />
        </a>
		<!--<div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">-->
                <?php
                    if(isset($_SESSION["setup"]) && $_SESSION["setup"] == 1) {
                        if (isset($_SESSION["employee"])) {
                    ?>

                            <div class="nav-item dropdown">
                                <a class="nav-link btn btn-primary" id="btn-top" data-bs-toggle="dropdown" style="text-transform:capitalize;"> <i class="fa fa-user" aria-hidden="true"></i> &nbsp;&nbsp; Hi, <?= $_SESSION["name"] ?>&nbsp;&nbsp; <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                                <!--data-bs-toggle="dropdown"-->
                                <div class="dropdown-menu rounded-0 m-0">
                                    <a href="my-profile" class="dropdown-item">
                                        <i class="fa fa-user" aria-hidden="true" style="color:#292C73;"></i>&nbsp;&nbsp; My Profile</a>
                                    <a href="applied-Jobs" class="dropdown-item">
                                        <i class="fa fa-suitcase" aria-hidden="true" style="color:#292C73;"></i>&nbsp;&nbsp; Applied Jobs</a>
                                    <a href="logout" class="dropdown-item">
                                        <i class="fa fa-sign-in" aria-hidden="true" style="color:#292C73;"></i>&nbsp;&nbsp; Logout</a>
                                </div>
                            </div>

                    <?php
                        } else if (isset($_SESSION["employer"])) {
                            ?>

                            <div class="nav-item dropdown">
                                <a class="nav-link btn btn-primary" id="btn-top" data-bs-toggle="dropdown" style="text-transform:capitalize;"><i class="fa fa-suitcase" aria-hidden="true"></i> &nbsp;&nbsp; Hi, <?= $_SESSION["name"] ?>&nbsp;&nbsp; <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                                <!--data-bs-toggle="dropdown"-->
                                <div class="dropdown-menu rounded-0 m-0">
                                    <a href="employer-dashboard" class="dropdown-item"><i class="fa fa-tachometer" aria-hidden="true" style="color:#292C73;">
                                    </i>&nbsp;&nbsp; Dashboard</a>
                                    <a href="view-applicants" class="dropdown-item"><i class="fa fa-id-badge" aria-hidden="true" style="color:#292C73;"></i>&nbsp;&nbsp; Candidates </a>
                                    <a href="company-profile" class="dropdown-item"><i class="fa fa-user" aria-hidden="true" style="color:#292C73;"></i>&nbsp;&nbsp; Company Profile</a>
                                    <a href="post-new-job" class="dropdown-item"><i class="fa fa-suitcase" aria-hidden="true" style="color:#292C73;"></i>&nbsp;&nbsp; Post a new Job</a>
                                    <a href="logout" class="dropdown-item"><i class="fa fa-sign-in" aria-hidden="true" style="color:#292C73;"></i>&nbsp;&nbsp; Logout</a>
                                </div>
                            </div>

                            <?php
                        }
                    } else if( isset($_SESSION["setup"]) && $_SESSION["setup"] == 0) {
                        $link = "";
                        if(isset($_SESSION["employee"])) { $link = "candidate-profile"; } else { $link = "employer-profile"; }
                            echo '<a href="'.$link.'" class="nav-item nav-link ms-auto btn-nav btn btn-sucess" id="btn-top"> Complete Profile</a>
								 <div class="nav-item dropdown">
									<a href="logout" class="nav-link btn btn-primary" id="btn-top">Logout</a>
								</div>';

                    } else {
                ?>
            	 <a href="https://employer.tankhwaa.com/" target="_blank" class="nav-item nav-link ms-auto btn-nav btn btn-sucess" id="btn-top">
                 <img src="img/empbag.png" class="empbag" alt="Employe Bag">  Employer Login</a>
                 <div class="nav-item dropdown">
                    <a href="candidate-login" class="nav-link btn btn-primary" id="btn-top"> Candidate &nbsp; Login</a>
                </div>
                <?php } ?>
                <!--<a class="btn btn-sucess" href="employer-login"><img src="img/empbag.png" class="empbag" alt="Employe Bag"> Employer Login</a>
                <a class="btn btn-primary" href="javascript:void(0);">Candidate &nbsp; Login</a>-->
               
            <!--</div>
        </div>  --> 
    </div>
</nav>