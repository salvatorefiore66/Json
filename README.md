# Json
Very straightforward to use and extend. Instantiate a new class JsonQuery passing by argument the
json string to the constructor. Follow various methods examples.

       $jstr0 =' 
       { 
         "items": 
       [';

       $jstr1='
       {
         "first_name" : "Sammy",
         "last_name" : "Shark",
         "location" : "Ocean",
         "description" : "this is the desc. n. 1",
         "websites" : [ 
           {
             "description" : "work",
             "URL" : "https://www.digitalocean.com/"
           },
           {
             "description" : "tutorials",
             "URL" : "https://www.digitalocean.com/community/tutorials"
           }
         ],
         "internet" : "servers",
         "online_media" : [
           {
             "online_media_1" : [
               { "description" : "twitter", "link" : "https://twitter.com/digitalocean0", "online_media" : "web site" },
               { "description" : "facebook","link" : "https://www.facebook.com/DigitalOceanCloudHosting0",  "online_media" : "web site"  }
             ]
           },
           {
             "online_media_2" : [
               { "description" : "twitter", "link" : "https://twitter.com/digitalocean1", "online_media" : "web site" },
               { "description" : "facebook","link" : "https://www.facebook.com/DigitalOceanCloudHosting1",  "online_media" : "web site"  }
             ]
      
           }
         ],
         "social_media" : [
           {
             "description" : "twitter",
             "link" : "https://twitter.com/digitalocean"
           },
           {
             "description" : "facebook",
             "link" : "https://www.facebook.com/DigitalOceanCloudHosting"
           },
           {
             "description" : "github",
             "link" : "https://github.com/digitalocean"
           }
         ]
       },'; 

       $jstr2='
       {
         "first_name" : "Jammy",
         "last_name" : "Shark",
         "location" : "Ocean",
         "description" : "this is the desc. n. 2",
         "websites" : [ 
           {
             "description" : "work",
             "URL" : "https://www.digitalocean.com/"
           },
           {
             "description" : "tutorials",
             "URL" : "https://www.digitalocean.com/community/tutorials"
           }
         ],
         "internet" : "servers",
         "social_media" : [
           {
             "description" : "twitter",
             "link" : "https://twitter.com/digitalocean"
           },
           {
             "description" : "facebook",
             "link" : "https://www.facebook.com/DigitalOceanCloudHosting"
           },
           {
             "description" : "github",
             "link" : "https://github.com/digitalocean"
           }
         ]
       }
       ]
       }'; 



       $jstr = $jstr0 . str_repeat($jstr1,1) . $jstr2;

       // Instantiate a new JsonQuery class passing the json string to the constructor
       $jq = new JsonQuery($jstr); 


       // Display the first 50 tree nodes for current json 
       // starting fron first node
       echo $jq->JsonTree(50);


       // Getting the number of nodes allocated by the json list
       $totnodes = $jq->jsonStrNumNode();
       echo "tot node allocated   " . $totnodes . "<br><br>";


       // Seeking a key with no depth. For json with unique keys.
       $arrayres = $jq->JSeekKey("internet");
       echo "key searched  > internet < - found : " . sizeof($arrayres) .  "<br><br>";


       // Seeking a key with depth. For json with nesting.
       $arrayres = $jq->JSeekKeyDepth("description",4);
       echo "key searched > description < with depth 4  - found : " . sizeof($arrayres) .  "<br><br>";


       // Seeking a key with depth starting the search at node number.
       // For json with nesting avoiding a likely header.
       $arrayres = $jq->JSeekKeyDepth("description",2,10);
       echo "key searched > description < with depth 2  - found : " . sizeof($arrayres) .  "<br><br>";

       // Seeking an arrangement of json array/object with key.
       $arrayres = $jq->JSeekKeyArr("websites");
       echo "object/array with key searched > websites <   - found : " . sizeof($arrayres) .  "<br><br>";


       // Seeking an arrangement of json array/object with key and depth.
       // Avoiding headers or unwanted objects set at the beginning
       // of json.
       $arrayres = $jq->JSeekKeyDepthArr("online_media",2,10);
       echo "object/array with key searched > online_media <  with depth 2  - found : " . sizeof($arrayres) .  "<br><br>";



       // callback function for checking the key reported in $keystoselect
       $func1 = function($jnode)
       {    
           echo "checking with func1 <br>";
           return true;

       };
    
    
    
       // callback function for checking the key reported in $keystoselect
       $func2 = function($jnode)
       {    
    
           echo "checking with func2 <br>";
           return true;
   
       };

       

       // Selecting only given keys - $keystoselect - from an arrangement 
       // of json array/object - $objectkey
       // The object/array is with key and of a certain depth.
       // A callback will be invoked for each key in
       // $keyselect. The callback will save the key when returning true, nothing otherwise.
       // The callback will receive by argument current json node.
       // Callback function can be set to null to avoid the call.
       // Avoiding headers or unwanted objects set at the beginning
       // of json


       $objectkey = "online_media";

       $keystoselect = array("description" => $func1,"link"  => $func2);

       $arrayres = $jq->JSeek($keystoselect,$objectkey,2,1);

       echo "object/array with key searched > online_media <  with depth 2  - found : " . sizeof($arrayres) .  "<br><br>";

       foreach($arrayres as  $arrayre)
             echo "$arrayre->key  -- $arrayre->value   <br><br>";
       



