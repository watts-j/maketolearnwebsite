# Theme inventory — maketolearn.org

Snapshot of production taken **2026-08-16** (see `docs/site-inventory/` for raw WP-CLI output).
This documents what the active theme is, how it's put together, what actually loads in the
browser, every piece of site-specific logic, known defects, and a ranked modernization plan.

## Environment facts

| Fact | Value | Notes |
| --- | --- | --- |
| WordPress | 7.0.4, **multisite** | Core fully up to date (no pending core update at snapshot) |
| PHP | **7.4.33** | **End-of-life November 2022** — no security fixes for ~4 years; blocks plugin updates (see plugin inventory) |
| Active theme | `m2l` v1.0.3 ("MakeToLearn (Sage Child)") | Child theme; author John Rhea (johnrhea.com) |
| Parent theme | `sage` v8.4.2 (Roots Sage Starter) | Released 2016; framework line long superseded (Sage 9/10/11) and **locally modified** — not stock |
| Multisite evidence | `active-network` plugins, `update_network_counts` cron, network-enabled themes | Confirm subsites with `wp site list` before structural changes |
| Other installed themes | generatepress 3.4.0 (inactive), 8 default Twenty* themes | All unused; several have pending updates |

## Architecture: how the two themes fit together

Sage 8 uses a **theme wrapper**: WordPress picks a template (e.g. `front-page.php`), and Sage's
`lib/wrapper.php` wraps it inside `base.php` (or `base-{template}.php` if one exists). Because
`locate_template()` checks the child theme first, **m2l's `base.php` and `base-front-page.php`
override Sage's wrapper for every page** — the parent's `base.php` is dead code in practice.

Layer by layer:

- **`m2l` (child, the real theme)** — all layout chrome (header/nav/footer/sidebar), the
  invention-kit navigation system, breadcrumbs, the InventionKits listing page template, the
  activities single view, all production CSS (`style.css`, 597 lines) and JS
  (`js/navigation.js`, 225 lines, vanilla underscores-style menu toggle).
- **`sage` (parent, modified)** — the wrapper mechanism, theme setup (menus, sidebars, thumbnail
  support), and `lib/extras.php`, which despite the generic name holds **site-critical code**:
  the `/invention-kits/…` rewrite rules, permalink filter, menu-order-based prev/next post
  navigation, and admin conveniences. Also holds fallback templates m2l doesn't override.

### What actually loads in the browser (important)

The asset pipeline is broken/absent on the server, and the theme silently tolerates it:

| Enqueued asset | Source | Status in production |
| --- | --- | --- |
| `sage/dist/styles/main.css` (Bootstrap 3.3.6 + Font Awesome 4.6.1 + Sage SCSS) | `lib/setup.php` | **404 — `dist/` does not exist on the server.** Never loads. |
| `sage/dist/scripts/main.js` (Bootstrap JS + DOM routing) | `lib/setup.php` | **404 — never loads.** |
| `sage/assets/custom.css` (hardcoded URL) | `lib/setup.php` | Loads but is **an empty file (0 bytes)** |
| `sage/style.css` (via child `functions.php`) | `m2l/functions.php` | Loads but contains **only the theme header comment — no CSS** |
| `m2l/style.css` | `m2l/functions.php` | ✅ Loads — **this is the site's entire stylesheet** |
| `m2l/js/navigation.js` | `m2l/functions.php` | ✅ Loads (no jQuery dependency) |
| Google Fonts (Open Sans, PT Serif) | hardcoded in `m2l/templates/head.php` | ✅ Loads (also see font plugins — three font systems installed) |

Consequences:

- **Bootstrap 3 never loads.** All Bootstrap classes in templates (`col-sm-*`, `nav-pills`,
  `btn btn-warning`, `well`, `thumbnail`, `container`, `row`) are styled only where
  `m2l/style.css` happens to define them; the rest is dead markup. The active
  `bootstrap-3-shortcodes` plugin emits markup that nothing styles.
- The `bower.json`/`gulpfile.js`/`package.json` toolchain (Bower + Gulp 3, 2015-era, both
  abandoned) is unused and unbuildable today — nothing to preserve.
- The child theme was minified with CodeKit (`config.codekit3`), producing `style-min.css` and
  `js/navigation-min.js` — **neither is enqueued**; dead files.

## Template map

Wrapper layer (child): `base.php` wraps every view; `base-front-page.php` wraps only the homepage.

| Template | Layer | Serves | Notes |
| --- | --- | --- | --- |
| `m2l/base.php` | wrapper | every page | Masthead, primary nav, **invention secondary/tertiary menus** (ACF-driven), breadcrumbs, then the wrapped template |
| `m2l/base-front-page.php` | wrapper | homepage | **Fixed 2026-08-16**: now renders the static front page chosen in Settings → Reading (wp-admin-editable); the legacy hardcoded latest-blog-post view remains only while that setting stays "Your latest posts". Deploy per [`HOMEPAGE-FIX.md`](HOMEPAGE-FIX.md) |
| `m2l/front-page.php` | main | homepage | Renders the front page's content (no page-title heading) |
| `m2l/template-inventionkit.php` | page template | "InventionKits Listing Template" | Lists `kitgroup` posts → their `inventions` terms sorted by ACF term field `order`, image from term field `image_url`, each linking to the term's first `activities` post |
| `m2l/templates/content-single-activities.php` | partial | single activity | Steps from CFS `steps` repeater; "Make page" meta box (ACF: difficulty, estimated_time, tools, parts, cad_file, resource_link) |
| `m2l/templates/{head,header,footer,sidebar,content}.php` | partials | all pages | head: Google Fonts + raw `the_field('header_script')`; header: inline-SVG logo; footer: SITE copyright; sidebar: per-activity step nav + widgets |
| `m2l/searchform.php`, `sidebar-primary.php` | partials | search box; homepage sidebar | |
| `sage/single.php`, `page.php`, `index.php`, `search.php`, `404.php` | fallbacks | standard views | Stock Sage |
| `sage/taxonomy-inventions.php` | fallback | inventions term archives | Contains a stray literal `a` that prints into the page (line 11) |
| `sage/template-inventionkit.php` | page template (old) | superseded by m2l's | Uses term **description as image URL**, holder.js placeholders, deprecated `get_terms()` signature, malformed `"` in img tag — should be deleted |
| `sage/template-custom.php` | page template | "Custom Template" | Trivial; check if any page still uses it |

## Site-specific logic (the code that must survive any rebuild)

1. **Invention-kit URL scheme** (`sage/lib/extras.php`): rewrite rules mapping
   `/invention-kits/{invention}/{activity}` → `activities` post and `/invention-kits/{term}` →
   `inventions` term, plus a `post_type_link` filter replacing `%inventions%` in activity
   permalinks. This is the backbone of the site's content URLs.
2. **Kit navigation** (`m2l/base.php`): on any `activities` page, builds a secondary menu of all
   activities in the same invention (ordered by `menu_order`), with ACF fields `conglomerate`,
   `makes_to_conglomerate`, `conglomerate_parent` driving an optional tertiary "Make" menu.
3. **Menu-order prev/next** (`sage/lib/extras.php`): overrides `get_previous/next_post_where/sort`
   to walk posts by `menu_order` — **globally, for every post type**, not just activities (side
   effect on blog posts, which all have `menu_order` 0).
4. **Step system**: activities store build steps in a **Custom Field Suite** repeater (`steps` →
   `steps_title`, `step`); templates render them with anchor navigation in the sidebar.
5. **Content model** (registered *outside* the theme — almost certainly in the Custom Post Type
   UI plugin's database settings; no `register_post_type` exists anywhere in either theme):
   - CPT `activities` (hierarchical, public) — 31 posts
   - CPT `kitgroup` "Invention Kit Groups" (public) — 2 posts
   - Taxonomy `inventions` on both — with ACF **term** fields `order`, `image_url`
   - ACF: 6 field groups / 30 fields (in DB, not synced to code)
6. **Menus/sidebars**: locations `primary_navigation` and `invention` (registered in parent);
   three widget areas — `primary` (child), `sidebar-primary` and `sidebar-footer` (parent) — and
   *both* `primary` and `sidebar-primary` are actually used (homepage vs. activity sidebar).
7. **Admin conveniences** (`sage/lib/extras.php`): inventions dropdown filter on the activities
   list screen; a help tab documenting the Guide/Introduction/menu-order editorial conventions
   (titles "Guide" and "Introduction" are magic values — also used by `display_sidebar()`).
8. **Hard plugin dependencies**: templates call `CFS()` and `get_field()`/`the_field()` directly —
   the site **fatals if Custom Field Suite or ACF is deactivated**.

## Defects and risks found in code review

PHP 8 blockers (must fix before the PHP upgrade):

1. `m2l/templates/content-single-activities.php:31` — `count($fields)` where `$fields` can be
   `null` → **fatal `TypeError` on PHP 8** for any "Make" activity without steps.
2. `m2l/base.php:41-56` — `get_the_terms()` result used as `$terms[0]->slug` unguarded → warnings
   /broken query for an activity with no invention term (PHP 8 turns many of these into louder
   failures).
3. `m2l/base.php:95-100` — `get_field(..., $nav_qu->ID)`: **`WP_Query` has no `ID` property**;
   this has silently passed `null` forever (falls back to current post). Works by accident;
   also `$conglom_parent->ID` on a possibly-null value, and `foreach ($makepages…)` on possibly
   `false`.
4. `m2l/template-inventionkit.php:49` and `sage/template-inventionkit.php` — `$posts_array[0]->ID`
   unguarded when a term has no activities.
5. `m2l/templates/content.php:13-18` — `foreach` over `CFS()->get('steps')` with no empty check.
6. `m2l/templates/sidebar.php:14` — `$terms[0]` unguarded.

Bugs regardless of PHP version:

7. `sage/taxonomy-inventions.php:11` — stray `a` printed into every inventions archive page.
8. `m2l/template-inventionkit.php:29-33` — inventions sorted via `$sorted[$order] = $term`:
   **two terms with the same ACF `order` value silently drop one kit from the listing**.
9. `m2l/base-front-page.php` — homepage never rendered the front page's own content, so wp-admin
   edits to the homepage did nothing. **Fixed in this repo 2026-08-16** — deploy and switch per
   [`HOMEPAGE-FIX.md`](HOMEPAGE-FIX.md).
10. `sage/lib/extras.php` — prev/next overrides apply to all post types (see above); REST
    filters `json_prepare_post/page` target the 2013 REST API v1 plugin — dead code; five
    `add_theme_support('soil-…')` calls but the Soil plugin is not installed — inert.

Security hygiene:

11. `m2l/base.php:115-160` — breadcrumbs echo path segments derived from `$_SERVER['REQUEST_URI']`
    without `esc_html()`/`esc_url()`. Low exploitability (URL-encoding survives), but it's the
    kind of thing to fix while in there. General escaping discipline is loose throughout
    (raw `echo get_field(...)` everywhere).
12. `m2l/templates/head.php:7` — `the_field('header_script')` prints an ACF field raw into
    `<head>` on every page: an admin-editable script-injection point. Audit what's stored in it,
    then either hardcode it or move to a managed snippets solution.

Housekeeping:

13. Text domains are a mix of `sage`, `maketolearn`, `eightwords`, `m2l-child`.
14. Dead files: `style-min.css`, `js/navigation-min.js`, `config.codekit3`, duplicate
    `labschool02.jpg` at theme root, `m2ltheme.zip` sitting in the server's themes directory.
15. Ghost cron `updraftplus_clean_temporary_files` — UpdraftPlus is no longer installed.
16. 9 inactive themes installed (GeneratePress + 8 defaults); keep one default as fallback,
    delete the rest.

## Ranked modernization plan

The live theme surface is genuinely small — one 597-line stylesheet, one 225-line script, ~10
meaningful PHP templates, and one lib file of real logic. That makes this very tractable.

1. **Safety net first.** Confirm backups (Site Tools → Security → Backups; DB export already
   taken). Create a SiteGround **staging** copy (Site Tools → WordPress → Staging) for
   everything below. Also run `wp site list` to confirm whether other subsites exist in the
   network before changing shared plugins/themes.
2. **Fix the PHP 8 blockers in the theme** (items 1–6 above — a few hours of guarded-access
   edits) plus the stray-`a` and duplicate-order bugs. Deploy to staging.
3. **Switch staging to PHP 8.3** (Site Tools → Devs → PHP Manager), click through the whole
   site (especially activities, kit listings, search, the homepage), fix anything that
   surfaces, then apply to production. *This is the master unlock*: PHP 7.4 is the reason
   plugin updates are blocked and the biggest security liability.
4. **Update plugins** (see `PLUGIN-INVENTORY.md`): ACF first (security-relevant, 2 years
   behind), then Constant Contact Forms (unblocked by PHP 8.1+), and the rest; delete the
   removal candidates.
5. **Flatten the theme.** Fold the parent's live logic (`lib/extras.php` rewrites/filters,
   `lib/setup.php` menus/sidebars, the wrapper or a simplification of it, fallback templates)
   into a single self-contained theme — no more 2016 framework, no Bower/Gulp, no dead
   Bootstrap markup. Alternative if a redesign is wanted anyway: rebuild on a maintained base
   and port the logic; either way the logic list in this document is the spec.
6. **Consolidate the content stack** (lower urgency, big long-term win): migrate the CFS
   `steps` repeater to ACF; export CPT UI definitions to code (CPT UI's built-in "get code"
   tool); register the CPTs/taxonomy in the theme or an mu-plugin so the content model is
   versioned; pick one font mechanism; remove the redundant field/font plugins.
7. **Housekeeping**: delete unused themes and dead files, clear the ghost cron
   (`wp cron event delete updraftplus_clean_temporary_files`), remove `m2ltheme.zip` from the
   server.

## Open questions

- Which subsites exist in the multisite network, and do they use these themes/plugins?
  (`wp site list`)
- What is stored in the `header_script` ACF field and in the Scripts n Styles plugin's snippets?
- Is `[bootstrap-3-shortcodes]` markup actually used in any post content? (Search the DB before
  removing the plugin.)
- Does any page still use `sage/template-custom.php` or the old Sage kit-listing template?
- Why is GeneratePress installed — an abandoned experiment, or a planned direction?
