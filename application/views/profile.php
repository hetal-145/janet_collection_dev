<?php
if ($this->session->userdata('loged_in')) {

$user_id = $this->session->userdata("user_id");
$userdata = $this->db->select("*")->where("user_id", $user_id)->get("user")->row_array();
unset($userdata["password"]);
//print_r($userdata); exit;

if(!empty($userdata["profile_image"])) {
    $statss = $this->m_tools->get_http_response_code(S3_PATH . $userdata["profile_image"]);		    
    if($statss == '200') {
	$uimg = S3_PATH . $userdata["profile_image"];
    }
    else if($statss == '404') {
	$uimg = base_url() . "assets/website/img/avtar.png";
    }
}
else {
    $uimg = base_url()."assets/website/img/avtar.png";
} 

?>
<div class="modal fade" id="mdl_profile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-body">
                <form action="#" method="post" name="profileUpdate" id="profileUpdate" enctype="multipart/form-data">
                    <div class="row mb-4">
                        <div class="col-6">
                            <h4 class="mb-0">Profile</h4>
                        </div>
                        <div class="col-6">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="col-12">
                            <p class="desc mb-0 mt-2"></p>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="media">
                                <div class="usr_profile_img mr-3">
                                    <img src="<?php echo $uimg; ?>" class="" id="img_preview" alt="">
                                    <label for="usr_prof_img" class="usr_prof_img_lbl mb-0"><i class="mdi mdi-pencil"></i></label>
                                    <input type="file" class="d-none" id="usr_prof_img" name="usr_prof_img">
                                </div>
                                <div class="media-body align-self-center">
                                    <h5 class="mt-0 mb-2"><?php if(!empty($userdata["name"])) { echo $userdata["name"]; } ?></h5>
                                    <?php if(!empty($userdata)) {  if($userdata["is_admin_verified"] == 1 ) { ?>
                                        <h6 class="mb-0">Id Verification (Age Verified)<img class="ml-1" src="<?php echo base_url(). 'assets/website/img/checked.svg'; ?>" alt=""></h6>
                                        <small class="c-pink">Your age has been verified by admin</small>
                                    <?php }} ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="apply_form">                    
                        <input class="form-control" name="user_id" type="hidden" value="<?php if(!empty($userdata["user_id"])) { echo $userdata["user_id"]; } ?>">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" name="firstname" type="text" placeholder="First Name" value="<?php if(!empty($userdata["firstname"])) { echo $userdata["firstname"]; } ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" name="lastname" type="text" placeholder="Last Name" value="<?php if(!empty($userdata["lastname"])) { echo $userdata["lastname"]; } ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control date_picker" name="birthdate" type="text" placeholder="Birthdate" value="<?php if(!empty($userdata["birthdate"])) { echo $userdata["birthdate"]; } ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" name="email" type="text" placeholder="E-mail" value="<?php if(!empty($userdata["email"])) { echo $userdata["email"]; } ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <div class="form-group">
                                    <input class="form-control numeric" name="mobileno" type="text" placeholder="Mobile" value="<?php if(!empty($userdata["mobileno"])) { echo $userdata["mobileno"]; } ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-right">
                                <button type="button" class="btn btn-pink" id="saveProfileBtn">Save</button>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <span class="success_msg" style="color:green"> </span>
                                <span class="error_msg" style="color:red"> </span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){   
    function readURL(input) {
	if (input.files && input.files[0]) {
	  var reader = new FileReader();
	  reader.onload = function(e) {
	    $('#img_preview').attr('src', e.target.result);
	  }
	  reader.readAsDataURL(input.files[0]);
	}
    }

    $("#usr_prof_img").on('change', function() {
	readURL(this);
    });
    
    $("#saveProfileBtn").on("click", function() {
        $('.success_msg').html('');
        $('.error_msg').html('');
        $('.form-control').removeClass('input_error');
        var valid = true;
        var frm = $(this).closest($('form[name = "profileUpdate"]'));
        
        var firstname = frm.find('[name = "firstname"]').val();
        if (!firstname || !firstname.trim()) {
            frm.find('[name = "firstname"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your first name'));
            valid = false;
        } 
        
        var lastname = frm.find('[name = "lastname"]').val();
        if (!lastname || !lastname.trim()) {
            frm.find('[name = "lastname"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your last name'));
            valid = false;
        } 
        
        var birthdate = frm.find('[name = "birthdate"]').val();
        if (!birthdate || !birthdate.trim()) {
            frm.find('[name = "birthdate"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your birtdate'));
            valid = false;
        } 
        
        var mobileno = frm.find('[name = "mobileno"]').val();
        if (!mobileno || !mobileno.trim()) {
            frm.find('[name = "mobileno"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your mobile number'));
            valid = false;
        } 
        
        var email = frm.find('[name = "email"]').val();
        if (!email || !email.trim()) {
            frm.find('[name = "email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your email address'));
            valid = false;
        }
            
        if (valid) {
            $(this).attr("disabled", "disabled");
            $(this).html('Processing...');
            var data = new FormData(frm[0]);
            $.ajax({
                url: "<?php echo base_url() . 'user/profile'; ?>",
                type: "post",
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (resp)
                {
                    //console.log(resp);
                    if (resp === '1') {
                        $("#saveProfileBtn").removeAttr('disabled');
                        $("#saveProfileBtn").html('Save');
                        $(".success_msg").html("Updated Successfully!"); 
                        $('#mdl_profile').modal('toggle');  
                        location.reload();
                    } 
                    else if (resp === '2') {
                        $("#saveProfileBtn").removeAttr('disabled');
                        $("#saveProfileBtn").html('Save');
                        $(".error_msg").html("Not Updated!");
                        valid = false;
                    } 
                    else {
                        $("#saveProfileBtn").removeAttr('disabled');
                        $("#saveProfileBtn").html('Save');
                        $(".error_msg").html(resp);
                        valid = false;
                    } 
                }
            });
        }
    });
});
</script>
<?php } 