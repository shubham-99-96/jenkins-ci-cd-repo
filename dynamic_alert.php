
<?php
//$alert = get_alert();
//if($alert) {
//
//?>
<!---->
<!--<div class="modal fade" id="applynowmodel">-->
<!--    <div class="modal-dialog modal-sm modal-dialog-centered">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-body" align="center">-->
<!--                <i class="fa fa-times close clsmodal" data-dismiss="modal" aria-hidden="true" style="float:right;cursor:pointer;"></i>-->
<!--                <p><img src="img/hurray.gif" class="img-fluid" alt="Hurray" width="80" /></p>-->
<!--                <h5 style="color:#605BE5;">HURRAY!</h5>-->
<!--                <h5 style="font-weight: 600;">--><?php //= $alert["msg"]; ?><!--</h5>-->
<!--                <input type="button" data-dismiss="modal" class="btn btn-primary applynow mt-3 col-md-5 clsmodal" value="OKAY">-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!--<script>-->
<!--$(window).on('load', function() {-->
<!--    $('#applynowmodel').modal('show');-->
<!--    setTimeout(function(){-->
<!--        $('#applynowmodel').modal('hide');-->
<!--    }, 3000);-->
<!--    $(".clsmodal").click(function (){-->
<!--        $('#applynowmodel').modal('hide');-->
<!--    })-->
<!--});-->
<!--</script>-->
<?php //} ?>

<?php
$alert = get_alert();
if(isset($alert) && isset($alert["status"]) && isset($alert["msg"])) {
    ?>
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius:10px;">
                <div class="modal-body alert alert-<?= $alert["status"] ?> mb-0" style="border-radius:10px;">
                    <img src="img/alert/<?= $alert["status"] ?>.png"/>
                	<strong><?= $alert["msg"] ?></strong>
                    <button type="button" class="btn btn-lg close" data-dismiss="modal" aria-label="Close" style="float: right;margin-top: -5px;padding: 0px;">
                    	<span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(window).on('load', function() {
            $('#myModal').modal('show');
            setTimeout(function () {
                $("#myModal").modal('hide');
            }, 3000);
        });
    </script>
<?php } ?>
