# Homepage fix — deploy guide

**The problem:** the theme's `m2l/base-front-page.php` hardcoded the landing page to display the
most recent blog post. The real front page's content was never rendered, so editing the homepage
in wp-admin did nothing — the long-standing complaint.

**The fix (already committed here):** `base-front-page.php` and `front-page.php` now render
whatever page is chosen under **Settings → Reading → "Your homepage displays" → A static page**,
making the homepage an ordinary, wp-admin-editable page. A safety guard preserves the old
behavior for as long as that setting remains "Your latest posts", which means:

- Uploading the fixed files changes **nothing** by itself — safe to deploy any time.
- The switch happens in wp-admin, with one setting.
- Rollback is that same setting flipped back — no file changes needed.

## Part 1 — Get the fix onto your PC

In PowerShell:

```powershell
cd $HOME\Documents\maketolearnwebsite
git pull origin claude/maketolearn-wordpress-migration-j6l8p7
git push
```

The pull brings down the fixed theme files (plus the latest docs); the push just keeps GitHub's
copy of your branch current.

## Part 2 — Upload the two changed files to SiteGround

```powershell
scp wp-content/themes/m2l/front-page.php siteground:www/maketolearn.org/public_html/wp-content/themes/m2l/
scp wp-content/themes/m2l/base-front-page.php siteground:www/maketolearn.org/public_html/wp-content/themes/m2l/
```

Check the live site afterwards: it should look **exactly the same** as before. That's expected —
the new behavior is dormant until Part 3.

## Part 3 — Flip the switch in wp-admin

1. **Create the homepage**: Pages → Add New. Title it (e.g. "Home"), build the content you want,
   click **Publish**. (If a suitable page already exists, use that one.) A ready-made landing
   page — the Sangala Initiative content — lives at
   [`site-content/sangala-home/`](../site-content/sangala-home/README.md) with its own paste
   instructions.
2. *Optional:* create an empty page called "News" (or "Blog") if you want the blog posts to keep
   a listing page of their own.
3. **Settings → Reading** → "Your homepage displays" → select **A static page** → Homepage:
   *Home* (and Posts page: *News*, if you made one) → **Save Changes**.
4. **Purge the cache**: in the black admin bar at the top, click **Purge SG Cache**.
5. View the site in a private/incognito window. The homepage is now your page — and from here on
   you edit it like any other page.

## Rollback (if you ever want the old homepage back)

Settings → Reading → "Your latest posts" → Save Changes → Purge SG Cache. Instant, no files
involved.

## Notes

- Blog posts are unaffected: their own URLs keep working, and they get a listing page again if
  you set a Posts page in step 3.
- This is a multisite network: any *other* site in the network using the m2l theme keeps its
  current behavior unless that site also selects a static front page (which is the correct,
  standard behavior anyway).
- The changed files also drop the automatic page-title heading on the homepage, so the page
  content starts clean — put whatever heading you want in the page itself.
