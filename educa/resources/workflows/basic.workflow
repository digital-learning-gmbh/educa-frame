{
   "name":"Basic Workflow",
   "version":"0.0.1",
   "definition":{
      "nodes":[
         {
            "type":"startBlock",
            "width":150,
            "height":36,
            "id":"1",
            "data":{
               "label":"Start"
            },
            "position":{
               "x":100,
               "y":100
            },
            "positionAbsolute":{
               "x":100,
               "y":100
            }
         },
         {
            "type":"endBlock",
            "width":150,
            "height":36,
            "id":"2",
            "data":{
               "label":"endBlock"
            },
            "position":{
               "x":100,
               "y":200
            },
            "positionAbsolute":{
               "x":100,
               "y":200
            }
         }
      ],
      "edges":[
         {
            "id":"e1-2",
            "source":"1",
            "target":"2"
         }
      ],
      "viewport":{
         "x":0,
         "y":0,
         "zoom":1
      }
   }
}