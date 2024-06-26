<?php
require_once "../Admin/Helpers/validation_helper.php";
$desc = "a";


var_dump(!(!empty($desc) && validate_address($desc)));