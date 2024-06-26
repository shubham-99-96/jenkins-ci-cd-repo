<?php
    require_once "Admin/Helpers/config_helper.php";
    require_once "Admin/Models/City.php";
    require_once "Admin/Models/Job_Types.php";
    require_once "Admin/Models/Employees.php";
    require_once "helper.php";
    employee_only();

    $City = new City($con);
    $Job_Types = new Job_Types($con);
    $city_data = $City->get_city();
    $job_type_data = $Job_Types->get_job_types(0,0,1);
    $job_types = [];
    foreach ($job_type_data as $job_type) {
        array_push($job_types,["label"=>$job_type["name"],"value"=>$job_type["name"],"id"=>$job_type["job_type_id"]]);
    }

    if(isset($_FILES["cv"])) {
        $targetDir = "Admin/Public/resume/";
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
                    $query = "INSERT INTO `RESUME`(`FILE_NAME`, `CREATED_AT`) VALUES ('".$fileName."','".date("Y-m-d H:i:s")."')";
                    if(mysqli_query($con,$query)) {
                        set_alert("success", "Successfully Uploaded Resume");
                    } else {
                        set_alert("danger","Failed to upload resume");
                    }
//                    echo "Uploaded file is moved";
                } else {
                    set_alert("danger","failed to upload resume");
                }
            } else {
                set_alert("danger","file too large .... ! ");
            }
        } else {
            set_alert("danger","failed to upload resume");
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
</head>

<body>
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->
    <div class="page-header">
    	<div class="menuheader">
			<?php require_once('menu.php'); ?>
        
            <div class="container-fluid my-4">
                <div class="row">
                    <div class="col-lg-7">
                        <h5 class="first-text mb-4"># Get A Job in 24 Hours</h5>
                        <h1 class="display-3 mb-4 animated slideInDown">INDIA'S <span class="titlecolor">MOST TRUSTED</span><br> JOB PLATFORM</h1>
                        <h1><span class="txt-type" data-wait="3000" data-words='["100% Employment", "0% Commission"]'></span></h1>
                        <div class="searchbox mt-5">
<!--                            <form method="post" action="find-job?key=--><?php //= get_from_key("&*Ygbhukyq3rw76") ?><!--">-->
                            <form method="get" action="Job-opening?key=<?= get_from_key("&*Ygbhukyq3rw76") ?>">
                            <div class="row">
                                <div class="col-md-5" id="autocomplete"><input id="job_type" type="text" class="form-control searchicon"
                                placeholder="Search Job by  “ Skills”">
                                <input type="hidden" name="job_type_id" value="" id="job_type_id">
                                </div>
                                <div class="col-md-4">
                                    <select name="city_id" id="city_id" class="form-control mapicon" style="background-color: #fff;">
                                        <option value="0" selected>All Cities</option>
                                        <?php
                                            foreach ($city_data as $city) {
                                                echo '<option value="'.$city["city_id"].'">'.$city["name"].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input onclick="return validate();" type="submit" name="find_job" class="btn btn-primary" value="find Jobs" style="font-weight: normal;">
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <img src="img/Group 214.png" alt="home" class="img-fluid homeimg" />
                        <div align="center" class="mobilehome"><img src="img/mobile.png" alt="home" class="img-fluid"/></div>
                    </div>
                </div>
            </div>
			<div class="container-fluid pb-4">
                <div class="owl-carousel home-carousel">
                    <div class="testimonial-item bg-light">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded" src="img/Byjus-logo.png" style="width: 40px; height: 40px;">
                            <div class="ps-3">
                                <h6 class="mb-1">BY JU’S</h6>
                                <small class="jobopen-text">(Job Opening)</small>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item bg-light">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded" src="img/IDC.png" style="width: 40px; height: 40px;">
                            <div class="ps-3">
                                <h6 class="mb-1">IDC</h6>
                                <small class="jobopen-text">(Job Opening)</small>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item bg-light">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded" src="img/testbook.png" style="width: 40px; height: 40px;">
                            <div class="ps-3">
                                <h6 class="mb-1">Textbook</h6>
                                <small class="jobopen-text">(Job Opening)</small>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item bg-light">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded" src="img/Sorditcon.png" style="width: 40px; height: 40px;">
                            <div class="ps-3">
                                <h6 class="mb-1">Sorditcon</h6>
                                <small class="jobopen-text">(Job Opening)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
		</div>
        <div class="container-fluid my-5">
            <h2 class="text-center wow fadeInUp" data-wow-delay="0.1s">Popular Job Types</h2>
            <p class="text-center mb-5 wow fadeInUp">Find the job that’s perfect for you. about 100+ new jobs everyday</p>
            <div class="row">
				<?php
                    for ($i=0;$i<count($job_type_data);$i++) {
//                            $job_type = $job_type_data[$i];
                        echo '
                         <div class="col-lg-3 col-6 wow fadeInUp mb-3" data-wow-delay="0.1s">
                            <a class="cat-item p-3" href="Job-opening?job_type_id='.$job_type_data[$i]["job_type_id"].'" style="height: 100%;width: 100%;">
                                <div class="row typejobalign">
                                    <div class="col-md-4">
                                        <img height="300px" width="300px" src="'.base_url.'/Public/images/job_type_images/'.$job_type_data[$i]["image"].'" alt="Telecaller" class="img-fluid typeofimg" />
                                    </div>
                                    <div class="col-md-8">
                                        <h6>'.$job_type_data[$i]["name"].'</h6>
                                        <small class="mb-0" style="color: #6C757D;">'.$job_type_data[$i]["jobs_count"].' Jobs Available</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        ';
                    }
                ?>
                <div class="col-lg-3 col-6 wow fadeInUp mb-3" data-wow-delay="0.1s">
                    <a class="cat-item p-3" href="Job-opening?work_mode=1" style="height: 100%;width: 100%;">
                    <div class="row typejobalign">
                        <div class="col-md-4">
                            <img height="300px" width="300px" src="<?= base_url ?>/Public/images/job_type_images/Job_Type_202404171129371f8d17.png" alt="Telecaller" class="img-fluid typeofimg" />
                        </div>
                        <div class="col-md-8">
                            <h6>Work From Home</h6>
                            <small class="mb-0" style="color: #6C757D;">Jobs Available</small>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
<!--            <div class="col-md-12 mt-5 wow fadeInUp" align="center">-->
<!--                <a class="btn btn-primary col-md-2" href="javascript:void(0);">see more</a>-->
<!--            </div>-->
        </div>

        <div class="container-fluid py-5 jobarewaiting">
            <div class="row my-5">
                <h1 class="text-center wow fadeInUp" data-wow-delay="0.1s" style="color: #fff;">Your Dream Jobs Are Waiting</h1>
                <h5 class="text-center mb-5 wow fadeInUp" style="font-weight:normal;color: #fff;">Search and connect with the right candidates faster</h5>
                
                <div class="col-md-12 wow fadeInUp btngap" align="center">
                   <button class="btn btn-hirenow col-md-2">
                       <a target="_blank" href="https://employer.tankhwaa.com/view-applicants"><img src="img/megaphone.png" class="img-fluid" alt="Mega phone" width="20px" style="margin-right: 5px;"> HIRE NOW</a>
                    </button>
                    <button class="btn btn-applynow col-md-2"><a href="Job-opening">APPLY NOW</a></button>
                </div>
            </div>
        </div>

        <div class="container-fluid my-5">
            <div class="row">
                <h2 class="text-center wow fadeInUp" data-wow-delay="0.1s">Are You Employer? Hire Now</h2>
                <p class="text-center mb-5 wow fadeInUp">Find the right candidate. Fast.</p>
                
                <div class="owl-carousel jobopening-carousel">
                    <?php
                    $Employee = new Employees($con);
                    foreach ($job_type_data as $job_type) {

                        $count = $Employee->get_job_type_count($job_type["job_type_id"]);

                        echo '
                            <div class="testimonial-item mt-1 mb-1" style="border: 1px solid #CFCED1; border-radius: 5px;">
                                <a target="_blank" href="https://employer.tankhwaa.com/view-applicants?job_type_id='.$job_type["job_type_id"].'">
									<div class="card" style="border-radius: 5px 5px 0px 0px;">
										<div class="card-body">
											<div class="row">
												<div class="col-md-4">
													<img src="'.base_url.'/Public/images/job_type_images/'.$job_type["image"].'" alt="'.$job_type["name"].'" class="img-fluid typeofimg" />
												</div>
												<div class="col-md-8">
													<div class="applicant">
													<small class="mb-0" style="color: #605BE5;">'.$count.'+ Applicant</small>
													</div>
												</div>
											</div>
											<div class="col-md-12 mt-2">
												<h5 class="mb-0">'.$job_type["name"].'</h5>
											</div>
										</div>    
									</div>    
									<div class="card-footer cardfooter">
										<div class="image">
											<img src="img/medical.png" alt="Medical" class="img-fluid" style="width: 20px;">
										</div>
										<div class="text">
											VIEW APPLICANTS
										</div>
									</div>
								</a>		
                            </div>
                        ';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="container-fluid my-5">
            <div class="row">
                <div class="row g-4" align="center">
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                        <h1 style="font-weight:900;color:#F5993A;"><span class="count">999</span>+</h1>
                        <h6 class="mb-3">Jobs</h6>
                        <p class="mb-0">Explore limitless possibilities with our diverse range of jobs!Let us help you find your dream job and take the next step towards a fulfilling career.</p>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                        <h1 style="font-weight:900;color:#F5993A;"><span class="count">599</span>+</h1>
                        <h6 class="mb-3">Applicants</h6>
                        <p class="mb-0">Our platform sees a surge of candidates daily, each eager to explore new opportunities.</p>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                        <h1 style="font-weight:900;color:#F5993A;"><span class="count">20</span>+</h1>
                        <h6 class="mb-3">Job Categories</h6>
                        <p class="mb-0">Discover endless possibilities with our 20+ job categories, ranging from Marketing and Sales to Technology.</p>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                        <h1 style="font-weight:900;color:#F5993A;"><span class="count mb-4">100</span>%</h1>
                        <h6 class="mb-3">Happy Users</h6>
                        <p class="mb-0">At Tankhwaa, our users' satisfaction is our top priority. We're delighted to see our users thriving and finding success with our platform.</p>
                    </div>
                </div>
        	</div>
    	</div>

        <div class="container-fluid py-5 submitcv">
            <div class="row">
                <div class="col-md-3">
                    <div class="avatar">
                        <img src="img/submitcvimg1.png" alt="Altug" />
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 class="text-center wow fadeInUp mb-4" data-wow-delay="0.1s" style="color: #fff;">Submit Your CV <br>
                        Let employers find you</h3>
                    <h5 class="text-center mb-5 wow fadeInUp" style="font-weight:normal;color: #fff;">Upload Your CV Here &nbsp;&nbsp;
                     <img src="img/Arrowjob.png" alt="Arrowjob" class="img-fluid" width="15px" /></h5>
                    <form id="cv_form" method="post" enctype="multipart/form-data">
                        <div class="col-md-12 wow fadeInUp" align="center">
                            <div class="position-relative mx-auto">
                                <input id="cv" type="file" name="cv">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3">
                    <div class="avatar mt-5">
                        <img src="img/submitcvimg2.png" alt="Altug" />
                    </div> 
                </div>
            </div>
        </div>
    
        <?php require_once('footer.php'); ?>
    </div>
</body>
	<?php require_once('js.php'); ?>
<script>
    $(function () {
        var job_types = <?= json_encode($job_types) ?>;
        $("#job_type").autocomplete({
            source: job_types,
            select : function(event, ui) {
                $("#job_type_id").val(ui.item.id);
            }
        });
    })

    $("#job_type").on("change",(e)=> {
        if($("#job_type").val().trim() === "") {
            $("#job_type_id").val("");
        }
    })

    $("#cv").on("change",(e)=>{
        alert("Changed Content submit the form");
        $("#cv_form").submit();
    })

    function validate() {
        let city_id = document.getElementById("city_id"),job_type=document.getElementById("job_type");

        if(job_type.value == "" && city_id.value == "") {
            alert("Please choose job type or city ");
            return false;
        }

    }
	// Init On DOM Load
  document.addEventListener("DOMContentLoaded", init);
  
  // Init App
  function init() {
    const txtElement = document.querySelector(".txt-type");
    const words = JSON.parse(txtElement.getAttribute("data-words"));
    const wait = txtElement.getAttribute("data-wait");
   //  Init TypeWriter
    new TypeWriter(txtElement, words, wait);
  }
  
</script>
</html>