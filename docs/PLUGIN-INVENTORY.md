# Plugin inventory — maketolearn.org

Based on `docs/site-inventory/plugins.csv`, collected from production **2026-08-16** (WP 7.0.4
multisite, PHP 7.4.33). 24 plugins installed; 23 active. "Update" reflects what wordpress.org
offered at snapshot time. Statuses marked *verify* should be checked against wordpress.org
before acting — abandonment notes are best-effort.

**Headline problems**

- **PHP 7.4 is blocking security updates.** Constant Contact Forms 2.21.0 requires PHP 8.1 —
  WP-CLI literally reports its update as `unavailable`. Expect more of this over time. The PHP
  upgrade (theme inventory, step 3) is the unlock.
- **ACF is ~2 years behind** (6.3.6.1 installed, 6.8.7 available) — the templates depend on it
  daily, and 6.3.6.1 sits right at the October 2024 security-release boundary. Update first.
- **Four overlapping content-modeling plugins** are active at once: Advanced Custom Fields,
  Custom Field Suite, CMB2, and Custom Post Type UI. The theme actively uses ACF *and* CFS;
  nothing found in theme code uses CMB2. Long-term: consolidate onto ACF + code-registered CPTs.
- **Three font systems**: Google Fonts hardcoded in the theme, plus the Custom Fonts and Fonts
  Plugin (olympus-google-fonts) plugins. Pick one.
- **Auto-updates are off for every plugin.** With a git snapshot now in place, consider enabling
  auto-updates at least for minor/security releases.
- Ghost cron from an uninstalled plugin: `updraftplus_clean_temporary_files`.

## Keep — current and earning their place

| Plugin | Version | Role / notes |
| --- | --- | --- |
| sg-cachepress (Speed Optimizer) | 7.8.1 | SiteGround cache/optimizer; network-active |
| sg-security (Security Optimizer) | 1.6.5 | SiteGround hardening (source of the xmlrpc block in `.htaccess`); network-active |
| sg-ai-studio | 1.2.7 | SiteGround AI tool; keep or remove per actual use |
| akismet | 5.7 | Anti-spam; network-active; no update pending |
| google-analytics-for-wordpress (MonsterInsights) | 11.1.2 | Analytics; current; noisy cron footprint is normal for it |
| duplicate-page | 4.5.9 | Editor convenience; maintained |
| advanced-custom-fields | 6.3.6.1 → **6.8.7** | **Update immediately** — theme depends on it (`get_field` throughout); currently 2 years behind |
| custom-post-type-ui | 1.19.3 | **Holds the site's CPT/taxonomy definitions in the database** (`activities`, `kitgroup`, `inventions`). Keep until definitions are exported to code (its Tools → Get Code feature), then retire |
| custom-field-suite | 2.6.7 | Powers the activity `steps` repeater — hard theme dependency. In maintenance mode upstream (*verify*); plan a CFS→ACF migration, keep until then |
| constant-contact-forms | 2.13.0 → 2.21.0 **(blocked: needs PHP 8.1)** | Mailing-list forms (`ctct_forms`/`ctct_lists` exist in DB; the homepage embed is commented out in the theme — check where forms are actually used). Update right after the PHP upgrade |

## Evaluate — likely redundant, remove after verifying

| Plugin | Version | Concern |
| --- | --- | --- |
| cmb2 | 2.12.0 | Metabox framework; no usage found in theme code — probably a leftover. Verify nothing else calls it, then remove |
| custom-fonts | 2.1.17 | Redundant with hardcoded Google Fonts and Fonts Plugin — keep exactly one font mechanism |
| olympus-google-fonts (Fonts Plugin) | 4.1.3 → 4.2.1 | Same as above; update it if it's the keeper, otherwise remove |
| widget-context | 1.4.0 | Scopes widget visibility; check whether any widget actually uses rules before removing |
| category-posts-in-custom-menu | 3.0.6 | Niche menu helper; verify a menu actually depends on it (the "Inventions" menu location is a candidate) |
| template-for-custom-post-types | 1.0.4 | Obscure, appears abandoned (*verify*); m2l ships its own CPT templates — likely removable |
| image-placeholder | 1.0 | v1.0, ancient; likely a leftover from the holder.js era of the old kit template |
| bootstrap-3-shortcodes | 3.3.12 | Abandoned (~2017) and Bootstrap 3's CSS **doesn't even load on this site** (see theme inventory) — any `[bootstrap]` shortcodes in content render unstyled. Search content for usage, convert those pages, remove |
| breeze | 2.5.13 (inactive) | Cloudways cache plugin, inactive, redundant with Speed Optimizer — delete |
| simple-301-redirects | 2.1.0 | Works, but redirects could live in SiteGround/htaccess; audit the stored redirect list either way |

## Remove — risk outweighs use

| Plugin | Version | Why |
| --- | --- | --- |
| wp-editor | 1.2.9.3 | In-wp-admin theme/plugin **file editor**; abandoned for many years (*verify*). It's an attack-surface amplifier and obsolete now that the code lives in git — edit locally, deploy instead |
| scripts-n-styles | 3.5.8 | Admin-editable arbitrary JS/CSS stored in the DB; long unmaintained (*verify*). **Export/audit its stored snippets first**, move anything needed into the theme, then remove |
| show-current-template | 0.5.4 | Developer diagnostic running in production; deactivate (reinstall when debugging) |
| wordpress-importer | 0.9.5 | Official tool, but it should be deactivated when not actively importing |

## Suggested sequence

1. Update ACF now (works on PHP 7.4).
2. Audit + remove the "Remove" tier (snapshot their settings first — the repo now gives you a
   place to preserve exported snippets/redirect lists under `docs/`).
3. After the PHP 8.3 upgrade: update Constant Contact Forms, then work through the "Evaluate"
   tier one per staging pass.
4. When CPT UI's definitions are exported to code and CFS content is migrated to ACF, retire
   both, ending at roughly 12–14 plugins with one field framework.
