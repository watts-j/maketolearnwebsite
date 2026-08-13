# maketolearn.org — WordPress site repository

Version-control home for the code behind [maketolearn.org](https://maketolearn.org), a WordPress site
hosted on SiteGround running a custom theme. The immediate goal is to pull the theme (and the rest of
`wp-content`) into this repo, capture the site's environment facts, and then have Claude Code inventory
everything as the first step toward modernizing the theme.

The site's files only exist on SiteGround right now, so the flow is: **enable SSH → pull → commit →
push → inventory**. Everything below is written for that flow, with a no-SSH fallback at the end.

## Step 1 — Enable SSH on SiteGround (one time)

1. Log in to SiteGround and open **Site Tools** for maketolearn.org.
2. Go to **Devs → SSH Keys Manager** and generate a new key (or import your existing public key).
   SiteGround only allows key-based SSH — there is no password login.
3. If you generated the key there, download the private key and save it locally
   (e.g. `~/.ssh/siteground_maketolearn`, then `chmod 600` it).
4. The Manage Keys panel shows your **username** and **hostname**. SiteGround's SSH port is **18765**.
5. Test the connection:

   ```sh
   ssh -i ~/.ssh/siteground_maketolearn -p 18765 <username>@<hostname>
   ```

The WordPress root on SiteGround's current platform is typically
`/home/customer/www/maketolearn.org/public_html` (i.e. `www/maketolearn.org/public_html` relative to
your SSH home directory). Confirm with `ls` once you're connected.

## Step 2 — Pull the site into this repo

Clone this repo on your machine, open `scripts/pull-site.sh`, and fill in the three variables at the
top (username, hostname, and the WordPress root from Step 1). Then, from the repo root:

```sh
bash scripts/pull-site.sh
```

The script does three things:

- **Rsyncs `wp-content/` into the repo** — themes, plugins, and mu-plugins — while excluding media
  (`uploads/`), caches, and backup archives. Code belongs in git; media and caches don't.
- **Copies the production `.htaccess`** to `docs/site-inventory/htaccess.txt` for reference
  (review it before committing — occasionally these contain IP allowlists you may not want public).
- **Runs `scripts/collect-site-info.sh` on the server via WP-CLI** (pre-installed on SiteGround) and
  downloads the results into `docs/site-inventory/`: WordPress/PHP versions, the full theme and
  plugin list with update status, registered post types, taxonomies, menus, cron jobs, and content
  counts. See `docs/site-inventory/README.md` for the file-by-file glossary.

Re-running the script is safe — it just refreshes the same files.

## Step 3 — Take a full backup separately (recommended, not in git)

Git gets the *code*. Before touching anything else, also capture a full snapshot **outside the repo**:

- **Files:** Site Tools → **Security → Backups** (or rsync the entire `public_html` to a local folder).
- **Database:** over SSH, `cd` to the WordPress root and run `wp db export ~/maketolearn-db.sql`,
  then download it — or use Site Tools → **Site → MySQL → phpMyAdmin → Export**.

Keep both somewhere private. **Do not commit them**: the database contains user emails and password
hashes, and `wp-config.php` contains database credentials and secret keys. The `.gitignore` here
blocks `*.sql`, `wp-config.php`, and `uploads/` as a guardrail, but treat that as a backstop, not
permission to copy them into the repo.

## Step 4 — Review, commit, push

```sh
git status          # sanity check: no wp-config.php, no *.sql, no uploads/
git add -A
git commit -m "Import wp-content and site inventory from production"
git push -u origin HEAD   # pushes whichever branch you're on
```

## Step 5 — Have Claude Code inventory it

Open a Claude Code session on this repo (claude.ai/code, or `claude` in a local clone) and ask
something like:

> Inventory this site. Produce `docs/THEME-INVENTORY.md` documenting the custom theme: template
> files and how they map to the template hierarchy, what `functions.php` (and any includes) sets up,
> custom post types, taxonomies, shortcodes, widgets, nav menus, enqueued and bundled JS/CSS
> libraries with their versions, and third-party dependencies. Produce `docs/PLUGIN-INVENTORY.md`
> from `docs/site-inventory/plugins.csv`. Flag anything deprecated, abandoned, insecure, or likely
> incompatible with current WordPress and PHP, and rank the modernization work.

From there, natural next steps are a deprecated-API and PHP-compatibility pass on the theme, testing
changes on a SiteGround staging copy (Site Tools → **WordPress → Staging**), and deploying updates
back via git or rsync.

## What belongs in this repo

| Commit                                      | Never commit                                    |
| ------------------------------------------- | ----------------------------------------------- |
| `wp-content/themes/` (the custom theme)     | `wp-config.php` (credentials, salts)            |
| `wp-content/plugins/`, `mu-plugins/`        | `*.sql` database dumps (PII, password hashes)   |
| `docs/site-inventory/` (WP-CLI outputs)     | `wp-content/uploads/` (media — use backups)     |
| `.htaccess` copy, after review              | Caches, backup archives, WordPress core files   |

WordPress core is deliberately not versioned — it's reinstallable byte-for-byte for any version, so
tracking it adds noise without information. The inventory records which version production runs.

## No-SSH fallback

If SSH isn't an option: Site Tools → **Site → File Manager**, right-click `public_html/wp-content`,
choose **Archive**, download the resulting `.zip`, and extract it into the repo root so it lands at
`wp-content/`. Delete `wp-content/uploads/` and any cache/backup folders before committing. For the
environment facts, use wp-admin's built-in report at **Tools → Site Health → Info** — click "Copy
site info to clipboard" and paste it into `docs/site-inventory/site-health.txt`. It covers most of
what the WP-CLI script collects.
