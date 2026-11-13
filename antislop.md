# Anti-Slop Rulebook (Staffly Edition)
Adapted from: https://github.com/miqdadbadjuber/anti-slop.git

Filter framework to prevent generic AI clichés, unearned decorative bloat, and cookie-cutter templates in UI, copywriting, and code.

---

## 1. Hard Gates (Strict Prohibitions)

- **[HG-01] No Cliché AI Gradients**: Never use arbitrary purple-to-blue or multi-color pastel gradient meshes without explicit product branding rationale.
- **[HG-02] No Unearned Glowing Blooms**: Avoid blurry ambient neon glows (`box-shadow: 0 0 20px ...`) floating randomly behind cards or dots. Use crisp, physical borders and deliberate contrast.
- **[HG-03] No Bento-Grid Slop**: Never cram unevenly sized cards into an asymmetrical grid purely for visual novelty. Layouts must strictly follow data density and content hierarchy.
- **[HG-04] No Low-Contrast Text**: Secondary and muted text must maintain a minimum contrast ratio of **4.5:1** against backgrounds (WCAG AA). Never use faint grey (`#6b7280` or lower) on dark surfaces.
- **[HG-05] No Fake / Broken Interactions**: Every button, link, and interactive pill must have an active handler, genuine endpoint, or meaningful state. No placeholder buttons that do nothing.
- **[HG-06] No Fluffy AI Copywriting**: Prohibit robotic buzzwords ("Empower your synergy", "Cutting-edge workforce solutions", "Elevate your journey"). Use clear, concrete, human language.
- **[HG-07] No Meaningless Hover Lifts**: Do not apply `translateY` hover animations to non-clickable containers. Micro-interactions are reserved for actionable triggers.
- **[HG-08] No Obvious Code Comment Noise**: Eliminate comments that simply restate the syntax (e.g. `// Start form`, `// Loop employees`). Comments must explain *why*, not narrate *what*.

---

## 2. Purpose-Gates (Permitted Only With Functional Rationale)

- **[PG-01] Purposeful Transitions**: Micro-interactions must be fast (≤ 200ms), serve spatial orientation, and strictly respect `prefers-reduced-motion`.
- **[PG-02] Semantic Badges**: Pill badges and counters must represent real, changeable system metrics (e.g., headcount, active roles, verified numbers), never decorative stickers.
- **[PG-03] Icons With Labels**: Icons must always accompany descriptive text or include `aria-label` for screen reader accessibility.

---

## 3. Quality Locks (Consistency & Accessibility)

- **[QL-01] Focus States**: All interactive controls (`button`, `input`, `select`, `a`) must have visible `:focus-visible` styling for keyboard navigation.
- **[QL-02] Modular Architecture**: Strict separation of concerns (Presentation in `/pegawai/`, Business Logic in `/proses/`, Configuration in `/config/`).
- **[QL-03] Sanitized Data Pipeline**: Every dynamic database output must pass through `htmlspecialchars()` (`e()` helper) to guarantee zero Cross-Site Scripting (XSS).
- **[QL-04] Secure Query Binding**: 100% of database queries must utilize PDO Prepared Statements with parameterized tokens.

---

## 4. Delivery Gate Verification

| Check | Requirement | Status |
| :--- | :--- | :--- |
| **HG-01 - HG-08** | Zero generic AI visual clichés or fake interactions | **PASS** |
| **WCAG AA Contrast** | All text readable on dark surfaces (≥ 4.5:1) | **PASS** |
| **Logic Integrity** | 100% backend CRUD, session auth, and exports untouched | **PASS** |
| **Keyboard Accessibility**| `:focus-visible` rings present on all interactive inputs | **PASS** |

