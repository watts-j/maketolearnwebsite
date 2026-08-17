# About Sangala page content

WordPress-ready version of the About Sangala page, for **maketolearn.org/sangala/** — the target
of the homepage's "About the Initiative" button. Same conversion treatment as
`site-content/sangala-home/`: title block and cross-link buttons in the body (buttons directly
under the title, matching the homepage), white background via the page's `sangala` body class,
contrast-darkened tan/grey text, photos extracted to real JPEGs, all CSS scoped under
`.sangala-about`.

## To put it live (recommended: over SSH, no hand-pasting)

Hand-pasting into the block editor proved fragile (pastes that land outside the block's code
area get converted and partially discarded). The reliable path installs the exact file content
with WP-CLI and sets the slug at the same time. `about-page.wp.html` is the deploy artifact —
the same content wrapped as a Custom HTML block.

1. Upload images (if not already done) and the content file:

   ```powershell
   scp site-content\sangala-about\images\*.jpg siteground:www/maketolearn.org/public_html/wp-content/uploads/sangala/
   scp site-content\sangala-about\about-page.wp.html siteground:about-page.html
   ```

2. Find the page's ID (look for the row whose slug matches the page you created, e.g. `6735-2`):

   ```powershell
   ssh siteground "cd www/maketolearn.org/public_html && wp post list --post_type=page --fields=ID,post_name,post_title | grep -i sangala"
   ```

   If no page exists yet, create it instead and skip step 3:

   ```powershell
   ssh siteground "cd www/maketolearn.org/public_html && wp post create --post_type=page --post_status=publish --post_title='About Sangala' --post_name=sangala ~/about-page.html && rm ~/about-page.html"
   ```

3. Update the existing page (replace `THE-ID` with the number from step 2). This sets the
   content exactly, retitles it, and fixes the slug to `sangala` in one shot; WordPress
   auto-redirects the old address:

   ```powershell
   ssh siteground "cd www/maketolearn.org/public_html && wp post update THE-ID ~/about-page.html --post_title='About Sangala' --post_name=sangala && rm ~/about-page.html"
   ```

4. Purge the cache (`wp sg purge` below, or the admin-bar button) and verify from the homepage
   button:

   ```powershell
   ssh siteground "cd www/maketolearn.org/public_html && wp sg purge"
   ```

## Manual fallback (hand-paste)

1. **Upload the images** (PowerShell, from the repo root — the `uploads/sangala/` folder already
   exists from the homepage deploy):

   ```powershell
   scp site-content\sangala-about\images\*.jpg siteground:www/maketolearn.org/public_html/wp-content/uploads/sangala/
   ```

2. **Create the page**: wp-admin (administrator account) → Pages → Add New → title
   **About Sangala**.

3. **Set the slug to `sangala`** — this is the step that makes the homepage button work. In the
   editor's right sidebar (Page tab), click the **URL** row and change the ending from
   `about-sangala` to `sangala`, so it reads `maketolearn.org/sangala`.

4. **Paste the content**: open `about-page.html` in Notepad, select all, copy; add a single
   **Custom HTML** block to the page and paste. **Publish**.

5. **Purge SG Cache** (top admin bar), then verify in a private window: the homepage's
   "About the Initiative" button should land here, and this page's "The Sangala Initiative"
   button should lead back.

Diagnostic tip: if the page shows the theme's patterned background instead of white, the slug
isn't exactly `sangala` (the white override keys off that body class).
