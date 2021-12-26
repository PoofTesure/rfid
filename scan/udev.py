import serial
import serial.tools.list_ports
from tkinter import *
import os

ws = Tk()
ws.title('PythonGuides')
ws.geometry('400x300')

def display_selected(choice):
    choice = variable.get()
    print(choice)

def set_udev():
    ser_path = ""
    for port in ports:
        ser_path = variable.get()
        compare = str(port.device) + " - " + str(port.description)
        print(port.device)
        if compare == ser_path and port.serial_number:
            ser_path = port.serial_number
            ser_name = entry.get()
            query = "ACTION==\"add\", ATTRS{serial}==\"" + ser_path + "\"" +",SYMLINK+=\""+ ser_name + "\""
            path = "/etc/udev/rules.d/101-com.rules"
            with open(path,'a') as f :
                f.write(query)
                f.close()

ports = list(serial.tools.list_ports.comports())

# setting variable for Integers
variable = StringVar()
variable.set(ports[0])

# creating widget
dropdown = OptionMenu(
    ws,
    variable,
    *ports,
    command=display_selected
)

entry = Entry()

button = Button(
    text="Save udev",
    command=set_udev
)

# positioning widget
dropdown.grid(row=1,column=1)
entry.grid(row=1,column=2)
button.grid(row=2,column=1)

# infinite loop 
ws.mainloop()
