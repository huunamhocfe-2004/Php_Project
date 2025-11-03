<?php
include('../server/connection.php');
$stmt = $conn->prepare('SELECT * FROM users');
$stmt->execute();
$users = $stmt->get_result();
?>

<?php include('../admin/layouts/app.php') ?>

<div class="content-wrapper">
    <!-- Tiêu đề trang -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Người dùng</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="create_user.php" class="btn btn-primary">Thêm người dùng mới</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Nội dung chính -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <form class="d-flex" action="../admin/search_user.php" method="GET">
                            <input class="form-control me-2" type="search" name="query_admin" 
                                   placeholder="Tìm kiếm người dùng" aria-label="Search" required>
                            <button class="btn btn-outline-dark" type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <?php if (isset($_GET['message'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $_GET['message'] ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $_GET['error'] ?>
                        </div>
                    <?php endif; ?>

                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th width="60">STT</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Mật khẩu</th>
                                <th width="100">Trạng thái</th>
                                <th width="100">Tùy chọn</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                            $stt = 1;
                            foreach ($users as $user) { ?>
                                <tr>
                                    <td><?php echo $stt++; ?></td>
                                    <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['user_password']); ?></td>
                                    <td>
                                        <svg class="text-success-500 h-6 w-6 text-success"
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="2" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </td>
                                    <td>
                                        <a href="edit_user.php?user_id=<?php echo $user['user_id'] ?>" 
                                           class="text-primary mr-2" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_user.php?user_id=<?php echo $user['user_id'] ?>" 
                                           class="text-danger" title="Xóa" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    <ul class="pagination pagination m-0 float-right">
                        <li class="page-item"><a class="page-link" href="#">«</a></li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">»</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php') ?>
