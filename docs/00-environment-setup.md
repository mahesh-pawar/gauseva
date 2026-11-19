# 00 — Environment Setup (Windows)

How this machine was prepared for GauSeva. Follow it top to bottom on a new machine.

## Final versions

| Tool | Version | Location |
|---|---|---|
| PHP (CLI) | 8.4.27 (Thread Safe, VS17 x64) | `C:\php84` |
| Composer | 2.5.8 | `C:\Users\HP\AppData\Local\ComposerSetup\bin` |
| PostgreSQL | 18.6 | `C:\Program Files\PostgreSQL\18` |
| pgAdmin | 4 (bundled with PostgreSQL) | `C:\Program Files\PostgreSQL\18\pgAdmin 4` |
| Laravel | 13.43.0 | `C:\Users\HP\My Learning Projects\gauseva` |

> Laravel 13 needs **PHP 8.2+**. XAMPP's PHP (7.4 in `C:\xampp`, 8.1 in `C:\xampp81`) is too old, so a separate PHP was installed. XAMPP stays untouched for old CodeIgniter projects — Apache uses its own PHP internally.

---

## 1. Check what is installed

```powershell
php -v
composer -V
psql --version
```

Find every PHP on the machine and which one is on PATH:

```powershell
(Get-Command php).Source
$env:Path -split ';' | Where-Object { $_ -match 'php' }
```

## 2. Install PHP 8.4 (zip, no installer)

1. Download **VS17 x64 Thread Safe** zip from https://www.php.net/downloads.php?os=windows
   (used: `php-8.4.27-1-Win32-vs17-x64.zip`)
2. Extract:
   ```powershell
   Expand-Archive -Path "$env:USERPROFILE\Downloads\php-8.4.27-1-Win32-vs17-x64.zip" -DestinationPath C:\php84
   ```
3. Create `php.ini`:
   ```powershell
   Copy-Item C:\php84\php.ini-development C:\php84\php.ini
   ```
4. Edit `C:\php84\php.ini`:
   - Set an **absolute** extension dir (relative `"ext"` breaks when running from other folders):
     ```ini
     extension_dir = "C:\php84\ext"
     ```
   - Uncomment (remove `;`) these extensions:
     ```ini
     extension=curl
     extension=fileinfo
     extension=gd
     extension=intl
     extension=mbstring
     extension=openssl
     extension=pdo_pgsql
     extension=pdo_sqlite
     extension=pgsql
     extension=sodium
     extension=sqlite3
     extension=zip
     ```
5. If PHP complains about a missing DLL, install the VC++ Redistributable:
   https://aka.ms/vs/17/release/vc_redist.x64.exe

Verify:

```powershell
C:\php84\php.exe -v
C:\php84\php.exe --ini          # should show C:\php84\php.ini
C:\php84\php.exe -m             # should list pdo_pgsql, pgsql, mbstring ...
```

## 3. Make PHP 8.4 the default `php`

`C:\xampp\php` was in the **User** PATH (not System PATH), so no admin rights were needed.

- GUI: Win → "Edit environment variables for your account" → **Path** → New → `C:\php84` → **Move Up** above `C:\xampp\php`.
- What was actually run (inserts `C:\php84` before `C:\xampp\php`, backup saved first):
  ```powershell
  $old = [Environment]::GetEnvironmentVariable('Path','User')
  Set-Content "$env:TEMP\user-path-backup.txt" $old
  $new = foreach ($p in ($old -split ';')) { if ($p -eq 'C:\xampp\php') { 'C:\php84' }; if ($p -ne 'C:\php84') { $p } }
  [Environment]::SetEnvironmentVariable('Path', ($new -join ';'), 'User')
  ```

**Close and reopen every terminal / VS Code**, then `php -v` → `8.4.27`.
Old PHP is still available as `C:\xampp\php\php.exe`.

## 4. PostgreSQL

Already installed. Check it is running:

```powershell
Get-Service postgresql-x64-18                                        # Status: Running, StartType: Automatic
& "C:\Program Files\PostgreSQL\18\bin\pg_isready.exe" -h 127.0.0.1 -p 5432
```

`psql` is not on PATH — call it with the full path (or add `C:\Program Files\PostgreSQL\18\bin` to PATH):

```powershell
& "C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -h 127.0.0.1
```

### Forgot the `postgres` password? (reset procedure)

`pg_hba.conf` = PostgreSQL's access rules (who can connect, from where, how they authenticate). First matching line wins.

1. Notepad **as Administrator** → open `C:\Program Files\PostgreSQL\18\data\pg_hba.conf`
2. Change METHOD on the first three `all` lines from `scram-sha-256` to `trust` (leave `replication` lines alone):
   ```
   local   all   all                    trust
   host    all   all   127.0.0.1/32     trust
   host    all   all   ::1/128          trust
   ```
3. PowerShell **as Administrator** (normal PowerShell fails with "Cannot open service"):
   ```powershell
   Restart-Service postgresql-x64-18
   ```
4. Set a new password:
   ```powershell
   & "C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -h 127.0.0.1
   ```
   ```sql
   ALTER USER postgres PASSWORD 'new-password';
   \q
   ```
5. **Always revert** the three lines back to `scram-sha-256` and restart the service again.
   (PostgreSQL listens on `0.0.0.0:5432`, so `trust` would let anyone on the network in.)
6. Check passwordless login is blocked again (should fail with `no password supplied`):
   ```powershell
   & "C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -h 127.0.0.1 -w -c "SELECT 1;"
   ```

## 5. Why not XAMPP `htdocs`?

- Laravel has its own dev server: `php artisan serve` → http://localhost:8000, from any folder.
- Laravel's web root is `public/`. Putting the whole project in `htdocs` risks exposing `.env`, `vendor/`, `storage/`.
- If Apache is ever needed: create a VirtualHost whose `DocumentRoot` points at `gauseva\public`.
