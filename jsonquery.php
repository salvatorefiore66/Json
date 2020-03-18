<?php 
include 'linkedlist.php';

// -----------------------------------------------------Class JsonQuery------------------------------------------------------------
// The main purpose: to be used for querying json.
// Very easy and straigthforward to use and extend.
// The constructor takes a json string as argument and will allocate a dynamic linked
// list containing the json structure.
// Once allocated by the constructor, the linked list will contain nodes with the analysed json objects arrays and values as 
// a numerical representation of the json tree.
// A json tree may have a main object or array with depth 0 from which a nested structure can originate. Each json object or array 
// subsequently increments the depth with nesting whilst elements are 1 level deeper than the array/object declaration.
// The list once populated with the analysed json, will eventually contain nodes with depth key and the actual values of the json
// as:
//
// { "depth":"nnn","key":"key","value":"fieldvalue","length":"num.items","next":"yes/no" }
//
// It is thus possible to seek and access json values specifying depth and key of the object/array or key from which originate. 
// The query methods can be subsequently used to select objects/arrays, key, values from the json list.
// JsonQuery class can handle mixed json structures of different formats at once.
// Querying can be quite fast and efficient although time to seek items can increase for high number of nodes. The class can easily
// manage hundred of thousands json structures at once.
// 
// For generating a json tree with the depth key and values : Class JsonQuery method JsonTree()
//


class JsonQuery
{

    protected $json;                               // current analised json
    protected $jsonNumNodes;                       // number of nodes composing the json string
    protected $jqueryList;                         // list containing analysed json string
 


    // constructor takes a json string as argument
    public function __construct($jstr)
    {
        $this->json = $jstr;
        
        // New linked list to contain json objects arrays and keys with values. 
        $this->jqueryList = new LinkedList(null,null);
      
        // Analysing the json string passed to the constructor populating the linked list
        // Throws an exception if there is an error during analysis.
        if($this->JAnalyse() === false)
        {
            throw new Exception("there is an error or no valid json in input");
        }
    
    }



    // Analysing a json string depth, nesting and actual key values.
    // A linked list containing records and objects decoded from a json string 
    // is populated  with  a numerical representation of the json stucture for
    // the whole string.
    private function JAnalyse()
    { 
        // Getting a json object from json string
        $jarray = json_decode($this->json,true);
        
        // Returns an error if json is not valid
        if(json_last_error()  !== JSON_ERROR_NONE)
            return false;
    
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($jarray), 
                        \RecursiveIteratorIterator::SELF_FIRST);
        
        foreach($iterator as $key => $value)
        {
            // getting current nesting depth
            $depth = $iterator->getDepth();

            $isvalid = "no";
                
            // Compose json string to be stored as node value 
            if(is_array($value))
                $isvalid = ($iterator->valid() == true) ?  "yes" : "no";
            
            $jstr = "{\"depth\":\"" . $depth . "\",\"key\":\"" . $key . "\",\"value\":\""
            . $value ."\",\"length\":\"" . count($value) .  "\",\"next\":\"" . $isvalid .  "\" }";
             
            
            // Insert a node in the list with depth key and field value as json string
            $this->jqueryList->insertNode($jstr);
        } 
        
        
        // Unsetting the recursive iterator
        unset($iterator);
        
        // Setting number of nodes for current json string
        $this->jsonNumNodes = $this->jqueryList->getTotListNode();
     
        return true;
    }
  
    
    // Seeking nodes in json with unique keys.
    // $startAtNode takes the number of node from where to start
    // the search.
    // Returns an array with the keys found and their values.
    public function JSeekKey($key,$startAtNode=1) 
    {
        $arrNode = array();

        // Seeking nodes with key
        $this->jqueryList->iteratorList(function($node) use ($key,&$arrNode)
            {
                if($node !== null)
                {
                    $jsonstr = json_decode($node->listvalue);
                    
                    if((string) $jsonstr->key == (string) $key)
                        $arrNode[] = $jsonstr;
            
                    return true;
                }
                else
                    return false;
                    
            },$startAtNode);

        return $arrNode;
    }
   
   
   
    // Seeking nodes in nested json with depth and keys.
    // $startAtNode takes the number of node from where to start
    // the search.
    // Returns an array with the keys found and their values.
    public function JSeekKeyDepth($key,$depth,$startAtNode=1) 
    {
        $arrNode = array();

        // Seeking nodes with depth and key
        $this->jqueryList->iteratorList(function($node) use ($depth,$key,&$arrNode)
            {
                if($node !== null)
                {
                    $jsonstr = json_decode($node->listvalue);
                
                    if((int) $jsonstr->depth == (int) $depth && (string) $jsonstr->key == (string) $key)
                        $arrNode[] = $jsonstr;
            
                    return true;
                }
                else
                    return false;
                    
            },$startAtNode);

        return $arrNode;
    }
    
    
    
    // Seeking an arrangement of json array/object with key.
    // $startAtNode takes the actual node from where to start
    // the search.
    // Returns an array with keys found and their values.
    public function JSeekKeyArr($key,$startAtNode=1) 
    { 
        $arrNode = array();
        
        // Seeking array nodes with key
        $this->jqueryList->iteratorList(function($node) use ($key,&$arrNode)
            {
                $jsonstr = json_decode($node->listvalue);
                $keydepth = $jsonstr->depth;
                
                // Array/object with sought key
                if((string) $jsonstr->key == (string) $key && (string) $jsonstr->value == "Array")
                {
                    $arrNode[] = $jsonstr;
                    
                    // Saving array nodes with key
                    $this->jqueryList->iteratorListAtNode(function($node) use ($key,$keydepth,&$arrNode)
                        {
                            $jsonstr = json_decode($node->listvalue);
                            
                            if((int) $jsonstr->depth > (int) $keydepth)
                            { 
                                $arrNode[] = $jsonstr;
                                return true;
                            }
                            else
                                return false;
                             
                        },$node->nextNode);
                }
                return true;
                
            },$startAtNode);
            
        return $arrNode;
    }
     
    
     
    // Seeking an arrangement of json array/object with key an depth.
    // $startAtNode takes the actual node number from where to start
    // the search.
    // Returns an array with keys found and their values.
    public function JSeekKeyDepthArr($key,$sdepth,$startAtNode=1) 
    { 
        $arrNode = array();
        
        // Iteratively seeking array nodes with key
        $this->jqueryList->iteratorList(function($node) use ($key,$sdepth,&$arrNode)
            {
                $jsonstr = json_decode($node->listvalue);
                $keydepth = $jsonstr->depth;
                
                // Array/object with sought key
                if((string) $jsonstr->key == (string) $key && (int) $jsonstr->depth == (int) $sdepth && (string) $jsonstr->value == "Array")
                {
                    $arrNode[] = $jsonstr;
                    // Iteratively saving array nodes with key
                    $this->jqueryList->iteratorListAtNode(function($node) use ($key,$keydepth,&$arrNode)
                        {
                            $jsonstr = json_decode($node->listvalue);
                            
                            if((int) $jsonstr->depth > (int) $keydepth)
                            { 
                                $arrNode[] = $jsonstr;
                                return true;
                            }
                            else
                                return false;
                             
                        },$node->nextNode);
                }
                return true;
                
            },$startAtNode);
            
        return $arrNode;
    }
    
    
    
    
    // Returns an HTML string to display the json tree for current json
    public function JsonTree($totnode=null,$startat=1)
    { 
        // Setting by default the number of nodes to display to the actual number of nodes
        // allocated by the json list.
        
        if($totnode === null)
            $totnode = $this->jsonNumNodes;
            
        $treeStr = "Json tree  <br><br>";
        $treeStr .= '( <b>depth nnn </b> < key > < value > )  <br><br>';
        
        // Traversing the json structure allocated in the list
        // The anonymous function is called back by the iterator at each iteration
        // it receives the node iterated through by argumen
        
        $this->jqueryList->iteratorList(function($node) use (&$treeStr,$totnode)
            {
                static $nodenum = 1;
                
                $jsonstr = json_decode($node->listvalue);
                
                $depth = $jsonstr->depth;
                $key = $jsonstr->key;
                $value = $jsonstr->value;
                $length =  $jsonstr->length;
                $next = $jsonstr->next;
                
                $treeStr .=  str_repeat("&nbsp;",$depth*8) . "<b>" . $depth . "</b>" . 
                " < key : " . $key  . " >" .  " < value : " . substr($value,0,15) . " >" . 
                " < length : " . $length  . " >"  . " < next : " . $next  . " >" . " <br><br>";
                
                if($nodenum++ >= $totnode)
                    return false;
                return true;
                
            },$startat);
            
        return $treeStr;
    }
    
    


    // Returning linked list allocated for current json string
    public function  jList ()
    {   
        return $this->jqueryList;
    }



    // Returning number of nodes composing current json string
    public function jsonStrNumNode() 
    {
        return $this->jsonNumNodes;
    }
    
    
    
    // Destructor
    public function __destruct()
    {
 
 

        
    }
    
    
    
}    
    



