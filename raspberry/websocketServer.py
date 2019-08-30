# boucle avec un sleep et un switch case

import asyncio
import datetime
import time
import random
import websockets

global connected
connected = set()

#websocket relance une pub_sub asynchrone a chaque appel
     
async def pub_sub(websocket, path):
    global connected

    connected.add(websocket)
    print("READER "+str(websocket.remote_address)+"    connected")
    print(len(connected))
    await asyncio.wait([websocket.send('connected')])
    while True:
        try:
            respo = await websocket.recv()

            if respo == "notification" :
                still_connected = set()
                for ws in connected :
                    if ws.open:
                        still_connected.add(ws)
                        await asyncio.wait([ws.send('notifDuRasp')])
                    else:
                        print("READER "+str(ws.remote_address)+" disconnected")
                connected=still_connected
                await asyncio.wait([websocket.send('notification sent')])

            elif respo == "refresh" :
                still_connected = set()
                for ws in connected :
                    if ws.open:
                        still_connected.add(ws)
                        #await asyncio.wait([ws.send('notifDuRasp')])
                    else:
                        print("READER "+str(ws.remote_address)+" disconnected")
                connected=still_connected
                await asyncio.wait([websocket.send('list refreshed : ' +str(len(connected)) +' users online')])

            elif respo == "accessGranted" :
                #allumer la led
                still_connected = set()
                for ws in connected :
                    if ws.open:
                        still_connected.add(ws)
                        await asyncio.wait([ws.send('access Granted')])

                    else:
                        print("READER "+str(ws.remote_address)+" disconnected")
                connected=still_connected
                await asyncio.wait([websocket.send('done')])

            else :
                await asyncio.wait([websocket.send('invalid call')])

            #await asyncio.wait([websocket.send('ok')])
            #print(respo)
            #await asyncio.sleep(100)
        except:
            print("session closed")
            break
             
start_serve = websockets.serve(pub_sub, '172.20.10.2', 5678) #changer l'ip pour correspondre à l'ip de la raspberry
                                                             #ne pas oublier d'ouvrir les ports sur l'host de connexion
 
asyncio.get_event_loop().run_until_complete(start_serve)

asyncio.get_event_loop().run_forever()
