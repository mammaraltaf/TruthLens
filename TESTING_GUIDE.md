# TruthLens — Layman Testing Guide

A plain-English walkthrough for testing every feature of the TruthLens application from scratch.

---

## 1. Prerequisites — Get the App Running

### Step 1 — Start Laragon
Open Laragon and click **Start All**. Make sure Apache and MySQL are both green.

### Step 2 — Open a Terminal in the Project Folder
```
D:\laragon\www\TruthLens
```

### Step 3 — Run Migrations & Seed the Database
This creates all tables and loads sample data (users, articles, badges):
```bash
php artisan migrate:fresh --seed
```

> **What this creates:**
> - Admin account
> - Demo user account
> - 8 sample articles with different credibility scores
> - All badge types (Trusted, Suspicious, Fake, Unverified)

### Step 4 — Open the App in Your Browser
```
http://localhost/TruthLens/public
```
Or if Laragon is configured with a vhost, simply:
```
http://truthlens.test
```

---

## 2. Seeded Login Accounts

| Role  | Email                    | Password   | Can Do                                      |
|-------|--------------------------|------------|---------------------------------------------|
| Admin | admin@truthlens.local    | `password` | Submit articles, vote, manage profile       |
| User  | user@truthlens.local     | `password` | Submit articles, vote, manage profile       |

> No email verification is required — you can log in immediately after registering too.

---

## 3. Feature-by-Feature Testing Checklist

---

### A. Browse Articles (No Login Required)

1. Go to the homepage `/` or click **Articles** in the nav
2. You should see **8 pre-loaded articles** with coloured badges:

| Badge       | Colour | Score Range | What it Means                     |
|-------------|--------|-------------|-----------------------------------|
| Trusted     | Green  | 70 – 100    | Mostly accurate claims            |
| Suspicious  | Yellow | 40 – 69     | Mixed or misleading claims        |
| Fake        | Red    | 0 – 39      | Predominantly false claims        |
| Unverified  | Grey   | No score    | No matching fact-checks found     |

3. Click any article card to open its detail page
4. On the detail page you can see:
   - The full article text
   - Credibility score (0–100)
   - Badge label
   - Claim reviews from fact-checkers (publisher name, rating, link)
   - Community vote counts (Real vs Fake)

---

### B. Log In as Admin

1. Click **Log in** (top-right)
2. Enter:
   - Email: `admin@truthlens.local`
   - Password: `password`
3. You are redirected to the **Dashboard**

---

### C. Dashboard

After login, the Dashboard shows:
- **Total submissions** you have made
- **Completed** (fact-checked) count
- **In Progress** count
- A table of your own submitted articles with their badge and verdict

---

### D. Submit an Article — URL Mode

1. Click **Submit Article** (or go to `/articles/create`)
2. Select **URL** as submission type
3. Fill in:
   - **Title:** `Scientists warn about social media misinformation`
   - **URL:** `https://www.bbc.com/news/technology`  _(any real URL works)_
   - **Category:** choose any (e.g. Technology)
4. Click **Submit**
5. You are redirected to the article detail page with the message:
   > _"Your submission is being analyzed. Refresh this page in a few seconds."_
6. **Refresh the page** — since the queue is in SYNC mode, processing is instant
7. The article now shows a credibility score and badge

> **What happens behind the scenes:**
> - The app fetches the page content from the URL
> - It sends the title + content to Google Fact Check API
> - The API returns matching claim reviews from fact-checkers worldwide
> - Each review's rating ("True", "False", "Misleading" etc.) is converted to a score
> - The average score determines the badge

---

### E. Submit an Article — Text Mode

1. Go to `/articles/create`
2. Select **Text** as submission type
3. Fill in:
   - **Title:** `The moon landing was faked by NASA`
   - **Content:** Paste any claim text, e.g.:
     ```
     Multiple sources claim the 1969 Apollo moon landing was staged in a Hollywood studio.
     Footage inconsistencies and shadow angles are cited as evidence.
     ```
   - **Category:** Science
4. Submit and refresh — you'll get a score based on fact-checker databases

---

### F. Vote on an Article (Real or Fake)

1. Open any article detail page
2. Scroll to the **Community Vote** section
3. Click **Real** or **Fake**
4. The vote count updates immediately
5. You can change your vote by clicking the other button
6. Logged-out visitors see the vote counts but cannot vote

---

### G. Register a New Account

1. Log out (click your name → **Log Out**)
2. Click **Register**
3. Fill in:
   - **Name:** any name
   - **Email:** any email (e.g. `test@example.com`)
   - **Password:** at least 8 characters
   - **Confirm Password:** same as above
4. Click **Register** — you are logged in immediately
   _(No verification email required)_
5. You are taken to the Dashboard (empty — no submissions yet)

---

### H. Edit Your Profile

1. Click your name in the top-right → **Profile**
2. You can update:
   - Display name
   - Email address
3. Click **Save** — changes apply immediately
4. To **delete your account**: scroll to the danger zone, enter your password, confirm

---

### I. Duplicate Article Detection

TruthLens prevents re-analysing the same content twice:

1. Submit an article with a URL you already submitted
2. The job detects the same content fingerprint (SHA-256 hash)
3. The new submission instantly inherits the score and badge from the original — no new API call is made

---

## 4. How Credibility Scoring Works

When an article is submitted, Google returns ratings like:

| Fact-Checker Rating      | Score Assigned |
|--------------------------|----------------|
| True                     | 90             |
| Mostly True              | 78             |
| Half True                | 55             |
| Mixed                    | 50             |
| Unproven / Unclear       | 45             |
| Misleading               | 28             |
| Mostly False             | 22             |
| False / Fake / Incorrect | 12             |
| Pants on Fire            | 5              |
| (no match found)         | No score       |

If multiple claims are found, the **average** of all scores is computed.

| Final Average | Badge Assigned | Verdict           |
|---------------|----------------|-------------------|
| 70 – 100      | Trusted        | mostly_supported  |
| 40 – 69       | Suspicious     | mixed             |
| 0 – 39        | Fake           | mostly_disputed   |
| No score      | Unverified     | no_match          |

---

## 5. Pre-Loaded Sample Articles (from Seeder)

These 8 articles are created by `php artisan migrate:fresh --seed`:

| # | Title (shortened)                         | Score | Badge      | Submitted by |
|---|-------------------------------------------|-------|------------|--------------|
| 1 | Hydration linked to better focus          | 82    | Trusted    | Demo User    |
| 2 | City council approves transit budget      | 76.5  | Trusted    | Admin        |
| 3 | Moon landing footage "newly leaked"       | 48    | Suspicious | Demo User    |
| 4 | Celebrity "miracle cure" quote            | 22    | Fake       | Demo User    |
| 5 | Local team wins regional match            | 71.25 | Trusted    | Admin        |
| 6 | Economists debate inflation outlook       | 58    | Suspicious | Demo User    |
| 7 | Undocumented Mars sample return claim     | —     | Unverified | Admin        |
| 8 | Satire mistaken for breaking news         | 35    | Suspicious | Demo User    |

---

## 6. Quick URL Reference

| Page                  | URL                          | Login Required |
|-----------------------|------------------------------|----------------|
| Homepage / Feed       | `/`                          | No             |
| All Articles          | `/articles`                  | No             |
| Article Detail        | `/articles/{id}`             | No             |
| Submit Article        | `/articles/create`           | Yes            |
| Dashboard             | `/dashboard`                 | Yes            |
| Edit Profile          | `/profile`                   | Yes            |
| Login                 | `/login`                     | No             |
| Register              | `/register`                  | No             |
| Logout                | `/logout` (POST)             | Yes            |

---

## 7. Troubleshooting

**App shows a blank page or 500 error**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**Articles stuck in "Pending" status**
Queue is set to SYNC so jobs run instantly. If this happens, check:
```bash
php artisan queue:work --once
```

**Google API returns no fact-checks for my article**
This is normal for very recent events or niche topics. The article will get the **Unverified** badge with no score.

**Database errors after code changes**
```bash
php artisan migrate:fresh --seed
```
> Warning: this wipes all data and re-seeds from scratch.

---

## 8. Re-enable Email Verification (When Ready)

When email sending is configured, undo these three changes:

1. **`app/Models/User.php`** — uncomment `implements MustVerifyEmail`
2. **`app/Http/Controllers/Auth/RegisteredUserController.php`** — uncomment `event(new Registered($user))`
3. **`app/Http/Requests/StoreArticleRequest.php`** — restore `&& $this->user()->hasVerifiedEmail()` in `authorize()`
4. **`routes/web.php`** — restore `['auth', 'verified']` middleware on article and dashboard routes
