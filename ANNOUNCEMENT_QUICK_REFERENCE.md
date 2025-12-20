# Quick Reference - Fitur Pemberitahuan Zoom

## 🚀 Mulai Testing

1. **Jalankan migration** (sudah dilakukan):
   ```bash
   php artisan migrate --step
   ```

2. **Buka halaman manage course**:
   ```
   http://127.0.0.1:8000/instructor/courses/1/manage
   ```

3. **Klik tombol "Tambah Modul Baru"** (di bagian atas halaman)

4. **Pilih radio button "Pemberitahuan Zoom"**

5. **Isi form**:
   - Judul: "Zoom Session - Topik ABC"
   - Tanggal: Pilih tanggal (ex: 19-12-2025)
   - Waktu: Pilih waktu (ex: 14:30)
   - Deskripsi: (opsional)

6. **Klik Simpan** → Announcement muncul di module "Pemberitahuan & Event"

---

## 📝 API Reference

### Create Announcement
**Route**: `POST /instructor/courses/{courseId}/modules`

**Payload**:
```json
{
  "module_category": "announcement",
  "announcement_title": "Zoom Session - Web Dev",
  "announcement_date": "2025-12-19",
  "announcement_time": "14:30",
  "announcement_description": "Diskusi tentang HTML, CSS, JavaScript"
}
```

**Response**: 
- Redirect ke manage page dengan success message
- Module "Pemberitahuan & Event" auto-create jika belum ada

### Delete Announcement
**Route**: `DELETE /announcements/{announcementId}`

**Response**: Redirect dengan success message

---

## 🔍 Code Locations

| Fitur | File | Method/Class |
|-------|------|--------------|
| Model | `app/Models/CourseAnnouncement.php` | `CourseAnnouncement` |
| Create | `app/Http/Controllers/InstructorController.php` | `storeModule()` |
| Delete | `app/Http/Controllers/InstructorController.php` | `deleteAnnouncement()` |
| View | `resources/views/instructor/courses/manage.blade.php` | Modal & Module List |
| JS | `public/js/instructor-manage.js` | Alpine.js data & methods |
| Routes | `routes/web.php` | Module routes + Announcement route |

---

## 🎯 Key Features

### Auto-Generate Hari
Saat user memilih tanggal, hari otomatis di-generate dalam bahasa Indonesia:
- Minggu, Senin, Selasa, Rabu, Kamis, Jumat, Sabtu

**Implementation**:
```javascript
// JavaScript (instructor-manage.js)
getDayOfWeek(dateString) {
    const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const date = new Date(dateString);
    return hari[date.getDay()];
}

// Update saat tanggal berubah
updateDayOfWeek() {
    this.announcementDayOfWeek = this.getDayOfWeek(this.announcementDate);
}
```

### Dynamic Module Container
- System otomatis membuat module "Pemberitahuan & Event" 
- Semua announcement disimpan dalam module ini
- Regular modules dan announcement modules tercampur di list tapi dibedakan dengan styling

### Conditional Form Rendering
Modal form berubah sesuai pilihan:
```html
<!-- Modul Pembelajaran -->
<div x-show="moduleCategory === 'module' || moduleEditMode">
    <input type="text" name="title" />
</div>

<!-- Pemberitahuan Zoom -->
<div x-show="moduleCategory === 'announcement' && !moduleEditMode">
    <input type="text" name="announcement_title" />
    <input type="date" name="announcement_date" />
    <input type="time" name="announcement_time" />
    <textarea name="announcement_description" />
</div>
```

---

## 🔒 Security Notes

1. **Authorization di Controller**:
   ```php
   public function deleteAnnouncement($id)
   {
       $announcement = CourseAnnouncement::findOrFail($id);
       if ($announcement->course->instructor_id !== auth()->id()) {
           abort(403);
       }
   }
   ```

2. **Route Protection**:
   - Semua routes berada dalam `Route::middleware(['auth'])->group()`
   - Hanya instructor bisa akses manage course

3. **CSRF Protection**:
   - Form menggunakan `@csrf` token
   - Route POST/DELETE dilindungi

---

## 📊 Database Query Examples

### Get All Announcements untuk Course
```php
$course = Course::with('announcements')->find($courseId);
// atau
$announcements = CourseAnnouncement::where('course_id', $courseId)->get();
```

### Get Announcements untuk Module
```php
$module = CourseModule::with('announcements')->find($moduleId);
$announcements = $module->announcements;
```

### Get Announcement dengan Auto-Generated Day
```php
$announcement = CourseAnnouncement::find($id);
echo $announcement->day_of_week; // "Jumat"
```

---

## 🐛 Troubleshooting

### Announcement tidak muncul di list
- [ ] Cek apakah module "Pemberitahuan & Event" sudah ada di database
- [ ] Cek apakah announcementnya ter-assign ke module yang tepat
- [ ] Refresh halaman / clear browser cache

### Hari tidak ter-generate
- [ ] Pastikan tanggal di-set dengan format YYYY-MM-DD
- [ ] Update harusnya otomatis saat tanggal berubah via `@change="updateDayOfWeek()"`
- [ ] Check browser console untuk error JavaScript

### Delete tidak bekerja
- [ ] Pastikan user adalah instructor yang memiliki course
- [ ] Check CSRF token ada di form
- [ ] Check console untuk network errors

---

## 📈 Future Enhancements

### Phase 2: Edit Announcement
```php
public function updateAnnouncement(Request $request, $id)
{
    // Similar ke updateModule
}
```

### Phase 3: Notify Students
```php
// Kirim notification ke students sebelum event
event(new AnnouncementCreated($announcement));
```

### Phase 4: Calendar Integration
- Tampilkan semua announcements dalam calendar widget
- Highlight tanggal dengan ada event

### Phase 5: Recurring Announcements
```sql
-- Add columns:
- repeat_type (none, daily, weekly, monthly)
- repeat_until (date)
```

---

## 📞 Support

**Dokumentasi lengkap**: `ANNOUNCEMENT_FEATURE_IMPLEMENTATION.md`

**Questions/Issues**:
1. Check di file markdown untuk detailed implementation
2. Review kode di controller/model/view
3. Test dengan browser DevTools F12

---

**Last Updated**: 19 Desember 2025
**Status**: ✅ Production Ready
