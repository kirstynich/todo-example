<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php include '../private/app.php'; ?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <?php include 'templates/includes.php'; ?>
        <?php
        
        $args = array(
            "_id"=>FILTER_SANITIZE_STRING,
            "description"=>FILTER_SANITIZE_STRING
        );
        
        $post_vars = filter_input_array(INPUT_POST, $args, false);
        
        $action = filter_input(INPUT_POST, "action", FILTER_SANITIZE_STRING);
        
        $todo = new Todo($post_vars["_id"]);
        
        switch($action)
        {
            case "remove": $todo->delete(); break;
            case "add": $todo->createTodo($post_vars["description"]); break;
            case "edit": $todo->setDescription($post_vars["description"]); $todo->store(); break;
        }
        
        $todoCursor = new TodoCursor();
        
        ?>
    </head>
    <body>
        <?php include 'templates/header.php'; ?>
        <section class="container">
            <div class="row">
                <div class="col-xs-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                    <article class="panel panel-default">
                        <header class="panel-heading clearfix">
                            <h3 class="panel-title pull-left">Todo list</h3>
                            <button class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#modalAdd" data-action="add">
                                <i class="fa fa-plus"></i> Add
                            </button>
                        </header>
                        <section class="panel-body">
                                <ul class="list-group">
                                    <li class="list-group-item">Todo</li>
                                    <li class="list-group-item text-strike">Done</li>
                                    <li class="list-group-item list-group-item-danger">Overdue</li>
                                    <?php foreach($todoCursor as $id => $todoItem){ ?>
                                    <li class="list-group-item clearfix">
                                        <span class="todo-description">
                                            <?php echo $todoItem["description"]; ?>
                                        </span>
                                        <div class="btn-group btn-group-sm pull-right">
                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalAdd" data-action="edit"  data-id="<?php echo $id; ?>">
                                                <i class="fa fa-cog"></i>
                                            </button>
                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalRemove" data-id="<?php echo $id; ?>">
                                                <i class="fa fa-times text-danger"></i>
                                            </button>
                                        </div>
                                    </li>
                                    <?php } ?>
                                </ul>
                        </section>
                        <footer class="panel-footer">
                            
                        </footer>
                    </article>
                </div>
            </div>
        </section>
        
        <!--Modal to add item to the todo list-->
        <div id="modalAdd" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <input type="hidden"name="_id" value="">
                        <input type="hidden" name="action" value="add">
                        <header class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Add Todo</h4>
                        </header>
                        <section class="modal-body">
                            <div class="form">
                                <div class="form-group">
                                    <label for="todo-description" class="control-label">
                                        Description:
                                    </label>
                                    <input type="text" class="form-control" name="description">
                                </div>
                            </div>
                        </section>
                        <footer class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" >Save</button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>
        
        <!--Modal to confirm removal of item-->
        <div id="modalRemove" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="_id">
                        <header class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Remove Todo</h4>
                        </header>
                        <section class="modal-body">
                            <span class="modal-message"></span>
                        </section>
                        <footer class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                            <button type="submit" class="btn btn-danger">Yes</button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>
        <script src="js/app.js" type="text/javascript"></script>
    </body>
</html>
