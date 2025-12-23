<form method="POST" action="<?= url('/auth/login') ?>">
    <?= App\Config\Security::csrfField() ?>
    
    <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" required value="<?= old('email') ?>">
        <?php if (hasError('email')): ?>
            <div class="text-danger small"><?= error('email') ?></div>
        <?php endif; ?>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
        <?php if (hasError('password')): ?>
            <div class="text-danger small"><?= error('password') ?></div>
        <?php endif; ?>
    </div>
    
    <button type="submit" class="btn btn-primary btn-auth">Login</button>
    
    <div class="text-center mt-3">
        <small>Don't have an account? <a href="<?= url('/auth/register') ?>">Register here</a></small>
    </div>
</form>