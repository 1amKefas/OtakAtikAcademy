{% raw %}
# Navbar Component Usage Guide

## Quick Start

To use the new navbar component in any Blade view:

```blade
@include('components.navbar')
```

That's it! No additional parameters needed.

---

## Component Location

**File:** `resources/views/components/navbar.blade.php`

---

## Features

### 1. **Logo Section**
- OtakAtik logo with orange gradient
- Links to dashboard

### 2. **Navigation Menu**
- About Us → `/dashboard`
- Our Course → `/course`
- My Courses → `/my-courses`
- History → `/purchase-history`

### 3. **Notification Bell**
- Icon with badge counter (red)
- Shows unread notification count
- Click to expand dropdown
- Sample notifications included
- "View All Notifications" link

### 4. **Profile Avatar**
- Circular badge with user's first initial
- Orange gradient background
- Click to expand dropdown

### 5. **Profile Dropdown**
Includes:
- User name and email at top
- **My Profile** → `/profile`
- **Purchase History** → `/purchase-history`
- **Settings** → `/settings`
- **Achievements** → `/achievements`
- **Help Center** → `/help`
- **Logout** button (red)

---

## How to Customize

### Change Avatar Colors
Edit line ~85 in `navbar.blade.php`:

```blade
<!-- Current: Orange -->
<div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full ...">

<!-- Change to: Blue -->
<div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full ...">
```

### Change Badge Color
Edit line ~70:

```blade
<!-- Current: Red -->
<span class="absolute -top-2 -right-2 bg-red-500 ...">

<!-- Change to: Green -->
<span class="absolute -top-2 -right-2 bg-green-500 ...">
```

### Modify Notification Count
Edit the badge display (if getting from database):

```blade
<!-- Current: Static variable -->
{{ $unreadNotifications ?? 0 }}

<!-- Use: Dynamic from database -->
{{ Auth::user()->unreadNotificationsCount() }}
```

### Add More Menu Items
Edit the profile dropdown section (around line ~110):

```blade
<a href="/my-route" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
    <i class="fas fa-icon-name w-4"></i>
    <span>Menu Item Name</span>
</a>
```

---

## JavaScript Explanation

The component includes inline JavaScript for interactivity:

```javascript
// Profile Button - Toggle dropdown
profileBtn.addEventListener('click', (e) => {
    e.stopPropagation(); // Prevent event bubbling
    profileDropdown.classList.toggle('hidden');
    notificationDropdown.classList.add('hidden'); // Close notification dropdown
});

// Notification Button - Toggle dropdown
notificationBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    notificationDropdown.classList.toggle('hidden');
    profileDropdown.classList.add('hidden'); // Close profile dropdown
});

// Close both dropdowns when clicking outside
document.addEventListener('click', () => {
    profileDropdown.classList.add('hidden');
    notificationDropdown.classList.add('hidden');
});
```

**Key Points:**
- `e.stopPropagation()` prevents click from bubbling to document
- Only one dropdown can be open at a time
- Clicking outside closes all dropdowns
- Uses `classList.toggle()` for smooth open/close

---

## Required Font Awesome Icons

Make sure Font Awesome is included in your base layout or view:

```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

**Icons Used in Navbar:**
- `fa-bell` - Notification bell
- `fa-user` - Profile
- `fa-history` - History
- `fa-cog` - Settings
- `fa-trophy` - Achievements
- `fa-question-circle` - Help
- `fa-sign-out-alt` - Logout
- `fa-book` - Course update (sample notification)
- `fa-check-circle` - Course complete (sample notification)
- `fa-clock` - Course expiring (sample notification)

---

## Bootstrap Your Views with Navbar

### Example View Structure

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title - OtakAtik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    
    <!-- NAVBAR COMPONENT -->
    @include('components.navbar')
    
    <!-- YOUR CONTENT HERE -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto">
            <!-- Page content -->
        </div>
    </section>
    
</body>
</html>
```

**Important:** Always add `pt-32` to your first section to account for navbar height (fixed position).

---

## Views Using This Navbar

Currently applied to:

1. ✅ `resources/views/course-detail.blade.php`
2. ✅ `resources/views/course.blade.php`
3. ✅ `resources/views/my-courses.blade.php`
4. ✅ `resources/views/purchase-history.blade.php`

### To Apply to More Views

Simply replace the navbar code with:
```blade
@include('components.navbar')
```

---

## Responsive Behavior

The component is responsive:
- **Desktop:** Full horizontal layout
- **Tablet:** Menu compresses, dropdowns adjusted
- **Mobile:** Menu items may stack (add mobile menu toggle if needed)

### To Add Mobile Menu Toggle

Add a hamburger button:

```blade
<!-- Add before the right section -->
<button class="md:hidden" id="mobileMenuBtn">
    <i class="fas fa-bars text-xl"></i>
</button>
```

Then add JavaScript to toggle the menu on mobile.

---

## Performance Notes

- Component uses vanilla JavaScript (no jQuery)
- Minimal CSS (Tailwind only)
- No external API calls
- Notifications are hardcoded (replace with dynamic data)
- Avatar is generated from user initial (no database lookup)

### Optimize for Production

1. **Cache the component:**
   ```blade
   @cache('navbar-component', 3600)
       @include('components.navbar')
   @endcache
   ```

2. **Move JavaScript to external file** if using multiple times:
   - Create `resources/js/navbar.js`
   - Move JavaScript code there
   - Include with `<script src="{{ asset('js/navbar.js') }}"></script>`

3. **Dynamically load notifications:**
   ```blade
   <div id="notificationDropdown" class="hidden ..." x-data="{ 
       notifications: {{ json_encode($user->notifications) }} 
   }">
   ```

---

## Testing the Component

1. **Login to the app:**
   ```
   admin@otakatik.com / 12345678
   ```

2. **Navigate to any updated page:**
   - `/course` - See navbar with course menu
   - `/my-courses` - See navbar
   - `/course/{id}` - See navbar

3. **Test interactions:**
   - Click avatar → Profile dropdown appears
   - Click notification bell → Notification dropdown appears
   - Click outside → Both dropdowns close
   - Try clicking avatar while notification is open → Notification closes

4. **Check responsive design:**
   - Resize browser to mobile width
   - Verify dropdowns still work
   - Check text truncation

---

## Troubleshooting

### Dropdowns not closing
- Check if Font Awesome is loaded
- Verify JavaScript is not blocked
- Open browser console for errors

### Avatar shows "U" for all users
- Check that `Auth::user()->name` is available
- Verify user is logged in
- Make sure name is not empty in database

### Icons not showing
- Confirm Font Awesome CDN link is correct
- Check icon names are spelled correctly
- Verify internet connection to CDN

### Styling looks wrong
- Make sure Tailwind CSS is compiled
- Check if `npm run dev` is running
- Clear browser cache
- Rebuild CSS: `npm run build`

---

## Future Enhancements

Potential improvements:

1. **Add notification persistence:**
   - Store in database
   - Use AJAX to fetch real notifications
   - Mark as read

2. **Profile dropdown customization:**
   - Add profile picture thumbnail
   - Show user role badge (Admin, Instructor, Student)
   - Add "My Courses" quick link

3. **Search functionality:**
   - Add search bar in navbar
   - Search courses in real-time
   - Search users (admin only)

4. **Dark mode toggle:**
   - Add sun/moon icon
   - Toggle dark mode CSS
   - Save preference

5. **Language selector:**
   - Add language dropdown
   - Support multiple languages
   - Save preference

---

## Quick Reference

| Item | Location | Tailwind Class |
|------|----------|----------------|
| Avatar size | Line 85 | `w-10 h-10` |
| Avatar color | Line 85 | `bg-gradient-to-br from-orange-500 to-orange-600` |
| Badge color | Line 70 | `bg-red-500` |
| Dropdown width | Line 74 | `w-80` |
| Icon color | Line 69 | `text-gray-600 hover:text-orange-500` |

---

## Support & Questions

For more information:
- See `NAVBAR_UPDATE.md` for implementation details
- Check `IMPLEMENTATION_STATUS.md` for routes that need to be created
- Review the component file directly: `resources/views/components/navbar.blade.php`

**Last Updated:** Nov 17, 2025
{% endraw %}
