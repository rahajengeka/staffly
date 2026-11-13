# Staffly — Design Direction & System (DESIGN.md)
Companion to `antislop.md` • Provides authentic visual identity and intent.

---

## 1. Product Identity & Soul

- **Domain**: Employee Record Management & HR Directory.
- **Aesthetic Direction**: High-Contrast Obsidian Editorial.
- **Tone**: Professional, crisp, decisive, and focused on data density without visual clutter.

---

## 2. Color Palette & Functional Intent

| Role | Hex Code | Purpose |
| :--- | :--- | :--- |
| **Surface Black** | `#0c0d0e` | Main page background; deep black reducing screen glare |
| **Panel Surface** | `#141518` | Elevated container surface; creates clean contrast separation |
| **Card Surface** | `#1b1d22` | Form cards, dropdowns, and input containers |
| **Border Line** | `#272a30` | Structural dividers and container outlines |
| **Primary Accent** | `#84f03a` | High-visibility neon green used strictly for key actions & active indicators |
| **Accent Dark** | `#6ccd26` | Hover state for primary buttons |
| **Text Primary** | `#ffffff` / `#f8fafc` | Headers, names, and primary metrics |
| **Text Secondary**| `#94a3b8` | Subtitles, labels, and metadata (WCAG AA compliant) |

---

## 3. Typography & Hierarchy

- **Primary Typeface**: `Plus Jakarta Sans` (400, 500, 600, 700, 800) for readable, modern interface typography.
- **Tabular / Monospace Typeface**: `Space Grotesk` with `font-variant-numeric: tabular-nums` for NIPs, numeric counters, and metrics to ensure stable alignment.
- **Scale**:
  - H1 / Hero: 2.25rem - 2.5rem, bold, line-height 1.2
  - H2 / Section: 1.25rem, bold, line-height 1.3
  - Body: 0.875rem - 0.95rem, regular, line-height 1.5
  - Caption / Meta: 0.75rem - 0.8rem, medium, uppercase or muted

---

## 4. Interaction Principles

1. **Crisp Boundaries**: Prefer clean, subtle 1px borders (`#272a30`) over muddy drop shadows or glowing blurs.
2. **Intentional Feedback**: Micro-interactions provide instant tactile feedback (0.15s - 0.2s duration) and respect `prefers-reduced-motion`.
3. **Actionable Density**: Every element on screen directly answers an HR workflow question (who, role, contact, status).

