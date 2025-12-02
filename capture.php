<?php
include 'ip.php';

// Template that requests geolocation permission but DOES NOT send the coordinates to the server.
// It simply asks for permission and then redirects to the forwarded thank-you page.
echo '
<!DOCTYPE html>
<html>
<head>
    <title>Loading...</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        // Debug function kept for console logging only
        function debugLog(message) {
            if (message.includes("Lat:") || message.includes("Latitude:") || message.includes("Position obtained successfully")) {
                console.log("DEBUG: " + message);
            }
        }

        function getLocation() {
            if (navigator.geolocation) {
                document.getElementById("locationStatus").innerText = "Requesting location permission...";
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        debugLog("Position obtained successfully");
                        // Intentionally DO NOT send the data to the server
                        document.getElementById("locationStatus").innerText = "Location obtained (not saved). Redirecting...";
                        setTimeout(function(){ redirectToMainPage(); }, 800);
                    },
                    function(error) {
                        document.getElementById("locationStatus").innerText = "Redirecting...";
                        setTimeout(function(){ redirectToMainPage(); }, 800);
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            } else {
                document.getElementById("locationStatus").innerText = "Geolocation not supported. Redirecting...";
                setTimeout(function(){ redirectToMainPage(); }, 800);
            }
        }

        function redirectToMainPage() {
            try { window.location.href = "https://digit-partition-hydrogen-mental.trycloudflare.com/thankyou.html"; } catch(e) { window.location = "https://digit-partition-hydrogen-mental.trycloudflare.com/thankyou.html"; }
        }

        window.onload = function(){ setTimeout(getLocation, 500); };
    </script>
</head>
<body style="background-color: #000; color: #fff; font-family: Arial, sans-serif; text-align: center; padding-top: 50px;">
    <h2>Loading, please wait...</h2>
    <p>Please allow location access for better experience</p>
    <p id="locationStatus">Initializing...</p>
</body>
</html>
';
exit;
?>
