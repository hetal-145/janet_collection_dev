<section class="contact_us page_section mt-66">
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 align-self-center mb-4">
                        <div class="row justify-content-start">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10 pt-3 pb-3">
                                <img class="app_icon mb-4" src="<?php echo base_url() . 'assets/website/img/logo.png'; ?>" alt=""/>
                                <h1 class="main_title mb-4 mt-0">Contact <small>Us</small></h1>
                                <p class="desc mb-0 pt-2">As a new business that is aimed to change your shopping routine forever, we welcome any feedback that can help us become better.</p>

                                <h2 class="main_title mb-4 mt-5">Open <small>Hours</small></h2>
                                <p class="mb-2"><label class="mb-0 c-pink mr-2"><b>Sun to Thu</b></label> <label class="mb-0">17 PM to 1 AM</label></p>
                                <p class="mb-0"><label class="mb-0 c-pink mr-2"><b>Fri to Sat</b></label> <label class="mb-0">17 PM to 3 AM</label></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 right_content contact_form">
                        <div class="card p-4">
                            <form method="post" action="#" name="contact_form" id="contact_form" class="contact_form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <h5 class="main_title mt-0 mb-3">Have <small>feedback?</small></h5>
                                            <p class="mb-0">Leave your comment, suggestion or complain below.</p>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" maxlength="150">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control numeric" id="contactno" name="contactno" placeholder="Contact No." maxlength="15">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" maxlength="150">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <textarea id="message" name="message" class="form-control" rows="4" placeholder="Message"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-pink_squre submit_contact_us">Send</button>
                                        <span class="success_msg" style="color:green"> </span>
                                        <span class="error_msg" style="color:red"> </span>
                                    </div>                                
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="contact_us_sec2 page_section">
    <div class="container-fluid">
        <div class="row inner_content ">
            <div class="col-12 mb-4 align-self-end">
                <div class="row justify-content-center">
                    <div class="col-12 text-center">
                        <h2 class="title">Are you interested?</h2>
                        <p class="txt1">DO YOU WANT TO BECOME OUR PARTNER?</p>
                        <p class="txt2 mt-4 mb-0">Contact us via email or whatsapp or simply fill in the form below and we<br>will get back to you shortly.</p>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8 email_phone mt-5">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 text-center mb-4">
                                <h4>E-mail</h4>
                                <p class="mb-0">shoutout@predrinkdelivery.com</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 text-center">
                                <h4>WhatsApp</h4>
                                <p class="mb-0">+1 123 456 7890</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    $(".submit_contact_us").on("click", function() {
        $('.success_msg').html('');
        $('.error_msg').html('');
        $('.form-control').removeClass('input_error');
        var valid = true;
        var frm = $(this).closest($('form[name = "contact_form"]'));
        
        var name = frm.find('[name = "name"]').val();
        if (!name || !name.trim()) {
            frm.find('[name = "name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your full name'));
            valid = false;
        } 
        
        var contactno = frm.find('[name = "contactno"]').val();
        if (!contactno || !contactno.trim()) {
            frm.find('[name = "contactno"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your contact no'));
            valid = false;
        } 
        
        var email = frm.find('[name = "email"]').val();
        if (!email || !email.trim()) {
            frm.find('[name = "email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your email address'));
            valid = false;
        } 
        
        var subject = frm.find('[name = "subject"]').val();
        if (!subject || !subject.trim()) {
            frm.find('[name = "subject"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your subject'));
            valid = false;
        } 
            
        if (valid) {
            $(this).attr("disabled", "disabled");
            $(this).html('Processing...');
            var data = new FormData(frm[0]);
            $.ajax({
                url: "<?php echo base_url() . 'contact_us/save'; ?>",
                type: "post",
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (resp)
                {
                    if (resp === 'success') {
                        frm.trigger('reset');
                        $(".submit_contact_us").removeAttr('disabled');
                        $(".submit_contact_us").html('Save');
                        $(".success_msg").html("Inquiry Sent Successfully!");                        
                    } else if (resp === 'error') {
                        $(".submit_contact_us").removeAttr('disabled');
                        $(".submit_contact_us").html('Save');
                        $(".error_msg").html("Error in sending Inquiry!");
                        valid = false;
                    } 
                }
            });
        }
    });
});
</script>