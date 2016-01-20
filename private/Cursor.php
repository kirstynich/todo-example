<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used as a base class for any queries ran in the database.
 * The class will only speak to one collections which should be passed in to the
 * constructor from a class extending this one.
 * 
 * The main functionality of this class is to bring the MongoCursor to derived classes
 * which will represent groups of objects found by a query. Putting the code into this class 
 * saves writing it for every class that extends this functionality.
 * 
 * Also should any of this code need to be changed or extra functionality added to
 * the other classes, it will only need to be changed here
 *
 * @author Joe Wheatley <joew@fnvi.co.uk>
 */
class Cursor extends Collection implements Iterator{
    
    /**
     *
     * @var MongoCursor
     */
    protected $cursor;
    
    /**
     * These are some default options for any method called on the database.
     * These set the write concern which gives us a return value when operations have been done
     * and also set the operations to work on multiple documents as
     * any objects of this type will always be acting for multiple documents
     * at a time
     * 
     * @var array
     */
    protected $options = array(
        "w"=>1,
        "multiple"=>true
    );
    
    /**
     * This is the query used through any objects of this class
     * or classes derived from this class
     * @var array 
     */
    protected $qry;
    
    /**
     * The constructor just calls the parent constructor in this case
     * @param string $collection
     */
    public function __construct($collection) {
        parent::__construct($collection);
    }
    
    /**
     * Gets a subsets for the data currently represented in this cursor
     * @param array $qry
     * @return Cursor
     */
    public function subset($qry){
        $temp = clone $this;
        $temp->qry = array_merge($qry, $this->qry);
        return $temp;
    }
    
    /**
     * Runs the query in the database.
     * 
     * The query isn't called from the constructor function, but is called once this cursor
     * is being iterated through. This prevents unnecessary calls to the database as the 
     * object may be created but only a count or a subset is required, saving cpu time
     */
    public function runQry(){
        $this->cursor = $this->collection->find($this->qry);
    }
    
    /**
     * Used to update all documents matching the criteria set in the query
     * 
     * The function takes an object used to update the matched documents that can conatin update
     * operators outlined in the mongodb documentation.
     * 
     * @param array $object 
     * @return array
     */
    public function updateAll($object){
        $output = $this->collection->update($this->qry, $object, $this->options);
        $this->rewind();
        return $output;
    }
    
    /**
     * Used to remove all documents matching the criteria set in the query
     * 
     * @return array
     */
    public function removeAll(){
        $output = $this->collection->remove($this->qry, $this->options);
        $this->rewind();
        return $output;
    }
    
    /**
     * Returns distinct values from the current collection that match the current query
     * 
     * @param string $field This is the field that we want the values from
     * @return array An array of values found in the collection matching the query
     */
    public function values($field){
        return $this->collection->distinct($field, $this->qry);
    }
    
    /**
     * Returns how many documents were found in the database using the current query
     * @return int
     */
    public function count(){
        return $this->collection->count($this->qry);
    }
    
    /**
     * Required for Iterator Interface
     * Runs the query on the database and rewinds the cursor to
     */
    public function rewind() {
        $this->runQry();
        $this->cursor->rewind();
    }
    
    /**
     * Required for Iterator Interface
     * Moves the cursor to the next item
     */
    public function next() {
        $this->cursor->next();
    }
    
    /**
     * Required for Iterator Interface
     * 
     * Checks the current document the cursor points to is valid
     * @return boolean
     */
    public function valid() {
        return $this->cursor->valid();
    }
    
    /**
     * Required for Iterator Interface
     * This function must be implemented in extended classes to provide the type
     * of document as a class
     * @return array
     */
    public function current() {
        $this->document = $this->cursor->current();
        return $this->document;
    }
    
    /**
     * Required for Iterator Interface
     * 
     * Simulates the key if the array was to be iterated through as a key value array.
     * 
     * @return string
     */
    public function key() {
        return $this->document["_id"]."";
    }
}
