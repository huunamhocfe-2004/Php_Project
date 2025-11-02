<?php

include('../server/connection.php');

if (isset($_POST['create_category'])) {
    $category_name = $_POST['category_name'];

    $stmt = $conn->prepare('INSERT INTO category(category_name) VALUES(?)');
    $stmt->bind_param('s', $category_name);
    if ($stmt->execute()) {
        header('location:list_categorys.php?message=Category added successfully');
    } else {
        header('location:list_categorys.php?error=Error adding category');
    }
}

?>

<?php include('../admin/layouts/app.php') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_categorys.php" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <form action="create_categorys.php" method="POST">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="category_name" id="category_name" class="form-control"
                                        placeholder="Name">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" name="create_category">Create</button>
                    <a href="list_categorys.php" class="btn btn-primary">Cancel</a>
                </div>
            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
</div>
<?php include('../admin/layouts/sidebar.php') ?>