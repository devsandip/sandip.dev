# Worklog

Append-only handoff notes. Newest first.

---

## 2026-05-17 — Added a /portfolio page and a top-nav link for it

**Did:**
- Added a new `#/portfolio` page that lists five projects in the same `.data-row` aesthetic used on the About page work history. Order: wndr.ist, blurb, scrask-bot, brewnbanter.app, my-claude-skills.
- Added "portfolio" to the top nav, between "about" and "work".
- Wired the home-page "Apps & hobby projects" link (was a dead `div`) to route to `#/portfolio`.
- Added a small teaser block inside `#/resume` (between Preview and Summary) that points to `#/portfolio` via a `cta-link` button.
- Added CSS for `.portfolio`, `.portfolio-hero`, `.portfolio-summary`, plus reusable bits: `.project-row` clickable title, `.project-links` for repo / live URL, `.inline-code`, `.portfolio-foot`, `.cta-link`.
- Updated the JS router to accept an optional `data-target` for in-page scroll (kept generic — not currently used).

**State now:**
- Five routes: home, about, portfolio, resume (labeled "work"), contact.
- Two of the five projects (wndr.ist and brewnbanter.app) are in private repos. Their rows link to the live URL only, no `repo →` link, to avoid 404s for visitors.
- Public repo links (blurb, scrask-bot, my-claude-skills) are wired through.
- `#/resume` content is intact end to end (hero, pitch with three reasons, PDF mock, summary), with one new "also · I build things" teaser added before the summary.
- Page renders under all four palettes; CSS uses variables throughout.

**Next:**
- Browser-check the deep-link click flow: home → "Apps & hobby projects" → lands on `#/portfolio` cleanly.
- Browser-check `#/resume` → "See the portfolio →" CTA.
- Decide whether to flip wndr.ist or brewnbanter.app public so the repo links can be added.

**Decisions:**
- Two of the project repos are private, so their cards omit the `repo →` link rather than send visitors to a private-repo 404. Live URLs are still surfaced.
- Tags use a mix of language and category (ai, aws, python, typescript, skills) instead of strict language to avoid two adjacent rows both showing "typescript".
- Dropped `sandip.dev` from the project list (was a placeholder during the earlier draft; user's final list of five didn't include it).
- Resume page keeps its original content end to end. Only a small `// also · I build things` block was inserted before the summary, plus a `See the portfolio →` CTA.
- Portfolio is in the top nav between "about" and "work" rather than after "work", because the natural read order is about-me → things-I-built → formal-resume → contact.
