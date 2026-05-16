# Session state — sandip.dev + blog.sandip.dev

_Last updated: 2026-05-16_

A resume point for the work-in-progress across sandip.dev and the WordPress
blog theme. Drop this in front of a fresh Claude session to pick up where
we left off.

---

## Repos / surfaces

| Surface              | Where it lives                                         | Deploy mechanism                          |
| -------------------- | ------------------------------------------------------ | ----------------------------------------- |
| sandip.dev (site)    | `index.html`, `referral.html`, root of this repo       | GitHub Pages from `main`                  |
| blog.sandip.dev      | WordPress (hosted), theme uploaded manually            | Theme zip at `wordpress-theme/sandip-dev.zip` → WP admin → Appearance → Themes → Add New |

GitHub repo: <https://github.com/devsandip/sandip.dev>

---

## What shipped this session

1. **Homepage social links wired up** (`index.html`)
   - LinkedIn → <https://www.linkedin.com/feed/>
   - Medium → <https://sandipdev.medium.com/>
   - GitHub → <https://github.com/devsandip>
   - Blog → <https://www.blog.sandip.dev/>
   - Apps & hobby projects → still no URL (intentional placeholder)

2. **Contact channels** (`index.html`)
   - LinkedIn, GitHub, Medium channels are now anchors with `target="_blank"` + `rel="noopener"`.
   - X (Twitter) and Phone left as non-link rows pending URLs.

3. **"// currently" sidebar** refreshed:
   - reading: _Humble Pi: When Math Goes Wrong in the Real World_ → linked to Goodreads
   - building: <https://wndr.ist/> + AI OS — linked
   - drinking: Pegasus Coffee Winslow's Revenge (linked) · double shot, no milk
   - thinking: about why most dashboards lie politely
   - avoiding: people

4. **WordPress theme** `wordpress-theme/sandip-dev/`
   - Mirrors sandip.dev: Inter + Fraunces + JetBrains Mono, four palettes (fog default, white, paper, ink), sticky masthead with green status dot, row-based post list.
   - Templates: `index.php`, `single.php`, `page.php`, `archive.php`, `search.php`, `searchform.php`, `404.php`, `comments.php`, plus `header.php` / `footer.php`.
   - `functions.php` exposes `sandip_dev_reading_time()` and a `sandip_dev_theme` filter for palette override; supports `?theme=fog|white|paper|ink` query param.
   - `editor-style.css` so Gutenberg preview matches.
   - Brand assets at `wordpress-theme/sandip-dev/assets/` (logo-mark PNG + white variant, favicon SVG).
   - Pre-zipped: `wordpress-theme/sandip-dev.zip` — uploaded to WordPress; the user activated it.

## Git state

Branch: `claude/pedantic-borg-1fb76c` (worktree at `/Users/sandipdev/Developer/sandip.dev/.claude/worktrees/pedantic-borg-1fb76c`).

Commits ahead of remote `claude/pedantic-borg-1fb76c`:

- `0e7d5b0` — Add WordPress theme matching sandip.dev look & feel
- `8c6fb0c` — Update currently list + link out Humble Pi, wndr.ist, Pegasus coffee (already on `main` via direct push)

Push policy seen in this session:

- Auto-mode classifier **blocks direct push to `main`** by default.
- It also **blocks push of the feature branch** unless explicitly approved.
- Workaround: either ask the user to authorize (they may approve a PR push), or have the user push manually.
- One PR was opened + merged this session: <https://github.com/devsandip/sandip.dev/pull/1> (squash-merged as `0c1503c`).

## Open / not done

- **Apps & hobby projects** link on home — still has no URL.
- **X (Twitter)** and **Phone** rows on Contact — no URLs.
- The WordPress theme commit (`0e7d5b0`) is **local only** — never pushed. If the user wants the theme tracked in GitHub, push or PR it.
- No screenshot.png for the WP theme (optional, only affects Appearance → Themes preview tile).
- No Customizer integration (palette is filter-based, not a UI toggle).
- Comments styling assumes default WP comment-form markup; not verified against the live blog yet.

## Quick re-entry checklist

```
cd /Users/sandipdev/Developer/sandip.dev
git fetch origin
git -C .claude/worktrees/pedantic-borg-1fb76c log --oneline -5
ls .claude/worktrees/pedantic-borg-1fb76c/wordpress-theme/sandip-dev
```

## URLs that came up

- Site: <https://sandip.dev>
- Blog: <https://www.blog.sandip.dev/>
- Repo: <https://github.com/devsandip/sandip.dev>
- wndr.ist: <https://wndr.ist/>
- Pegasus coffee (Winslow's Revenge): <https://pegasuscoffee.com/products/winslows-revenge?_pos=3&_fid=ec0217a2c&_ss=c>
- Humble Pi (Goodreads): <https://www.goodreads.com/book/show/39074550-humble-pi>
