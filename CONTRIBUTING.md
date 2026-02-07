# 🤝 Contributing to Kyle-HMS

Thank you for considering contributing to Kyle-HMS! This document provides guidelines and instructions for contributing to the project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)
- [Testing Guidelines](#testing-guidelines)
- [Documentation](#documentation)

---

## Code of Conduct

### Our Pledge

We are committed to providing a welcoming and inspiring community for all. We expect all contributors to:

- Use welcoming and inclusive language
- Be respectful of differing viewpoints
- Accept constructive criticism gracefully
- Focus on what is best for the community
- Show empathy towards other community members

### Our Standards

**Positive behavior includes:**
- Being respectful and professional
- Providing constructive feedback
- Focusing on the issue, not the person
- Acknowledging and learning from mistakes

**Unacceptable behavior includes:**
- Harassment, discrimination, or trolling
- Personal attacks or insults
- Publishing private information
- Any conduct inappropriate in a professional setting

---

## How Can I Contribute?

### Reporting Bugs

Before creating a bug report:

1. **Check existing issues** to avoid duplicates
2. **Update to the latest version** to see if the issue persists
3. **Gather information** about the bug

**Bug Report Template:**

```markdown
**Bug Description**
A clear description of what the bug is.

**Steps to Reproduce**
1. Go to '...'
2. Click on '...'
3. See error

**Expected Behavior**
What you expected to happen.

**Actual Behavior**
What actually happened.

**Screenshots**
If applicable, add screenshots.

**Environment**
- OS: [e.g., Windows 10, Ubuntu 22.04]
- PHP Version: [e.g., 8.2.12]
- Browser: [e.g., Chrome 120]
- Laravel Version: [e.g., 12.0]

**Additional Context**
Any other relevant information.
```

### Suggesting Enhancements

**Enhancement Suggestion Template:**

```markdown
**Feature Description**
A clear description of the feature you'd like to see.

**Problem It Solves**
What problem does this feature address?

**Proposed Solution**
How would this feature work?

**Alternatives Considered**
What other solutions did you consider?

**Additional Context**
Mockups, examples, or references.
```

### Your First Code Contribution

Unsure where to begin? Look for issues labeled:

- `good first issue`: Simple issues perfect for newcomers
- `help wanted`: Issues that need community support
- `bug`: Bug fixes are always appreciated
- `enhancement`: New features or improvements

---

## Development Setup

### Prerequisites

- PHP >= 8.2
- Composer >= 2.8
- Node.js >= 18.x
- MySQL >= 8.0
- Git

### Fork and Clone

1. **Fork the repository** on GitHub
2. **Clone your fork** locally:

```bash
git clone https://github.com/YOUR_USERNAME/Kyle-HMS.git
cd Kyle-HMS
```

3. **Add upstream remote:**

```bash
git remote add upstream https://github.com/nounsunheng/Kyle-HMS.git
```

### Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env
# DB_DATABASE=kyle_hms_dev
# DB_USERNAME=root
# DB_PASSWORD=
```

### Setup Database

```bash
# Create database
mysql -u root -p
CREATE DATABASE kyle_hms_dev;
EXIT;

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed
```

### Start Development Server

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

Visit: http://localhost:8000

---

## Coding Standards

### PHP Standards (PSR-12)

Follow PSR-12 coding standard:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    // Constants in UPPER_SNAKE_CASE
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    // Properties
    protected $fillable = [
        'name',
        'status',
    ];

    // Methods in camelCase
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // Query scopes prefixed with 'scope'
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
```

### Laravel Best Practices

#### 1. Eloquent over Raw Queries

```php
// ✅ Good
$users = User::where('status', 'active')->get();

// ❌ Avoid
$users = DB::select('SELECT * FROM users WHERE status = ?', ['active']);
```

#### 2. Use Eager Loading

```php
// ✅ Good
$appointments = Appointment::with(['patient', 'doctor'])->get();

// ❌ Bad - N+1 queries
$appointments = Appointment::all();
foreach ($appointments as $appointment) {
    echo $appointment->patient->name;
}
```

#### 3. Use Form Requests

```php
// ✅ Good - Separate validation logic
public function store(StoreAppointmentRequest $request)
{
    Appointment::create($request->validated());
}

// ❌ Avoid - Validation in controller
public function store(Request $request)
{
    $request->validate([
        'patient_id' => 'required',
        // ... many more rules
    ]);
}
```

#### 4. Use Blade Components

```php
// ✅ Good
<x-primary-button>Submit</x-primary-button>

// ❌ Avoid
<button class="btn btn-primary">Submit</button>
```

### JavaScript/Blade Standards

```javascript
// Use const/let, not var
const patientName = 'John Doe';
let appointmentCount = 0;

// Use arrow functions
const getFullName = (first, last) => `${first} ${last}`;

// Use template literals
console.log(`Patient: ${patientName}`);
```

### CSS/Tailwind Standards

```html
<!-- ✅ Good - Utility classes -->
<div class="bg-blue-500 text-white p-4 rounded-lg">
    Content
</div>

<!-- ❌ Avoid - Inline styles -->
<div style="background-color: #3B82F6; color: white; padding: 1rem;">
    Content
</div>
```

### Code Formatting

Use Laravel Pint for automatic formatting:

```bash
# Format all files
./vendor/bin/pint

# Check without making changes
./vendor/bin/pint --test
```

---

## Commit Guidelines

### Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

#### Types

- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **style**: Code style changes (formatting, no logic change)
- **refactor**: Code refactoring
- **perf**: Performance improvements
- **test**: Adding or updating tests
- **chore**: Maintenance tasks (dependencies, build, etc.)

#### Scopes

- **appointments**: Appointment-related changes
- **auth**: Authentication/authorization
- **patients**: Patient-related changes
- **doctors**: Doctor-related changes
- **admin**: Admin-related changes
- **ui**: User interface changes
- **db**: Database changes

#### Examples

**Good commit messages:**

```
feat(appointments): Add appointment cancellation feature

- Allow patients to cancel appointments
- Notify doctors via email
- Update appointment status

Closes #45

---

fix(auth): Resolve login redirect issue

Users were redirected to wrong dashboard after login.
Now correctly redirects based on user role.

Fixes #67

---

docs(readme): Update installation instructions

Added Docker installation steps and improved clarity
in the database setup section.

---

refactor(models): Improve appointment query scopes

Simplified scope methods and added missing scopes
for better query readability.
```

**Bad commit messages:**

```
❌ updated stuff
❌ fix bug
❌ changes
❌ WIP
❌ asdfasdf
```

### Atomic Commits

Make small, focused commits:

```bash
# ✅ Good - Separate commits
git commit -m "feat(appointments): Add booking form validation"
git commit -m "feat(appointments): Add booking confirmation email"
git commit -m "test(appointments): Add booking validation tests"

# ❌ Bad - One large commit
git commit -m "Added appointment booking feature with validation, emails, and tests"
```

---

## Pull Request Process

### Before Submitting

1. **Update your fork:**

```bash
git fetch upstream
git checkout main
git merge upstream/main
git push origin main
```

2. **Create a feature branch:**

```bash
git checkout -b feature/your-feature-name
```

3. **Make your changes**

4. **Test your changes:**

```bash
# Run tests
php artisan test

# Check code style
./vendor/bin/pint --test

# Build assets
npm run build
```

5. **Commit your changes** following commit guidelines

6. **Push to your fork:**

```bash
git push origin feature/your-feature-name
```

### Creating Pull Request

1. **Go to GitHub** and create a Pull Request
2. **Fill in the PR template:**

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Related Issues
Closes #123

## Testing
- [ ] All tests pass
- [ ] Added new tests
- [ ] Manual testing completed

## Screenshots
If applicable, add screenshots

## Checklist
- [ ] Code follows project standards
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] No new warnings
```

3. **Wait for review**

### Pull Request Guidelines

**Do:**
- Keep PRs focused on a single feature/fix
- Write clear descriptions
- Update documentation
- Add tests for new features
- Respond to review comments
- Keep PRs up to date with main branch

**Don't:**
- Submit large PRs with multiple features
- Leave unresolved review comments
- Force push after reviews (unless requested)
- Ignore CI failures

### Review Process

1. **Automated checks** run (tests, linting)
2. **Code review** by maintainers
3. **Requested changes** if needed
4. **Approval** when ready
5. **Merge** into main branch

---

## Testing Guidelines

### Writing Tests

Every new feature should include tests:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_book_appointment()
    {
        // Arrange
        $patient = User::factory()->create();
        $patient->assignRole('patient');
        
        $schedule = Schedule::factory()->create([
            'schedule_date' => now()->addDays(1),
        ]);

        // Act
        $response = $this->actingAs($patient)
            ->post(route('patient.appointments.store'), [
                'schedule_id' => $schedule->id,
                'appointment_time' => '09:00',
                'reason' => 'Regular checkup',
            ]);

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->patient->id,
            'schedule_id' => $schedule->id,
        ]);
    }

    public function test_patient_cannot_book_past_date()
    {
        // Arrange
        $patient = User::factory()->create();
        $patient->assignRole('patient');
        
        $schedule = Schedule::factory()->create([
            'schedule_date' => now()->subDays(1), // Past date
        ]);

        // Act
        $response = $this->actingAs($patient)
            ->post(route('patient.appointments.store'), [
                'schedule_id' => $schedule->id,
                'appointment_time' => '09:00',
                'reason' => 'Regular checkup',
            ]);

        // Assert
        $response->assertStatus(422); // Validation error
        $this->assertDatabaseMissing('appointments', [
            'patient_id' => $patient->patient->id,
        ]);
    }
}
```

### Test Coverage

Aim for high test coverage:

```bash
# Run tests with coverage report
php artisan test --coverage

# Minimum coverage threshold
# - Overall: 80%
# - Critical features: 90%+
```

### Test Types

1. **Unit Tests** - Test individual methods
2. **Feature Tests** - Test complete features
3. **Browser Tests** - Test UI interactions (optional)

---

## Documentation

### Code Documentation

Use PHPDoc blocks:

```php
/**
 * Book a new appointment for a patient
 *
 * @param \App\Models\Schedule $schedule
 * @param array $data Appointment data
 * @return \App\Models\Appointment
 * @throws \Exception If schedule is full
 */
public function bookAppointment(Schedule $schedule, array $data): Appointment
{
    if ($schedule->is_full) {
        throw new \Exception('Schedule is full');
    }

    return Appointment::create($data);
}
```

### README Updates

Update README.md when:
- Adding new features
- Changing installation steps
- Modifying configuration
- Adding dependencies

### Inline Comments

```php
// ✅ Good - Explains WHY
// We need to check availability before booking
// because multiple users might book simultaneously
if ($schedule->available_slots > 0) {
    // ...
}

// ❌ Bad - Explains WHAT (code is self-explanatory)
// Check if available slots is greater than 0
if ($schedule->available_slots > 0) {
    // ...
}
```

---

## Release Process

### Version Numbering

We follow [Semantic Versioning](https://semver.org/):

- **MAJOR**: Breaking changes (v2.0.0)
- **MINOR**: New features (v1.1.0)
- **PATCH**: Bug fixes (v1.0.1)

### Release Checklist

- [ ] All tests pass
- [ ] Documentation updated
- [ ] CHANGELOG.md updated
- [ ] Version bumped
- [ ] Git tag created
- [ ] Release notes written

---

## Getting Help

### Communication Channels

- **GitHub Issues**: Bug reports, feature requests
- **Email**: nounsunheng290503@gmail.com
- **GitHub Discussions**: General questions, ideas

### Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [Tailwind CSS Documentation](https://tailwindcss.com)
- [Project README](README.md)
- [Technical Documentation](TECHNICAL_DOCUMENTATION.md)

---

## Recognition

Contributors will be recognized in:

- README.md (Contributors section)
- CHANGELOG.md (Release notes)
- GitHub Contributors page

---

## License

By contributing to Kyle-HMS, you agree that your contributions will be licensed under the MIT License.

---

## Thank You! 🎉

Your contributions make Kyle-HMS better for everyone. We appreciate your time and effort!

**Happy Coding!**

---

**Questions?**  
Contact: nounsunheng290503@gmail.com  
GitHub: [@nounsunheng](https://github.com/nounsunheng)
