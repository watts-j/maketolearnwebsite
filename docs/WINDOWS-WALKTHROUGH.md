# Windows walkthrough: pulling the site into this repo

Step-by-step instructions for pulling maketolearn.org's code from SiteGround into this repository
from a Windows PC, using only built-in tools (PowerShell's `ssh`/`scp`) plus Git, which you already
have. This replaces `scripts/pull-site.sh`, which needs `rsync` (not available on Windows).

Throughout: type or paste each command into the **same PowerShell window** and press Enter. Where a
command should print something, the expected result is described — check it before moving on.

> **Safety note:** your SSH private key is a password-equivalent. It stays on your PC. Never paste
> it into a chat, email, or website.

## Part A — Save your SSH key and set up the connection (one time)

1. In SiteGround **Site Tools → Devs → SSH Keys Manager**, find your key and open its **⋮ (kebab)
   menu**:
   - **SSH Credentials** — note the **Username** and **Hostname** shown there. The port is **18765**.
   - **Private Key** — a text block starting with `-----BEGIN ... PRIVATE KEY-----`. Copy the
     whole thing, including the BEGIN and END lines.

2. Open PowerShell: press **Start**, type `powershell`, press Enter.

3. Create the folder SSH uses for keys (safe to run even if it already exists):

   ```powershell
   mkdir $HOME\.ssh -Force
   ```

4. Save the private key into a file:

   ```powershell
   notepad $HOME\.ssh\siteground.key
   ```

   Notepad asks to create the file — click **Yes**, paste the key text, **Save**, and close Notepad.

   **Formatting:** the file's entire contents must be exactly the key block — starting with the
   `-----BEGIN OPENSSH PRIVATE KEY-----` line and ending with the `-----END OPENSSH PRIVATE KEY-----`
   line, both included, with nothing before or after. Do **not** include the `Hostname:`,
   `Username:`, or `Port:` lines Site Tools shows alongside the key — those belong in the config
   file in the next step — and no quote marks around anything.

5. Create an SSH shortcut so every later command can just say `siteground`:

   ```powershell
   notepad $HOME\.ssh\config
   ```

   Click **Yes** to create it, paste the block below, replace the two `PASTE-...` values with the
   Hostname and Username from step 1, then save and close:

   ```
   Host siteground
     HostName PASTE-HOSTNAME-HERE
     User PASTE-USERNAME-HERE
     Port 18765
     IdentityFile ~/.ssh/siteground.key
   ```

   Replace **only** the two `PASTE-...` values. Keep `HostName` and `User` spelled exactly as
   shown, with no colons anywhere: SSH's config format is `Keyword value` separated by a space,
   and it calls the username `User`. Pasting Site Tools' `Hostname: ...` / `Username: ...` lines
   as-is produces the `Bad configuration option` error covered in the fixes below.

6. Test the connection:

   ```powershell
   ssh siteground
   ```

   - First time, it asks `Are you sure you want to continue connecting?` — type `yes`, Enter.
   - If you set a passphrase when generating the key, it asks for it (typing is invisible — normal).
   - Success looks like a Linux prompt (something ending in `$`). Type `exit` and press Enter to
     come back to PowerShell.

   **If it fails:**
   - `Bad configuration option: hostname:` (or `username:`) — the config file contains Site
     Tools' credential labels pasted as-is (`Hostname: ssh...`, `Username: u1234-...`). SSH has
     its own spelling: no colons anywhere, and the username keyword is `User`, not `Username`.
     Reopen the file (`notepad $HOME\.ssh\config`) and make its contents exactly the step 5
     block, so the middle lines read like `HostName ssh.example.com` and `User u1234-ab12cd34`.
   - `Could not resolve hostname siteground` — ssh isn't finding the config file. Run
     `dir $HOME\.ssh`: if the file shows up as `config.txt`, Notepad added a hidden extension —
     rename it with `ren $HOME\.ssh\config.txt config` and retry. If there's no config file at
     all, redo step 5. You can always test the connection without the shortcut using the full
     command: `ssh -i $HOME\.ssh\siteground.key -p 18765 YOUR-USERNAME@YOUR-HOSTNAME`
   - `no such identity: ... siteground.key: No such file or directory` — the key file also got a
     hidden `.txt` extension: `ren $HOME\.ssh\siteground.key.txt siteground.key`. (Choosing
     "All files" as Notepad's *Save as type* prevents this in the future.)
   - `Load key ...: invalid format` — the file's contents aren't a clean key. Check the first and
     last lines with `(Get-Content $HOME\.ssh\siteground.key)[0]` and the same with `[-1]` — they
     must be exactly the `-----BEGIN/END OPENSSH PRIVATE KEY-----` lines. If extra text shows up,
     re-copy the key from Site Tools and rebuild the file (step 4). If the lines are right, clear
     Notepad's hidden encoding marker:
     `(Get-Content $HOME\.ssh\siteground.key) | Set-Content $HOME\.ssh\siteground.key -Encoding Ascii`
   - `Permission denied (publickey)` — the key file is wrong/incomplete: redo step 4, making sure
     the BEGIN and END lines are included, and that the username in `config` is exactly right.
   - `WARNING: UNPROTECTED PRIVATE KEY FILE` / `Bad permissions` — lock the file down, then retry:

     ```powershell
     icacls $HOME\.ssh\siteground.key /inheritance:r
     icacls $HOME\.ssh\siteground.key /grant:r "${env:USERNAME}:R"
     ```

## Part B — Get this repo onto your PC

```powershell
cd $HOME\Documents
git clone https://github.com/watts-j/maketolearnwebsite.git
cd maketolearnwebsite
```

If a GitHub sign-in window pops up, complete it in the browser. You should end up in a folder
containing `README.md`, `scripts`, and `docs`.

## Part C — Checkpoint: confirm the site's folder on the server

```powershell
ssh siteground "ls www"
```

Expect to see `maketolearn.org` listed. If your folder is named differently, use your name in place
of `maketolearn.org` in every command below. Then:

```powershell
ssh siteground "ls www/maketolearn.org/public_html/wp-content/themes"
```

The names printed are the installed themes — one of them is the custom theme.

## Part D — Copy the code into the repo

```powershell
mkdir wp-content
scp -r siteground:www/maketolearn.org/public_html/wp-content/themes wp-content/
scp -r siteground:www/maketolearn.org/public_html/wp-content/plugins wp-content/
scp -r siteground:www/maketolearn.org/public_html/wp-content/mu-plugins wp-content/
scp siteground:www/maketolearn.org/public_html/.htaccess docs/site-inventory/htaccess.txt
```

Filenames scroll by as they download; the plugins step can take a few minutes. If the `mu-plugins`
line says `No such file or directory`, that's fine — many sites don't have that folder. We are
deliberately **not** copying media uploads, the database, or WordPress core (see README for why).

## Part E — Collect the environment inventory

This runs WP-CLI on the SiteGround server and downloads the results (WordPress/PHP versions, plugin
list, post types, etc.) into `docs/site-inventory/`:

```powershell
scp scripts/collect-site-info.sh siteground:
ssh siteground "cd www/maketolearn.org/public_html && bash ~/collect-site-info.sh ~/site-inventory"
scp "siteground:site-inventory/*" docs/site-inventory/
```

The middle command should end with `Inventory written to ...`; a few `warn:` lines are okay.
Optional tidy-up of the server afterwards:

```powershell
ssh siteground "rm -r ~/site-inventory ~/collect-site-info.sh"
```

## Part F — Review, commit, push

```powershell
git status
```

Everything listed should live under `wp-content/` or `docs/site-inventory/`. There must be **no**
`wp-config.php`, no `.sql` files, no `uploads/`. Then:

```powershell
git add -A
git commit -m "Import wp-content and site inventory from production"
git push
```

If `git commit` says "Please tell me who you are", run the two `git config` commands it prints
(with your name and email), then run the commit and push again.

## Part G — Optional but smart: a full backup, outside the repo

The repo holds code only. Grab a database backup too, and keep it somewhere private on your PC —
**not** inside the repo folder (it contains user emails and password hashes):

```powershell
ssh siteground "cd www/maketolearn.org/public_html && wp db export ~/db-backup.sql"
scp siteground:db-backup.sql $HOME\Documents\maketolearn-db-backup.sql
ssh siteground "rm ~/db-backup.sql"
```

A full-file backup lives in Site Tools → **Security → Backups**.

## Part H — Hand it to Claude Code

Open a Claude Code session on this repo and say: *"The site files are pushed — run the inventory."*
See README Step 5 for the full suggested prompt.
