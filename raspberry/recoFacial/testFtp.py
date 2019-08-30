import pysftp

myHostname = "home737842737.1and1-data.host"
myUsername = "u93606195-iot"
myPassword = "Dfs15@15"
cnopts = pysftp.CnOpts()
cnopts.hostkeys = None

with pysftp.Connection(host=myHostname, username=myUsername, password=myPassword, cnopts=cnopts) as sftp:
    print("Connection succesfully stablished ... ")

    # Define the file that you want to upload from your local directorty
    # or absolute "C:\Users\sdkca\Desktop\TUTORIAL2.txt"
    localFilePath = './pictures/tayfun.png'

    # Define the remote path where the file will be uploaded
    remoteFilePath = '/pictures/tayfun.png'

    sftp.put(localFilePath, remoteFilePath)
 
# connection closed automatically at the end of the with-block
 