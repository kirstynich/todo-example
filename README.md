# todo-example
This is a small example of using PHP and Mongo to make a to do list.

Before progressing this example prseumes the following
* You have a MongoDB database and authorisation credentials to access it
* You have a web server with PHP and the MongoPHP driver installed
* You are using an IDE or text editor with Git functionality, or have a git client installed on your machine

We will provide the MongoDB credentials and can provide web space with FTP access if required.
If you don't have git built into an IDE or text editor the Github desktop client is easy to use if you are unfamiliar with git.

It is reccommended that you do a git commit after each step. This will allow you to roll back a step should you make mistakes, and will allow you to see how git is used for version control with a history of work done, and notes against work done which are useful when collaborating on code.

It uses PHP, Mongo, jQuery and Bootstrap and requires small amounts of work to get familiar with these technologies,
Tasks inlude:
* Writing PHP functions
* Using jQuery to find and manipulate html elements
* Using the PHP Mongo driver for updates/queries
* Using Modals with bootstrap
* Using PHP classes
* Basic CSS editing
* Using git repositories

## Fork this repository
If you are familiar with Github then you know what to do, fork it then clone to your machine to work on it.
If not then at the top right of this webpage you will see a button that says fork.
Click that and you will have your own copy of this source code to freely edit, from there
clone the repository to your local machine using your git client/IDE. The url is abailable 
near the top of the page it should be:
https://github.com/FNVI/todo-example.git

Your username and password will be the ones you used to sign up with github

## Add credentials
Now you have your own local copy of the code it's time to start editing.
First of all add your database credentials to get access to the database.
In app.php located in the private folder, edit the following

```php
define("DB_NAME", "your database");

define("DB_USER","your username");

define("DB_PASS","your password");
```

If you run the code to test the connection works, you should be able to add todo items using the add button on the page.

## Removing Items
### Modals
You can now add items to the list, but cannot remove them.
There are a couple of things to do, first go to the button with the red cross (located on line 111 of index.php located in the public folder).
This button will be used to trigger a modal.
Bootstrap has built in functionality for modals, all that is required to get a button to trigger a modal is to add 2 properties to an element. Add the 2 data properties used in the example below to the button in the code. The data-target value should contain the id of the modal to remove an item (line 164)
```html
<button data-toggle="modal" data-target="#modalid">
Click me!
</button>
```
Now when you click the red x on an item, the remove modal should appear)
### PHP post data
There is no functionality to the modal yet, this will need to be added.
The form in the modal will post data but this needs to be handled on the server side.

To keep this example simple, the post data will be sent to the same page, you will notice on index.php in the public folder around line 16 to 40 there is some php code. This is where the POST request is handled.

As tempting as it is to simply use $_POST and $_GET for request parameters, they don't allow for typing and validation and therfore do not protect against injection attacks. Although SQL Injection isn't a threat of ours (as we use Mongo), it is still recommended not to use them. Instead the filter_input and filter_input_array methods are used. (for more information click [here](http://php.net/manual/en/function.filter-input-array.php)) which allow for better handling. There are examples of both in the code.

Our remove item form sends 2 pieces of data which are the action and the _id of the todo in the collection.
The action parameter is already dealt with on line 25 so you just need to get the _id.
To do this just add another item to the array on line 16 where the comment is. The _id field is a string too so the result should be as follows
```php
$args = array(
    "_id"=>FILTER_SANITIZE_STRING,
    "description"=>FILTER_SANITIZE_STRING
);
```

The _id parameter will now be available using $post_vars["_id"].
A todo object is created on line 28. Pass the _id parameter into the constructor. The todo object will now load the relevant todo whenever $post_vars["_id"] has a valid _id.

Finally there is a switch on line 31 which handles what to do with the requests using the action variable, add a case for "remove" in there calling the delete function on the todo object like below.
```php
switch($action)
{
    case "remove": $todo->delete(); break;
    case "add": $todo->createTodo($post_vars["description"]); break;

```


Now this has been added, using your git client to commit your code with a brief message of what you have done, and push it to the remote repository

## Marking items as done
This section is in a few parts and repeats some the tasks from the last section.
You'll work with PHP classes, call some code from the Mongo PHP driver and more

You should ideally have a knowledge of using classes and ineritance, if not don't worry too much.
In this example we have a base class that everything else derives from called collection.
This is our handle to the database. 2 classes that are derived from collection are document and cursor.

The Document class represents a document in a collection and has the basic CRUD methods. Each type of document in our database will have its own class that is a child of the Document class. In this case that would be the Todo class.
The Todo class has methods that only relate to Todo documents, and as such when it calls the parent class it passes the Todo collection name as a parameter.

The cursor document will be explained later.

First of all open the Todo.php in the private folder.

A public method will need to be added to mark the todo as done. The syntax is virtually identical to adding methods to classes in other popular languages. Inside the function you will have to set the done property of the document to true.

*Hint - If you read some of the other methods in here you should see a very similar function*

Now go back to index.php. You will need to add another case into the switch statement like in the previous task.
The value to check for this time can be found in a hidden input on the panel body or by clicking the done button on an item and checking the request parameters. The code to be executed will need to call your method on the $todo object as well as the store method to save the change to the database.

**Please bear in mind you will have to call the store function in the case you added above to save the item**

Lastly it would great to have the user interface reflect that an item has been done.
Firstly go to public/css/style.css and add a css class for done items that puts a line through text.

Next there is a shorthand if else statement just inside the foreach loop on index.php that looks like this

```php
    $class = $todoItem["done"] ? "" : "";
```
Add the name of your class as the string to be used if $todoItem["done"] is true
([for reference](http://www.abeautifulsite.net/how-to-use-the-php-ternary-operator/))

Run the example and check that clicking the done button on an item put a line through any done items.
If it works commit the style.css, index.php and Todo.php files with a short message to say you added the "done" functionality.
If you have any issues don't be afraid to get in touch and ask.

## Editing todo items
In the index.php file find the btn-group that is used for each item. You will need to add a new button to edit items.
This button should be put right after the done button (inside the if statement).
The button should
* have a cog icon
* trigger the modal that adds todo items
* have data-id attribute set to the id of the item
* have the data-action attribute set to edit

Use font awesome for the [icon](http://fontawesome.io/)

*(hint just copy the button that removes items. Change the icon and target then add the data-action attribute)*

The button should now appear between the done and remove buttons and trigger the add item modal when clicked.

The add item modal will now be used serve as an add and edit item modal, as such it will need to display the current item when used for editing and when the save button is clicked it will need to tell the werver which item was being edited along with the new description and that it is editing the item, not adding it.
To do this you'll need to edit the jQuery event handler that opens the modal.

Open up public/js/app.js and go to the empty if statement in the middle of the of the second event handler.

The description and _id variables will need setting inside this statement. Use the first event handler for guidance

Once done, run the the example and check that the description is now in the text box on the modal, it also may be worth clicking the save button and checking the request parameters contain the right value for _id.

Now the last part is hooking that up on the server for when the message is sent.

Head back to index.php to the switch statement and add the case for "edit" and make it call suitable methods from the Todo and Document classes to set the description and store the item.
**Please bear in mind you will have to call the store function in the case you added above to save the item**

Test the code and then commit all files used an push to the remote repository.

If you have any issues and nothing from this example works, get in touch and we'll be able to help.

## Further
If you get bored you could try adding a button to remove all items, or possibly test mongo queries by passing a query into the TodoCursor constructor to only show certain items on the list.
The example below will only show items not done.
```php
$query = array("done" => false);
$todoCursor = new TodoCursor($query);
```
