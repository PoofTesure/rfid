from types import TracebackType
import serial
import mysql.connector
import cv2 as cv
import threading
import datetime
from threading import Lock
import json
from pathlib import Path
import os
import time
import serial.tools.list_ports
import fnmatch
import traceback
from pyzbar import pyzbar
import sys

class Arduino:
    def __init__(self, port):
        self.ser = serial.Serial(port, 38400,timeout=1)

    def read(self):
        if self.ser.in_waiting > 0:
            try:
                line = self.ser.readline().decode('ascii').rstrip()
                print("Id yang terscan : " + line)
                return line
            except Exception as Argument:
                print(Argument)


    def write(self, line):
            self.ser.write(line)
    
    def flush(self):
        self.flush()
        


class dbConnection:
    def __init__(self,user,password,database="rfid",host="localhost"):
        self.db = mysql.connector.connect(
            host = host,
            user = user,
            passwd = password,
            database = database
        )
        self.cursor = self.db.cursor()
        self.db.commit()

    def checkID(self,line):
        self.db.commit()
        self.cursor.execute("SELECT id, name FROM id_rfid WHERE rfid_uid=" + line)
        result = self.cursor.fetchone()
        if self.cursor.rowcount >= 1:
            return result
        else:
            return False

    def insertData(self,uid,picturePath) :
        stmt = "INSERT INTO data_id (user_id,picture) VALUES (%s, %s);"
        self.cursor.execute(stmt,(uid[0],picturePath))
        self.db.commit()
    
    def requestOTP(self,otp):
        self.db.commit()
        stmt = f"SELECT otp_code, active_for, user_id FROM otp WHERE otp_code={otp};"
        try:
            self.cursor.execute(stmt)
        except Exception as Argument:
            print(Argument)
            return
        result = self.cursor.fetchone()
        print(result)
        if self.cursor.rowcount >= 1 :
            if result[1] >0 :
                usage = int(result[1])
                usage -= 1
                stmt = f"UPDATE otp SET active_for={usage} WHERE otp_code ={otp};"
                #print(stmt)
                self.cursor.execute(stmt)
                self.db.commit()
                return result


class Camera:
    last_frame = None
    last_ready = None
    lock = Lock()

    def __init__(self, rtsp_link):
        self.barcode = 0
        self.new_barcode = 0
        self.otpstatus = False
        capture = cv.VideoCapture(rtsp_link)
        thread = threading.Thread(target=self.rtsp_cam_buffer, args=(capture,), name="rtsp_read_thread")
        thread.daemon = True
        thread.start()
        thread1 = threading.Thread(target=self.timeout, name="barcode_timeout", daemon=True)
        thread1.start()

    def timeout(self):
        while True:
            #print("Timeout")
            #print(self.barcode)
            time.sleep(10)
            self.barcode = 0
            self.new_barcode = 0
            self.otpstatus = False

    def rtsp_cam_buffer(self, capture):
        while True:
            with self.lock:
                self.last_ready, self.last_frame = capture.read()
                self.last_frame = self.read_qr(self.last_frame)
                cv.imshow("Camera", self.last_frame)
                if cv.waitKey(1) & 0xFF == 27:
                    break
                if self.barcode != self.new_barcode:
                    self.otpstatus = True
                    self.barcode = self.new_barcode

        capture.release()
        cv.destroyAllWindows()

    def read_qr(self, last_frame):
        barcodes = pyzbar.decode(self.last_frame)
        for barcode in barcodes:
            x, y , w, h = barcode.rect
            
            self.new_barcode = barcode.data.decode('utf-8')
            cv.rectangle(self.last_frame, (x, y),(x+w, y+h), (0, 255, 0), 2)
            
        return self.last_frame

    def getFrame(self):
        if (self.last_ready is not None) and (self.last_frame is not None):
            return self.last_frame.copy()
        else:
            return None

    def take_picture(self):
        currentDate = datetime.datetime.now().strftime("%y-%m-%d_%H.%M.%S")
        relative_path = '/photo/' + currentDate + ".png"
        cv.imwrite("/var/www/html/photo/%s.png" % currentDate, self.last_frame.copy())
        return relative_path


class readMode:
    def __init__(self, status=0):
        self.status = status
        thread = threading.Thread(target=self.readJson, daemon=True)
        thread.start()
    
    def readJson(self):
        while True:
            path = "/var/www/html/script/readMode.json"
            with open(path) as f :
                decode = json.loads(f.read())
                self.status = decode['readMode']
                f.close()
            time.sleep(1)
    
    def writeJson(self,id):
        decode = {}
        decode['ID'] = id
        decode['readMode'] = 0
        encoded = json.dumps(decode)
        path = "/var/www/html/script/readMode.json"
        f = open(path, "w")
        f.write(encoded)
        f.close()


def scan_arduino():
    ports = list(serial.tools.list_ports.comports())
    arduino = []
    for p in ports:
        try:
            if "Arduino" in p.manufacturer:
                arduino.append(p)
        except:
            pass
    return arduino

def find(pattern, path):
    result = []
    for root, dirs, files in os.walk(path):
        for name in files:
            if fnmatch.fnmatch(name, pattern):
                result.append(os.path.join(root, name))
    return result

def cctv_cred(path):
    login = ""
    with open(path) as f:
        cam_ip = f.readline().strip()
        cam_user = f.readline().strip()
        cam_pass = f.readline().strip()
        login = "rtsp://" + cam_ip +"/user="+ cam_user +"&password=" + cam_pass +"&channel=1&stream=0.sdp?"
    return login

def createThread(scanner):
    thread = threading.Thread(target=main_loop, args=(scanner,))
    thread.start()


def main_loop(scanner):
    if __name__ == "__main__":
        try:
            arduino = Arduino(port="/dev/scan"+scanner)
            cap = Camera("/dev/video"+scanner)
            read1 = readMode()
            #arduino.flush()
            while True:
                database = dbConnection(user='admin',password='FaFen542')
                if read1.status == 1:
                    time.sleep(0.1)
                    arduino.write("readmode".encode('utf-8'))
                    while read1.status == 1:
                        ID = arduino.read()
                        if ID:
                            read1.writeJson(ID)
                            break

                ID = arduino.read()
                if ID:
                    giveAccess = database.checkID(ID)
                    #print(giveAccess)
                    if giveAccess:
                        print("Selamat Datang " + giveAccess[1])
                        time.sleep(0.05)
                        arduino.write(bytes("1,"+giveAccess[1],encoding='utf-8'))
                        rel_path = cap.take_picture()
                        database.insertData(uid=str(giveAccess[0]),picturePath=rel_path)
                    else:
                        time.sleep(0.1)
                        arduino.write(bytes("0",encoding='utf-8'))
                #print(cap.barcode)
                if cap.otpstatus:
                    print (cap.barcode)
                    result = database.requestOTP(cap.barcode)
                    if result:
                        print(result)
                        print("OTP Success")
                        time.sleep(0.2)
                        arduino.write(bytes("1,"+str(cap.barcode),encoding='utf-8'))
                        rel_path = cap.take_picture()
                        database.insertData(uid=str(result[2]),picturePath=rel_path)
                    else:
                        time.sleep(0.1)
                        arduino.write(bytes("0",encoding='utf-8'))
                    cap.otpstatus = False
        except Exception as Argument:
            print(Argument)
            #Create log file
            f = open("/home/pi/rfid/scan/error.log","a")
            currentDate = datetime.datetime.now().strftime("%y-%m-%d_%H.%M.%S")

            f.write(currentDate + " " + traceback.format_exc() + "\n")
            f.close()
        time.sleep(0.5)

if __name__ == "__main__":
    
    arduino_name = sys.argv
    print(sys.argv[1])

    main_loop(sys.argv[1])

    print("Ready")
