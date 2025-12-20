# 🏗️ Sistem Arsitektur - Fitur Pemberitahuan Zoom

```
┌─────────────────────────────────────────────────────────────────────┐
│                    INSTRUCTOR MANAGE COURSE PAGE                    │
│                    /instructor/courses/{id}/manage                  │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
                   ┌─────────────────────┐
                   │  Klik: "Tambah      │
                   │  Modul Baru"        │
                   └─────────┬───────────┘
                             │
                             ▼
        ┌────────────────────────────────────────────┐
        │  MODAL: Buat Modul/Pemberitahuan Baru    │
        │  ┌──────────────────────────────────────┐ │
        │  │ Pilih Tipe:                        │ │
        │  │ ○ Modul Pembelajaran               │ │
        │  │ ● Pemberitahuan Zoom               │ │
        │  └──────────────────────────────────────┘ │
        └────────┬──────────────────────────────────┘
                 │
         ┌───────┴───────┐
         │               │
         ▼               ▼
    ┌─────────┐     ┌──────────────────┐
    │ MODUL   │     │ PEMBERITAHUAN    │
    │ FORM    │     │ FORM             │
    │         │     │                  │
    │ Title   │     │ Title            │
    │ [____]  │     │ [______________] │
    │         │     │                  │
    │         │     │ Date             │
    │         │     │ [______________] │
    │         │     │                  │
    │         │     │ Time             │
    │         │     │ [______________] │
    │         │     │                  │
    │         │     │ Day (Auto-Gen)   │
    │         │     │ [Jumat]  ◀─ Auto │
    │         │     │                  │
    │         │     │ Description      │
    │         │     │ [______________] │
    └─────────┘     └──────────────────┘
         │                   │
         │ POST              │ POST
         │ /courses/{id}     │ /courses/{id}
         │ /modules          │ /modules
         │                   │
         ▼                   ▼
    ┌──────────┐        ┌──────────────────────┐
    │ Create   │        │ Create Announcement  │
    │ Module   │        │                      │
    │          │        │ 1. Get/Create Module │
    │ INSERT   │        │    "Pemberitahuan &  │
    │ course_  │        │    Event"            │
    │ modules  │        │                      │
    │          │        │ 2. INSERT course_    │
    │ ┌──────┐ │        │    announcements     │
    │ │DB    │ │        │                      │
    │ │✓ OK  │ │        │ 3. Auto-generate day│
    │ └──────┘ │        │    from date (mutator)
    └──────────┘        │                      │
                        │ ┌────────────────┐  │
                        │ │ DB INSERT ✓ OK │  │
                        │ └────────────────┘  │
                        └──────────┬───────────┘
                                   │
                                   ▼
                        ┌──────────────────────┐
                        │ LOAD Course w/       │
                        │ Modules &            │
                        │ Announcements        │
                        │                      │
                        │ SELECT courses.*     │
                        │ WITH modules         │
                        │ WITH announcements   │
                        └──────────┬───────────┘
                                   │
                                   ▼
                   ┌─────────────────────────────┐
                   │ RENDER MANAGE PAGE          │
                   │                             │
                   │ ┌───────────────────────┐   │
                   │ │ Modul 1               │   │
                   │ │ └─ Material 1         │   │
                   │ │ └─ Quiz 1             │   │
                   │ │ └─ Material 2         │   │
                   │ └───────────────────────┘   │
                   │                             │
                   │ ┌───────────────────────┐   │
                   │ │ 🔴 Pemberitahuan &    │   │◀─ SPECIAL
                   │ │    Event [ANNOUNCE]   │   │   STYLING
                   │ │ ├─ Zoom Session 1     │   │
                   │ │ │  Jumat, 19 Des 2025 │   │
                   │ │ │  14:30               │   │
                   │ │ │  [Edit] [Delete]     │   │
                   │ │ ├─ Zoom Session 2     │   │
                   │ │ │  Sabtu, 20 Des 2025 │   │
                   │ │ │  10:00               │   │
                   │ │ │  [Edit] [Delete]     │   │
                   │ │ └─ [+ Tambah...]      │   │
                   │ └───────────────────────┘   │
                   │                             │
                   │ ┌───────────────────────┐   │
                   │ │ Modul 2               │   │
                   │ │ └─ Material 1         │   │
                   │ └───────────────────────┘   │
                   └─────────────────────────────┘
```

---

## 🔄 Data Flow

### Step 1: Form Submission
```
User Input
    ↓
JavaScript (Alpine.js)
    ├─ module_category check
    ├─ Form validation
    └─ POST to /courses/{id}/modules
         ↓
      Laravel Route
         ↓
      InstructorController@storeModule()
```

### Step 2: Backend Processing
```
InstructorController@storeModule()
    ↓
Check module_category
    ├─ "announcement" branch:
    │   ├─ Validate announcement fields
    │   ├─ Get/Create Module "Pemberitahuan & Event"
    │   ├─ Auto-generate day via CourseAnnouncement mutator
    │   └─ INSERT to course_announcements
    │       ↓
    │    Mutator setAnnouncementDateAttribute()
    │       ├─ Set announcement_date = value
    │       └─ Set day_of_week = generateDayOfWeek(value)
    │
    └─ "module" branch:
        ├─ Validate module fields
        ├─ Get last order
        └─ INSERT to course_modules
             ↓
          Return back() with success message
```

### Step 3: Page Reload
```
Load Course with Relations
    ├─ Course::with(['modules' => [...]])
    ├─ modules.materials
    ├─ modules.quizzes
    └─ modules.announcements
         ↓
      Blade Template Rendering
         ├─ Check if module->title === "Pemberitahuan & Event"
         ├─ If YES: render announcements list (red styling)
         └─ If NO: render materials + quizzes list (blue styling)
              ↓
         HTML sent to browser
```

---

## 📊 Database Schema & Relations

```
                    ┌─────────────┐
                    │  courses    │
                    ├─────────────┤
                    │ id (PK)     │
                    │ title       │
                    │ instructor_ │
                    │ id (FK)     │
                    └──────┬──────┘
                           │ 1
                           │
                      (1:Many)
                           │
                      Many │
                           ▼
        ┌──────────────────────────────────┐
        │  course_modules                  │
        ├──────────────────────────────────┤
        │ id (PK)                          │
        │ course_id (FK) ──┐               │
        │ title            │  "Modul 1"   │
        │ order            │               │
        └──────────────────┼───────────────┘
                           │
                      (1:Many)
                           │
          ┌────────────────┼────────────────┐
          │                │                │
          │(1:Many)        │          (1:Many)
          │                │                │
    ┌─────▼──────┐   ┌─────▼──────┐   ┌────▼───────────┐
    │ course_    │   │  quizzes   │   │ course_        │
    │ materials  │   │            │   │ announcements  │
    │            │   │            │   │                │
    │ order      │   │sort_order  │   │ announcement_  │
    │ title      │   │ title      │   │ date (FK)      │
    │ content    │   │ duration   │   │ announcement_  │
    │ ...        │   │ ...        │   │ time           │
    └────────────┘   └────────────┘   │ day_of_week    │
                                        │ type: "zoom"   │
                                        │ description    │
                                        │                │
                                        └────────────────┘
                                        
    Module "Pemberitahuan & Event" = Container
    untuk semua CourseAnnouncement
```

---

## 🎯 Key Auto-Generation Logic

### Day of Week Generation

**Location**: `app/Models/CourseAnnouncement.php`

```php
// Mutator - Runs when announcement_date is set
public function setAnnouncementDateAttribute($value)
{
    $this->attributes['announcement_date'] = $value;
    $this->attributes['day_of_week'] = self::generateDayOfWeek($value);
}

// Helper method
public static function generateDayOfWeek($date)
{
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    
    $carbonDate = is_string($date) ? Carbon::parse($date) : $date;
    return $hari[$carbonDate->dayOfWeek];
}

// Example:
// Input: 2025-12-19
// Output: "Jumat" (because Dec 19, 2025 is Friday)
```

---

## 🎨 Styling Differences

### Regular Module
```
┌─────────────────────────────────────┐
│ 📚 Modul 1: Web Development         │
│ ├── 3 Item Konten                   │
│ ├── [Edit] [Delete]                 │
└─────────────────────────────────────┘
    Colors: Blue accent (bg-blue-100, text-blue-700)
    Icon: fas fa-book-open
```

### Announcement Module
```
┌──────────────────────────────────────────┐
│ 📢 Pemberitahuan & Event [ANNOUNCEMENT]  │
│ ├── 1 Pemberitahuan                       │
│ ├── [Delete]  (no Edit - for module)     │
└──────────────────────────────────────────┘
    Colors: Red accent (bg-red-100, text-red-700)
    Icon: fas fa-bell
    
Announcement Item:
┌──────────────────────────────────────────┐
│ 🎥 Zoom Session - AI & ML                │
│ Jumat, 19 Des 2025 • 14:30                │
│ Diskripsi singkat...                      │
│ [Edit] [Delete]                           │
└──────────────────────────────────────────┘
    Colors: Red border (border-red-200), bg-red-50
    Icon: fas fa-video
```

---

## ✅ Implementation Checklist

- [x] Database migration created
- [x] Model created with mutator for auto-generate
- [x] Controller method updated (storeModule)
- [x] New controller method added (deleteAnnouncement)
- [x] Routes added/updated
- [x] View modal updated with conditional rendering
- [x] JavaScript Alpine.js methods updated
- [x] Relationships added to models
- [x] Security/Authorization checks added
- [x] Documentation created

**Date Completed**: 19 Desember 2025  
**Status**: ✅ Ready for Testing
