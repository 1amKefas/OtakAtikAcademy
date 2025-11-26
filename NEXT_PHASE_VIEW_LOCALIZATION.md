# Next Phase: Localizing Views and Components

## Overview

The multi-language foundation is now complete. The next phase is to update all existing views to use the localization helpers (`__()` function) instead of hardcoded text.

## Priority Views to Update

### Phase 1: Authentication Views (High Priority)
These are the first pages users see and should support multiple languages immediately.

#### 1. Login Page
**File**: `resources/views/auth/login.blade.php`

Changes needed:
```blade
<!-- Before -->
<h1>Sign in</h1>
<p>Start your learning journey today</p>
<input placeholder="Email">
<input placeholder="Password">
<button>Sign In</button>
<a href="#">Forgot password?</a>
<p>Don't have an account? <a href="#">Sign up</a></p>

<!-- After -->
<h1>{{ __('auth.login_title') }}</h1>
<p>{{ __('auth.login_subtitle') }}</p>
<input placeholder="{{ __('auth.email_label') }}">
<input placeholder="{{ __('auth.password_label') }}">
<button>{{ __('auth.login') }}</button>
<a href="#">{{ __('auth.forgot_password') }}</a>
<p>{{ __('auth.no_account') }} <a href="#">{{ __('auth.sign_up_link') }}</a></p>
```

**Keys needed from `resources/lang/en/auth.php`:**
- `login_title` ✅
- `login_subtitle` ✅
- `email_label` ✅
- `password_label` ✅
- `login` ✅
- `forgot_password` ✅
- `no_account` ✅
- `sign_up_link` ✅

#### 2. Register Page
**File**: `resources/views/auth/register.blade.php`

Changes:
```blade
<h1>{{ __('auth.register_title') }}</h1>
<p>{{ __('auth.register_subtitle') }}</p>
<!-- etc. -->
<button>{{ __('auth.register') }}</button>
<p>{{ __('auth.already_have_account') }} <a href="#">{{ __('auth.sign_in_link') }}</a></p>
```

**Keys**: All already exist in `resources/lang/*/auth.php`

#### 3. Email Verification Page
**File**: `resources/views/auth/verify-email.blade.php` (if exists)

```blade
<h1>{{ __('auth.verify_email_title') }}</h1>
<p>{{ __('auth.verify_email_message') }}</p>
```

**Note**: Add these keys to `resources/lang/en/auth.php` and `resources/lang/id/auth.php`

### Phase 2: Navigation & Layout (High Priority)
These appear on every page.

#### 4. Navbar Component
**Files**: `resources/views/layouts/app.blade.php` or navbar partial

```blade
<a href="{{ route('courses') }}">{{ __('messages.courses') }}</a>
<a href="{{ route('profile') }}">{{ __('messages.profile') }}</a>
<a href="{{ route('settings') }}">{{ __('messages.settings') }}</a>
<a href="{{ route('achievements') }}">{{ __('messages.achievements') }}</a>
<a href="#" onclick="logout()">{{ __('messages.logout') }}</a>
```

**Keys**: All exist in `resources/lang/*/messages.php`

#### 5. Sidebar/Menu Component
Similar to navbar - replace hardcoded text with `__()` helpers

### Phase 3: Dashboard & Main Pages (Medium Priority)
These are seen frequently but after initial authentication.

#### 6. Dashboard Page
**File**: `resources/views/student/dashboard.blade.php`

```blade
<h1>{{ __('messages.welcome_back', ['name' => auth()->user()->name]) }}</h1>
<h2>{{ __('messages.my_courses') }}</h2>
<button>{{ __('messages.sign_up') }}</button>
```

#### 7. Courses Page
**File**: `resources/views/student/courses.blade.php`

```blade
<h1>{{ __('messages.courses') }}</h1>
<p>{{ __('messages.no_courses_yet') }}</p> <!-- Add this key -->
<button>{{ __('messages.browse_courses') }}</button> <!-- Add this key -->
```

#### 8. Profile Page
**File**: `resources/views/student/profile.blade.php`

Already has many hardcoded strings

### Phase 4: Forms & Validation (Medium Priority)
Error messages, placeholders, labels.

#### 9. Assignment Submission Form
Replace all labels and placeholders

#### 10. Quiz Form
Replace all labels and help text

#### 11. Forum Post Form
Replace all labels and placeholders

### Phase 5: Email Templates (Lower Priority)
Email content can be multi-language.

#### 12. Email Templates
**Files**: `resources/views/emails/*.blade.php`

```blade
<!-- Before -->
<p>Hello {{ $name }},</p>
<p>Please verify your email address.</p>

<!-- After -->
<p>{{ __('messages.hello') }} {{ $name }},</p>
<p>{{ __('emails.verify_email_message') }}</p>
```

**Add to language files**: `emails` section or use existing sections

---

## Implementation Strategy

### Step 1: Add Missing Keys
Several views need keys that aren't in language files yet. Add them to all language files:

**Add to `resources/lang/en/auth.php`:**
```php
'verify_email_title' => 'Verify Your Email',
'verify_email_message' => 'Please check your email for the verification link',
```

**Add to `resources/lang/id/auth.php`:**
```php
'verify_email_title' => 'Verifikasi Email Anda',
'verify_email_message' => 'Silakan cek email Anda untuk tautan verifikasi',
```

### Step 2: Update Views Systematically

Start with the most important views:
1. Login page (high visibility)
2. Register page (high visibility)
3. Navigation (appears everywhere)
4. Dashboard (frequently used)
5. Other pages

### Step 3: Test Each Page

For each page updated:
```bash
1. Register test user
2. Change language to Indonesian in Settings
3. Navigate to the updated page
4. Verify all text is in Indonesian
5. Check for any hardcoded strings
```

### Step 4: Translation Review

Once all views are updated:
1. Review all translation keys
2. Ensure consistency in tone and terminology
3. Have Indonesian speaker review translations
4. Update any awkward phrasing

---

## Quick View List

### Files to Update (Priority Order)

1. ⭐⭐⭐ `resources/views/auth/login.blade.php`
2. ⭐⭐⭐ `resources/views/auth/register.blade.php`
3. ⭐⭐⭐ `resources/views/layouts/app.blade.php` (navbar)
4. ⭐⭐ `resources/views/student/dashboard.blade.php`
5. ⭐⭐ `resources/views/student/courses.blade.php`
6. ⭐⭐ `resources/views/student/profile.blade.php`
7. ⭐ `resources/views/student/assignments.blade.php`
8. ⭐ `resources/views/quiz/show.blade.php`
9. ⭐ `resources/views/forum/index.blade.php`
10. ⭐ `resources/views/emails/*.blade.php`

---

## Command to Find Hardcoded Text

```bash
# Find common English words in views (rough search)
grep -r "Sign in\|Register\|Dashboard\|Courses" resources/views/

# Find potential unlocalized strings (contain spaces and capital letters)
grep -r "class=\".*\">[A-Z][a-z].*[^}]<" resources/views/ | head -20
```

---

## Translation Checklist Template

For each page updated, ensure:
- [ ] All user-visible text uses `__()` helper
- [ ] No hardcoded English strings remain
- [ ] Placeholders are localized
- [ ] Button text is localized
- [ ] Error/success messages are localized
- [ ] Help text is localized
- [ ] Page tested in both EN and ID
- [ ] Form validation messages use localization

---

## Language File Coverage

### Current Status
```
messages.php ✅
├── Navigation (8 items)
├── Common Actions (6 items)
├── Messages (8 items)
└── Total: 28 keys

auth.php ✅
├── Form labels (11 items)
├── Login messages (6 items)
├── Register messages (4 items)
├── OAuth messages (2 items)
└── Total: 23 keys

settings.php ✅
├── Sections (6 items)
├── Account & Security (12 items)
├── Language Settings (6 items)
├── Notifications (7 items)
├── Delete Account (4 items)
└── Total: 35 keys
```

### Planned Addition
```
emails.php (NEW)
├── Subject lines
├── Body text
├── Action links
└── Greetings

validation.php (NEW)
├── Field validation errors
├── Custom error messages

dashboard.php (NEW)
├── Dashboard-specific text
├── Widget titles

courses.php (NEW)
├── Course-related text

forum.php (NEW)
├── Forum-specific text
```

---

## How to Add New Language File

Example: Add a `validation.php` language file

**Step 1**: Create files
```bash
touch resources/lang/en/validation.php
touch resources/lang/id/validation.php
```

**Step 2**: Add content to `resources/lang/en/validation.php`
```php
<?php
return [
    'name.required' => 'Name is required',
    'email.required' => 'Email is required',
    'password.required' => 'Password is required',
];
```

**Step 3**: Translate to Indonesian in `resources/lang/id/validation.php`
```php
<?php
return [
    'name.required' => 'Nama harus diisi',
    'email.required' => 'Email harus diisi',
    'password.required' => 'Kata sandi harus diisi',
];
```

**Step 4**: Use in controller
```php
$request->validate([
    'name' => 'required',
    'email' => 'required',
], [], __('validation'));
```

---

## Success Criteria

Once this phase is complete:
- ✅ All user-facing text is localized
- ✅ Users can switch language and see changes immediately
- ✅ Preference persists across sessions
- ✅ New users can select language on registration
- ✅ Admin can see which languages are supported
- ✅ Adding new languages is straightforward

---

## Estimated Effort

- Login/Register pages: ~15 minutes
- Navigation: ~10 minutes
- Dashboard: ~20 minutes
- All other pages: ~1-2 hours
- Testing: ~30 minutes
- **Total: ~2.5-3 hours**

## Next Steps

1. Start with login page (most visible)
2. Then register page
3. Then navigation (impacts everything)
4. Test thoroughly in both languages
5. Deploy to Vercel
6. Have Indonesian speaker do final review
7. Iterate with feedback

---

**Status**: Foundation complete ✅ | Views pending update ⏳
