
#from imutils.video import VideoStream
#from VideoCapture import Device
import pysftp
import RPi.GPIO as GPIO
import os
#import recognize_5s
import fileinput
#import cv2
import requests
import datetime
import sys
import subprocess
import time
import calendar;

def takePicture():
    print("[INFO] starting video stream...")
    vs = VideoStream(src=0).start()
    time.sleep(0.5)
    frame = vs.read()
    ts = calendar.timegm(time.gmtime())
    # t = datetime.datetime.fromtimestamp(ts).strftime('%Y-%m-%d _%H-%M-%S')
    filename = 'picture_' +str(ts)
    p = os.path.sep.join(["pictures", "{}.png".format(filename)])
    cv2.imwrite(p, frame)
    print("[INFO] picture saved...")
    vs.stop()
    print("[INFO] closing video stream...")
    time.sleep(0.5)
    return filename

def takePictureSimplify():
    #read the absolute path
    script_dir = os.path.dirname(__file__)
    # call the .sh to capture the image
    os.system('./webcam.sh')
    #get the date and time, set the date and time as a filename.
    currentdate = datetime.datetime.now().strftime("%Y-%m-%d_%H%M")
    # create the real path
    rel_path = "picture_"+ currentdate +".jpg"
    #  join the absolute path and created file name
    abs_file_path = os.path.join(script_dir, rel_path)
    return abs_file_path

def uploadPicure():
    filename = takePictureSimplify() 

    if not filename:
        print("[ERROR] filename empty...")
        return

    myHostname = "home737842737.1and1-data.host"
    myUsername = "u93606195-iot"
    myPassword = "Dfs15@15"
    cnopts = pysftp.CnOpts()
    cnopts.hostkeys = None

    with pysftp.Connection(host=myHostname, username=myUsername, password=myPassword, cnopts=cnopts) as sftp:
        print("Connection succesfully stablished ... ")

        # Define the file that you want to upload from your local directorty
        # or absolute "C:\Users\sdkca\Desktop\TUTORIAL2.txt"
        localFilePath = './pictures/'+filename

        # Define the remote path where the file will be uploaded
        remoteFilePath = '/pictures/'+filename

        sftp.put(localFilePath, remoteFilePath)
    
    # connection closed automatically at the end of the with-block
    return
    

def button_callback(channel):
    uploadPicure()
    payload = {'token': '$2y$09$jQo3kfKavGADKScASuNqDeMbI9jdI7EY8WNEAmwqZRcHQXkTdy7P.', 'id_rasp': '1'}
    r = requests.post("https://tchenioguillaume.fr/iot/notification/create.php", data=payload)
    print(r.status_code)
    print("button pressed")
    #os.system("recognize_5s.py --detector face_detection_model \
#	--embedding-model openface_nn4.small2.v1.t7 \
#	--recognizer output/recognizer.pickle \
#	--le output/le.pickle")


GPIO.setwarnings(False) # Ignore warning for now
GPIO.setmode(GPIO.BOARD) # Use physical pin numbering
GPIO.setup(10, GPIO.IN, pull_up_down=GPIO.PUD_DOWN) # Set pin 10 to be an input pin and set initial value to be pulled low (off)

GPIO.add_event_detect(10,GPIO.RISING,callback=button_callback) # Setup event on pin 10 rising edge

message = input("Press enter to quit\n\n")# Run until someone presses enter

GPIO.cleanup() # Clean up
