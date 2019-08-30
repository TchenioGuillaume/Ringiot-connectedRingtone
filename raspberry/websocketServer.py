import asyncio
import datetime
import time
import random
import websockets

global connected
connected = set()

condition =  set()
     
async def pub_sub(websocket, path):
    global connected

    if path == '/newConnection' : #quelqu'un se connecte
        connected.add(websocket)
        print("READER "+str(websocket.remote_address)+"    connected")
        print(len(connected))
        await asyncio.wait([websocket.send('connected')])
        while True:
            await asyncio.sleep(100)


    elif path == '/notification' : #trigger quelqu'un devant la porte
        #envoyer la notification aux utilisateurs connectés
        still_connected = set()
        for ws in connected :
            if ws.open:
                still_connected.add(ws)
                await asyncio.wait([ws.send('notifDuRasp')])
            else:
                print("READER "+str(ws.remote_address)+" disconnected")
        connected=still_connected

    elif path == '/refresh' : #pour rafraichir la liste d'utilisateurs toutes les tant de minutes
        still_connected = set()
        for ws in connected :
            if ws.open:
                still_connected.add(ws)
                #await asyncio.wait([ws.send(data)])
            else:
                print("READER "+str(ws.remote_address)+" disconnected")
        connected=still_connected

    elif path == '/accessGranted' : #allume la led par signal d'un utilisateur
        #peut être aussi envoyer la notif granted aux autres utilisateurs

        #allumer la led

        still_connected = set()
        for ws in connected :
            if ws.open:
                still_connected.add(ws)
                await asyncio.wait([ws.send('accesAutorise')])
            else:
                print("READER "+str(ws.remote_address)+" disconnected")
        connected=still_connected
             
start_serve = websockets.serve(pub_sub, '192.168.43.215', 5678)
asyncio.get_event_loop().run_until_complete(start_serve)
asyncio.get_event_loop().run_forever()
