////////////////////////////////// SETUP ////////////////////////////////////////

Start a web server (apache, etc) with this folder as the root, you should be served index.html


////////////////////////////////// USAGE ////////////////////////////////////////

To use the program, enter a username (blank is acceptable) and you will be presented with 
an image. If you hover over the image, you will notice that there is a grid to identify your 
positoin. Click anywhere on the image to fetch the next image in the password. Each click 
point results in a new image (can be a repeat of a previous image, but not of the n-1 image) 
that will be used to form the password. After 5 clicks, the final password will be echoed from 
the server and displayed on screen for evaluation.


//////////////////////////////// DISCLAIMER /////////////////////////////////////

This implementation was meant only as a demonstration, it is not very secure (especially 
if an attacker has javascript access), but it will generate unique passwords by using 5 
passes of the sha256 hash function (check changeImage.php for details), so it is useful 
for evaluating things like ease-of-use and user experience.


////////////////// INFORMATION FLOW/UNDERSTANDING THIS PROGRAM //////////////////

You can see the "password" (dataChain) as it is formed; it is passed back and forth between 
the javascript (for caching purposes since we did not dive into sessions) and the php 
(calculation of the password is dependent on previous states and new information). 

The initial image is found using the "login" POST, which (in php) uses the username 
(posted from js) to deterministically choose an image. Since the initial images will be
fairly unique to users, a user can easily tell if they entered their username correctly.

Each of the following images (5 total) are found via the "changeImage" POST which uses the
dataChain, click position, and image to determine which image to serve next and then echoes
back an updated dataChain. The images sent and received are verified to exist (and in js
it is verified that they can be loaded as images) and if the image is not validated, the
image in html will not change (neither will the password) but you can effectively "try again" 
to enter that part of the chain. This is a case that wasn't really considered beyond its 
possibility to exist, so this program would probably not handle attacks well.

On the 5th image, a "checkLogin" POST happens, which would normally authenticate against
a database of sorts but in this case it only serves to generate and echo the final password.


