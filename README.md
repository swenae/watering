# Plant watering with Raspberry Pi

This is the "watering" script to taking care of our plants controlled by a Raspberry Pi. The python script works on Raspberry Pi 2, Zero W (1/2) and above
with pump, relay and magnetic switch.

### Explanation
"watering.py" is the main control script on the Pi. Put it in a separate directory ("/home/pi/scripts") and start it after the Pi boots up.

"index.php" is our PHP script for our web interface. Put in a specific directory under the document root of your webspace. After that you can remotly control the Pi putting this URL to the adressbar of your browser.     

### Blink codes
See it on the top gadget LED... 

- static LED on/off - start/stop watering
- blink 1x - valve1 on - or activate control 
- blink 2x - valve2 on - or deactivate control
- blink 3x - reboot
- blink 4x - shutdown

### Schemes
Documents in the schemes directory:

circuit.pdf      - electronic circuit diagram <br>
waterpipes.pdf   - water pipes scheme <br>

### Parts list
- Raspberry Pi Zero W (1/2)
- centrifugal pump with 1.2m head
- magnetic valves (2x)
- relais modules (3x)
- ADC similiar ADS1115
- moisture sensor v1.2 (2x)
- power supply 5V
- housing, cables
- various hoses, clamps
- adapters, control valves, ground spikes

### Images
Images in the image directory:

watering.jpg     - ready solution on a barrel  <br>
controlunit.jpg  - control unit under construction <br>
web.png          - web frontend <br>
