import os
from threading import Thread


def sockServer():
    os.system("python3 websocketServer.py")

def bouton():
    os.system("python3 button.py")
    
tread1 = sockServer()
tread2 = bouton()

tread2.start()
tread1.start()

tread1.join()
tread2.join()