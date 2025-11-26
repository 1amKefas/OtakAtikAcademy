# OtakAtik Academy - Features Recommendations & Implementation Plan

## 1️⃣ PAGE ACHIEVEMENTS

### Content Structure (inspired by Coursera):

```
ACHIEVEMENTS PAGE
├── Header
│   ├── Total Achievements Earned
│   ├── Progress Stats (courses completed, certificates, skills)
│   └── Share Achievements Button
│
├── SECTION: Certificates
│   ├── Filter (By Date, Course, Status)
│   └── Card Grid
│       ├── Course Name
│       ├── Completion Date
│       ├── Certificate Preview/Download Button
│       └── Share to LinkedIn
│
├── SECTION: Badges
│   ├── Earned Badges
│   │   ├── Badge Icon
│   │   ├── Badge Name
│   │   ├── Description
│   │   └── Earned Date
│   │
│   └── Available Badges (not yet earned)
│       ├── Greyed out icons
│       ├── Requirements
│       └── Progress indicator
│
├── SECTION: Skills Mastered
│   ├── Skill Tag Cloud
│   ├── Proficiency Level
│   └── Endorsed by (count)
│
└── SECTION: Milestones
    ├── First Course Completed
    ├── 5 Courses Milestone
    ├── Streak Counter (consecutive days learning)
    └── Total Learning Hours
```

### DB Tables Needed:
- `certificates` - store certificate data
- `badges` - badge definitions
- `user_badges` - link users to badges
- `skills` - skill definitions
- `user_skills` - proficiency tracking

---

## 2️⃣ PAGE HELP CENTER

### Content Structure (inspired by Coursera):

```
HELP CENTER PAGE
├── Header
│   ├── Search Bar (full-text search)
│   └── Popular Topics
│
├── SECTION: Browse by Category
│   ├── Getting Started
│   │   ├── "How to sign up"
│   │   ├── "Verify your email"
│   │   ├── "Set up your profile"
│   │   └── "First course enrollment"
│   │
│   ├── Learning & Courses
│   │   ├── "How to view course materials"
│   │   ├── "Download videos for offline"
│   │   ├── "Submit assignments"
│   │   ├── "Take quizzes"
│   │   ├── "Earn certificates"
│   │   └── "Course deadlines & extensions"
│   │
│   ├── Account & Profile
│   │   ├── "Manage account settings"
│   │   ├── "Change password"
│   │   ├── "Delete account"
│   │   ├── "Privacy settings"
│   │   └── "Update payment methods"
│   │
│   ├── Payment & Billing
│   │   ├── "Pricing & plans"
│   │   ├── "How to make payment"
│   │   ├── "Refund policy"
│   │   ├── "Invoice & receipts"
│   │   └── "Payment methods accepted"
│   │
│   ├── Technical Issues
│   │   ├── "Video not playing"
│   │   ├── "Slow connection"
│   │   ├── "Browser compatibility"
│   │   └── "Cache & cookies help"
│   │
│   ├── Certificates & Achievements
│   │   ├── "Share certificate"
│   │   ├── "Verify certificate authenticity"
│   │   ├── "Add to LinkedIn"
│   │   └── "Download certificate"
│   │
│   └── Contact Support
│       ├── "Email support"
│       ├── "Live chat"
│       ├── "Report technical issue"
│       └── "Request feature"
│
├── SECTION: Frequently Asked Questions (FAQ)
│   └── Most common 10-15 questions
│
├── SECTION: Contact Support
│   ├── Email Form
│   ├── Live Chat Widget
│   └── FAQ Status/Response Time
│
└── FOOTER: Feedback
    └── "Was this helpful?" buttons
```

### DB Tables Needed:
- `help_categories` - category definitions
- `help_articles` - article content + metadata
- `help_article_translations` - multi-language support
- `support_tickets` - user support requests
- `article_feedback` - track helpful/not helpful

---

## 3️⃣ MULTI-LANGUAGE SYSTEM

### Implementation:
1. **Laravel Localization** - use `resources/lang/{locale}/messages.php`
2. **UI Language Selector** - in Settings → Account & Security
3. **Database Locale Storage** - `users.locale` column
4. **Session/Cookie Persistence** - remember user's language choice
5. **Full App Translation** - EN → ID (or more languages later)

### Files to Create/Modify:
```
resources/
├── lang/
│   ├── en/
│   │   ├── messages.php
│   │   ├── auth.php
│   │   ├── settings.php
│   │   ├── achievements.php
│   │   └── help.php
│   │
│   └── id/
│       ├── messages.php
│       ├── auth.php
│       ├── settings.php
│       ├── achievements.php
│       └── help.php
```

### Implementation Steps:
1. Add `locale` column to users table (migration)
2. Create language files (EN + ID)
3. Add language selector in Settings UI
4. Update UserController to save locale preference
5. Set middleware to apply user's locale on every request
6. Wrap all UI text with `{{ __('key') }}`
7. Test full app in both languages

---

## PRIORITY ORDER:

1. **Multi-Language System** (foundation for everything else)
2. **Help Center Page** (easier to implement, high value)
3. **Achievements Page** (more complex, requires data structures)

---

## WHAT TO CODE NEXT:

### Phase 1: Multi-Language
- [ ] Migration: add `locale` column to users
- [ ] Create `resources/lang/{en,id}/*.php` files
- [ ] Add language selector in Settings
- [ ] Create middleware to set locale from user preference
- [ ] Wrap existing UI text with `__()` helper

### Phase 2: Help Center
- [ ] Migration: create help_categories, help_articles tables
- [ ] Create HelpController
- [ ] Create routes: `/help`, `/help/{slug}`, `/help/search`
- [ ] Create views: help_center.blade.php, article.blade.php
- [ ] Seed sample articles
- [ ] Add to navigation

### Phase 3: Achievements
- [ ] Migration: create certificates, badges, user_badges, skills, user_skills
- [ ] Create AchievementController
- [ ] Create logic to award badges/certificates on course completion
- [ ] Create views: achievements.blade.php
- [ ] Add to dashboard/navigation

---

**Ready to start coding? Start with Phase 1 (Multi-Language)?**
