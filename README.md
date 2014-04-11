Project H.A.S Component Documentation - Error Handling Component
-----------------------------------------------------------------
Author Group: **Home Automation Systems**

Prepared for: Dr. Curtis Busby-Earle

Prepared by: Aston Hamilton, Renee Whitelocke, Orane Edwards

March 18, 2014

Version number: 000-0002


##Component Description
This component exposes 4 interfaces to manage its dataset of device entities.

This component is deployed on the UWI Server but it will not work because it depends on components from other teams that are not yet deployed to the UWI server. To integrate with the other components, this component was deployed on a public server and it is the endpoints to the public server that are used throughout this documetnation.

This component uses the functionality from the Database component that was built by the **Church Financial System** team.

    Database component: https://github.com/uwi-mase-2014-ccd/component-church-financial-system-database-management-services

###Create Device Interface
This interface accepts a name and a list of values for a device. The component will then use the interface provided by the database component to save a new record for the device. The database component will assign a unique ID to this device.

	Endpoint: POST http://ticketmanager.mysoftware.io:8100/component-device-management/src/create.php
	
**Example Invocation:**

Request Body:
```javascript
name=Living Room - Main Lights
values[0][title]=Wattage
values[0][value]=65W
values[1][title]=State
values[1][value]=On
```

Response Body:
```javascript
{
  "code": 200,
  "data": {
    "device": {
      "name": "Living Room - Main Lights",
      "values": [
        {
          "title": "Wattage",
          "value": "65W"
        },
        {
          "title": "State",
          "value": "On"
        }
      ]
    },
    "device-row": "Successful",
    "message": "Success"
  },
  "debug": {}
}
```


###Read Devices Interface
This interface accepts an optional ID parameter in the Query String of the URL that points to this interface. This ID, is a unique ID that is assigned to the device by the database component. If the ID is supplied, the component will use the interface provided by the database component to fetch the device with the associated ID and return it to the user. If no ID is supplied, all the devices maintained by the component will be returned.

	Endpoint: GET http://ticketmanager.mysoftware.io:8100/component-device-management/src/read.php

**Example Invocation:**

Response Body:
```javascript
{
  "code": 200,
  "data": {
    "devices": [
      {
        "id": "1",
        "name": "Main Gate",
        "values": [
          {
            "title": "State",
            "value": "Closed"
          }
        ]
      },
      {
        "id": "10",
        "name": "West Wing - Upper Bathroom Shower",
        "values": [
          {
            "title": "Max Flow Rate",
            "value": "65 Litres/Hour"
          },
          {
            "title": "State",
            "value": "Off"
          }
        ]
      },
      {
        "id": "11",
        "name": "Living Room - Main Lights",
        "values": [
          {
            "title": "Wattage",
            "value": "65W"
          },
          {
            "title": "State",
            "value": "On"
          }
        ]
      }
    ],
    "message": "Success"
  },
  "debug": {}
}
```

###Update Device Interface
This interface accepts a name, value and an ID for a device. The component will use the interface provided by the database component to update the name and value of the device associated with the input ID.

	Endpoint: POST http://ticketmanager.mysoftware.io:8100/component-device-management/src/update.php

**Example Invocation:**

Request Body:
```javascript
id=11
name=Living Room - Main Lights
values[0][title]=Wattage
values[0][value]=65W
values[1][title]=State
values[1][value]=Off
```

Response Body:
```javascript
{
  "code": 200,
  "data": {
    "device": {
      "id": "11",
      "name": "Living Room - Main Lights",
      "values": [
        {
          "title": "Wattage",
          "value": "65W"
        },
        {
          "title": "State",
          "value": "Off"
        }
      ]
    },
    "device-row": "Successful",
    "message": "Success"
  },
  "debug": {}
}
```

###Delete Device Interface
This interface accepts an ID in the Query String of the URL that points to this interface. The component will use an interface proved by the database component to delete the record of the device associated with the input ID.

	Endpoint: POST http://ticketmanager.mysoftware.io:8100/component-device-management/src/delete.php


**Example Invocation:**

Request Body:
```javascript
id=11
name=Living Room - Main Lights
values[0][title]=Wattage
values[0][value]=65W
values[1][title]=State
values[1][value]=Off
```

Response Body:
```javascript
{
  "code": 200,
  "data": {
    "device": {
      "id": "11"
    },
    "device-row": "Successful",
    "message": "Success"
  },
  "debug": {}
}
```
