# Site inventory

Environment facts captured from the production site on SiteGround, written here by
`scripts/collect-site-info.sh` (invoked over SSH by `scripts/pull-site.sh`). These complement the
code in `wp-content/`: the code shows *what exists*, these files show *what production actually runs*.

| File                 | What it tells you                                                        |
| -------------------- | ------------------------------------------------------------------------ |
| `php-version.txt`    | PHP on the server's CLI. The web PHP version is set in Site Tools → Devs → PHP Manager — note it here manually if it differs. |
| `wp-version.txt`     | WordPress core version (plus DB revision and package details).           |
| `wp-core-updates.csv`| Whether a core update is available, and to which versions.               |
| `themes.csv`         | Every installed theme: active/inactive, current version, update pending. |
| `plugins.csv`        | Every installed plugin, same columns — the raw material for the plugin inventory. |
| `post-types.csv`     | Registered post types; non-core entries reveal theme/plugin features.    |
| `taxonomies.csv`     | Registered taxonomies, same idea.                                        |
| `menus.csv`          | Nav menus defined in production.                                         |
| `cron-events.csv`    | Scheduled (WP-Cron) jobs — often reveals plugin behavior worth knowing.  |
| `site-options.txt`   | `siteurl`, `home`, site title, and the active `template`/`stylesheet` (these two differing means a child theme is in play). |
| `content-counts.txt` | How many posts exist per post type — shows which content types actually matter. |
| `htaccess.txt`       | Copy of the production `.htaccess`. Review before committing.            |

A `.err` file next to any of these means that one command failed on the server (its error output is
inside); the rest of the inventory is unaffected.
