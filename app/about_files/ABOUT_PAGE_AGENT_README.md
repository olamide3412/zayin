# About Page — Implementation Spec (for AI coding agents)

## Context
This is a **Laravel + Vue.js** project. The Hero section of the `/about` page is
already built. This spec covers the remaining sections — **History**,
**Mission**, and **Core Values** (Core Values does not exist on the page yet
and must be built from scratch) — so their arrangement, style, and animation
match the reference video (`About_page_Recording_2026-08-17_142924.mp4`), while
using this project's own brand colors instead of the reference site's.

Reference site (content/structure only — do not copy its color values):
https://moorfields.ae/about/

## Do not reuse the reference site's colors
The reference site uses a teal/navy/amber palette. **Do not copy those hex
values.** Every component in this spec reads colors from CSS custom
properties (`--brand-primary`, `--brand-accent`, `--brand-surface`,
`--brand-surface-alt`) with neutral placeholder fallbacks. Before shipping:

1. Define the real brand tokens once, globally (e.g. in `resources/css/app.css`
   or a `:root` block loaded on every page):
   ```css
   :root {
     --brand-primary: #___;       /* headings, dark section backgrounds */
     --brand-accent: #___;        /* icons, hover fills, bullets, dots */
     --brand-surface: #___;       /* light section background (values) */
     --brand-surface-alt: #___;   /* light section background (mission) */
   }
   ```
2. Delete the `var(--brand-x, #fallback)` fallback hex codes once the real
   tokens are confirmed, so a missing global variable fails loudly instead of
   silently rendering placeholder colors.
3. Confirm text stays readable against `--brand-accent` when a value card's
   hover fill is applied (see Core Values below) — swap text color per-state
   if the real accent is dark.

## Page structure (top to bottom)
1. **Hero** — already built, not in scope.
2. **Intro** — short paragraph(s) introducing the hospital/company (plain
   text block, no animation observed beyond a simple fade-in with the rest of
   the page load).
3. **History** — dark full-bleed section, horizontal timeline.
4. **Mission** — light section, two columns: checklist text (left) + image
   with a decorative striped accent (right).
5. **Core Values** — light section, 2-column × 3-row grid of value cards.
   **This is the section that's missing and needs to be added.**
6. Footer — already built, not in scope.

Components for sections 3–5 are provided in this bundle:
`HistoryTimeline.vue`, `OurMission.vue`, `CoreValues.vue`. They're framework-
correct Vue 3 (`<script setup>`, Composition API) with scoped CSS and no
external animation library — just `IntersectionObserver` for scroll reveals,
so there's no new dependency to install. Adjust markup/classes as needed to
match this project's existing component conventions (props, slots, a shared
`Section.vue` wrapper, etc.) — treat these as a functional reference
implementation, not a drop-in black box.

## Section-by-section behavior (from the reference video)

### History (timeline)
- Full-width dark section (`--brand-primary` background, white text).
- A horizontal center line runs through the section; each milestone alternates
  above/below the line, connected by a short vertical stalk and a circular dot
  marker on the line.
- Each milestone shows a year, a small photo, and 1–2 short bullet points.
- Reveal animation: milestones fade up/down into place as the section scrolls
  into view (see `HistoryTimeline.vue` — staggered ~120ms per item).
- On narrow viewports this needs a decision: the reference site doesn't show
  a mobile breakpoint in the recording. Recommend collapsing to a single
  vertical stacked timeline below ~700px rather than horizontal scroll —
  confirm with design before shipping.

### Mission
- Light section (`--brand-surface-alt`), two columns on desktop, stacked on
  mobile.
- Left: heading, one intro line, then a checklist (chevron-style bullet, not
  a plain disc).
- Right: a photo with a small diagonal-stripe decorative accent overlapping
  its bottom-right corner (recreated with a CSS `repeating-linear-gradient`
  in `OurMission.vue` — no image asset needed).
- Reveal animation: both columns fade up on scroll-into-view, media column
  slightly delayed after the text column.

### Core Values (new section)
- Light section (`--brand-surface`), heading "Our Core Values" + one-line
  subtitle.
- 2-column × 3-row card grid on desktop, single column on mobile
  (breakpoint used: 720px).
- Each card: white background, rounded corners, circular icon badge, bold
  title, short description, and a **left accent bar** (~6px) in
  `--brand-accent`.
- **Hover/focus state**: the accent bar expands to fill the entire card
  background (this was the standout interaction in the recording — confirm
  it reads as an editorial highlight, not a low-contrast trap, once real
  brand colors are in).
- **Scroll-in animation**: cards fade up with a staggered delay (~90ms per
  card) the first time the section enters the viewport; it does not replay
  on scroll-out/scroll-in again.
- Content is 6 values. Placeholder copy is included in `CoreValues.vue`,
  paraphrased for length/structure only — **replace with this project's own
  approved value statements before shipping**, don't reuse the reference
  site's wording verbatim.

## Accessibility / quality bar
- All animations are wrapped in `@media (prefers-reduced-motion: reduce)` —
  keep this when adapting the components.
- Hover interactions are duplicated on `:focus-within` so keyboard users get
  the same affordance.
- Icons are decorative (`aria-hidden="true"`); the card title carries the
  meaning, not the icon.
- Run a contrast check on final `--brand-accent` against both white card text
  and the hover-fill state.

## Open questions for a human to confirm before merge
- Real hex values for `--brand-primary` / `--brand-accent` / the two surface
  tones.
- Real photography for the History milestones and Mission image (recording
  shows placeholders from the reference hospital's archive — not reusable).
- Final, approved wording for the six Core Values (current copy is
  placeholder).
- Mobile behavior for the History timeline (horizontal scroll vs. stacked).
