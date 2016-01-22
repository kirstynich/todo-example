<?php

/*
 * THIS FILE DOES NOT NEED EDITING!
 * 
 * It may be useful to have a read of it if you're bored to see how a base class could handle a single connection to the database for the rest of the applications lifecycle
 */

/**
 * This a base class used to connect to the database
 * 
 * The abstract keyword means that the class cannot used on its own to creat an object
 *
 * @author Joe Wheatley <joew@fnvi.co.uk>
 */
abstract class Collection {

    /**
     * This is the handle to the database.
     * It shouldn't be accessed anywhere else except the function to call it.
     * This is static to prevent creating multiple connections to the database when multiple classes are used
     * @var MongoDB 
     */
    private static $db = null;
    
    /**
     * This is the collection used by the object
     * @var MongoCollection 
     */
    protected $collection;
    
    /**
     * An associative array to represent a document from the database.
     * 
     * This is created at the collection level as it will be used for both document objects and cursor objects
     * having it here allows both to use it.
     * 
     * @var array 
     */
    protected $document;
    
    /**
     * This constructor opens a connection to the database
     * 
     * The name of the collection should be passed in here as a parameter.
     * Any class that uses this as a base should call this function in its own constructor and set this value.
     * 
     * This is implemented so that each class only interacts with one collection.
     * This is inline with the way Mongo interacts with only one collection at a time
     * 
     * @param String $collection The name of the collection
     */
    public function __construct($collection) {
        $db = collection::getMongoConnection();
        $this->collection = $db->selectCollection($collection);
    }
    
    /**
     * Gets and sets the database connection
     * 
     * If a connection isn't open then this creates the connection, stores it and returns it.
     * On further calls it return the original connection
     * @return MongoDB
     */
    private static function getMongoConnection(){
        $m = new MongoClient(self::getMongoURI());
        if(self::$db === null){
            self::$db = $m->selectDB(DB_NAME);
        }
        return self::$db;
    }
    
    /**
     * Creates a MongoURI string from global constants
     * 
     * 
     * 
     * @return String
     */
    private static function getMongoURI(){
        $user = DB_USER;
        $pass = urlencode(DB_PASS);
        $server = DB_SERVER;
        $port = DB_PORT ? DB_PORT : 27017;
        $database = DB_NAME;

        return sprintf("mongodb://%s:%s@%s:%s/%s",$user,$pass,$server,$port,$database);
    }
}
