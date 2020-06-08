<?php

// -------------------------------Class  LinkedList------------------------------------
// Salvatore G. Fiore copyright 2020 www.salvatorefiore.com
// The main purpose: to be used for allocating dynamically a linked list with (x)nodes.
//
// head----->node----->node----->node----->node----->node----->null
// head<-----node<-----node<-----node<-----node<-----node<-----null
//
// The node is interlinked in a sequential way to other nodes (next and previous)
// Each node is self contained and the list is able to manage 
// a value (generic type) for each node;
//
// The list can use callback user functions before each node insertion and soon after. 
// If callbeforenode returns false the node will not be inserted. 
// Methods for the management of the list include sorting searching 
// cutting unsetting inserting renumbering circular deleting.
// Can handle millions of  nodes depending on memory availability. 
// Unsetting a class instance will free the memory allocated for the nodes and all 
// contents will be lost. 
//
// Callback functions will be invoked as : 
// before callback (type Integer node_number_to_be_inserted)
// after  callback (type ListNode node)
      
      

class LinkedList
{
       protected $head;                   // head of the list
       protected $lastNode;               // last inserted node
       protected $callbeforenode;         // callback function before insertion
       protected $callafternode;          // callback function after insertion
       protected $totNode;                // total nodes in the list
    
     
       // Constructor with two arguments.
       // Callback user functions invoked before each node insertion and soon after.
       
       public function __construct($before=null,$after=null) 
       {
            $this->head = null;
            $this->lastNode = null;
            $this->totNode = 0;
            $this->callbeforenode = $before; 
            $this->callafternode = $after;
       }
    
    
       // Inserting a new node. Returns the node number
       // just inserted.
       public function insertNode($item) 
       { 
       	    // callback function
       	    if($this->callbeforenode !== null)
       	    {
                if(call_user_func($this->callbeforenode,$this->lastNode->nodeNum+1) === false)
                    return false;
       	    }
          
            
            $node = new ListNode($item);
            $node->nextNode = null;
            $node->prevNode = $this->lastNode;
            
            if($this->head !== null)
               $this->lastNode->nextNode = $node; 
            else
               // set the head
               $this->head = $node;
               
            $this->lastNode = $node; 
            $this->lastNode->nodeNum = ++$this->totNode;
            if($this->callafternode !== null)
       	    {
                call_user_func($this->callafternode,$this->lastNode);
       	    }
    
            return  $this->lastNode->nodeNum;
        }
        
        
        
        // Inserting a new node at a given position. Returns the inserted node.
        public function insertNodeAt($n,$item) 
        {
          
            if($n > $this->lastNode->nodeNum)
       	    {
       	        return $this->insertNode($item);
       	    }
       	    
            if($this->callbeforenode !== null)
       	    {
                if(call_user_func($this->callbeforenode,$n) === false)
                    return false;
       	    }
          
            $node = new ListNode($item);
       	    $currnode= $this->getNode($n);
       	        
       	    if($n === 1)
       	    {
       	        $prevNode = null;
       	        $this->head = $node;
       	    }
       	    else
       	    {
       	        $prevNode = $this->getNode($n - 1);
       	        $prevNode->nextNode = $node;
       	    
       	    }
       	        
       	    // update pointers to previous and next nodes
       	    $node->nextNode = $currnode;
       	    $node->prevNode = $prevNode;
       	    $currnode->prevNode = $node;
       	    
       	    
            if($this->callafternode !== null)
       	    {
                call_user_func($this->callafternode,$node);
       	    }
        
            $this->renumList();
            return  $node;
        }
        
       
        
        
        // Allocating n nodes in one go. Values to be inserted
        // are passed in the array $value
        public function allocateNode($n,$value)
        {
             for($x = 0; $x< $n; $x++)
             
                 // insert nodes containing values
                 $this->insertNode($value[$x]);
        }
  
  
  
  
  
  
        // Allocating n nodes in one go. Position of insertion and values
        // to be inserted are passed respectively in the array $n and $value
        public function allocateNodeAt($values)
        {
            foreach($values as $key => $value)
                // insert nodes containing values
                $this->insertNodeAt($key,$value);
        }
  
  
  
  
  
  
        // Iterator traversing the list. Callback user function is called at each
        // iteration receiving the current node passed as argument. 
        // Iteration may be offset starting at $startAtNode
        // For reverse order iterations $mode should be set to "-"
        // The iteration can end prematurely if the callable user function returns false;
        public function iteratorList($callback,$startAtNode=1,$mode="") 
        {         
        
            $currentNode = $this->getNode($startAtNode);
        
            while($currentNode !== null) 
            {
                if(call_user_func($callback,$currentNode) === false)
                    return false;
                if($mode !== "-")
                    $currentNode = $currentNode->nextNode; 
                else
                    $currentNode = $currentNode->prevNode; 
                
            }
            return true;
        }
  
  
  
    
        // Iterator traversing the list. Callback user function is called at each
        // iteration receiving the current node passed as argument. 
        // Iteration may be offset starting at $Node.
        // The iteration can end prematurely if the callable user function returns false;
        // The method cam receive the node number or the actual node from where to
        // start the iteration.
        public function iteratorListAtNode($callback,$Node=null,$mode="") 
        {         
        
            if($Node === null)
                $currentNode = $this->head;
            else if(is_numeric($Node))
                $currentNode = $this->getNode($Node);
            else
               $currentNode = $Node;
        
            while($currentNode !== null) 
            {
                if(call_user_func($callback,$currentNode) === false)
                    return false;
       	    
                if($mode !== "-")
                    $currentNode = $currentNode->nextNode; 
                else
                    $currentNode = $currentNode->prevNode; 
                
            }
            return true;
        }
  
  
  
  
        // Iterator traversing the list. The  callback user function
        // which is called at each iteration receiving the current node passed as argument.
        // The callback can however change the current list node position being iterated receiving
        // a reference to it.
        // Iteration may be offset starting at $Node.
        // The iteration can end prematurely if the callable user function returns false;
        public function iteratorNode($callback,$Node=null,$mode="") 
        {         
        
            if($Node === null)
                $currentNode = $this->head;
            else
               $currentNode = $Node;
        
            while($currentNode !== null) 
            {
        
                if($callback($currentNode) === false)
                    return false;
       	    
                if($mode !== "-")
                    $currentNode = $currentNode->nextNode; 
                else
                    $currentNode = $currentNode->prevNode; 
                
            }
            return true;
        }
  
  
      
        

        // Seeking the first node value passed as argument
        // Returns the number of node containing the value otherwise
        // 0 if the value has not been found
        public function findFirstLinear($item) 
        {         
            $currentNode = $this->head;
                       
            while($currentNode->listvalue != $item && $currentNode !== null) 
                    $currentNode = $currentNode->nextNode; 
            if($currentNode !== null)  
                   return $currentNode->nodeNum;
            else
                   return 0;
        }
    
    
    
    
    
        // Seeking the first node value passed as argument
        // Returns the node containing the value otherwise null if the value has not been found.
        // A callback function can be called for user check routine passing current node value. 
        // If callback returns true the search is stopped returning current node.
        // Returns null if the node value has not been found
        public function findFirstNodeLinear($item,$callback=null) 
        { 
            $currentNode = $this->head;
                       
            while($currentNode !== null)
            {
                if($callback !== null)
                {
                    if(call_user_func($callback,$currentNode) === true)
                        break;
                }
                else
                {
                    // direct comparison
                    if($currentNode->listvalue === $item)
                        break;
                    
                }
                $currentNode = $currentNode->nextNode; 
            }
            
            return $currentNode;
        }
     
     
     
        
        // Printing the whole list
        public function listList() 
        {
          
            $currentNode = $this->head;
            while($currentNode !== null)
            {
               // replace this line with your display class method
               echo $currentNode->listvalue . " -" . $currentNode->nodeNum . "-  " . " < " .$currentNode->prevNode->nodeNum . " > ";
                                                        
               $currentNode = $currentNode->nextNode;
            
            }
        }
 
 
    
        // Swapping the value of two nodes
        // the node numbers are passed by parameter
        public function swapNodes($nodeoneNum,$nodetwoNum)
        {
            
            $cNode= $this->getNode($nodeoneNum); 
            $nNode = $this->getNode($nodetwoNum);
      
            // Access the nodes and swap values
            $tempvar = $cNode->listvalue;
            $cNode->listvalue = $nNode->listvalue;
            $nNode->listvalue = $tempvar; 
            
        }
        
        
        
        
     
        // Renumbering the whole list >= 1 <= n
        public function renumList() 
        {
          
            $currentNode = $this->head;
            $numb = 0;
            
            while($currentNode !== null)
            {
                        
                $currentNode->nodeNum = ++$numb;
                                                        
                $currentNode = $currentNode->nextNode;
            
            }
            $this->totNode = $numb;
        }
     
    
    
    
    
    
         // Delete a node from the list. Node number is passed as argument.
        // Returns the total nodes in the list
        public function deleteNode($n) 
        {  
        
            $currentNode = $this->head;
            
            while($currentNode !== null && $currentNode->nodeNum < $n)
                $currentNode = $currentNode->nextNode;
                
            if($n === 1)
            {
                $this->head = $this->head->nextNode;
                $this->head->prevNode = null;
            }
            elseif($n === $this->totNode)
            { 
                $this->lastNode = $this->lastNode->prevNode;
                $this->lastNode->nextNode = null;
            }
            else
            {   
              $currentNode->prevNode->nextNode = $currentNode->nextNode;
              $currentNode->nextNode->prevNode = $currentNode->prevNode;
            }
    
            
            unset($currentNode);

        
            $this->totNode = $this->totNode-1;
            $this->renumList();
            return $this->totNode;
        }
 
    


    
        // Cutting the list at node n
        public function cutList($n) 
        {
            $oldnode = null;
            $currentNode = $this->head;
            
            while($currentNode !== null)
            {
                if($currentNode->nodeNum ===  $n)
                {
                    $oldnode = $currentNode->nextNode;
                    $currentNode->nextNode = null;
                    $this->lastNode = $currentNode;
                
                    $this->totNode = $n;
                    break;
                }
                
                $currentNode = $currentNode->nextNode;
            }
            $currentNode = $oldnode;
            while($currentNode !== null)
            {
                $currentNode = $currentNode->nextNode;
            
                unset($oldnode);
                
                $oldnode = $currentNode;
            }
        }
     
        
    
        // Descending order list sorting with bubble sort
        // It's a bit slow for high number of nodes > 80000
        public function ListSortDesc() 
        {
            static $len=0;
            $tempvar;
            $lenght = $this->lastNode->nodeNum;
            $cNode = $this->head;
            $nNode = $this->head->nextNode;
           
            for($n=0; $n < $lenght-1; $n++)
            {         
                if($cNode->listvalue <  $nNode->listvalue)
                {              
                    $tempvar = $nNode->listvalue;
                    $nNode->listvalue = $cNode->listvalue;
                    $cNode->listvalue = $tempvar;                           
                }
              
                $cNode = $cNode->nextNode;
                $nNode = $cNode->nextNode;
            }     
            while($len < $lenght)
            {
                $len++;
                $this->ListSortDesc();  
            }       
            $this->lastNode = $cNode;
        }
        
        
    
    
        // Ascending order list sorting with bubble sort
        // It's a bit slow for high number of nodes > 80000
        public function ListSortAsc() 
        {
            static $len=0;
            $tempvar;
            $lenght = $this->lastNode->nodeNum;
            $cNode = $this->head;
            $nNode = $this->head->nextNode;
           
            for($n=0; $n < $lenght-1; $n++)
            {         
                if($cNode->listvalue > $nNode->listvalue)
                {              
                    $tempvar = $cNode->listvalue;
                    $cNode->listvalue = $nNode->listvalue;
                    $nNode->listvalue = $tempvar;                           
                }
                                   
                $cNode = $cNode->nextNode;
                $nNode = $cNode->nextNode;
            }     
            while($len < $lenght)
            {
                $len++;
                $this->ListSortAsc();  
            }       
            $this->lastNode = $cNode;
        }
     
        
        
        
        // Transforms a linked list in a circular list
        public function makecircular()
        {
            $this->lastNode->nextNode = $this->head; 
        }
        
      
        
        
        // Returning the value of a node 
        // the node number is passed by parameter
        public function getValue($itemnumb)
        {
            $currentnode = $this->head;
             
            for($x=0; $x < $itemnumb && $currentnode !== null;  $x++)
            {
                $node = $currentnode;
                $currentnode = $currentnode->nextNode;
            }  
         
            return $node->listvalue;
        }
         
    
    
     
        // Returning a node with the node number
        // passed by parameter
        // Return null if the node does not exists
        public function getNode($itemnumb)
        {
            $node = null;
            $currentnode = $this->head;
            
            if($itemnumb <= $this->totNode)
            {
                for($x=0; $x < $itemnumb && $currentnode !== null;  $x++)
                {
                    $node = $currentnode;
                    $currentnode = $currentnode->nextNode;
                }
            }
            return $node;     
         
        }
        
        
        
        // Returning a node offset from $Node
        // The function can also receive the node number in $Node.
        // Returns the offset node. If the mode is "-" the offset is calculated
        // on the left of the $Node
        public function getNodeOffset($offset,$Node,$mode=null)
        {
            if(is_numeric($Node))
                $Node = $this->getNode($Node);
      
            $node = null;
            $currentnode = $Node; 
            
            for($x=0; $x < $offset+1 && $currentnode !== null;  $x++)
            {
            
                $node = $currentnode;
                
                if($mode !== "-")
                    $currentnode = $currentnode->nextNode; 
                else
                    $currentnode = $currentnode->prevNode; 
               
            }  
            return $node;     
        }
        
     
        
         // Renumbering and linking backwards the whole list >= 1 <= n
        public function linkBackList() 
        {
           
            $numb = 0;
            $prevnode = null;
            $currentNode = $this->head;
            
            while($currentNode !== null)
            {
                $currentNode->prevNode = $prevnode;
                $currentNode->nodeNum = ++$numb;
                $prevnode = $currentNode; 
                $currentNode = $currentNode->nextNode;
    
            }
            $this->totNode = $numb;
        }
     
        
        
        // Returning first node
        public function getfirstNode()
        {
            return $this->head;
        }
       
         
        // Returning last node
        public function getlastNode()
        {
            return $this->lastNode;
        }
        
        
        
        // Returning total number of nodes in the list
        public function getTotListNode()
        {
            return $this->totNode;
        }
        
       
        // Destructor
        public function __destruct()
        {  
            
        }
        
}



// Class node contains a value an index, a pointer 
// to next node a pointer to previous node
class ListNode
{
    public $listvalue;                   // the node item value
    public $nodeNum;                     // the node sequential index in the list
    public $nextNode;                    // pointer to next node in the list / last node pointing to null
    public $prevNode;                    // pointer to previous node in the list / first node pointing to null

    public function __construct($listitem) 
    {
        $this->listvalue = $listitem;
        $this->nextNode = null;
    }
} 



?>