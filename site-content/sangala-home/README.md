# Sangala Initiative homepage content

WordPress-ready version of the Sangala Initiative landing page, converted for use as the
maketolearn.org front page:

- The original file's page **header** (eyebrow, title, tagline) and its three **buttons** now
  live in the body as ordinary content — the theme supplies the site's own header and footer.
- **White background** (the theme's patterned background is overridden on the front page only),
  with the eyebrow tan and secondary grey darkened so all text is legible on white.
- The original's ~800 KB of base64-embedded images are extracted to real JPEGs in `images/`
  (the page references them at `/wp-content/uploads/sangala/`).
- All CSS is scoped under `.sangala-home`, so it can't restyle the rest of the site.

## To put it live

1. **Upload the images** (PowerShell, from the repo root):

   ```powershell
   ssh siteground "mkdir -p www/maketolearn.org/public_html/wp-content/uploads/sangala"
   scp site-content\sangala-home\images\*.jpg siteground:www/maketolearn.org/public_html/wp-content/uploads/sangala/
   ```

   (Files copied this way serve at their URLs but won't be listed in wp-admin's Media Library —
   that's normal.)

2. **Paste the content**: open `home-page.html` in Notepad
   (`notepad site-content\sangala-home\home-page.html`), select all, copy. In wp-admin, edit
   the **Home** page, add a single **Custom HTML** block, paste, and click **Update**. Do this
   logged in as the administrator account (the `<style>` block requires an admin's editing
   rights to survive saving).

3. **Purge SG Cache** (top admin bar) and view the site in a private window.

Requires the homepage fix from `docs/HOMEPAGE-FIX.md` to be deployed, with Settings → Reading
pointing at the Home page.

Note: the "About the Initiative" button links to `/sangala/`, which doesn't exist yet — create
a page with the slug `sangala` when ready, or edit the link in the block.
