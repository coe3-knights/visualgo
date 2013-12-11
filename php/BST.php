<?php
  class BST{
    protected $root;
    protected $height;
    protected $elements;
    protected $isValidBst;

    public function __construct(){
      $this->init();
    }

    public function seedRng($seed){
      mt_srand($seed);
    }

    public function clearAll(){
      $this->init();
    }

    public function getAllElements(){
      return array_keys($this->elements);
    }

    public function getHeight(){
      return $this->height;
    }

    public function isValid(){
      return $this->isValidBst;
    }

    public function toGraphState(){
      $graphState = array("vl" => array(), "el" => array());

      foreach($this->elements as $key => $value){
        $vertexState = array(
          "cxPercentage" => $value->cxPercentage,
          "cyPercentage" => $value->cyPercentage,
          "text" => $value->value
          );
        $graphState["vl"] += array($key => $vertexState);
        if($this->root != $value->value){
          $edgeState = array(
            "vertexA" => $value->parent->value,
            "vertexB" => $value->value
            );
          $graphState["el"] += array($key => $edgeState);
        }
      }

      return $graphState;
    }

    public function insertRandomElements($amt){
      $insertionSequence = array();

      for($i = 0; $i < $amt; $i++){
        $newElement = mt_rand(1,99);
        if(!array_key_exists($newElement, $this->elements)){
          $this->insert($newElement);
          $insertionSequence[] = $newElement;
        }
        else $i--;
      }

      return $insertionSequence;
    }

    public function generateRandomBst($amt){
      $insertionSequence = array();

      for($i = 0; $i < $amt; $i++){
        $newElement = mt_rand(1,99);
        if(!array_key_exists($newElement, $this->elements)){
          $this->insert($newElement);
          $insertionSequence[] = $newElement;
        }
        else $i--;
      }

      return $insertionSequence;
    }

    public function generateRandomUniformBst($amt){

    }

    public function generateLinkedListBst($amt, $direction){
      $insertionSequence = array();
      $tempInsertionSequence = array();

      for($i = 0; $i < $amt; $i++){
        $newElement = mt_rand(1,99);
        if(!array_key_exists($newElement, $tempInsertionSequence)){
          $tempInsertionSequence[$newElement] = true;
        }
        else $i--;
      }

      $insertionSequence = array_keys($tempInsertionSequence);

      if($direction == BST_LINKED_LIST_ASCENDING) sort($insertionSequence);
      else rsort($insertionSequence);

      foreach($insertionSequence as $value){
        $this->insert($value);
      }

      return $insertionSequence;
    }

    public function getMinValue(){
      $temp = array_keys($this->elements);
      sort($temp);
      return $temp[0];
    }

    public function getMaxValue(){
      $temp = array_keys($this->elements);
      rsort($temp);
      return $temp[0];
    }

    public function successor($val){
      $successorSequence = array();

      $node = $this->elements[$val];
      if(is_null($node)) return $successorSequence;

      $successorSequence[] = $val;

      if(!is_null($node->rightChild)){
        $node = $node->rightChild;
        while(!is_null($node->leftChild)){
          $successorSequence[] = $node->value;
          $node = $node->leftChild;
        }
        $successorSequence[] = $node->value;
      }
      else{
        while(!is_null($node->parent)){
          $node = $node->parent;
          $successorSequence[] = $node->value;
          if($node->value > $val) break;
        }
      }

      return $successorSequence;
    }

    public function predecessor($val){
      $predecessorSequence = array();

      $node = $this->elements[$val];
      if(is_null($node)) return $predecessorSequence;

      $predecessorSequence[] = $val;

      if(!is_null($node->leftChild)){
        $node = $node->leftChild;
        while(!is_null($node->rightChild)){
          $predecessorSequence[] = $node->value;
          $node = $node->rightChild;
        }
        $predecessorSequence[] = $node->value;
      }
      else{
        while(!is_null($node->parent)){
          $node = $node->parent;
          $predecessorSequence[] = $node->value;
          if($node->value < $val) break;
        }
      }

      return $predecessorSequence;
    }

    public function search($val){
      $searchSequence = array();

      $node = $this->elements[$this->root];

      while(!is_null($node) && $node->value != $val){
        array_push($searchSequence,$node->value);

        if($val > $node->value){
          $node = $node->rightChild;
        }
        else $node = $node->leftChild;
      }

      if($node->value == $val) array_push($searchSequence,$val);

      return $searchSequence;
    }

    public function inorderTraversal(){
      $traversalSequence = array();
      $this->inorderTraversalRec($this->elements[$this->root], $traversalSequence);
      return $traversalSequence;
    }

    protected function inorderTraversalRec($node, &$traversalSequence){
      if(is_null($node)) return;
      $this->inorderTraversalRec($node->leftChild, $traversalSequence);
      $traversalSequence[] = $node->value;
      $this->inorderTraversalRec($node->rightChild, $traversalSequence);
    }

    public function preorderTraversal(){
      $traversalSequence = array();
      $this->preorderTraversalRec($this->elements[$this->root], $traversalSequence);
      return $traversalSequence;
    }

    protected function preorderTraversalRec($node, &$traversalSequence){
      if(is_null($node)) return;
      $traversalSequence[] = $node->value;
      $this->preorderTraversalRec($node->leftChild, $traversalSequence);
      $this->preorderTraversalRec($node->rightChild, $traversalSequence);
    }

    public function postorderTraversal(){
      $traversalSequence = array();
      $this->postorderTraversalRec($this->elements[$this->root], $traversalSequence);
      return $traversalSequence;
    }

    protected function postorderTraversalRec($node, &$traversalSequence){
      if(is_null($node)) return;
      $this->postorderTraversalRec($node->leftChild, $traversalSequence);
      $this->postorderTraversalRec($node->rightChild, $traversalSequence);
      $traversalSequence[] = $node->value;
    }

    public function insert($val){
      $newNode = new BSTNode($val);
      $cxPercent = 50;
      $cyPercent = 10;
      $xDifferencePercent = 50;

      $this->elements[$val] = $newNode;

      if(is_null($this->root)){
        $this->root = $val;
        $height = 1;
      }

      else{
        $parentNode = NULL;
        $node = $this->elements[$this->root];

        do{
          $xDifferencePercent/=2;
          $cyPercent += 10;
          $parentNode = $node;
          if($newNode->value > $parentNode->value){
            $node = $parentNode->rightChild;
            $cxPercent += $xDifferencePercent;
          }
          else{
            $node = $parentNode->leftChild;
            $cxPercent -= $xDifferencePercent;
          }
        }while(!is_null($node));

        if($newNode->value > $parentNode->value){
          $parentNode->rightChild = $newNode;
        }
        else $parentNode->leftChild = $newNode;

        $newNode->parent = $parentNode;
        $newNode->$height = 1 + $parentNode->height;
      }

      $newNode->cxPercentage = $cxPercent;
      $newNode->cyPercentage = $cyPercent;

      $this->updateHeightUp($newNode);
    }

    public function delete($val){
      $node = $this->elements[$val];
      $noLeftChild = is_null($node->leftChild);
      $noRightChild = is_null($node->rightChild);
      $isRoot = is_null($node->parent);

      if($noLeftChild && $noRightChild){
        if($isRoot) $this->clearAll();
        else{
          $parentNode = $node->parent;
          if($node->val > $parentNode->val){
            $parentNode->rightChild = NULL;
          }
          else $parentNode->leftChild = NULL;

          $this->updateHeightUp($parentNode);
        }
      }

      else if($noLeftChild){
        $rightChildNode = $node->rightChild;
        $parentNode = $node->parent;
        $rightChildNode->parent = $parentNode;
        if($node->val > $parentNode->val){
            $parentNode->rightChild = $rightChildNode;
        }
        else $parentNode->leftChild = $rightChildNode;

        $this->updateHeightUp($parentNode);
      }

      else if($noRightChild){
        $leftChildNode = $node->leftChild;
        $parentNode = $node->parent;
        $leftChildNode->parent = $parentNode;
        if($node->val > $parentNode->val){
            $parentNode->rightChild = $leftChildNode;
        }
        else $parentNode->leftChild = $leftChildNode;

        $this->updateHeightUp($parentNode);
      }

      else{
        $successorSequence = $this->successor();
        $successorNode = $this->elements($successorSequence[count($successorSequence) - 1]);
        $successorRightChildNode = $successorNode->rightChild;
        $successorParentNode = $successorNode->parent;
        $parentNode = $node->parent;
        $rightChildNode = $node->rightChild;
        $leftChildNode = $node->leftChild;

        $successorRightChildNode->parent = $successorParentNode;
        if($successorNode->val > $successorParentNode->val){
            $successorParentNode->rightChild = $successorRightChildNode;
        }
        else $successorParentNode->leftChild = $successorRightChildNode;

        $successorNode->parent = $parentNode;
        $successorNode->leftChild = $leftChildNode;
        $successorNode->rightChild = $rightChildNode;
        $successorNode->height = $node->height;

        $leftChildNode->parent = $successorNode;
        $rightChildNode->parent = $successorNode;
        if($successorNode->val > $parentNode->val){
            $parentNode->rightChild = $successorNode;
        }
        else $parentNode->leftChild = $successorNode;

        $this->updateHeightUp($successorParentNode);
      }

      unset($this->elements[$val]);
    }

    public function swap($val1, $val2){
      $allKeys = $this->getAllElements();
      if(!(in_array($val1, $allKeys) && in_array($val2, $allKeys))) return $this->isValidBst;

      $node1 = $this->elements[$val1];
      $heightNode1 = $node1->height;
      $parentNode1 = $node1->parent;
      $leftChildNode1 = $node1->leftChild;
      $rightChildNode1 = $node1->rightChild;
      $cxPercentageNode1 = $node1->cxPercentage;
      $cyPercentageNode1 = $node1->cyPercentage;

      $node2 = $this->elements[$val2];
      $heightNode2 = $node2->height;
      $parentNode2 = $node1->parent;
      $leftChildNode2 = $node1->leftChild;
      $rightChildNode2 = $node1->rightChild;
      $cxPercentageNode2 = $node2->cxPercentage;
      $cyPercentageNode2 = $node2->cyPercentage;

      $node1->parent = $parentNode2;
      $node1->leftChild = $leftChildNode2;
      $node1->rightChild = $rightChildNode2;
      $node1->height = $heightNode2;
      $node1->cxPercentage = $cxPercentageNode2;
      $node1->cyPercentage = $cyPercentageNode2;

      $leftChildNode2->parent = $node1;
      $rightChildNode2->parent = $node1;
      if($node2->value > $parentNode2->value){
        $parentNode2->rightChild = $node1;
      }
      else $parentNode2->leftChild = $node1;

      $node2->parent = $parentNode1;
      $node2->leftChild = $leftChildNode1;
      $node2->rightChild = $rightChildNode1;
      $node2->height = $heightNode1;
      $node2->cxPercentage = $cxPercentageNode1;
      $node2->cyPercentage = $cyPercentageNode1;

      $leftChildNode1->parent = $node2;
      $rightChildNode1->parent = $node2;
      if($node1->value > $parentNode1->value){
        $parentNode1->rightChild = $node2;
      }
      else $parentNode1->leftChild = $node2;

      $this->isValidBst = false;

      return $this->isValidBst;
    }

    protected function init(){
      $this->root = NULL;
      $this->height = 0;
      $this->elements = array();
      $this->isValidBst = true;
    }

    // Recursively updates height of itself and the nodes above it until root
    protected function updateHeightUp($node){
      $noLeftChild = is_null($node->leftChild);
      $noRightChild = is_null($node->rightChild);
      $isRoot = is_null($node->parent);

      if($noLeftChild && $noRightChild){
        $node->height = 0;
      }
      else if($noLeftChild) $node->height = $node->rightChild->height + 1;
      else if($noRightChild) $node->height = $node->leftChild->height + 1;
      else $node->height = max($node->rightChild->height, $node->leftChild->height) + 1;

      if(!$isRoot){
        $this->updateHeightUp($node->parent);
      }
      else $this->height = $node->height;
    }

    // Recursively updates height of itself and all the nodes below it
    protected function updateHeightDown($node){
      $noLeftChild = is_null($node->leftChild);
      $noRightChild = is_null($node->rightChild);
      $isRoot = is_null($node->parent);

      $leftChildHeight = -1;
      $rightChildHeight = -1;

      if(!$noRightChild){
        $this->updateHeightDown($node->rightChild);
        $rightChildHeight = $node->rightChild->height;
      }
      if(!$noLeftChild){
        $this->updateHeightDown($node->leftChild);
        $leftChildHeight = $node->leftChild->height;
      }

      $node->height = max($leftChildHeight,$rightChildHeight) + 1;
      if($isRoot) $this->height = $node->height;
    }
  }

  class BSTNode{
    protected $value;
    protected $height;
    protected $parent;
    protected $leftChild;
    protected $rightChild;
    protected $cxPercentage;
    protected $cyPercentage;

    function __construct($val){
      $this->value = $val;
      $this->height = 1;
      $this->parent = NULL;
      $this->leftChild = NULL;
      $this->rightChild = NULL;
    }

    public function __get($property) {
      if (property_exists($this, $property)) {
        return $this->$property;
      }
    }

    public function __set($property, $value) {
      if (property_exists($this, $property)) {
        $this->$property = $value;
      }

      return $this;
    }
  }
?>