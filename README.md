# Json
Json related files for querying storing converting
<?php


include 'jsonquery.php';

   
 
   // Very straightforward to use and extend. Instantiate a new class JsonQuery passing by argument the
   // json string to the constructor. Follow various methods examples.

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
      "description" : "work1",
      "URL" : "https://www.digitalocean.com/"
    },
    {
      "description" : "work2",
      "URL" : "https://www.digitalocean.com/"
    },
    {
      "description" : "work3",
      "URL" : "https://www.digitalocean.com/"
    },
    {
      "description" : "work4",
      "URL" : "https://www.digitalocean.com/"
    },
    {
      "description" : "work5",
      "URL" : "https://www.digitalocean.com/"
    },
    {
      "description" : "tutorials",
      "URL" : "https://www.digitalocean.com/community/tutorials"
    }
  ],
  "internet" : "servers",
  "old_internet_specials" : "old media services",
  "online_media" : [
    {
      "online_media" : [
        { "description" : "twitter", "link" : "https://twitter.com/digitalocean0", "online_media" : "web site" },
        { "description" : "facebook","link" : "https://www.facebook.com/DigitalOceanCloudHosting0",  "online_media" : "web site"  },
        { "media" : "facebook","link" : "https://www.facebook.com/DigitalOceanCloudHosting1",  "online_media" : "web site"  },
        { 
           "online_media" : [
              { "description" : "same key nested", "link" : "https://samekeynested"},
              { "description" : "same key nested 1", "link" : "https://samekeynested_1"}
              ]
        }
           
      ]
    },
    {
      "online_media_1" : [
        { "description" : "twitter", "link" : "https://twitter.com/digitalocean0", "online_media" : "web site" },
        { "description" : "facebook","link" : "https://www.facebook.com/DigitalOceanCloudHosting0",  "online_media" : "web site"  },
        { "media" : "facebook","link" : "https://www.facebook.com/DigitalOceanCloudHosting1",  "online_media" : "web site"  }
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
  ],
  "internet_specials" : "the old guard",
  "new_internet_specials" : "new media service"
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
    },
    {
      "special description" : "github",
      "special link" : "https://github.com/digitalocean"
    }
  ]
}
]
}'; 



    ini_set('memory_limit', '1000M');
    ini_set('max_execution_time',1000);
 
  
  
    //  EXAMPLE 1 Load test with ready to query json list based on apache server running php 7.2
    
    //  Test Items     Loading Time (sec. taken)    
    //    
    //      100               0.0
    //     1000               0.04
    //    10000               9.67
    //    20000              15.18
    //    40000              34.79
    //    70000              62.03
    //    80000              73.53
    //   150000             138.44
    //           1080/ sec.
    
 /* 
    $testitems ='
    {
      "first_name" : "Sammy",
      "last_name" : "Shark",
      "location" : "Ocean",
      "description" : "this is the desc. n. 1"
    }';
 
    
    // prepare a string with n testitems  == x
    $jstr = '[';
   
    for($x = 0; $x < 10000; $x++)
        $jstr .= $testitems . ',';
    $jstr .=  $testitems . ']';



    // Instantiate a new JsonQuery class passing the json string to the constructor
    $jq = new JsonQuery($jstr); 
    
  
 */
 /*   
    //
    //  EXAMPLE 2.  Load test, searching/replacing  json keys/values, based on apache server running php 7.2
    //
 
   
    //json strings    Load Time (sec.)  Querying time (sec.)
    //     100          0.07                0.16    
    //     700          7.30                1.95
    //    1000         15.28                2.96
    //    1500         24.09                5.22
    //    2000         27.61               11.44
    //    2500         34.17               13.99
    //    3000         42.33               19.92
    //    3500         49.50               25.64
    //    5000         71.26               49.42
    //    8000        121.11              110.00


    // prepare structures
    $jstr = $jstr0;
    for($x = 0; $x < 1; $x++)
        $jstr .= $jstr1;
        
    $jstr .= $jstr2;
    
    // Instantiate a new JsonQuery class passing the json string to the constructor
    $jq = new JsonQuery($jstr); 

       
    // NOTE : All insertion, moving and replacing methods do not check if the copying/moved/inserted/replaced nodes contain keys already in use, as for json
    // keys must be unique in the context in which are used.
  
  
    // Getting the number of nodes allocated for the json list
    $totnodes = $jq->jsonStrNumNode();
    //echo "tot node allocated   " . $totnodes . "<br><br>";


    // Seeking a unique key. Starting the search at node number 5
    $noderes = $jq->JSeekKeyUnique("internet_specials",5);
    //echo "unique key searched  :  <br> > internet_specials < - found : "  . count($noderes) . "<br><br>";
    
    // uncomment next lines to see the query results
    //foreach($noderes as $nodere)
       //echo " node " . $nodere->nodeNum . " <  key > " . json_decode($nodere->listvalue)->key . " < value > " . json_decode($nodere->listvalue)->value . "<br><br>";
   

    // Seeking unique keys. Starting the search at node number
    $noderes = $jq->JSeekKeyUnique(array("internet_specials","old_internet_specials","new_internet_specials","not_found"),5);
    //echo "unique keys searched  : <br> > internet_specials  old_internet_specials  new_internet_specials < - found : " .  count($noderes) . "<br><br>";
    
    // uncomment next lines to see the query results
    //foreach($noderes as $nodere)
       //echo " node " . $nodere->nodeNum . " <  key > " . json_decode($nodere->listvalue)->key . " < value > " . json_decode($nodere->listvalue)->value . "<br><br>";
       
     
    // Seeking keys. Starting the search at node number 1
    $noderes = $jq->JSeekKey(array("description","internet","websites"),1);
    //echo "key searched  > description internet websites < - found : " . sizeof($noderes) .  "<br><br>";


    // Seeking keys with no depth. For json with unique keys.
    $arrayres = $jq->JSeekKey("internet",26);
    //echo "key searched  > internet < - found : " . sizeof($arrayres) .  "<br><br>";


    // Seeking keys with depth 4. For json with nesting.
    $arrayres = $jq->JSeekKeyDepth("description",4);
    //echo "key searched > description < with depth 4  - found : " . sizeof($arrayres) .  "<br><br>";


    // Seeking key with depth starting the search at node number 10. !
    // For json with nesting avoiding a likely header.
    $arrayres = $jq->JSeekKeyDepth("description",2,10); 
    //echo "key searched > description < with depth 2  - found : " . sizeof($arrayres) .  "<br><br>";


    // Seeking arrangements of json array/objects having as key "websites".
    $arrayres = $jq->JSeekKeyArr("websites");
    //echo "searched object/array having as key \"websites\"    - found : " . sizeof($arrayres) .  "<br><br>";

    // uncomment next lines to see the query results
    // foreach($arrayres as $keys)
    //  echo "  " . $keys->listvalue . " " . "  <br>";


    // Seeking an arrangement of json array/object having as key "online_media_1" and depth 4. 
    $arrayres = $jq->JSeekKeyDepthArr("online_media_1",4,1);
    //echo "object/array with key searched > online_media_1 <  with depth 4  - found : " . sizeof($arrayres) .  "<br><br>";
    

    // Seeking arrangements of json array/object having as key "social_media". 
    $arrayres = $jq->JSeekKeyArr("social_media",109);
    //echo "object/array with key searched > social_media <  - found : " . sizeof($arrayres) .  "<br><br>";
    
    // uncomment next lines to see the query results
    //foreach($arrayres as $keys)
      //echo "  " . $keys->listvalue . " " . "  <br>";

    
    // callback function for checking the key reported in $keystoselect
    $func1 = function($jnode)
    {    
       // uncomment next lines to see the call
       // echo "checking with func1 <br>";
       return true;

    };
    

    // callback function for checking the key reported in $keystoselect
    $func2 = function($jnode)
    {    
       // uncomment next lines to see the call
       // echo "checking with func2 <br>";
       return true;

    };


    // Selecting only given keys as in $keystoselect - from an arrangement 
    // of json array/object as in $objectkey.
    // The object/array is with key and of a certain depth.
    // A callback will be invoked for each key in
    // $keyselect. The callback will save the key when returning true, nothing otherwise.
    // The callback will receive by argument current json node.
    // Callback function can be set to null to avoid the call.
    // Avoiding headers or unwanted objects set at the beginning
    // of json is set to 1
    
    $objectkey = "websites";
    $keystoselect = array("description" => $func1,"link"  => null);

    $arrayres = $jq->JSeek($keystoselect,$objectkey,2,1);

    //echo "object/array with key searched > websites <  with depth 2  - found : " . sizeof($arrayres) .  "<br><br>";
    // uncomment next lines to see the query results
    //foreach($arrayres as  $arrayre)
    //{
       //echo json_decode($arrayre->listvalue)->key . "  " . json_decode($arrayre->listvalue)->value .  "  <br><br>";
    //} 
      
    
    // Seeking unique path list node in json. The search starting at list node 1.
    // Returns the node of the path end, null if the path has not
    // been found. The path to be found can also be passed as dot notation
    // "items.1.social_media.1.link".
    
    $keypath = array("items",1,"social_media",1,"link");
 
    //echo "Seeking path : items,1,social_media,1,link <br><br>";
    $node = end($jq->JSeekPath($keypath,1)); 
    
    //if($node === null)
       //echo "path not found";
    //else
       //echo "path end found at node  " . $node->nodeNum;
 
 
    // Trace back a path for a given node number
    $nodenum = $jq->jList()->getNode(67);
    
    //echo  "<br><br>path for node 67  " . "<br>";
    
    // Returns a full path in dot notation given a path end node $nodenum
    // $nodenum should be set to the actual path end node or the 
    // the json list node number
    //echo  $jq->JGetPath($nodenum) . " <br> ";


    // Seeking the array parent or owner of node $Node
    // Returns null if the $Node does not belong
    // to an array/object/index
    //echo $jq->getMyArray($jq->jList()->getNode(2))->nodeNum;
        

    // Seeking all indices of the keys "websites". online_media_1
    // The search starting at list node 1.
    // Returns all nodes indices of the key "websites" found.
    $arrn = $jq->JSeekKeyIndex(array("websites","online_media_1"));
    
    //echo "<br><br> indices found for keys websites  online_media_1 " . count($arrn) .  "<br>";
    
    // uncomment next two lines to view all nodes fetched with the query
    //foreach($arrn as $arr)
        //echo " <br><br>index  key " .  json_decode($arr->listvalue)->key . " index value " . json_decode($arr->listvalue)->value . " found in node n. " . $arr->nodeNum;
  
   

    $keystosave = array( 
        
        'websites'=> array(           
            'URL'),
            
        'online_media_1'=> array(    
            'link')
    );

   
    // Seeking all indices of json arrays "websites" and "online_media_1"
    // and subsequently extracts elements applying a key filtering.
    // The search starting at list node 1.
    // Returns all nodes with keys in $keystosave
    
    $arrnode = $jq->JSeekIndexElement($jq->JSeekKeyIndex(array("online_media_1","websites")),$keystosave);
    
    
    //echo "<br><br>elements of  online_media_1 <br>";
    
    //if($arrnode == null || count($arrnode) === 0)
       //echo "no elements in the index";
    //else
    //{  
        //foreach($arrnode as $arr)
        //{
            //echo json_decode($arr->listvalue)->key; 
            //echo " at node " . $arr->nodeNum . "<br><br>"; 
        //}
 
    //}


       
    // Seeking all indices of the key  "websites" "online_media_1" "social_media"
    // The search starting at list node 1.     
    // It handles also same key nested arrays/objects.
    // Returns all indices of the found keys along with the complete path in a multidimensional.
    // array See example.
    $arrn = $jq->JSeekKeyIndexMulti(array("websites","online_media_1","social_media"));
    //echo "<br><br> indices found for array \"websites\" \"online_media_1\" \"social_media\"  " . sizeof($arrn,1) . "<br>";
    
    // uncomment next lines to view the keys found
    //echo "<pre>";
    //show($arrn,7);
    //echo "</pre>";
   
 
    $keysfilter = array( 
        
        'websites'=> array(           
            'URL'),
            
        'online_media_1'=> array(    
            'link')
    );

  
 
    // Seeking all indices  and elements of the keys "websites". online_media_1
    // The search starting at list node 1.
    // Returns in a multidimensional array the listvalue of all nodes indices of the keys "websites" and "online_media_1".
    $arrn = $jq->JSeekKeyIndicesAndElementsMulti(array("websites","online_media_1"),$keysfilter,1);
   
    // uncomment next lines to view the keys found
    echo "<pre>";
    print_r($arrn);
    echo "</pre>";
  

  
    // Seeking unique path for a json array/object list node.
    // The search starting at list node 1.
    // Returns all indices of the path "items","0","websites","0" end or null if the path has not
    // been found.
    // NOTE : The path end node must be an index.
    $arrn = $jq->JSeekPathIndex("items.0.websites.0",1);
    
    //echo "<br><br>indices found for path " .  " \"index of - items 0 websites 0\"  : " . count($arrn) .  "<br>";
   
    // uncomment next two lines to view all nodes fetched with the query
    //foreach($arrn as $arr)
        //echo "<br>index  key > " .  json_decode($arr->listvalue)->key . "  < found in node n. " . $arr->nodeNum;
  
  
  

    // Seeking unique path for a json array/object list node.
    // The search starting at list node 1.
    // Returns all indices of the path "items","0","online_media","0","online_media_1" end or null if the path has not
    // been found.
    
    $keypath = array("items","0","online_media","1","online_media_1");
    $arrn = $jq->JSeekPathIndex($keypath,1);
    
    //echo "<br><br>" . " there are " . count($arrn) . " index of - items 0 online_media 0 online_media_1 " . "<br><br>";
  
   
    //echo  " index of - index of - items 0 online_media 0 online_media_1  - contains the following items " . "<br><br>";


    // uncomment next two lines to view all nodes fetched with the query
    //foreach($arrn as $arr)
       //echo "<br>index  key > " .  json_decode($arr->listvalue)->key . "  < found in node n. " . $arr->nodeNum;
  
    
  
  
    // Replacing the values of given keys - $keystoselect - from an arrangement 
    // of json array/object - $objectkey.
    //
    // The object/array  with key and of a certain depth.
    // The callback will receive by argument current json arrangements nodes as context 
    // and current node iterated through.
    //  
    // The callback will replace the value of the associate key when returning true, 
    // nothing otherwise.
    // Callback function can be set to null to avoid the call.
    // The search starting at list node 1.
    // Returns in an array the replaced node values.

    $objectkey = "online_media_1";

    $keystoselect = array("description" => $func1,"link"  => $func2);
    
    $valuereplace = array("description" => "new description", "link" => "new link");

    $replaced = $jq->JReplace($keystoselect,$valuereplace,$objectkey,4,1); 

    //echo "object/array value replaced with key searched > online_media_1 <  with depth 4  - found : " . count($replaced) .  "<br><br>";
    
  

    // Replacing nodes values in json with unique keys. 
    // Set to 10 the number of node where to start
    // the search for replacement. 
    // Returns in an array the replaced node values.

    $keystoselect = array("location" => "new location","first_name" => "name changed");

    $replaced = $jq->JReplaceVal($keystoselect,10);
    
    //echo "value replaced with key searched > location , first_name<   - found : " . count($replaced) .  "<br><br>"; 
    
    

    // Replacing  nodes values in nested json with depth and keys.
    // Returns the number of values replaced.
    // Replace for all keys 'internet' at depth 2 the value 'new val int.'
    // Returns in an array the replaced node values.
    $keystoselect = array("first_name" => "Sammy jr.","internet" => "new. int.");
    $replaced = $jq->JReplaceValDepth($keystoselect,2);
    //echo "values replaced with key searched > first_name internet < depth 2 - found : " .  count($replaced) .  "<br><br>";




    // Seeking first node in json with unique key and replacing 
    // the associated value. The search starting at list node 1.
    //
    // The search stops at the first key found
    // Returns in an array the replaced node values.
    $keystoselect = array("internet_specials" => "new intern. spec.","new_internet_specials" => "new. i. spe.");
    $replaced = $jq->JReplaceValUnique($keystoselect,1); 
    
    //echo "value replaced with key searched > internet_specials , new_internet_specials <   - found : " . count($replaced) .  "<br><br>";
  
  
  
 
    // Seeking unique path list node in json and replacing the key value.
    // The search starting at list node 1.
    // Returns the node of the path end, null if the path has not
    // been found.
    $keypath = array("items.1.social_media.1.description" => "new desk.1","items.1.social_media.1.link" => "new link 1");
    $node = $jq->JReplaceValPath($keypath,1); 
    

    //echo "value replaced for  items 1 social_media 1 link at node  "  .  "   " . $node->nodeNum .  "<br><br>";
  
  
  

    // Replacing elements values of a json array  with the values passed by parameter;
    // The array with replacing values can also be multidimensional so each dimension of the array 
    // will replace the corresponding index in the json array. If the value is null it will not
    // be replaced.
    
    $keypath = array("items","1","social_media");
    
    $keyvalue = array("special description" => "new desk.", "special link" => "new lnk");
    
    $arrn = $jq->JReplacePathIndexElement($keypath,$keyvalue,1); 

    //echo "values replaced for items 1 social_media > " . count($arrn) . "<br><br>";
    
    
    

    // Replacing elements values of a json array  with the values passed by parameter;
    // The array with replacing values can also be multidimensional so each dimension of the array 
    // will replace the corresponding index in the json array. If the value is null it will not
    // be replaced
    
    $keypath = array("items","1","social_media","0");
    
    $keyvalue = array("description" => "new 0", "link" => "new 0");
    
    $arrn = $jq->JReplacePathIndexElement($keypath,$keyvalue,1); 

    //echo "values replaced for items,1,social_media,0 > "  . count($arrn) . "<br><br>";
    

    // Copying nodes 
    // Copying portion of the json tree from a given path $frompath to a destination path $topath by inserting 
    // new nodes copies of the origin path. Node numbers can also be passed or even actual list nodes.
    // If destination path $insertpath is null or not passed to the method then the copy will be
    // appended to the json tree. 
    // Setting $asindexof the copy will be inserted as an index of the path in $asindexof.
    // The $startnode can be set at the node number from where to start the path search or with 
    // an actual node. $mode can be set to "before" "after" to indicate precisely the insertion point
    // of a node before or after  $insertat.
    // Returns the number of inserted keys (nodes)
    // NOTE : The method does not check if the copied nodes contain keys already in use in the moving position 
    // array/object.
    // public function JCopy($from,$to,$insertat,$mode="after",$asindexof=null,$startnode=null) 
    
    $frompath="items.0.online_media";
    $topath="items.0.online_media.0.online_media.3.online_media.1.link";
    $insertpath=null;
    $asindexof=null;
    
    $jq->JCopy($frompath,$topath,$insertpath,"after",$asindexof,1);
  
  
  
    // Moving nodes 
    // Moving portion of the json tree from a given path $frompath to a destination path $topath by moving 
    // existing nodes. Node numbers can also be passed or even actual list nodes.
    // If destination path $insertpath is null or not passed to the method then the nodes will be
    // appended to the json tree. 
    // Setting $asindexof the nodes will be moved as an index of the path in $asindexof.
    // The $startnode can be set at the node number from where to start the path search or with 
    // an actual node. $mode can be set to "before" "after" to indicate precisely the insertion point
    // of moving nodes before or after  $insertat.
    // Returns the number of moved keys (nodes)
    // NOTE : The method does not check if the moved nodes contain keys already in use in the moving position 
    // array/object. 
    // public function JMove($from,$to,$insertat,$mode="after",$asindexof=null,$startnode=null) 
    
    // Moving the key old_internet_specials from one array index to another
    $frompath="items.0.old_internet_specials";
    $topath="items.0.old_internet_specials";
    $movetopath="items.1";
  
    
    $jq->JMove($frompath,$topath,$movetopath,"after",null,1);
    
  
    echo $jq->JsonTree();
    
    
 */
 /*  
    //
    // EXAMPLE 3. A new json created and populated runtime, using templates and copy and paste, moving technique.
    //
  
  
    // Instantiate a new JsonQuery class without an initial json string
    // The empty json list will be populated runtime using insertion techniques, moving and copying 
    // nodes. Also templates previously stored or ceated runtime are used to populate
    // the json tree list.
    $jqins = new JsonQuery(); 

    $keysinsert_1 = array("description_1" => "ins new desc_1", "link_1" => "ins new link_1");
    $keysinsert_2 = array("description_2" => "ins new desc_2", "link_2" => "ins new link_2");
    $keysinsert_3 = array("description" => "between 1 and 2", "link" => "between 1 and 2");

  
    // Appending keys.
    // keys and values are passed in $keysinsert
    // Returns the number of inserted keys
    $jqins->JInsertKey($keysinsert_1);
  
     

    // Inserting keys at node 3
    
    $jqins->JInsertKey($keysinsert_2,3);


  
    // Inserting a new key by using the path. Keys and values are passed in $keysinsert
    // Inserting new keys at position $nodepath. $nodepath can receive an actual list node,
    // a dot notation path or a node number. If a  path is passed in $nodepath then a node number
    // from where to start the search of the path can be passed in $nodenum . The keys will
    // be appended if $nodenum and $nodepath are null. 
    // Keys and values are passed in $keysinsert
    // Returns the number of inserted keys.
    // JInsertKey($keysinsert,$nodepath=null,$nodenum=null)
    $jqins->JInsertKey($keysinsert_3,"link_1");
 
 
 
    // Appending an array ($carkeys) with a new key "newarray" and populating its elements with keys and values.
    // The populating array containing keys and values can be multidimensional or multidimensional associative with key.
    // multi dimension array 
    $carkeys = array(
      array(
        "name"=>"Urus", 
        "type"=>"SUV", 
        "brand"=>"Lamborghini"
       ),
      array(
        "name"=>"Cayenne", 
        "type"=>"SUV", 
        "brand"=>"Porsche"
       ),
       array(
        "name"=>"Bentayga", 
        "type"=>"SUV", 
        "brand"=>"Bentley"
      ),
      array(
        "name"=>"Bentayga", 
        "type"=>"SUV", 
        "brand"=>"Bentley"
      )
    );


    // Inserting new array ($key) containing keys and values ($keyinsert).
    // Inserting will happen at position $nodepath. $nodepath can receive an actual list node,
    // a dot notation path or a node number.
    // If $nodenum and $nodepath are both  null or not passed to the method then the array keys will be
    // appended.
    // To insert the key as index in the right position set the $depth otherwise
    // it will be inserted  at depth of the neighbour.
    // The array containing keys and values can be multidimensional or multidimensional 
    // associative array with key.
    // Returns the number of inserted keys.
    // JInsertKeyArr($key,$keyinsert,$depth,$nodepath=null,$nodenum=null) 
    $jqins->JInsertKeyArr("newcararray",$carkeys,null,null,3);



    // Inserting an array with a new key "order" and populating its elements with keys and values.
    // The populating array $customerarray containing keys and values can be multidimensional and associative (serialized).
    $customerarray=[                     
        'customer'=>[               
            0=>'Jane Doe ',         
            1=>'Nash Patel '
        ],
        'order_date'=>[       
            0=>'7 October 2015 ',  
            1=>'14 October 2014 ', 
            2=>'12 October 2016 '  
        ]
    ];

    
    // Insert array $carkeys at path "newcararray.2". 
    // it will be inserted  at depth of the neighbour.
    $jqins->JInsertKeyArr("1.1",$carkeys,null,"newcararray.2");
    
 
 
    // Inserting a blank node to make space between keys.
    // Inserting new keys at position $nodepath. $nodepath can receive an actual list node,
    // a dot notation path or a node number. If a  path is passed in $nodepath then a node number
    // from where to start the search of the path can be passed in $nodenum . The keys will be appended 
    // if $nodenum and $nodepath are null. 
    // The number of keys to insert is passed in $totkey.
    // Returns the nodes of inserted keys in an array.
    $jqins->JInsertKeyVoid(5,"newcararray.1.name",1);
    
    $jqins->JInsertKeyVoid(1,1);
   
   
   
    // Inserting  an array as index populating its elements with keys and values.
    // array as index
    $newcarcolor = array(
     
        "name"=>"blue", 
        "type"=>"sport", 
        "brand"=>"super color"

    ); 
    
  
    $path = "newcararray.1";
    $asindexof = "newcararray.1";
    $jqins->JInsertKeyArr("oneoffnewcarcolor",$newcarcolor,null,$path);
    
  
    echo $jqins->JsonTree(); 

 */


   
/**
 * function show($data) function printRLevel($data, $level = 5)
 * Courtesy of :
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */ 
function show($data)
{
	$args = func_get_args();

	$last = array_pop($args);

	if (is_int($last))
	{
		$level = $last;
	}
	else
	{
		$level = 4;

		$args[] = $last;
	}

	// Dump Multiple values
	if (count($args) > 1)
	{
		$prints = array();

		$i = 1;

		foreach ($args as $arg)
		{
			$prints[] = "[Value " . $i . "]\n" . printRLevel($arg, $level);
			$i++;
		}

		echo '<pre>' . implode("\n\n", $prints) . '</pre>';
	}
	else
	{
		// Dump one value.
		echo '<pre>' . printRLevel($data, $level) . '</pre>';
	}
}


function printRLevel($data, $level = 5)
{
	static $innerLevel = 1;

	static $tabLevel = 1;
    
	$self = __FUNCTION__;

	$type       = gettype($data);
	$tabs       = str_repeat('    ', $tabLevel);
	$quoteTabes = str_repeat('    ', $tabLevel - 1);
	$output     = '';
	$elements   = array();

	$recursiveType = array('object', 'array');

	// Recursive
	if (in_array($type, $recursiveType))
	{
		// If type is object, try to get properties by Reflection.
		if ($type == 'object')
		{
			$output     = get_class($data) . ' ' . ucfirst($type);
			$ref        = new \ReflectionObject($data);
			$properties = $ref->getProperties();

			foreach ($properties as $property)
			{
				$property->setAccessible(true);

				$pType = $property->getName();

				if ($property->isProtected())
				{
					$pType .= ":protected";
				}
				elseif ($property->isPrivate())
				{
					$pType .= ":" . $property->class . ":private";
				}

				if ($property->isStatic())
				{
					$pType .= ":static";
				}

				$elements[$pType] = $property->getValue($data);
			}
		}
		// If type is array, just retun it's value.
		elseif ($type == 'array')
		{
			$output   = ucfirst($type);
			$elements = $data;
		}

		// Start dumping data
		if ($level == 0 || $innerLevel < $level)
		{
			// Start recursive print
			$output .= "\n{$quoteTabes}(";

			foreach ($elements as $key => $element)
			{
			
				$output .= "\n{$tabs}[{$key}] => ";


				// Increment level
				$tabLevel = $tabLevel + 2;
				$innerLevel++;

				$output  .= in_array(gettype($element), $recursiveType) ? $self($element, $level) : $element;

				// Decrement level
				$tabLevel = $tabLevel - 2;
				$innerLevel--;
			}

			$output .= "\n{$quoteTabes})\n";
		}
		else
		{
			$output .= "\n{$quoteTabes}*MAX LEVEL*\n";
		}
	}
	else
	{
		$output = $data;
	}

	return $output;
}




?>
