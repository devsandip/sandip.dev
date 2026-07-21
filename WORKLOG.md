# Worklog

Append-only handoff notes. Newest first.

---

## 2026-07-21 — Sentinel case study verified: three layout bugs, the walkthrough, a site-wide header fix

**Did:**
- Found the Sentinel case study already built, committed and live, so ran the verification pass in `sentinel/docs/portfolio/CLAUDE.md` instead of rebuilding it. Console clean, no 404s, all four palettes re-theme the diagram, desktop clean. It failed at 375px.
- Fixed two `1fr` grid blowouts with the same root cause. `1fr` is `minmax(auto, 1fr)`, and the auto floor holds a track open to its widest child's min-content. The article grid collapsed to `1fr` at 880px and resolved to 680px inside a 335px container. The header grid did it for the nav: 294px of links in a 202px track, which overflowed every page on the site by 73px and predated Sentinel entirely.
- Fixed the sticky contents rail painting over the diagram. `.article-rail` is `position: sticky`, so it is a positioned box and paints above in-flow content whatever the DOM order. The bleed figure and all three `.sn-wide` tables start left of the prose column and ran underneath it. Gave them `position: relative`, `z-index: 1` against the rail's `0`, and an opaque `var(--paper)` background.
- Published the walkthrough, `c7pvcOekoXk`, into the reserved slot through `youtube-nocookie`, lazy-loaded, behind a `.sn-video-live` modifier that leaves the placeholder styles intact. Linked it in the rail, under the embed, and on the homepage portfolio row.
- Applied the header fix across `index.html`, `referral.html`, `ludllm.css` and `sentinel.css`. Below 560px the logo drops its wordmark and keeps its mark, link padding tightens at 560 and again at 380. All five links stay visible down to 320px.

**State now:**
- `main` at `42fac4c`, live on Pages. Verified on `https://sandip.dev` at 320, 375, 561 and 1280: `scrollWidth` equals the viewport on the homepage, both case studies and referral.
- The Sentinel page passes every check in its build doc, including the `scrollWidth` one that no page on the site could pass before the header fix.
- `sentinel.css` above the Sentinel banner is still a byte-identical copy of `ludllm.css`, verified by diff. The invariant and the diff command are now written into the build doc.
- Nothing changes above 560px. Wordmark, link padding and the 1200px full-bleed figure re-checked at 1280.

**Next:**
- Decide on the mobile wordmark. Hiding it below 560px was my call, not a request. The alternatives are wrapping the nav to a second line or collapsing to a hamburger, both bigger changes.
- Look at the rail fix on a real phone. The browser pane's scroll kept wedging and screenshots of the tall page came back blank, so paint order was proved by hit-testing with the fix toggled on and off, not by eye.
- Re-read the line under the video. It still opens "Alongside it:", written when the slot was empty and the live app was the walkthrough.

**Decisions:**
- Fixed the header at the declaration rather than layering override blocks on top. Seven `auto 1fr` declarations became `auto minmax(0, 1fr)`, which leaves one place to read instead of two.
- Hid the logo wordmark below 560px rather than collapsing the nav. It closes 89 of the 92px shortfall on its own and keeps all five links reachable, which a hamburger does not.
- Put the shared header fix into `ludllm.css` and `sentinel.css` with the same text at the same position, so the shared prefix stays diffable. Sentinel-only rules still go after the banner.
- The opaque background on the breakout elements is load-bearing, not decoration. Without it the rail's "Live:" and "Code:" lines read through the gaps between table rows and under the figcaption.
- Left the 73px nav overflow alone when I first found it and fixed it only when asked. It hit every page on the site, which is a wider call than the one case study I had been asked to verify.

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
