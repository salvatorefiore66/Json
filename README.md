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


// Display the first 25 tree nodes for current json 
// starting fron first node
echo $jq->JsonTree();


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
// Avoiding headers or unwanted objects set at the beginning
// of json.
$arrayres = $jq->JSeekKeyArr("websites");
echo "object/array with key searched > websites <   - found : " . sizeof($arrayres) .  "<br><br>";


// Seeking an arrangement of json array/object with key and depth.
// Avoiding headers or unwanted objects set at the beginning
// of json.
$arrayres = $jq->JSeekKeyDepthArr("online_media",2,10);
echo "object/array with key searched > online_media <  with depth 2  - found : " . sizeof($arrayres) .  "<br><br>";


