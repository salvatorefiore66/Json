<?php 


include 'linkedlist.php';


// -----------------------------------------------------Class JsonQuery-----------------------------------------------------------
// Salvatore G. Fiore copyright 2020 www.salvatorefiore.com
// The main purpose: to be used for querying and structure json.
//
// Very easy and straigthforward to use and extend.
// The constructor takes a json string as argument and will allocate a dynamic double linked
// list containing the json structure.
// Once allocated by the constructor, the linked list will contain nodes with the analysed json objects arrays and values as
// a numerical representation of the json tree.
// A json tree may have a main object or array with depth 0 from which a nested structure can originate. Each json object or array 
// subsequently increments the depth with nesting whilst elements are 1 level deeper than the array/object declaration
// The list once populated with the analysed json, will eventually contain nodes with depth key and the actual values of the json
// as:
//
// { "depth":"nnn","key":"key","value":"fieldvalue","length":"tot items","type":"type"}
//
// It is thus possible to seek and access json values specifying depth and key of the object/array or key from which originated.
// The query methods can be subsequently used to select objects/arrays, key, values from the json list.
// JsonQuery class can handle mixed json structures of different formats at once.
// Querying can be quite fast and efficient although time to seek items can increase for high number of nodes. The class can easily
// manage hundred of thousands json structures at once.
// 
// 
//


class JsonQuery
{

    protected $json;                               // first analysed json string
    protected $jqueryList;                         // node list containing analysed json 
    protected $jsonsave;                           // json nodes list converted to string
   
  
    // constructor takes a json string as argument
    public function __construct($jstr=null)
    {
        
        $this->json = $jstr;

        // New linked list to contain json objects arrays and keys with values. 
        $this->jqueryList = new LinkedList(null,null);
        
    
        // Creating a new empty json 
        if($this->json == null || $this->json === "")
            $this->JNewJson();
        
          
        // Analysing the json string passed to the constructor populating the linked list
        // Throws an exception if there is an error during analysis.
        else
        { 
            if($this->JAnalyse() === false)
                throw new Exception("there is an error or no valid json in input");
        }
    
    }

    
    
    // Initiate a new empty json 
    private function JJson()
    {   
        $this->json = "{}";
        
        return true;
    }




    // Build a new json string from the list nodes.
    private function JNewJson()
    {   
        return true;
    }

    

    // Analysing a json string depth, nesting and actual key values.
    // A linked list containing records and objects decoded from a json string 
    // is populated  with  a numerical representation of the json stucture for
    // the whole string.
    private function JAnalyse()
    { 
        // Getting an array from json string
        $jarray = json_decode($this->json,null,512,JSON_OBJECT_AS_ARRAY);
        
        // Returns an error if json is not valid
        if(json_last_error()  !== JSON_ERROR_NONE)
            return false;
    
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($jarray), 
                        \RecursiveIteratorIterator::SELF_FIRST);
                        
        foreach($iterator as $key => $value)
        {
            // getting current nesting depth
            $depth = $iterator->getDepth();
            $type = gettype($value);
        
            // Compose json string to be stored as node value
            $jstr = "{\"depth\":\"" . $depth . "\",\"key\":\"" . $key . "\",\"value\":\""
            . $value ."\",\"length\":\"" . sizeof($value,1) .  "\",\"type\":\""
            . $type .    "\" }";
             
            
            // Inserting a node in the list with depth key and field value as json string
            $this->jqueryList->insertNode($jstr);
        
        } 
        
        // Unsetting the recursive iterator
        unset($iterator);
    
        return true;
    }
  
 

 
    // Seeking an arrangement of json array/object or a field with key and depth.
    // Select keys from array/object as indicated in $keyselect by the
    // return of a callback function. A callback will be invoked for each key in
    // $keyselect. The callback will save the key when returning true, nothing otherwise.
    // The callback will receive by argument current json arrangements nodes as context 
    // and current node iterated through.
    //  
    // Callback function can be set to null to avoid the call.
    // $startAtNode takes the actual node number from where to start
    // the search.
    // Returns an array with nodes.
    public function JSeek($keyselect,$key,$sdepth,$startAtNode=1) 
    { 
        $arr = array();
        
        // Iteratively seeking array nodes with key and saving context
        $this->jqueryList->iteratorListAtNode(function($node) use ($key,$sdepth,$keyselect,&$arr)
            {
                $jsonstr = json_decode($node->listvalue);
                $keydepth = $jsonstr->depth;
                
                // Array/object with sought key
                if((string) $jsonstr->key == (string) $key && (int) $jsonstr->depth == (int) $sdepth)
                {
                    $arr[] = $node;
                    
                    if((string) $jsonstr->type === "array")
                    {
                        // reset array pointer
                        //prev($arr);
                        
                        // Iteratively saving array nodes with key
                        $this->jqueryList->iteratorListAtNode(function($node) use ($key,$keydepth,$keyselect,&$arr)
                            {
                                $jsonstr = json_decode($node->listvalue);
                            
                                if((int) $jsonstr->depth > (int) $keydepth)
                                { 
                                    
                                    $arr[] = $node;
                                
                                    return true; 
                                }
                                else
                                    return false;
                             
                            },$node->nextNode); 
                    }
                }
                return true;
                
            },$this->getJNode($startAtNode));
        
        $arrNode = array();
        
        foreach($arr as $arrnode)
        {
            foreach($keyselect as $keysel => $func)
            {
                
                if((string) json_decode($arrnode->listvalue)->key === (string) $keysel)
                {
                    // callback function
                    if($func !== null) 
                    {
                        if(call_user_func($func,$arr,$arrnode) !== false)
                            $arrNode[] = $arrnode;
                    }
                    else 
                        $arrNode[] = $arrnode;
                }
       	                
            }
                                
        }
    
        return $arrNode;
    }


 
    // Seeking key nodes in json with unique keys.
    // $startAtNode takes the number of node from where to start
    // the search or the actual json list node.
    // A single string type with sought key should be passed in $key.
    // Otherwise to search more keys an array with sought keys should be passed.
    // Returns an array of json list nodes with the keys found.
    public function JSeekKey($soughtkeys,$startAtNode=1) 
    {
        $Nodesresult = null;
        $soughtkeys = $this->StrToArray($soughtkeys);
        $node = $this->getJNode($startAtNode);
   
        $keyscount = count($soughtkeys);
        
        // Seeking nodes with key in $keys
        while($node !== null)
        {
                foreach($soughtkeys as $soughtkey)
                {
                    if((string) json_decode($node->listvalue)->key == (string) $soughtkey)
                    {
                        $Nodesresult[] = $node;
                        break;
                    }
                }
                $node = $node->nextNode;
        }
        return $Nodesresult;
    }





    // Seeking unique key(s) node in json with unique keys.
    // $startAtNode takes the number of node or the actual json list node from
    // where to start the search.
    // The search will report only first occurrence for each key found.
    // A single string type with sought key should be passed in $key.
    // Otherwise to search more keys an array with sought keys should be passed.
    // Returns the json list nodes with the keys found
    public function JSeekKeyUnique($soughtkeys,$startAtNode=1) 
    {
        $Nodesresult = null;
        $soughtkeys = $this->StrToArray($soughtkeys);
        $node = $this->getJNode($startAtNode);
   
        $keyscount = count($soughtkeys);
        
        // Seeking nodes with key in $keys
        while($node !== null && $keyscount > 0)
        {
                for($posfound=0; $posfound < $keyscount;$posfound++)
                {
                    if((string) json_decode($node->listvalue)->key == (string) $soughtkeys[$posfound])
                    {
                        $Nodesresult[] = $node;
                        array_splice($soughtkeys,$posfound,1);
                        $keyscount--;
                        break;
                    }
                }
                $node = $node->nextNode;
        }
        return $Nodesresult;
    }



 
    // Seeking key nodes in json with unique keys and depth.
    // $startAtNode takes the number of node from where to start
    // the search or the actual json list node.
    // A single string type with sought key should be passed in $key.
    // Otherwise to search more keys an array with sought keys should be passed.
    // Returns an array of json list nodes with the keys found.
    public function JSeekKeyDepth($soughtkeys,$depth,$startAtNode=1) 
    {
        $Nodesresult = null;
        $soughtkeys = $this->StrToArray($soughtkeys);
        $node = $this->getJNode($startAtNode);
   
        $keyscount = count($soughtkeys);
        
        // Seeking nodes with key in $keys
        while($node !== null)
        {
                foreach($soughtkeys as $soughtkey)
                {
                    if((int) json_decode($node->listvalue)->depth == (int) $depth  && (string) json_decode($node->listvalue)->key == (string) $soughtkey)
                    {
                        $Nodesresult[] = $node;
                        break;
                    }
                }
                $node = $node->nextNode;
        }
        return $Nodesresult;
    }

 
 
 
    // Seeking arrangements of json array/object with key.
    // $startAtNode takes the node number from where to start
    // the search or the actual json list node.
    // Returns an array with nodes
    public function JSeekKeyArr($key,$startAtNode=1) 
    { 
        $arrNode = array();
        
        // Seeking array nodes with key
        $this->jqueryList->iteratorListAtNode(function($node) use ($key,&$arrNode)
            {
                $jsonstr = json_decode($node->listvalue);
                $keydepth = $jsonstr->depth;
                
                // Array/object with sought key
                if((string) $jsonstr->key == (string) $key && (string) $jsonstr->type == "array")
                {
                    $arrNode[] = $node;
                    
                    // Saving array nodes with key
                    $this->jqueryList->iteratorListAtNode(function($node) use ($key,$keydepth,&$arrNode)
                        {
                            $jsonstr = json_decode($node->listvalue);
                            
                            if((int) $jsonstr->depth > (int) $keydepth)
                            { 
                                $arrNode[] = $node;
                                return true;
                            }
                            else
                                return false;
                             
                        },$node->nextNode);
                }
                return true;
                
            },$this->getJNode($startAtNode));
            
        return $arrNode;
    }
     
    

    // Seeking an arrangement of json array/object with key and depth.
    // $startAtNode takes the actual node number from where to start
    // the search.
    // Returns an array with nodes
    public function JSeekKeyDepthArr($key,$sdepth,$startAtNode=1) 
    { 
        $arrNode = array();
        
        // Iteratively seeking array nodes with key
        $this->jqueryList->iteratorListAtNode(function($node) use ($key,$sdepth,&$arrNode)
            {
                $jsonstr = json_decode($node->listvalue);
                $keydepth = $jsonstr->depth;
                
                // Array/object with sought key
                if((string) $jsonstr->key == (string) $key && (int) $jsonstr->depth == (int) $sdepth && (string) $jsonstr->type == "array")
                {
                    $arrNode[] = $node;
                    // Iteratively saving array nodes with key
                    $this->jqueryList->iteratorListAtNode(function($node) use ($key,$keydepth,&$arrNode)
                        {
                            $jsonstr = json_decode($node->listvalue);
                            
                            if((int) $jsonstr->depth > (int) $keydepth)
                            { 
                                $arrNode[] = $node;
                                return true;
                            }
                            else
                                return false;
                             
                        },$node->nextNode);
                }
                return true;
                
            },$this->getJNode($startAtNode));
            
        return $arrNode;
    }
    
    
    
    
    // Seeking arrangements of json array/object having keys in $keys.
    // For json with unique keys.
    // $startAtNode takes the node number from where to start
    // the keys search or the actual json list node.
    // Returns all nodes found indices of arrays/objects arrangements having the sought keys.
    // The nodes found are returned in an array.
    public function JSeekKeyIndex($keys,$startAtNode=1) 
    { 
        $arrNode = array();
        
        $soughtkeys = $this->StrToArray($keys);
        
        // Seeking array nodes with key
        $this->jqueryList->iteratorListAtNode(function($node) use ($soughtkeys,&$arrNode)
            {
                $jsonstr = json_decode($node->listvalue);
                foreach($soughtkeys as $key)
            
                    // Array/object with sought keys
                    if((string) $jsonstr->key == (string) $key && (string) $jsonstr->type == "array")
                    {
                
                        $arrNode[] = $node;
                    
                        $totn = json_decode($node->listvalue)->length;
                        // iterating through the list to next index
                        $this->jqueryList->iteratorNode(function(&$node) use (&$arrNode,&$totn)
                        {
                            $jsonstr = json_decode($node->listvalue);
                            $nexnodenum = (int) $jsonstr->length;
                            $totn -= $nexnodenum + 1;
                            $arrNode[] = $node;
                            
                            $node = $this->jqueryList->getNodeOffset($nexnodenum,$node);
                
                            if($totn === 0)
                            { 
                                $arrNode[] = null;
                                return false;
                            }
                            else
                                return true;
            
                        }, $node->nextNode); 
                
                    }
                    return true;
                
            },$this->getJNode($startAtNode));
            
        return $arrNode;
    }
    

    
    // Seeking arrangements of json array/object having keys in $keys.
    // It can handle also same key nested arrays/objects.
    // $startAtNode takes the node number or the actual json list node from
    // where to start the keys search.
    // Returns  in a multidimensional array all nodes indices of arrays/objects arrangements having the sought keys.
    // along with the complete path.
    // The nodes are returned in a multidimensional array having array names as path keys + found key(s).
    // NOTE: The resulting multidimensional array contains recursive objects (node -> node -> node.....). Loopin
    // should be handled carefully.
    
    public function JSeekKeyIndexMulti($keys,$startAtNode=1) 
    { 
        $arrNode = array();
        $path = array();
    
        $soughtkeys = $this->StrToArray($keys);
        
        // Seeking array nodes with key
        $this->jqueryList->iteratorListAtNode(function($node) use ($soughtkeys,&$arrNode,&$path)
            {
                $jsonstr = json_decode($node->listvalue);
                
                if((string) $jsonstr->type == "array")
                {
                    // add path key to current path if key exists it will not be inserted twice
                    $path[$jsonstr->depth] = $jsonstr->key;
                
                    foreach($soughtkeys as $key)
            
                        // Array/object with sought key
                        if((string) $jsonstr->key == (string) $key)
                        {
                            $totn = json_decode($node->listvalue)->length;
            
                            // iterate through the list to next index
                            $this->jqueryList->iteratorNode(function(&$node) use (&$arrNode,&$totn,$pathnodes,&$path)
                            {
                                $jsonstr = json_decode($node->listvalue);
                                
                                $path[$jsonstr->depth] = $jsonstr->key;
                                
                                $nexnodenum = (int) $jsonstr->length;
                                
                                $totn -= $nexnodenum + 1;
                            
                                $this->SetMultiArrayToValue($arrNode, array_slice($path,0,$jsonstr->depth+1),$node);
                
                                $node = $this->jqueryList->getNodeOffset($nexnodenum,$node);
                            
                                if($totn === 0)
                                    return false;
                                else
                                    return true;
                            }, $node->nextNode); 
                        }
                        return true;
                }
            },$this->getJNode($startAtNode));
            
        return $arrNode;
    }
     
     

    // Seeking arrangements of json array/object having keys in $keys.
    // It can handle also same key nested arrays/objects.
    // $startAtNode takes the node number or the actual json list node from
    // where to start the keys search.
    // Returns  in a multidimensional array all nodes indices of arrays/objects arrangements having the sought keys.
    // along with the complete path.
    // The nodes are returned in a multidimensional array having array names as path keys + found key(s).
    // NOTE: The resulting multidimensional array contains * ONLY * the listvalue of the filtered out json list nodes.
    // The method will filter out on each key of the index element iterated through. To indicate the method the index element keys
    // to be saved an array with key names should be passed in $keysfilter.
    // If $keysfilter is not passed then all keys of the elements will be returned.
    // $keysfilter is an array containing for each index key one or more keys to
    // be saved:
    // Example:    array( 'indexkey1' =>  array( 'fieldkey1','fieldkey2'),
    //                    'indexkey2' =>  array( 'fieldkey1','fieldkey2'));
    
    public function JSeekKeyIndicesAndElementsMulti($keys,$keysfilter=null,$startAtNode=1) 
    { 
     
        $arrNode = array();
        $path = array();
    
        $soughtkeys = $this->StrToArray($keys);
        
        // Seeking array nodes with key
        $this->jqueryList->iteratorListAtNode(function($node) use ($soughtkeys,&$arrNode,&$path,$keysfilter)
            {
                $jsonstr = json_decode($node->listvalue);
                
                if((string) $jsonstr->type == "array")
                {
                    // adding path key to current path if key exists it will not be inserted twice
                    $path[$jsonstr->depth] = $jsonstr->key;
                
                    foreach($soughtkeys as $key)
            
                        // Array/object with sought key
                        if((string) $jsonstr->key == (string) $key)
                        {
                            $totn = json_decode($node->listvalue)->length;
            
                            // iterating through the list to next index
                            $this->jqueryList->iteratorNode(function(&$node) use (&$arrNode,&$totn,$pathnodes,&$path,$keysfilter,$key)
                            {
                                $jsonstr = json_decode($node->listvalue);
                                
                                $path[$jsonstr->depth] = $jsonstr->key;
                                
                                $nexnodenum = (int) $jsonstr->length;
                        
                                $length = $nexnodenum;
                                
                                $totn -= $nexnodenum + 1;
        
                                $index = $node->nextNode;
            
                                $y = 0;
                                
                                // saving the indexes nodes elements
                                for($X = 0; $X < $length; $X++) 
                                {
                                    if($keysfilter !== null)
                                    {
                                        if(in_array((string) json_decode($index->listvalue)->key,$keysfilter[$key]))
                                        {
                                            // add path for current index
                                            $path[$jsonstr->depth+1] = $y++;
                                            
                                            // saving element for current index
                                            $this->SetMultiArrayToValue($arrNode, array_slice($path,0,$jsonstr->depth+2)  ,$index->listvalue);
                                        }
                                    }
                                    else
                                    {
                                        // adding path for current index
                                        $path[$jsonstr->depth+1] = $y++;
                                        
                                        // saving element for current index
                                        $this->SetMultiArrayToValue($arrNode, array_slice($path,0,$jsonstr->depth+2)  ,$index->listvalue);
                                    } 
                                    
                                    $index = $index->nextNode;
                                }  
                
                                $node = $this->jqueryList->getNodeOffset($nexnodenum,$node);
                            
                                if($totn === 0)
                                    return false;
                                else
                                    return true;
                            }, $node->nextNode); 
                        }
                        return true;
                }
            },$this->getJNode($startAtNode));
            
        return $arrNode;
    }
     
     



    // Seeking unique path node indices of json array at $path.
    // $startnode takes the number of node from where to start
    // the search. It can also take the actual node from where to start
    // the search. If $startnode is null the search will start at the first
    // node of the json list. 
    // NOTE : The path end node must be an index.
    // Returns all nodes of the array indices or null if the path has not
    // been found.
    // NOTE : The array returned with the nodes of the array indices 
    public function JSeekPathIndex($path,$startnode=null)
    {
        // contains the indexes nodes 
        $arrnode = array();

        // seeking the path entry node
        $Node = end($this->JSeekPath($path,$startnode));
        if($Node == null)
            return null;   

        // case of seeking an index (need to know the parent including the index)
        if(json_decode($Node->nextNode->listvalue)->type !== "array")
        {
            // seeking node parent/owner of current node
            if(($parnode = $this->getMyArray($Node)) !== null)
            {
                $arrnode[] = $parnode;
                $arrnode[] = $Node;
                $arrnode[] = null;
            }
        }
        else
        {
            $arrnode[] = $Node;
            $totn = json_decode($Node->listvalue)->length;
            
            // iterating through the list
            $this->jqueryList->iteratorNode(function(&$node) use (&$arrnode,&$totn)
            {
            
                $jsonstr = json_decode($node->listvalue);
                $nexnodenum = (int) $jsonstr->length;
                $totn -= $nexnodenum + 1;
                $arrnode[] = $node;
                
                $node = $this->jqueryList->getNodeOffset($nexnodenum,$node);

                if($totn === 0)
                {
                    $arrnode[] = null;
                    return false;
                }
                else
                    return true;
    
            
            }, $Node->nextNode); 
        }
            
        return $arrnode;  
    }




 
    // Returns all nodes with the key in $keys as ListNode elements
    // belonging to json array indices in $indices.
    // NOTE : The method takes  indices as input in the array and each group
    // of indices report a head key and  are separated by a null closing the
    // group.
    // If $keys is not passed then all keys of the elements will be returned.
    // $keys is an array containing for each index key one or more keys to
    // be saved:
    // Example:    array( 'indexkey1' =>  array( 'fieldkey1','fieldkey2'),
    //                    'indexkey2' =>  array( 'fieldkey1','fieldkey2'));
    public function JSeekIndexElement($indices,$keys=null)
    {
        $indexcount = count($indices);
        
        $x = 0;
        
        // iterating through each index node
        while($x < $indexcount)
        {   
            $index = $indices[$x];
            
            // get the header key 
            $headerkey = (string) json_decode($index->listvalue)->key;
    
            // if the end of nodes indices for current key has been
            // reached goes to next set of indices
            while($index !== null)
            {
                
                $index = $indices[++$x];
        
                $length = (int) json_decode($index->listvalue)->length;
                
                $index = $index->nextNode;
                
                // saving the indexes nodes elements
                for($X = 0; $X < $length; $X++) 
                {
                    if($keys !== null)
                    {
                        if(in_array((string) json_decode($index->listvalue)->key,$keys[$headerkey]))
                        
                            $arrNode[] = $index;
                    }
                    else
                        $arrNode[] = $index;
                    
             
                    $index = $index->nextNode;
                }
            }  
            $x++;
        }
        return $arrNode;
    }
  



    // NOTE : All insertion, moving and replacing methods do not check if the copying/moved/inserted/replaced nodes contain keys already in use when 
    // copying/moving/inserting/replacing the nodes.
    
    
    
    
    
    // Seeking an arrangement of json array/object or a field with key and depth and
    // Replacing keys values as indicated in $keyselect. A callback will be invoked for each key in
    // $keyselect. The callback will replace the value for the key with the value reported
    // in $valuereplace when returning true, nothing otherwise.
    // The callback will receive by argument current json arrangements nodes as context 
    // and current node iterated through.
    //  
    // Callback function can be set to null to avoid the call.
    // $startAtNode takes the  node number or the actual node from where to start
    // the search.
    // Returns in an array the replaced values nodes.
    public function JReplace($keyselect,$valuereplace,$key,$sdepth,$startAtNode=1) 
    { 
    
        $arr = array();
        
        // Iteratively seeking array nodes with key
        $this->jqueryList->iteratorListAtNode(function($node) use ($key,$sdepth,$keyselect,$valuereplace,&$arr)
            {
                $jsonstr = json_decode($node->listvalue);
                $keydepth = $jsonstr->depth;
                
                // Array/object with sought key
                if((string) $jsonstr->key == (string) $key && (int) $jsonstr->depth == (int) $sdepth)
                {
                    $arr[] = $jsonstr;
                    
                    if((string) $jsonstr->type === "array")
                    {
                        // resetting array pointer
                        //prev($arr);
                        
                        // Iteratively saving array nodes with key
                        $this->jqueryList->iteratorListAtNode(function($node) use ($key,$keydepth,$keyselect,$valuereplace,&$arr)
                            {
                                $jsonstr = json_decode($node->listvalue);
                            
                                if((int) $jsonstr->depth > (int) $keydepth)
                                { 
                                    
                                    // saving the pointer to the node
                                    $arr[] = $node;
                                
                                    return true; 
                                }
                                else
                                    return false;
                             
                            },$node->nextNode); 
                    }
                }
                return true;
                
            },$this->getJNode($startAtNode));
        
        
    
        foreach($arr as $arrnode)
        {
            $arrnodejstr = json_decode($arrnode->listvalue);
            foreach($keyselect as $keysel => $func)
            {
                
                if($arrnodejstr->key === $keysel)
                {
                    // callback function
                    if($func !== null) 
                    {
                        if(call_user_func($func,$arr,$arrnode) !== false) 
                        {
                            $arrnodejstr->value = $valuereplace[$arrnodejstr->key];
                            $arrnode->listvalue = json_encode($arrnodejstr);
                    
                            
                            $arrReplaced[] = $arrnode;
                        }
                    }
                    else 
                    {
                            $arrnodejstr->value = $valuereplace[$arrnodejstr->key];
                            $arrnode->listvalue = json_encode($arrnodejstr);
                
                            
                            $arrReplaced[] = $arrnode;
                    }
                }
            }
                                
        }
    
        return $arrReplaced;
    }
    



    // Replacing nodes values in json with unique keys.
    // $startAtNode takes the  node number or the actual node from where to start
    // the search.
    // To replace more keys an array with sought keys should be passed in $soughtkeys;
    // Returns in an array the replaced values nodes.
    public function JReplaceVal($soughtkeys,$startAtNode=1) 
    {
        $arrReplaced = array();
        $soughtkeys = $this->StrToArray($soughtkeys);
   
        $keyscount = count($soughtkeys);
    
        // Seeking nodes with key
        $this->jqueryList->iteratorListAtNode(function($node) use ($soughtkeys,$val,&$arrReplaced)
            {
                $jsonstr = json_decode($node->listvalue);
                
                foreach($soughtkeys as $soughtkey => $val)
                
                    if((string) $jsonstr->key == (string) $soughtkey)
                    {
                        $jsonstr->value  = $val;
                        $node->listvalue = json_encode($jsonstr);
                        
                        $arrReplaced[] = $node;
                    }
            
                return true;
                
            },$this->getJNode($startAtNode));
        
        return $arrReplaced;
    }
  

   
    // Replacing value of first node sought in json tree.
    // $startAtNode takes the  node number or the actual node from where to start
    // the search.
    // The search stops at the first key found in json tree for each of the key in $soughtkeys.
    // To replace more keys an array with sought keys should be passed in $soughtkeys;
    // Returns in an array the replaced values nodes.
    public function JReplaceValUnique($soughtkeys,$startAtNode=1) 
    {
        $soughtkeys = $this->StrToArray($soughtkeys);
        $node = $this->getJNode($startAtNode);
   
        $keyscount = count($soughtkeys);
        
        
        // Seeking nodes with key in $keys
        while($keyscount > 0 && $node !== null )
        {
                $posfound = 0;
                foreach($soughtkeys as $key => $val)
                {
                    if((string) json_decode($node->listvalue)->key == $key)
                    {
                        
                        $jsonstr = json_decode($node->listvalue);
                        $jsonstr->value  = $val;
                        $node->listvalue = json_encode($jsonstr);
                        
                        $arrReplaced[] = $node;
                        
                        array_splice($soughtkeys,$posfound,1);
                        $keyscount--;
                        
                        break;
                    }
                    $posfound++;
                }
                $node = $node->nextNode;
        }
        return $arrReplaced;
    }




    // Replacing  nodes values in nested json with depth and keys.
    // To replace more keys and corresponding values an array with sought keys
    // should be passed in $soughtkeys
    // $startAtNode takes the  node number or the actual node from where to start
    // the search.
    // Returns in an array the replaced values nodes.
    public function JReplaceValDepth($soughtkeys,$depth,$startAtNode=1) 
    {
        $arrReplaced = array();
        
        //$soughtkeys = $this->StrToArray($soughtkeys);

        // Seeking nodes with depth and key
        $this->jqueryList->iteratorListAtNode(function($node) use ($depth,$soughtkeys,$val,&$arrReplaced)
            {
                $jsonstr = json_decode($node->listvalue);
                
                foreach($soughtkeys as $soughtkey => $val)
                
                    if((int) $jsonstr->depth == (int) $depth && (string) $jsonstr->key == (string) $soughtkey)
                    {
                    
                        $jsonstr->value  = $val;
                        
                        $node->listvalue = json_encode($jsonstr);

                        $arrReplaced[] = $node;
                    }
 
                return true;
                
            },$this->getJNode($startAtNode));

        return  $arrReplaced;
    }
  
  
    
    // Replacing the value for unique path nodes in json list.
    // To replace more keys and corresponding values an array with more paths
    // should be passed in $paths.
    // $startAtNode takes the number of node from where to start
    // the search. It can also take the actual node from where to start
    // the search. If $startnode is null the search will start at the first
    // node of the json list.
    // Returns the nodes of the path end with replaced value.
    public function JReplaceValPath($paths,$startnode=1) 
    {  
        $arrNode = array();
        
        foreach($paths as $path => $value)
        {
            // seeking the path entry node
            $Node = end($this->JSeekPath($path,$startnode));
            if($Node == null)
                continue;   
    

            // replacing the node value
            $jsonstr = json_decode($Node->listvalue);
                

            $jsonstr->value = $value;
            $Node->listvalue = json_encode($jsonstr);
            $arrNode[] = $Node;
        }
        return $arrNode;
        
    }
    
  

    // Replacing path node elements from a json array.
    // $startAtNode takes the number of node from where to start
    // the search of the keys. It can also take the actual node from where to start
    // the search. If $startnode is null the search will start at the first
    // node of the json list.
    // The path end $key must be an array index of it.
    // Returns all nodes as ListNode index of the path with value replaced 
    // or null if the path has not been found or no indices to replace the value of have been found.
    // The array with replacing values can also be multidimensional so each dimension of the array 
    // will replace the corresponding index in the json array. If the value is null it will not
    // be replaced.
    public function JReplacePathIndexElement($path,$keyvalue,$startnode=1) 
    {
        $indices = $this->JSeekPathIndex($path,$startnode);
        if($indices == null)
            return null;
        $arrnodes = $this->JSeekIndexElement($indices);
        if(count($arrnodes) == 0 || $arrnodes == null)
            return null;
        
        $x=0;
        foreach($arrnodes as $arrnode)
        {   

            // replacing the node value with given key
            $jsonstr = json_decode($arrnode->listvalue);
            
            foreach($keyvalue as $key => $value)
            { 
                if((string) $jsonstr->key == (string) $key)
                { 
                    $jsonstr->value = $value;
                    $arrnode->listvalue = json_encode($jsonstr);
                    break;
                }
            }
            
        }
        return $arrnodes;
    }
 
 

 
    // Inserting the values of keys in $keysinsert into arrangements
    // of json arrays/objects/keys - $soughtkey
    // The  new keys will be inserted  near the key $soughtkey where found.
    // The keys will be inserted at the same depth of their neighbour key.
    // A callback will be invoked for each key in $keysinsert which will be
    // inserted.
    // The callback will insert the value of the associate key when returning true
    // nothing otherwise.
    // The callback will receive by argument current json node.
    // Callback function can be set to null to avoid the call.
    // Returns the number of inserted keys 
    public function JInsert($keysinsert,$soughtkey,$startnode=1)
    {  
        // setting the node where to start inserting
        $Node = $this->jqueryList->getNode($startnode);
        
        echo " starting at node " . $Node->nodeNum;
        $totnodeinserted = 0;
        
        while(($Node = $this->JSeekKeyUnique($soughtkey,$Node)) !== null)
        {
            // getting the depth of the found key
            $depth =  json_decode($Node->listvalue)->depth;
            $nodenuminsert = $Node->nodeNum;
    
            $n = $this->JInsertNewKeyDepth($keysinsert,$depth,$nodenuminsert);
            
            $Node = $this->jqueryList->getNode($nodenuminsert+sizeof($keysinsert,1)+1);
        }
    
        return $totnodeinserted; 
    }
 
 
 
 /* 
 
    // Inserting new keys from a json array in unique path node elements.
    // $startAtNode takes the number of node from where to start
    // the search. It can also take the actual node from where to start
    // the path search. If $startnode is null the search will start at the first
    // node of the json list.
    // The path end $key must be an array or an index  of it
    // Returns number of nodes inserted or null if the path has not been found or no indices
    // where to insert have been found
    // -------------------------------------------------------------------------------------------------------   To be modified
    // The array with inserting values can also be multidimensional so each dimension of the array 
    // will be inserted in  the corresponding index in the json array. If the value is null it will not
    // be inserted.
    public function JInsertPathArrayElement($key,$keyvalue,$startnode=null) 
    {
        $indices = $this->JSeekPathArray($key,$startnode);
        if($indices == null)
            return null;
        $arrnodes = $this->JSeekArrayElement($indices);
        if(count($arrnodes) === 0 || $arrnodes == null)
            return null;
    
        foreach($arrnodes as $arrnode)
        {    
            // inserting the node value with given key
            $jsonstr = json_decode($arrnode->listvalue);
            $depth = $jsonstr->$depth;
            $n=0;
            foreach($keyvalue as $key => $value)
            {
                $type = gettype($value);
            
                // Compose json string to be stored as node value
                $jstr = "{\"depth\":\"" . $depth . "\",\"key\":\"" . $key. "\",\"value\":\""
                . $value ."\",\"length\":\"" . sizeof($value,1) .  "\",\"type\":\""
                . $type . "\" }";
            
                // inserting new node
                $this->jqueryList->insertNodeAt($arrnode->nodeNum+$n++,$jstr);

            }
            
        }
        return $arrnodes;
     
    }

*/ 



    // Inserting blank nodes (void) to make space between keys.
    // Inserting at position $nodepath. $nodepath can receive an actual list node,
    // a dot notation path or a node number. If a  path is passed in $nodepath then a node number
    // from where to start the search of the path can be passed in $nodenum . The keys will be appended 
    // if $nodenum and $nodepath are both null. 
    // The number of keys to insert is passed in $totkey.
    // Returns the inserted keys in an array.
    public function JInsertKeyVoid($totkey=1,$nodepath=null,$nodenum=null) 
    {   
        $arrnode = array();
        
        $numdepth = $this->JGetFirstAvalaibleSpaceNode($nodepath,$nodenum);
        $nodenum = $numdepth[0];
        $depth = $numdepth[1];
    
        $key = '!void!';
        $value = 'none';
        $size = 1;
        $type = 'none';
      
        // Composing json string to be stored as node value
        $jstr = "{\"depth\":\"" . $depth . "\",\"key\":\"" . $key. "\",\"value\":\""
        . $value ."\",\"length\":\"" . $size .  "\",\"type\":\""
        . $type . "\" }";
        
        // inserting new void node
        for($x = 0; $x < $totkey; $x++)
        { 
            $arrnode[] = $this->jqueryList->insertNodeAt($nodenum++,$jstr);
        } 

    }
 
 
 
 
    // Inserting new keys at position $nodepath. $nodepath can receive an actual list node,
    // a dot notation path or a node number. If a  path is passed in $nodepath then a node number
    // from where to start the search of the path can be passed in $nodenum . The keys will be appended 
    // if $nodenum and $nodepath are null. 
    // Keys and values are passed in $keysinsert.
    // Returns the number of inserted keys.
    public function JInsertKey($keysinsert,$nodepath=null,$nodenum=null) 
    {   
        $numdepth = $this->JGetFirstAvalaibleSpaceNode($nodepath,$nodenum);
   
        $nodenum = $numdepth[0];
        $depth = $numdepth[1];
    
        $n=0;
        
        foreach($keysinsert as $key => $value)
        {  
            $type = gettype($value);
            
            // Composing json string to be stored as node value
            $jstr = "{\"depth\":\"" . $depth . "\",\"key\":\"" . $key. "\",\"value\":\""
            . $value ."\",\"length\":\"" . sizeof($value,1) .  "\",\"type\":\""
            . $type . "\" }";
            
            // Inserting new node
            // Checking if space is void
            $this->jqueryList->insertNodeAt($nodenum+$n++,$jstr);
        } 
        return $n;
    }
  
  
   
    // Inserting a new array ($key) containing keys and values ($keyinsert) in the json list tree.
    // Inserting will happen at position $nodepath. $nodepath can receive an actual list node,
    // a dot notation path or a node number.
    // If $nodenum and $nodepath are both  null or not passed to the method then the array keys will be
    // appended.
    // To insert the key as index in the right position set the $depth otherwise
    // it will be inserted  at the corresponding nearest neighbour depth.
    // The array containing keys and values can be multidimensional or multidimensional 
    // associative array with key.
    // Returns the number of inserted keys.
    public function JInsertKeyArr($key,$keyinsert,$depth=null,$nodepath=null,$nodenum=null) 
    {   
        $numdepth = $this->JGetFirstAvalaibleSpaceNode($nodepath,$nodenum);
   
        $nodenum = $numdepth[0];
        
        if($depth == null)
            $depth = $numdepth[1];


        // Composing json string to be stored as node value (the array key)
        $jstr = "{\"depth\":\"" . $depth++ . "\",\"key\":\"" . $key . "\",\"value\":\""
        . "Array" ."\",\"length\":\"" . sizeof($keyinsert,1) . "\",\"type\":\""
            . "array" . "\" }";
       
        // Inserting a array node in the list with depth key and field value as json string
        $this->jqueryList->insertNodeAt($nodenum,$jstr);
        
        $iterator = new RecursiveIteratorIterator( new RecursiveArrayIterator($keyinsert), RecursiveIteratorIterator::SELF_FIRST);       
        
        $n = 0; 
        foreach($iterator as $key => $value)
        {
            // getting current nesting depth
            $currdepth = $iterator->getDepth() + $depth;
          
            $type = gettype($value);
            
            // Composing json string to be stored as node value
            $jstr = "{\"depth\":\"" . $currdepth . "\",\"key\":\"" . $key . "\",\"value\":\""
            . $value ."\",\"length\":\"" . sizeof($value,1) . "\",\"type\":\""
            . $type . "\" }";
             
            // Inserting a node in the list with depth key and field value as json string
            $this->jqueryList->insertNodeAt(++$nodenum,$jstr);
             
            $n++;
        } 
        return $n;
    }
    
 
  

    // Inserting new array ($key) containing keys and values ($keyinsert) at a given path
    // in the json list tree $path (it should be passed as dot notation).
    // If $path is null or not passed to the method then the array keys will be
    // appended to the json tree.
    // The array containing keys and values $keyinsert can be multidimensional or multidimensional 
    // associative array with key.
    // Setting $asindexof the array $keyinsert will be inserted as an index of the
    // path as reported in $asindexof dot notation.
    // Returns the number of inserted keys.
    public function JInsertPathKeyArr($key,$keyinsert,$path=null,$asindexof=null) 
    {   
        if(($node =  end($this->JSeekPath($path))) != null) 
        {
            $depth = substr_count($asindexof,".");
            $nodenum = $node->nodeNum; 
        }
        else
        {
            $nodenum = $this->jsonStrNumNode()+1;
            $depth = 0;
        }

        // Composing json string to be stored as node value (the array key)
        $jstr = "{\"depth\":\"" . $depth++ . "\",\"key\":\"" . $key . "\",\"value\":\""
        . "Array" ."\",\"length\":\"" . sizeof($keyinsert,1) . "\",\"type\":\""
            . "array" . "\" }";
        
        // Inserting a node in the list with depth key and field value as json string
        $this->jqueryList->insertNodeAt($nodenum,$jstr);
        
        $iterator = new RecursiveIteratorIterator( new RecursiveArrayIterator($keyinsert), RecursiveIteratorIterator::SELF_FIRST);       
        
        $n = 0; 
        
        foreach($iterator as $key => $value)
        {
            // getting current nesting depth
            $currdepth = $iterator->getDepth() + $depth;
          
            $type = gettype($value);
            
            // Composing json string to be stored as node value
            $jstr = "{\"depth\":\"" . $currdepth . "\",\"key\":\"" . $key . "\",\"value\":\""
            . $value ."\",\"length\":\"" . sizeof($value,1) . "\",\"type\":\""
            . $type . "\" }";
             
            // Inserting a node in the list with depth key and field value as json string
            $this->jqueryList->insertNodeAt(++$nodenum,$jstr);
             
            $n++;
        } 
        return $n;
    }
    
 
 
    

    
    // Copying portion of the json tree from a given path $from to a destination path $to by inserting 
    // new nodes. Node numbers can also be passed in $from,$to,$insertpath or even actual list nodes.
    // If destination path $insertpath is null or not passed to the method then the copy will be
    // appended to the json tree. 
    // Setting $asindexof the copy will be inserted as an index of the path in $asindexof.
    // The $startnode can be set at the node number from where to start the path search or with 
    // an actual node. $mode can be set to "before" "after" to indicate precisely the insertion point
    // of a node before or after  $insertat.
    // Returns the number of inserted keys (nodes)
    // 
    public function JCopy($from,$to,$insertat,$mode="after",$asindexof=null,$startnode=null) 
    {   
        
        $fromnode = 0;
        $tonode =  0;
        $destnode = 0;
        
        if(($fromnode =  end($this->JSeekPath($from,$startnode))) != null)
    
            if(($tonode =  end($this->JSeekPath($to,$startnode))) != null)

                if(($destnode = end($this->JSeekPath($insertat))) != null)
    
                    $destnode =  $destnode->nodeNum;
            
                else
            
                    $destnode =  $this->jsonStrNumNode()+1;
    
    

        $node = $fromnode;
        $fromnode =  $fromnode->nodeNum; 
        $tonode =  $tonode->nodeNum;
        
        
        if($mode == "after" || $mode == "AFTER" )
            ++$destnode;
        else
            --$destnode;
                    
        $n=0;
        
        $startdepth = substr_count($asindexof,".");
    
        while($node->nodeNum < $tonode+1) 
        {  
            $jstr = json_decode($node->listvalue);
            
            $jstr->depth = (int ) $jstr->depth  + (int) $startdepth;
                 
            $listvalue = json_encode($jstr);
        
            $this->jqueryList->insertNodeAt($destnode++,$listvalue);
            $node = $node->nextNode;
            $n++;
        } 
    
        return $n; 
    }



    
    // Moving portion of the json tree from a given path $from to a destination path $to by moving 
    // existing nodes. Node numbers can also be passed in $from,$to,$insertpath or even actual list nodes.
    // If destination path $insertpath is null or not passed to the method then the nodes will be
    // appended to the json tree. 
    // Setting $asindexof the nodes will be moved as an index of the path in $asindexof.
    // The $startnode can be set at the node number from where to start the path search or with 
    // an actual node. $mode can be set to "before" "after" to indicate precisely the insertion point
    // of moving nodes before or after  $insertat.
    // Returns the number of moved keys (nodes)
    // 
    public function JMove($from,$to,$insertat,$mode="after",$asindexof=null,$startnode=null) 
    {   
        
        $fromnode = 0;
        $tonode =  0;
        $destnode = 0;
        
        if(($fromnode =  end($this->JSeekPath($from,$startnode))) != null)
    
            if(($tonode =  end($this->JSeekPath($to,$startnode))) != null)

                if(($destnode = end($this->JSeekPath($insertat))) != null)
    
                    $destnode =  $destnode->nodeNum;
            
                else
            
                    $destnode =  $this->jsonStrNumNode()+1;

        $node = $fromnode;
        $fromnode =  $fromnode->nodeNum; 
        $tonode =  $tonode->nodeNum;
    
        if($mode == "after" || $mode == "AFTER" )
            ++$destnode;
        else
            --$destnode;
                    
        $n=0;
        
        $startdepth = substr_count($asindexof,".");
    
        while($node->nodeNum < $tonode+1) 
        {  
           
            $jstr = json_decode($node->listvalue);
            
            $jstr->depth = (int ) $jstr->depth  + (int) $startdepth;
                 
            $listvalue = json_encode($jstr);
        
            $this->jqueryList->insertNodeAt($destnode++,$listvalue);
          
            // saving node to be deleted at the end of moving
            $arrNodes[] = $node;
           
            $node = $node->nextNode;
    
            $n++;
        } 
      
       
        // delete moved nodes
        foreach($arrNodes as $key => $node)
            $this->jqueryList->deleteNode($node->nodeNum);
        
        return $n; 
    }







    // Recalculating arrays/objects length in the json list reversing
    // the search for objects or arrays in the indices. The method starts from node
    // $startnode and recalculates lengths of arrays from $startnode upwards to the first
    // list node. $startnode can receive the node number where to start
    // counting or the actual node or an actual path in dot notation as "key.key1.key2.key3".
    // Note that it recalculates just the path/branch to which $startnode belongs upwards.
    public function JReCalcLength($startnode)
    { 

        $Node = $this->JGetNode($startnode);
        
        // $Node now contains the lower point before the counting.
        $startdepth = json_decode($Node->listvalue)->depth;
        
        echo " start at " . $Node->nodeNum .  " -- " . $startdepth . " ";
        
   
        // searching the botton in the pile
        while($Node->nextNode != null && json_decode($Node->nextNode->listvalue)->depth == $startdepth) 
            $Node = $Node->nextNode;
    
        // $Node now contains the lower point before the counting.
        $n = json_decode($Node->listvalue)->length;
    
        echo " repositioned at " . $Node->nodeNum .  " -- " . $startdepth . " ";
        
        // $Node now contains the lower point before the counting.
        $startnum =  $Node->nodeNum;
     
        echo $startnum . " " . $startdepth;
        
    
        $depth=1;
        while($Node != null)
        { 
            $jstr = json_decode($Node->listvalue);
            
            echo "node    " . $Node->nodeNum . "<br>";
        
            echo "value    " . $Node->listvalue . "<br>";
            
            echo "$jstr->type ---  $jstr->depth ---   $startdepth-$depth" . "<br>";
            
            if((string) $jstr->type == "array" && (int) ($startdepth-$depth) == (int )$jstr->depth && (int )$jstr->depth >= 1)
            {

                $jstr->size = $n;
            
                $Node->listvalue = json_encode($jstr);
                
                echo "value    " . $Node->listvalue . "<br>";
                
                ++$depth;
            }
            ++$n;
            $Node = $Node->prevNode;
        }
    
    }



    // Returns an HTML string to display a very basic json tree for current json.
    public function JsonTree($totnode=null,$startat=1)
    { 
        // Setting by default the number of nodes to display to the actual number of nodes
        // allocated by the json list.
        if($totnode == null)
            $totnode = $this->jqueryList->getTotListNode(); 
            
        $treeStr = "<br><br> Json tree  <br><br>";
        $treeStr .= '( <b>depth nnn </b> < key > < value > )  <br><br>';
        
        // Traversing the json structure allocated in the list
        // The anonymous function is called back by the iterator at each iteration
        // it receives the node iterated through by argument
        $this->jqueryList->iteratorList(function($node) use (&$treeStr,$totnode)
            {
                static $nodenum = 1;
                
                $jsonstr = json_decode($node->listvalue);
                
                $depth = $jsonstr->depth;
                $key = $jsonstr->key;
                $value = $jsonstr->value;
                $length =  $jsonstr->length;
                $type = $jsonstr->type;
        
        
                $treeStr .=  str_repeat("&nbsp;",$depth*6) . "<b>" . $depth . "</b>" . 
                " < k : " . $key  . " >" .  " < v : " . substr($value,0,15) . " >" . 
                " < l : " . $length  . " >"  . " < t : " . $type  . " > "   .  $nodenum . "<br><br>";
                
                if($nodenum++ >= $totnode)
                    return false;
                return true;
                
            },$startat);
            
        return $treeStr;
    }
    


    // Seeking unique path node in json.
    // $startAtNode takes the number of node from where to start
    // the search. It can also take the actual node from where to start
    // the search. If $startnode is null the search will start at the first
    // node of the json list. The path can be passed as don notation in a single string with keys separated 
    // by the character '.'. ( example "key.key1.key2.key3" ).
    // The path can also be passed as strings in an array $path = array("key","key1","key2","key3").
    // The method will seek the path node after node considering the actual length of arrays/objects.
    // Returns the actual nodes of the path (in order) end found in an array, null if the path has not
    // been found.
    public function JSeekPath($path,$startnode=null) 
    {
        $Node = $this->getJNode($startnode);
    
        $pathnodes = $this->ExplodeToArray($path,".");

 
        $nexnodenum = 0;
        $startdepth = json_decode($Node->listvalue)->depth;
        
        // array will contain path nodes that have been found
        $patharr = array();
        do
        {
                $keysought = (string) current($pathnodes);
        
                // move the node pointer 
                for($x= 0; $x<$nexnodenum; $x++)
                    $Node = $Node->nextNode;

                $jsonstr = json_decode($Node->listvalue);
            
                if((string) $jsonstr->key == $keysought && (int) $jsonstr->depth == key($pathnodes)+$startdepth)
                {
                   
                    // comparing the depth to make sure it's an array/object/element of current path
                    $nexnodenum = 1;
                    // saving the node for current path
                    $patharr[] = $Node;
                    next($pathnodes); 
                }
                else  
                    $nexnodenum = (int) $jsonstr->length;
        }while(current($pathnodes) !== false && $Node !== null);
       
    
        return (count($patharr) == count($pathnodes)) ? $patharr : array_slice($pathnodes,0,0);

    }
   
  
    
    
    // Returns a full path in dot notation given only the path end node.
    // $pathendnode should receive the actual path end node or the 
    // the json list node number
    public function JGetPath($pathendnode)
    {
        if(is_object($pathendnode))
            $Node = $pathendnode;
        else
            $Node = $this->getJNode($pathendnode);
            
        $depth = 0;
        if($Node !== null)
        {
            $nodeNum = $Node->nodeNum;
        
            $Node = $this->jqueryList->getfirstNode();
        
            while($Node->nodeNum <= $nodeNum && $Node !== null)
            { 
                $jstr = json_decode($Node->listvalue);
                if((string) $jstr->type == "array")
                {
            
                    // adding path key to current path if key exists it will not be inserted twice
                    $depth = $jstr->depth;
                    $path[$depth] = $jstr->key;
                    
                }
                $Node = $Node->nextNode;
            }
        
            $path = implode('.',array_slice($path,0,$depth+1));
            if((string) $jstr->type !== "array")
                $path .= "." . $jstr->key;
        }
        else
            $path = "";
        
        return $path;
    }




    // Returns the actual node requested through $pathnode as a number
    // as a string path or path array(). If the node is searched through a
    // path then the node number or the actual node from where to start the search
    // should be passed.    
    public function JGetNode($pathnode=null,$startnode=1)
    { 
        if(is_numeric($pathnode))
           $Node = $this->jqueryList->getNode($pathnode);
        
        elseif(is_string($pathnode) || is_array($pathnode))
           $Node = end($this->JSeekPath($pathnode,$startnode));

        elseif($pathnode == null && $startnode == null)
            $Node = null;
        else
           $Node = $pathnode;
        
        return $Node;
    }




    
    // Returns the actual json list node of the node number passed in 
    // $Node. $Node can also receive the actual json list node and in that
    // case it will be returned to the caller. If the node is null then the
    // first node of the json list will be returned to the caller.
    public function getJNode($Node=1)
    {
        if($Node == null)
            $Node = 1;
            
        if(is_numeric($Node))
            return $this->jqueryList->getNode($Node);
        else
            return $Node;
    }
    

    
    
    // Returns the parent or owner of node $Node
    // Returns null if the $Node does not belong
    // to an array/object/index
    public function getMyArray($Node=null)
    {  
        if(is_numeric($Node))
            $Node =  $this->jqueryList->getNode($Node);
       
        $depth = json_decode($Node->listvalue)->depth;
        
        while($Node !== null)
        {     
            if(json_decode($Node->listvalue)->depth == $depth-1)
                break;
                
            $Node = $Node->prevNode;
        }
        return $Node;
    }





    // Checking for the first available space for a new node insertion given path, 
    // or node number, or an actual node.
    // Returns in an array the insertion point node number and depth.
    public function JGetFirstAvalaibleSpaceNode($nodepath=null,$nodenum=null)
    {  
        $node = $this->JGetNode($nodepath,$nodenum);
    
        // Setting insertion depth and node number
        if ($node == null) 
        { 
            $nodenum = $this->jsonStrNumNode()+1;
            
            // appending or path not found
            if($this->jsonStrNumNode() > 0)
                $depth = json_decode($this->jqueryList->getlastNode()->listvalue)->depth;
            
            // first insertion
            else
                $depth=0;
        }
        else
        {
            $nodenum = $node->nodeNum;
            $depth = json_decode($node->listvalue)->depth;
        }
        
        return array($nodenum,$depth);
    }





    // Transforming a string into a single element array. If an array is passed in 
    // $stringorarray the array will be returned.
    public function StrToArray($stringtoarray)
    {
        if(is_string($stringtoarray))
            $arr[] = $stringtoarray;
        else
            $arr = $stringtoarray;
            
        return $arr;
    }
    
    
    
    
    
    // Transforming a string containing substrings separated by a $separator
    // into an array of strings. 
    // If an array is passed in $stringorarray the array will be returned.
    public function ExplodeToArray($stringtoarray,$delimiter)
    {
        if(is_null($stringtoarray))
            $arr[] = "";
            
        elseif(is_string($stringtoarray))
            $arr = explode($delimiter,$stringtoarray);
        else
            $arr = $stringtoarray;
            
        return $arr;
    }
    
    
    
        

    // Setting a multidimensional array $array with a value.
    // The key of the array where to insert the value are passed in the $keys
    // as a string "index0.index1.index2.index3" or as array("index0","index1","index2","index3")
    // The value to be inserted is passed in $value.
    public function SetMultiArrayToValue(array &$arr, $keys, $value)
    {
      //  echo "keys received " . $keys . "<br>";
        
        $ancestors = $this->ExplodeToArray($keys,'.');
        
      //  echo "keys transformed " . $keys . "<br>";
        $current = &$arr;
  
        foreach ($ancestors as $key)
        {

            // To handle the original input, if an item is not an array, 
            // replace it with an array with the value as the first item.
            if (!is_array($current)) 
            {
                $current = array( $current);
            }

            if (!array_key_exists($key, $current))
            {
                $current[$key] = array();
            }
            $current = &$current[$key];
        }
        $current = $value;
    }

    
    

    // Returning linked list allocated for current json string
    public function  jList ()
    {   
        return $this->jqueryList;
    }



    // Returning number of nodes composing current json string
    public function jsonStrNumNode() 
    {
        return $this->jqueryList->getTotListNode();
    }
    
    
    
    // Destructor
    public function __destruct()
    {

    }
    

    
}    
    



