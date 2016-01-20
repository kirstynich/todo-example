<?php

/*
 * 
 */

/**
 * Represents a document in a collection
 * 
 * This class represents a document in a collection. This is an abstract class as
 * at this point there can't be an object of this type. Any classes that use this
 * class as a base will represent documents of certain types.
 * 
 * This class calls the collection in mongo directly and handles basic CRUD oprations
 * for all derived classes.
 * 
 * The class also implements the ArrayAccess functionality. This allows any objects created
 * using this class to be used like an array
 *
 * @author Joe Wheatley <joew@fnvi.co.uk>
 */
abstract class Document extends Collection implements ArrayAccess{
    
    
    /**
     * This is the query to get to the document inside the collection
     * @var array
     */
    protected $qry = null;
    
    /**
     * The constructor here doesn't really need to do much other than call the parent constructor
     * @param String $collection
     */
    public function __construct($collection) {
        parent::__construct($collection);
    }
    
    /**
     * Fetches a document from the collection
     * 
     * This function is a way of getting a document from the collection into the object
     * Since there will always be an _id field its a safe bet to load from there
     * however there will generally always be another unique field to search by
     * so this function allows classes that extend this class to specify which field
     * 
     * @param String|MongoId $var The value of the field to check
     * @param String $field
     * @return array
     */
    public function loadDocument($var, $field = "_id"){
        $query = null;
        if($field === "_id" && MongoId::isValid($var))
        {
            $query = array($field=>new MongoId($var));
        }
        else
        {
            $query = array($field=>$var);
        }
        $this->document = $this->collection->findOne($query);
        if($this->document !== null)
        {
            $this->qry = $query;
        }
        
        return $this->document;
    }
    
    /**
     * Stores the current document in the collection.
     * 
     * If the document does not exist then it is inserted.
     * If the document does then it overwrites the original document from the collection.
     * @return Boolean If successful returns true.
     */
    public function store(){
        if($this->document)
        {
            $result = $this->collection->save($this->document);
            return $result["ok"] ? true : false;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Deletes the current document from the collection
     * @return Boolean Returns true on success
     */
    public function delete(){
        if($this->document)
        {
            return $this->collection->remove(array("_id"=> new MongoId($this->document["_id"])));
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Required for ArrayAccess
     * Checks if a key exists in the document
     * 
     * @param String $offset
     * @return Boolean
     */
    public function offsetExists($offset) {
        return isset($this->document[$offset]);
    }
    
    /**
     * Required for ArrayAccess
     * Gets a value from the document if it exists
     * 
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->document[$offset] : null;
    }
    
    /**
     * Required for ArrayAccess
     * Removes item from the document
     * 
     * @param string $offset
     */
    public function offsetUnset($offset) {
        unset($this->document[$offset]);
    }
    
    /**
     * Required for ArrayAccess
     * 
     * @param type $offset
     * @param type $value
     */
    public function offsetSet($offset, $value) {
        if($offset)
        {
            $this->document[$offset] = $value;
        }
    }
}
