# Plant watering with Raspberry Pi

This is the "watering" script to taking care of our plants controlled by a Raspberry Pi. The python script works on Raspberry Pi 2/.../Zero W
with pump, relay and magnetic switch.

### Explanation
"watering.py" is the main control script on the Pi. Put it in a separate directory ("/home/pi/scripts") and start it after the Pi boots up.
"index.php" is our PHP script for our web interface. Put in a specific directory under the document root of your webspace. After that you can remotly control the Pi putting this URL to the adressbar of your browser.     

### Parts list
- Raspberry Pi Zero W (1/2)
- PiCam R1.3 or higher
- RPi and cam housing
- PiCam connection cabel
- Servo (MG90)
- PCB, buttons, LED, some resistors
- Homemade feeding tower

### Figures
Images in the image directory are:

feeder.jpg    - working feeder <br>
scheme.png    - connection scheme <br>
mechanics.png - mechanics in the feeding towers <br>
web.png       - web frontend <br>


