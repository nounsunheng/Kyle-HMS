<form method="POST" action="<?= url('/auth/register') ?>">
    <?= App\Config\Security::csrfField() ?>
    
    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="pname" class="form-control" required value="<?= old('pname') ?>">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="pemail" class="form-control" required value="<?= old('pemail') ?>">
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="pdob" class="form-control" required value="<?= old('pdob') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Gender</label>
            <select name="pgender" class="form-control" required>
                <option value="">Select...</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="tel" name="ptel" class="form-control" required value="<?= old('ptel') ?>">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Address</label>
        <textarea name="paddress" class="form-control" rows="2" required><?= old('paddress') ?></textarea>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required minlength="8">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    
    <button type="submit" class="btn btn-primary btn-auth">Register</button>
    
    <div class="text-center mt-3">
        <small>Already have an account? <a href="<?= url('/auth/login') ?>">Login here</a></small>
    </div>
</form>