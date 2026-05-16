# Sandip Dev — WordPress theme

A WordPress theme that mirrors the [sandip.dev](https://sandip.dev) look: Inter + Fraunces + JetBrains Mono, generous whitespace, a sticky header with a status dot, and a row-based post index that echoes the "elsewhere on the internet" list from the home page.

## Install

1. Zip this folder: `zip -r sandip-dev.zip sandip-dev/`
2. In WordPress admin → **Appearance → Themes → Add New → Upload Theme** → pick the zip → **Install** → **Activate**.

Or via SFTP: drop the `sandip-dev/` folder into `wp-content/themes/`.

## Configure

- **Permalinks:** Settings → Permalinks → **Post name** (recommended).
- **Reading:** Settings → Reading → set "Your homepage displays" to **Your latest posts** (or pick a static page).
- **Menus:** Appearance → Menus → create a menu, assign it to the **Primary** location. If you skip this, the theme falls back to a built-in nav linking to sandip.dev sections + "blog" (active).
- **Footer menu:** assign to **Footer** location, or skip — the built-in footer links work fine.

## Palettes

Four palettes ship with the theme, switchable per-request via `?theme=fog|white|paper|ink`, or globally via filter in a child theme / mu-plugin:

```php
add_filter( 'sandip_dev_theme', function () {
    return 'paper'; // fog | white | paper | ink
} );
```

Default is **fog** (matches the sandip.dev default).

## What's included

| Template       | Purpose                                       |
| -------------- | --------------------------------------------- |
| `index.php`    | Blog index with editorial row list            |
| `single.php`   | Single post w/ deck, featured image, post-nav |
| `page.php`     | Static page                                   |
| `archive.php`  | Category / tag / date / author archives       |
| `search.php`   | Search results + form                         |
| `404.php`      | Not-found                                     |
| `comments.php` | Threaded comments + reply form                |
| `searchform.php` | Shared search form                          |
| `editor-style.css` | Gutenberg editor preview                  |

## Notes

- Fonts are loaded from Google Fonts (Inter, JetBrains Mono, Fraunces) — same set as sandip.dev.
- The logo mark expects `assets/logo-mark.png` (and `logo-mark-white.png` for the **ink** dark palette). Replace these to rebrand.
- Reading-time helper: `sandip_dev_reading_time()` — outputs e.g. `4 min read` based on a 220 wpm estimate.
- The site title in the header is hard-printed as `sandip.dev/blog` to match the masthead pattern; change in `header.php` if you fork.
