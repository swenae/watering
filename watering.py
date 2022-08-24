#!/usr/bin/env python
# -*- coding: utf-8 -*-

#*****************************************************************************
#
# This is the "watering" script to taking care of our plants.
#
# Module        : main module, watering.py
# Author        : Swen Hopfe (dj)
# Design        : 2022-08-10
# Last modified : 2022-08-18
#
# The python script works on Raspberry Pi 2/.../Zero W
# with pump, relay and magnetic ventiles.
#
#*****************************************************************************

import RPi.GPIO as GPIO
import time
import datetime
import requests
import os
import Adafruit_ADS1x15

TIME1      = 60
TIME2      = 30
TIME_ADD1  = 20
TIME_ADD2  = 10

# Create an ADS1115 ADC (16-bit) instance.
adc = Adafruit_ADS1x15.ADS1115()

SERVER_URL = "https://www.smartewelt.de/bewae/index.php"
SERVER_PARAMS = {
    "psk": "a9b8c7d6e5f4",
    "request": "1"
}
#SERVER_PARAMS = {
#    "request": "1"
#}

GAIN = 1

act = True
sw1 = False
sw2 = False
sw_time = datetime.time(17, 00, 00)

REL01    = 23
REL02    = 24
REL03    = 25
LED_PIN  = 27

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)

GPIO.setup(REL01, GPIO.OUT)   # ventile1
GPIO.setup(REL02, GPIO.OUT)   # ventile2
GPIO.setup(REL03, GPIO.OUT)   # pump
GPIO.setup(LED_PIN, GPIO.OUT) # led

t0 = time.time()

def blink():
    GPIO.output(LED_PIN, True)
    time.sleep(0.5)
    GPIO.output(LED_PIN, False)
    time.sleep(0.5)

while True:

    t1 = time.time()
    if t1-t0 > 5:

        v1 = 0
        v2 = 0

        print(".")
        t0 = time.time()
        resp = requests.get(SERVER_URL, params=SERVER_PARAMS)
        cmd_b = resp.content
        cmd = str(cmd_b)
        print(cmd)
        if cmd == "b'10SEC'":
            print("Wasser")
            GPIO.output(REL01,   True)
            GPIO.output(REL02,   True)
            GPIO.output(REL03,   True)
            GPIO.output(LED_PIN, True)
            time.sleep(10)
            GPIO.output(REL03,   False)
            GPIO.output(REL01,   False)
            GPIO.output(REL02,   False)
            GPIO.output(LED_PIN, False)
        elif cmd == "b'ACTIVATE'":
            blink()
            act = True
            print("Aktiv")
        elif cmd == "b'DEACTIVATE'":
            blink()
            blink()
            act = False
            print("Aus")
        elif cmd == "b'REBOOT'":
            blink()
            blink()
            blink()
            os.system("sudo reboot")
        elif cmd == "b'SHUTDOWN'":
            os.system("sudo shutdown -h now")
            blink()
            blink()
            blink()
            blink()
        elif cmd == "b'EXIT'":
            break;

        # pump and switches control

        if act == "True":
            if (t1 - sw_time > 0) and (t1 - sw_time < TIME1):
                if sw1 == False:
                    sw1 = True
                    # Magnetventil1 ein
                    blink()
                    GPIO.output(REL01, True)
                    time.sleep(1)
                    # Pumpe ein
                    GPIO.output(LED_PIN, True)
                    GPIO.output(REL03, True)
                if (t1 - sw_time > 0) and (t1 - sw_time < TIME2):
                    if sw2 == False:
                        sw2 = True
                        # Magnetventil2 ein
                        blink()
                        blink()
                        GPIO.output(LED_PIN, True)
                        GPIO.output(REL02, True)
                else:
                    if sw2 == True:
                        sw2 = False
                        # Magnetventil2 aus
                        v2 = adc.read_adc(1, gain=GAIN)
                        if v2 > 29000:
                            time.sleep(TIME_ADD1)
                        v2 = adc.read_adc(1, gain=GAIN)
                        if v2 > 18000:
                            time.sleep(TIME_ADD2)
                        GPIO.output(LED_PIN, False)
                        GPIO.output(REL02, False)
                        blink()
                        blink()
            else:
                if sw1 == True:
                    sw1 = False
                    # Pumpe aus
                    v1 = adc.read_adc(0, gain=GAIN)
                    if v1 > 29000:
                        time.sleep(TIME_ADD1)
                    v1 = adc.read_adc(0, gain=GAIN)
                    if v1 > 18000:
                        time.sleep(TIME_ADD2)
                    GPIO.output(LED_PIN, False)
                    GPIO.output(REL03, False)
                    time.sleep(1)
                    # Magnetventil1 aus
                    GPIO.output(REL01, False)

GPIO.cleanup()

