<h1 class="h3 mb-3">My account</h1>
<form method="post" action="<?= e(url('admin/account')) ?>" class="card card-body" style="max-width:560px">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name" required value="<?= e($user['name']) ?>"></div>
    <div class="mb-3"><label class="form-label">Email (login)</label><input class="form-control" type="email" name="email" required value="<?= e($user['email']) ?>"></div>
    <hr>
    <h2 class="h6 text-secondary text-uppercase">Change password</h2>
    <div class="mb-3"><label class="form-label">Current password</label><input class="form-control" type="password" name="current_password" autocomplete="current-password"></div>
    <div class="mb-3"><label class="form-label">New password (min. 10 characters)</label><input class="form-control" type="password" name="new_password" autocomplete="new-password"></div>
    <div class="mb-3"><label class="form-label">Confirm new password</label><input class="form-control" type="password" name="confirm_password" autocomplete="new-password"></div>
    <div><button class="btn btn-primary">Save</button></div>
</form>
