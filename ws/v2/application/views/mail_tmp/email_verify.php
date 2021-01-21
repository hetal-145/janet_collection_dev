<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex" />

    <title>Welcome to Wadio</title>
    <link
      href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,700|Nunito:400,700&display=swap"
      rel="stylesheet"
    />

    <link
      rel="stylesheet"
      href="https://cdn.wadio.app/assets/welcome/css/styles.css"
    />
    
  </head>
  <body>
    <div class="welcome">
      <img
        class="logo"
        src="https://cdn.wadio.app/assets/welcome/images/logo.svg"
        alt="Wadio" style="margin-top:100px;"
      />

      <div>
        <img
          class="illustration"
          src="https://cdn.wadio.app/assets/welcome/images/illustration.svg"
        />
        <h1>
          Welcome
        </h1>
	
        <p>
          <?php echo $msg; ?>
        </p>
        <div class="action">
          <a class="btn open_app" href="javascript:void(0);"><span>Open App</span></a>
        </div>
      </div>
    </div>
      
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $(".open_app").on('click', function(){
	var iOS = navigator.userAgent.match(/iPhone|iPad|iPod/i);
	var android = navigator.userAgent.match(/Android/i);
	if (iOS == 'iPhone' || iOS == 'iPad' || iOS == 'iPod') {
	    setTimeout(function () {
		window.location = "wadio://com.wadio.app"; //ios_store_url
	    }, 5000);
	    window.location = "wadio://com.wadio.app";
	    
	} else if (android == 'Android') {
	    setTimeout(function () {
		window.location = "app://com.wadio"; //android_store_url
	    }, 5000);
	    window.location = "app://com.wadio"; //android_app_url
	    
	} else {
	    window.location = "wadio://com.wadio.app"; //web_url
	}
    });    
});    
</script>

  </body>
</html>
