# Email Verification URL Setup Guide

## Current Status:

- ✅ Email verification flow working
- ✅ Vercel deployment configured
- ❌ Custom domain (`otakatik.my.id`) not pointing to Vercel yet

## Quick Start - Test with Vercel URL:

Your current Vercel URL:
```
https://otakatik-academy-2umevfph2-kefashutabarats-projects.vercel.app
```

**For testing now:**
1. Set in Vercel environment variables:
   ```
   APP_ENV=production
   APP_URL=https://otakatik-academy-2umevfph2-kefashutabarats-projects.vercel.app
   VERCEL_URL=otakatik-academy-2umevfph2-kefashutabarats-projects.vercel.app
   ```

2. Register new user
3. Check email - verification link should be:
   ```
   https://otakatik-academy-2umevfph2-kefashutabarats-projects.vercel.app/email/verify/...
   ```

4. Click link - should work and redirect to dashboard

---

## Setup Custom Domain (`otakatik.my.id`)

### Step 1: Add Domain to Vercel

1. Go to Vercel Dashboard → Your Project → Settings → Domains
2. Click "Add Domain"
3. Enter: `otakatik.my.id`
4. Vercel akan show DNS records yang perlu ditambahkan

### Step 2: Update DNS Records

Setelah add domain di Vercel, Anda akan mendapat DNS records (misalnya):

```
Type    Name                        Value
CNAME   otakatik                    cname.vercel-dns.com
```

Go to your domain registrar (e.g., Niagahoster, Rumahweb, etc):
1. Find DNS settings
2. Add CNAME record sesuai Vercel instructions
3. Wait 5-30 minutes untuk propagation

### Step 3: Update Vercel Environment

In Vercel Project Settings → Environment Variables:

```
APP_ENV=production
APP_URL=https://otakatik.my.id
VERCEL_URL=otakatik-academy-2umevfph2-kefashutabarats-projects.vercel.app
```

### Step 4: Test

```
https://otakatik.my.id/email/verify/35/d0279191a648d0f81df063eb1150f868aafc95fb?expires=1764186946&signature=...
```

Harusnya berfungsi dan redirect ke dashboard.

---

## How Email Verification URL Generation Works Now:

```php
// Priority order untuk base URL:
1. If APP_URL is valid custom domain → Use APP_URL
2. If APP_URL adalah localhost/example → Use VERCEL_URL
3. Fallback → Use APP_URL

// Generate temporary signed route
// Replace base URL untuk consistency
// Send email dengan verification link
```

**Examples:**

**Scenario 1: Custom Domain Setup**
```
APP_URL=https://otakatik.my.id
VERCEL_URL=otakatik-academy-2umevfph2.vercel.app

→ Email link: https://otakatik.my.id/email/verify/...
```

**Scenario 2: Only Vercel URL (No custom domain yet)**
```
APP_URL=https://otakatik.my.id (tidak resolve)
VERCEL_URL=otakatik-academy-2umevfph2.vercel.app

→ Email link: https://otakatik-academy-2umevfph2.vercel.app/email/verify/...
```

**Scenario 3: Local Development**
```
APP_URL=http://localhost:8000
VERCEL_URL not set

→ Email link: http://localhost:8000/email/verify/...
```

---

## Troubleshooting:

### "This site can't be reached - DNS_PROBE_FINISHED_NXDOMAIN"
- Domain DNS not propagated yet
- Check via: `nslookup otakatik.my.id` (Windows) or `dig otakatik.my.id` (Mac/Linux)
- Wait 5-30 minutes, then try again

### Email link goes to Vercel domain instead of custom domain
- Check Vercel environment variables
- Ensure APP_URL is correctly set
- Clear cache: `vercel env pull` locally

### "Invalid signature" on verification link
- Domain mismatch between email link generation and verification
- Check that signed middleware is using correct domain
- Verify APP_URL consistency between email send and link click

### Still getting "otakatik.my.id not found"
- DNS not pointing to Vercel yet
- Contact your domain registrar to verify CNAME records
- Use Vercel URL for testing in the meantime

---

## Files That Handle URL Generation:

1. **app/Models/User.php** - `sendEmailVerificationNotification()`
   - Detects environment
   - Chooses correct base URL
   - Generates temporary signed route
   - Sends email

2. **routes/web.php** - `verification.verify` route
   - Validates signed URL
   - Marks email as verified
   - Redirects to dashboard

3. **.env.example** - Configuration
   - APP_URL: Your application domain
   - VERCEL_URL: Fallback for Vercel preview/staging

---

## Testing Locally:

```bash
# Start local dev server
php artisan serve

# Register at http://localhost:8000/register

# Check storage/logs/laravel.log for email content
# Or use Laravel pail: php artisan pail

# Verification link should be: http://localhost:8000/email/verify/...
```

---

## Next Steps:

1. **For now:** Use Vercel URL for testing
   - Set APP_URL to your Vercel URL temporarily
   - Test registration & email verification
   - Verify it works end-to-end

2. **Later:** Setup custom domain
   - Add otakatik.my.id to Vercel
   - Update DNS at registrar
   - Update APP_URL to custom domain
   - Email links will automatically use custom domain
