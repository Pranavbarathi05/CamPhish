# CamPhish
Grab cam shots from target's phone front camera or PC webcam just by sending a link.

# What is CamPhish?
<p>CamPhish is a technique to capture cam shots from a target's phone front camera or PC webcam. CamPhish hosts a fake website on an in-built PHP server and can expose it over the internet (for example using a Cloudflare Tunnel) to generate a link which you forward to the target. The webpage asks for camera permission and, if the target allows it, this tool captures camshots from the target's device.</p>

<p>An optional GPS location-capture feature has been added and can be enabled or disabled.</p>

## Features
<p>This tool includes several automatic webpage templates designed to engage a target on the webpage and increase the chance of obtaining camera access:</p>
<ul>
  <li>Festival greeting template</li>
  <li>Live YouTube / TV template</li>
  <li>Customizable templates (insert your own content)</li>
  <li>More templates can be added</li>
</ul>
<p>A cleanup script is included to remove captured images, logs, and other unnecessary files.</p>

# Installing and requirements
<p>This tool requires PHP for the webserver, and utilities like <code>wget</code> and <code>unzip</code> for downloading dependencies. On Debian-based systems (e.g., Kali), run:</p>

```

apt-get -y install php wget unzip

```

## Installing (Kali Linux / Termux):

```

git clone [https://github.com/techchipnet/CamPhish](https://github.com/techchipnet/CamPhish)
cd CamPhish
bash camphish.sh

```

## Clean logs & unnecessary files :

```

bash cleanup.sh

```
<p>Running the cleanup script will remove captured camera files and any saved location logs.</p>

## Warning:

<p><b>Version: 2.1:</b> fixed errors and updated templates to be more suitable.</p>
<ul>
  <li>A warning structure has been added to discourage misuse.</li>
  <li>Any changes made to this code are the responsibility of the person who made them.</li>
  <li>To be used for educational purposes and authorized security testing only (obtain explicit, written consent from the device owner).</li>
  <li>Added: improved loading screen with optional location request.</li>
</ul>

### Important Notice
Unauthorized re-uploading or redistribution of this project is prohibited by the original author.

<p>CamPhish is provided to assist with penetration testing and security research. The project authors and maintainers are not responsible for any misuse or illegal purposes.</p>

<p>CamPhish is inspired by <a href="https://github.com/techchipnet/CamPhish.git">https://github.com/techchipnet/CamPhish.git</a>. Big thanks to @techchipnet.</p>
```
