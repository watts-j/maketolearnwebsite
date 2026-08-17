# About Sangala page content

WordPress-ready version of the About Sangala page, for **maketolearn.org/sangala/** — the target
of the homepage's "About the Initiative" button. Same conversion treatment as
`site-content/sangala-home/`: title block and cross-link buttons in the body (buttons directly
under the title, matching the homepage), white background via the page's `sangala` body class,
contrast-darkened tan/grey text, photos extracted to real JPEGs, all CSS scoped under
`.sangala-about`.

## To put it live

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
