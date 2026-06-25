# TruthLens — User & Feature Guide

A plain-language guide to what TruthLens does, how each feature works, and what to expect when you use it.

---

## 1. What is TruthLens?

TruthLens is a web application that helps people **check whether claims in news and social posts have been reviewed by professional fact-checkers**.

When someone submits an article (by link or pasted text), the system:

1. Reads the content.
2. Searches a large database of **published fact-check reviews** (via Google’s Fact Check Tools service).
3. Assigns a **credibility score** and coloured **badge** when matches are found.
4. Lets signed-in users **vote** whether they think the story is credible or not.

TruthLens does **not** decide absolute truth. It combines **automated fact-check signals** with **community opinion**.

---

## 2. Who can do what?

| Action | Visitor (not logged in) | Logged-in user | Moderator | Admin |
|--------|-------------------------|----------------|-----------|-------|
| Browse the public feed | Yes | Yes | Yes | Yes |
| Open article details | Yes | Yes | Yes | Yes |
| Submit a new article | No | Yes | Yes | Yes |
| Vote on articles | No | Yes* | Yes* | Yes* |
| Report misleading content | No | Yes | Yes | Yes |
| View personal dashboard | No | Yes | Yes | Yes |
| Edit profile | No | Yes | Yes | Yes |
| Staff area (moderation tools) | No | No | Yes | Yes |
| View all users’ articles | No | No | Yes | Yes |
| Review article reports | No | No | Yes | Yes |
| Download reports (CSV) | No | No | Yes | Yes |
| Manage publisher sources (ban/trust) | No | No | Yes | Yes |

\* Voting requires a signed-in account whose email is marked as verified. Accounts created by the local database seeder are pre-verified for testing.

---

## 3. Main pages

| Page | Address | Purpose |
|------|---------|---------|
| Home / Feed | `/` or `/articles` | List of completed article checks |
| Article detail | `/articles/{id}` | Full results, score, fact-check links, votes |
| Check article | `/articles/create` | Submit a URL or pasted text (login required) |
| Dashboard | `/dashboard` | Your submission history and stats |
| Profile | `/profile` | Update name, email, bio, password |
| Staff overview | `/admin` | Moderation dashboard (moderator & admin only) |
| All articles (staff) | `/admin/articles` | Every submission from all users |
| Report queue (staff) | `/admin/reports` | Review and resolve user flags |
| Download reports (staff) | `/admin/reports/export` | CSV export of the report queue |
| Sources (staff) | `/admin/sources` | Manage domains, trust scores, bans |
| Register | `/register` | Create an account |
| Log in | `/login` | Sign in |

---

## 4. Features in detail

### 4.1 Public feed

The feed shows articles that have finished processing (`completed` status).

Each card displays:

- **Badge** — Trusted, Suspicious, Fake, or Unverified
- **Title** — The headline you entered or extracted from the page
- **Short excerpt** — Beginning of the article text
- **Score** — Number out of 100 (if available)
- **Vote counts** — How many people marked it credible vs not credible
- **Time** — When it was submitted

Only completed articles appear here. New submissions disappear from the feed until analysis finishes.

---

### 4.2 Submit an article (credibility check)

**Login required.**

Two submission types:

#### A. Article URL

1. Choose **Article URL**.
2. Enter the **web address** of the page.
3. Enter a **title** that states the **claim** you want checked (required).
4. Optionally add a **category** (e.g. Health, Politics).

The system downloads the page, removes HTML/navigation noise, and keeps the readable text for analysis.

**Important:** The title should describe a **specific claim or rumor**, not a neutral news headline. Example:

- Good: `5G mobile networks cause or spread COVID-19`
- Poor: `Breaking news from BBC`

#### B. Raw text

1. Choose **Raw text**.
2. Paste the **body** of the article (minimum 40 characters).
3. Add a **title** (recommended) and optional **category**.

After you click **Run analysis**, you are taken to the article page. Refresh after a few seconds to see the final badge and score.

---

### 4.3 What happens behind the scenes

```
You submit → Status: Pending
       ↓
System processes → Status: Processing
       ↓
   ┌──────────────────────────────────────┐
   │ 1. Fetch URL text (if URL mode)      │
   │ 2. Build search text from title/body │
   │ 3. Query fact-check database         │
   │ 4. Convert ratings to a score        │
   │ 5. Assign badge                      │
   └──────────────────────────────────────┘
       ↓
Status: Completed → Appears on feed
```

**Duplicate detection:** If the same text was already analyzed, the new submission **reuses the existing score** instead of calling the API again. You will see a notice linking to the original article.

**Source tracking:** For URL submissions, the website domain (e.g. `bbc.com`) is recorded and shown on the article page. The domain alone does **not** determine the badge.

---

### 4.4 Credibility score (0–100)

When fact-checkers have reviewed **similar claims**, TruthLens collects their written ratings (e.g. “False”, “Mostly true”, “Misleading”) and converts each to a number.

| Fact-checker rating (examples) | Points |
|-------------------------------|--------|
| True | 90 |
| Mostly true | 78 |
| Half true | 55 |
| Mixed | 50 |
| Unproven / unclear | 45 |
| Misleading | 28 |
| Mostly false | 22 |
| False / fake / incorrect | 12 |
| “Pants on fire” (extreme false) | 5 |

If several reviews are found, the system uses the **average** of all converted scores.

If **no matching reviews** are found, there is **no score**.

---

### 4.5 Badges

| Badge | Colour | Score range | Meaning |
|-------|--------|-------------|---------|
| **Trusted** | Green | 70–100 | Matched reviews lean toward true / supported |
| **Suspicious** | Yellow | 40–69 | Mixed or partly misleading |
| **Fake** | Red | 0–39 | Matched reviews lean toward false / disputed |
| **Unverified** | Grey | No score | No matching fact-check reviews found |

**Unverified is normal** for:

- Ordinary news stories with no viral false claim
- Recent events not yet fact-checked
- Pages where the title does not match known debunked rumors
- Technical fetch issues (empty page text)

Unverified does **not** always mean “the article is true” or “the app is broken.” It usually means **no automated match** was available.

---

### 4.6 Fact-check references

On the article detail page, when matches exist, you will see a **Fact-check references** section.

Each entry shows:

- **Publisher** — e.g. PolitiFact, Snopes, AFP Fact Check
- **Review title**
- **Rating** — The fact-checker’s label
- **Link** — Opens the original review in a new tab

Always read these sources yourself. TruthLens points you to them; it does not replace them.

---

### 4.7 Community voting

Logged-in users can vote on any article:

- **Credible** — You believe the content is reliable
- **Not credible** — You believe it is misleading or false

Rules:

- One vote per account per article
- You can change your vote by clicking the other button
- Vote totals are visible to everyone
- Visitors can see counts but cannot vote

Community votes are **separate** from the automated score. An article can score low automatically but still receive many community votes.

---

### 4.8 Report misleading content

**Login required.**

On any article detail page, use the **Report content** panel in the sidebar to flag a submission for staff review.

Choose a **reason**:

| Category | When to use it |
|----------|----------------|
| **Misleading** | Content appears deceptive or twisted |
| **Satire** | Presented as fact but is satire/parody |
| **Out of context** | Quote or clip missing important context |
| **Fabricated** | Appears wholly made up |
| **Other** | Does not fit the categories above |

Add optional **details** (up to 2,000 characters), then click **Submit report**.

Rules:

- One **pending** report per account per article at a time
- Staff (moderators and admins) review reports in the **Report queue**
- Reports are marked **reviewed** or **dismissed**; the reviewer and timestamp are recorded

---

### 4.9 Dashboard

Your personal overview after login:

- **All submissions** — Total articles you submitted
- **Completed** — Finished analyses
- **In progress** — Still pending or processing
- **Recent activity table** — Your latest submissions with badge, score, and status

Use **New check** to submit another article.

---

### 4.10 Profile & account

From the menu under your name → **Profile**:

- Update **display name** and **email**
- Add an optional **bio**
- Change **password**
- **Delete account** (requires password confirmation)

---

### 4.11 Registration & login

- **Register** — Name, email, password (minimum 8 characters)
- **Log in** — Email and password
- **Forgot password** — Email reset link (requires mail server configuration)

Email verification is currently **disabled** for new sign-ups in this build, but voting still checks for a verified email flag. Seeded test accounts are pre-verified.

---

### 4.12 User roles (system level)

The application supports three roles in the database (via [Spatie Permission](https://github.com/spatie/laravel-permission)):

| Role | Typical use |
|------|-------------|
| **admin** | Full staff access; all moderation permissions |
| **moderator** | Staff moderation: articles, reports, sources |
| **user** | Default role for new registrations |

**Staff permissions** (assigned to moderator; admin receives all):

| Permission | What it allows |
|------------|----------------|
| `view all articles` | Browse `/admin/articles` — all users’ submissions |
| `review reports` | Open `/admin/reports`, resolve flags, download CSV |
| `manage sources` | Open `/admin/sources`, adjust trust scores, ban domains |

Day-to-day use (submit, vote, personal dashboard) is the same for users, moderators, and admins. Only **staff** roles see the **Staff** link in the navigation.

---

### 4.13 Staff area (moderator & admin)

Open **Staff** in the navbar or go to `/admin`.

#### Overview (`/admin`)

Summary cards for total articles, completed analyses, pending reports, and banned sources. Quick links to each moderation section.

#### All articles (`/admin/articles`)

- Lists **every article** submitted by **any user**
- Filter by status: all, completed, pending, processing, failed
- Columns: ID, title, URL, author (name & email), type, badge, score, status, submitted date
- **View** opens the public article page

#### Report queue (`/admin/reports`)

- Lists user-submitted flags from article pages
- Filter by: all, pending, reviewed, dismissed
- For each report: article link, reporter, category, details, status, reviewer
- **Reviewed** — staff has actioned the report
- **Dismiss** — report rejected or not actionable
- **Download CSV** — exports the current filter to a spreadsheet (`/admin/reports/export`)

CSV columns: report ID, article ID, article title, reporter name/email, category, details, status, reviewed by, reviewed at, created at.

#### Publisher sources (`/admin/sources`)

- Lists domains seen in URL submissions (e.g. `snopes.com`, `bbc.com`)
- Edit **trust score** (0–100) per domain
- **Ban** / **Unban** domains (`is_banned` on the `sources` table)
- Filter to banned domains only

Banned domains are recorded for moderation; future submissions from those domains can be restricted in later versions.

---

## 5. What TruthLens does **not** do

Understanding these limits avoids confusion:

| Expectation | Reality |
|-------------|---------|
| “Check if this website is trustworthy” | **No** — Domains are not scored |
| “Verify any news URL automatically” | **No** — Only **claims** with existing fact-check coverage match |
| “Read the entire internet” | **No** — Only indexed fact-check publisher reviews |
| “Guarantee the badge is correct” | **No** — Scores reflect third-party reviews + your community |

**Random news URLs** (sports, politics, company homepages) will usually show **Unverified** unless the **title** describes a claim that fact-checkers have already written about.

---

## 6. Tips for reliable results

1. **Use a claim-style title** — Especially for URL mode.
2. **Prefer text mode** when testing — You control the exact wording.
3. **Use well-known debunked topics for demos** — e.g. moon landing hoax, 5G and COVID, vaccine microchip myths.
4. **Refresh** the article page after submitting.
5. **Do not resubmit identical text** — Duplicates copy the old result.
6. **Read fact-check links** on the detail page.

### Example that typically scores

**Text mode**

- **Title:** `5G mobile networks cause or spread COVID-19`
- **Content:** `Social media posts claim 5G towers spread coronavirus. Health authorities say viruses do not travel on radio waves.`
- **Expected:** Fake or Suspicious badge, score roughly 25–40

**URL mode**

- **Title:** `COVID-19 vaccines contain microchips to track people`
- **URL:** `https://www.snopes.com/fact-check/bill-gates-microchip/`
- **Expected:** Fake badge with fact-check references

### Example that usually stays Unverified

- **Title:** `Army Chief visits Tehran`
- **URL:** Any general news site

→ Normal reporting; no standard fact-check entry for that headline.

---

## 7. Article statuses

| Status | What it means |
|--------|----------------|
| **pending** | Just submitted; waiting to start |
| **processing** | Analysis running |
| **completed** | Finished; visible on feed with final badge |
| **failed** | Processing error (rare) |

If stuck on pending/processing for a long time, ensure background jobs are running (see technical README).

---

## 8. Getting started (technical summary)

For installers and developers, see **README.md**. Short version:

1. Install PHP, Composer, MySQL.
2. Copy `.env.example` to `.env` and set database + `GOOGLE_FACT_CHECK_API_KEY`.
3. Run:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate --seed
   ```
4. Open the site in a browser (e.g. `http://truthlens.test` or your server URL).

### Local test accounts (after seeding)

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@truthlens.local` | `password` |
| Moderator | `moderator@truthlens.local` | `password` |
| User | `user@truthlens.local` | `password` |

Change these before going live.

### Environment notes

- **`QUEUE_CONNECTION=sync`** — Analysis runs immediately (good for local testing).
- **`APP_ENV=local`** — SSL verification for outbound requests is relaxed on Windows/Laragon.
- **`GOOGLE_FACT_CHECK_LANGUAGE`** — Default `en-US`; affects which fact-check language index is searched.

---

## 9. Troubleshooting (FAQ)

**Why is my article Unverified?**

Most often: no fact-checker has published a review matching your title/claim. Try a clearer claim title or a topic known to be widely fact-checked.

**The URL opened fine in my browser but TruthLens shows little text**

Some sites block automated fetching or use heavy JavaScript. The analysis may rely only on your title.

**I submitted the same article twice and got the same score instantly**

Duplicate detection linked it to the first submission. Change the text or delete the older entry to force a new analysis.

**I see a score but no fact-check references**

You may be viewing a duplicate submission. Open the original article (linked in the blue notice) for full details.

**cURL / SSL errors on Windows**

Run `php artisan config:clear`. On local Laragon installs, SSL verification is disabled by default when `APP_ENV=local`. See README.md.

**Votes do not save**

Ensure you are logged in and your account email is verified. Seeded test users work out of the box.

**Blank page or 500 error**

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 10. Privacy & responsibility

- Submitted URLs and text are stored in the application database.
- Scores come from **third-party fact-check publishers**, not from TruthLens staff.
- Community votes reflect user opinion, not verified fact.
- Use results as **one input** in your own judgment, not as the final word.

---

## 11. Quick reference — how a badge is chosen

```
Fact-check API returns reviews?
        │
        ├─ No  → Unverified (grey)
        │
        └─ Yes → Average score calculated
                    │
                    ├─ 70–100 → Trusted (green)
                    ├─ 40–69  → Suspicious (yellow)
                    └─ 0–39   → Fake (red)
```

---

*TruthLens — Credibility platform. For technical installation details, see [README.md](README.md). For database design, see [DATABASE.md](DATABASE.md).*
