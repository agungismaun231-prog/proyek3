---
version: 1.0.0
name: jasaku-web-design-system
description: Reverse-engineered design system specification for the JASAKU Laravel web application. Features a dual-theme (dark-first by default with an explicit light mode override) engineering-centric SaaS aesthetic. Characterized by high-contrast typography, deep slate canvas surfaces, hair-line border definitions, luminous cobalt and sky-blue interactive accents, and 4px/8px rhythmic geometric spacing.

colors:
  # Primitive Palette: Brand Blues
  blue-50: "#eff6ff"
  blue-100: "#dbeafe"
  blue-300: "#93c5fd"
  blue-400: "#60a5fa"
  blue-500: "#3b82f6"
  blue-600: "#2563eb"
  blue-700: "#1d4ed8"

  # Primitive Palette: Semantics
  success: "#10b981"
  warning: "#f59e0b"
  danger: "#ef4444"
  info: "#06b6d4"
  indigo: "#6366f1"
  indigo-accent: "#818cf8"

  # Semantic Palette: Dark Theme (Default)
  dark:
    bg-base: "#0b1120"
    bg-surface: "#111827"
    bg-elevated: "#1a2234"
    bg-card: "rgba(26, 34, 52, 0.9)"
    border: "rgba(255, 255, 255, 0.08)"
    border-subtle: "rgba(255, 255, 255, 0.05)"
    text-primary: "#f1f5f9"
    text-secondary: "#94a3b8"
    text-muted: "#64748b"
    shadow-sm: "0 1px 4px rgba(0, 0, 0, 0.3)"
    shadow-md: "0 4px 20px rgba(0, 0, 0, 0.4)"
    shadow-lg: "0 8px 40px rgba(0, 0, 0, 0.5)"
    shadow-hero: "0 20px 60px rgba(0, 0, 0, 0.5)"

  # Semantic Palette: Light Theme Override (.theme-light)
  light:
    bg-base: "#f1f5f9"
    bg-surface: "#ffffff"
    bg-elevated: "#f8fafc"
    bg-card: "#ffffff"
    border: "#e2e8f0"
    border-subtle: "#cbd5e1"
    text-primary: "#0f172a"
    text-secondary: "#475569"
    text-muted: "#94a3b8"
    shadow-sm: "0 1px 4px rgba(0, 0, 0, 0.08)"
    shadow-md: "0 4px 20px rgba(0, 0, 0, 0.1)"
    shadow-lg: "0 8px 40px rgba(0, 0, 0, 0.12)"
    shadow-hero: "0 20px 60px rgba(0, 0, 0, 0.08)"

typography:
  font-family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
  hero-display:
    fontFamily: "{typography.font-family}"
    fontSize: "clamp(44px, 6vw, 80px)"
    fontWeight: 900
    lineHeight: 1.05
    letterSpacing: "-3px"
  section-display:
    fontFamily: "{typography.font-family}"
    fontSize: "clamp(32px, 4vw, 48px)"
    fontWeight: 800
    lineHeight: 1.1
    letterSpacing: "-1.5px"
  page-title:
    fontFamily: "{typography.font-family}"
    fontSize: "24px"
    fontWeight: 800
    lineHeight: 1.2
    letterSpacing: "-0.5px"
  kpi-value:
    fontFamily: "{typography.font-family}"
    fontSize: "28px"
    fontWeight: 800
    lineHeight: 1.1
    letterSpacing: "-1px"
  revenue-value:
    fontFamily: "{typography.font-family}"
    fontSize: "32px"
    fontWeight: 800
    lineHeight: 1.1
    letterSpacing: "-1px"
  card-title:
    fontFamily: "{typography.font-family}"
    fontSize: "16px"
    fontWeight: 700
    lineHeight: 1.3
    letterSpacing: "-0.3px"
  body:
    fontFamily: "{typography.font-family}"
    fontSize: "14px"
    fontWeight: 400
    lineHeight: 1.6
    letterSpacing: "normal"
  body-dense:
    fontFamily: "{typography.font-family}"
    fontSize: "13.5px"
    fontWeight: 500
    lineHeight: 1.6
    letterSpacing: "normal"
  label-eyebrow:
    fontFamily: "{typography.font-family}"
    fontSize: "11px"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "0.8px"
    textTransform: "uppercase"
  fine-print:
    fontFamily: "{typography.font-family}"
    fontSize: "12px"
    fontWeight: 500
    lineHeight: 1.4
    letterSpacing: "normal"
  badge:
    fontFamily: "{typography.font-family}"
    fontSize: "11px"
    fontWeight: 600
    lineHeight: 1.0
    letterSpacing: "0.2px"

rounded:
  none: "0px"
  sm: "6px"
  md: "10px"
  lg: "14px"
  xl: "20px"
  2xl: "24px"
  pill: "20px"
  full: "50%"

spacing:
  2xs: "4px"
  xs: "8px"
  sm: "12px"
  md: "16px"
  lg: "20px"
  xl: "24px"
  2xl: "28px"
  3xl: "32px"
  4xl: "40px"
  5xl: "48px"
  6xl: "64px"
  7xl: "96px"

transitions:
  default: "all 0.2s cubic-bezier(0.4, 0, 0.2, 1)"
  smooth-dash: "all 0.25s cubic-bezier(0.22, 0.61, 0.36, 1)"

components:
  # Navigation
  sidebar:
    width: "260px"
    height: "100vh"
    backgroundColor: "{colors.dark.bg-surface}"
    borderColor: "{colors.dark.border}"
    zIndex: 1050
    transition: "{transitions.default}"

  sidebar-brand:
    height: "60px"
    padding: "0 16px"
    borderColor: "{colors.dark.border}"

  brand-logo:
    width: "34px"
    height: "34px"
    background: "linear-gradient(135deg, {colors.blue-600}, {colors.blue-400})"
    rounded: "{rounded.md}"
    boxShadow: "0 2px 10px rgba(37, 99, 235, 0.35)"

  sidebar-nav-label:
    typography: "{typography.label-eyebrow}"
    textColor: "{colors.dark.text-muted}"
    padding: "14px 20px 5px"

  sidebar-link:
    padding: "9px 16px 9px 18px"
    margin: "1px 8px"
    rounded: "{rounded.md}"
    typography: "{typography.body-dense}"
    textColor: "{colors.dark.text-secondary}"
    backgroundColor: "transparent"
    borderColor: "transparent"
    transition: "{transitions.default}"

  sidebar-link-hover:
    backgroundColor: "{colors.dark.bg-elevated}"
    textColor: "{colors.dark.text-primary}"
    borderColor: "{colors.dark.border}"

  sidebar-link-active:
    background: "linear-gradient(135deg, rgba(37, 99, 235, 0.15), rgba(96, 165, 250, 0.07))"
    textColor: "{colors.blue-400}"
    borderColor: "rgba(37, 99, 235, 0.2)"
    indicatorColor: "{colors.blue-500}"

  topbar:
    height: "60px"
    padding: "0 24px"
    backgroundColor: "{colors.dark.bg-surface}"
    borderColor: "{colors.dark.border}"
    zIndex: 1040

  user-avatar:
    width: "28px"
    height: "28px"
    background: "linear-gradient(135deg, {colors.blue-600}, {colors.blue-400})"
    rounded: "{rounded.full}"
    textColor: "#ffffff"
    typography: "{typography.badge}"

  # Buttons
  button-primary:
    background: "linear-gradient(135deg, {colors.blue-600}, {colors.blue-500})"
    textColor: "#ffffff"
    typography: "{typography.body-dense}"
    fontWeight: 600
    rounded: "{rounded.sm}"
    padding: "9px 16px"
    border: "none"
    boxShadow: "0 2px 8px rgba(37, 99, 235, 0.3)"
    transition: "{transitions.default}"

  button-primary-hover:
    background: "linear-gradient(135deg, {colors.blue-700}, {colors.blue-600})"
    transform: "translateY(-1px)"
    boxShadow: "0 4px 14px rgba(37, 99, 235, 0.4)"

  button-outline-primary:
    backgroundColor: "transparent"
    textColor: "{colors.blue-400}"
    borderColor: "rgba(37, 99, 235, 0.4)"
    rounded: "{rounded.sm}"
    padding: "9px 16px"
    transition: "{transitions.default}"

  button-outline-primary-hover:
    backgroundColor: "rgba(37, 99, 235, 0.1)"
    borderColor: "{colors.blue-500}"
    textColor: "{colors.blue-400}"

  button-danger:
    backgroundColor: "rgba(239, 68, 68, 0.9)"
    textColor: "#ffffff"
    rounded: "{rounded.sm}"
    padding: "9px 16px"
    border: "none"

  button-danger-hover:
    backgroundColor: "{colors.danger}"

  button-sm:
    padding: "5px 12px"
    fontSize: "12px"
    rounded: "{rounded.sm}"

  # Cards & Containers
  card:
    backgroundColor: "{colors.dark.bg-card}"
    borderColor: "{colors.dark.border}"
    rounded: "{rounded.lg}"
    boxShadow: "{colors.dark.shadow-sm}"
    transition: "{transitions.default}"

  card-hover:
    boxShadow: "{colors.dark.shadow-md}"

  card-header:
    backgroundColor: "transparent"
    borderColor: "{colors.dark.border}"
    padding: "16px 20px"
    typography: "{typography.body}"
    fontWeight: 600
    textColor: "{colors.dark.text-primary}"

  card-body:
    padding: "20px"

  kpi-card:
    backgroundColor: "{colors.dark.bg-card}"
    borderColor: "{colors.dark.border}"
    rounded: "{rounded.lg}"
    padding: "22px 20px 18px"
    boxShadow: "{colors.dark.shadow-sm}"
    accentBarHeight: "3px"
    transition: "{transitions.smooth-dash}"

  kpi-card-hover:
    transform: "translateY(-3px)"
    boxShadow: "{colors.dark.shadow-md}"

  stats-card:
    backgroundColor: "{colors.dark.bg-card}"
    borderColor: "{colors.dark.border}"
    rounded: "{rounded.lg}"
    padding: "20px"
    boxShadow: "{colors.dark.shadow-sm}"
    transition: "{transitions.default}"

  stats-card-hover:
    transform: "translateY(-2px)"
    boxShadow: "{colors.dark.shadow-md}"

  # Tables
  table-header-cell:
    backgroundColor: "{colors.dark.bg-elevated}"
    textColor: "{colors.dark.text-muted}"
    typography: "{typography.label-eyebrow}"
    padding: "12px 16px"
    borderColor: "{colors.dark.border}"

  table-body-cell:
    textColor: "{colors.dark.text-secondary}"
    typography: "{typography.body-dense}"
    padding: "12px 16px"
    borderColor: "{colors.dark.border}"

  table-row-hover:
    backgroundColor: "{colors.dark.bg-elevated}"

  # Badges
  badge-pending:
    backgroundColor: "rgba(245, 158, 11, 0.15)"
    textColor: "{colors.warning}"
    borderColor: "rgba(245, 158, 11, 0.3)"
    rounded: "{rounded.pill}"
    padding: "3px 9px"
    typography: "{typography.badge}"

  badge-confirmed:
    backgroundColor: "rgba(6, 182, 212, 0.15)"
    textColor: "{colors.info}"
    borderColor: "rgba(6, 182, 212, 0.3)"
    rounded: "{rounded.pill}"
    padding: "3px 9px"
    typography: "{typography.badge}"

  badge-in-progress:
    backgroundColor: "rgba(59, 130, 246, 0.15)"
    textColor: "{colors.blue-400}"
    borderColor: "rgba(59, 130, 246, 0.3)"
    rounded: "{rounded.pill}"
    padding: "3px 9px"
    typography: "{typography.badge}"

  badge-completed:
    backgroundColor: "rgba(16, 185, 129, 0.15)"
    textColor: "{colors.success}"
    borderColor: "rgba(16, 185, 129, 0.3)"
    rounded: "{rounded.pill}"
    padding: "3px 9px"
    typography: "{typography.badge}"

  badge-cancelled:
    backgroundColor: "rgba(239, 68, 68, 0.15)"
    textColor: "{colors.danger}"
    borderColor: "rgba(239, 68, 68, 0.3)"
    rounded: "{rounded.pill}"
    padding: "3px 9px"
    typography: "{typography.badge}"

  # Form Inputs
  form-control:
    backgroundColor: "{colors.dark.bg-elevated}"
    borderColor: "{colors.dark.border}"
    textColor: "{colors.dark.text-primary}"
    placeholderColor: "{colors.dark.text-muted}"
    rounded: "{rounded.sm}"
    padding: "9px 14px"
    typography: "{typography.body-dense}"
    transition: "{transitions.default}"

  form-control-focus:
    borderColor: "{colors.blue-500}"
    boxShadow: "0 0 0 3px rgba(37, 99, 235, 0.15)"
    backgroundColor: "{colors.dark.bg-elevated}"

  form-label:
    typography: "{typography.label-eyebrow}"
    textColor: "{colors.dark.text-secondary}"
    marginBottom: "6px"

  # Alerts
  alert-danger:
    backgroundColor: "rgba(239, 68, 68, 0.1)"
    borderColor: "rgba(239, 68, 68, 0.25)"
    borderLeftWidth: "4px"
    textColor: "#fca5a5"
    rounded: "{rounded.md}"
    padding: "12px 16px"

  alert-warning:
    backgroundColor: "rgba(245, 158, 11, 0.1)"
    borderColor: "rgba(245, 158, 11, 0.25)"
    borderLeftWidth: "4px"
    textColor: "{colors.warning}"
    rounded: "{rounded.md}"
    padding: "12px 16px"

  alert-success:
    backgroundColor: "rgba(16, 185, 129, 0.1)"
    borderColor: "rgba(16, 185, 129, 0.25)"
    borderLeftWidth: "4px"
    textColor: "{colors.success}"
    rounded: "{rounded.md}"
    padding: "12px 16px"

  # Modals
  modal-content:
    backgroundColor: "{colors.dark.bg-surface}"
    borderColor: "{colors.dark.border}"
    rounded: "{rounded.lg}"
    boxShadow: "{colors.dark.shadow-lg}"

  modal-header:
    borderColor: "{colors.dark.border}"
    padding: "16px 20px"
---

## Overview

The JASAKU web frontend is a modern, high-density service management and operational dashboard built with Laravel Blade and Bootstrap 5 utilities. It is engineered with a **dark-first visual hierarchy** (`#0b1120` canvas), contrasted with luminous royal blue and sky blue accent strokes (`#2563eb` / `#60a5fa`). 

An explicit, class-driven light mode override (`body.theme-light`) is built into the core layout, swapping deep navy panels for pristine `#ffffff` surfaces and `#e2e8f0` structural dividers while preserving interactive brand identities.

Visual structure is established through subtle 1px translucent borders (`rgba(255,255,255,0.08)`) and soft, high-radius diffused shadows rather than heavy drop-shadows or solid opaque containers.

---

## Colors

### Brand & Accent

The primary brand identity is anchored by a high-energy blue ramp combined with specific semantic roles:

* **Brand Primary (`{colors.blue-600}` — `#2563eb`)**: Primary interaction trigger, CTA gradients, button active states, and focused controls.
* **Brand Bright (`{colors.blue-400}` — `#60a5fa`)**: Active navigation text, icons, logo accents, and dark-mode highlight links.
* **Brand Deep (`{colors.blue-700}` — `#1d4ed8`)**: Primary button hover gradient stop and hero banner background anchor.
* **Brand Soft (`{colors.blue-500}` — `#3b82f6`)**: Active navigation indicator tick and form input focus stroke.
* **Cyan Info (`{colors.info}` — `#06b6d4`)**: Confirmed status indicators and secondary diagnostic metrics.
* **Emerald Success (`{colors.success}` — `#10b981`)**: Completed tasks, paid invoices, and positive revenue metrics.
* **Amber Warning (`{colors.warning}` — `#f59e0b`)**: Pending bookings, upcoming invoice warnings, and attention chips.
* **Rose Danger (`{colors.danger}` — `#ef4444`)**: Overdue invoices, cancellations, rejection actions, and destructive confirmations.
* **Indigo Accent (`{colors.indigo}` — `#6366f1`)**: Secondary statistical category bar in KPI analytics.

Gradients are strictly applied to primary action surfaces and brand badges:
* `linear-gradient(135deg, #2563eb, #3b82f6)`: Primary buttons and CTA elements.
* `linear-gradient(135deg, #2563eb, #60a5fa)`: Brand mark containers and user avatars.
* `linear-gradient(90deg, <accent>, <tint>)`: KPI card bottom status underline trims.

### Surface

Layering in dark mode uses three discrete steps of luminance:
* **Canvas Base (`{colors.dark.bg-base}` — `#0b1120`)**: Root page canvas, background behind the layout grid.
* **Surface (`{colors.dark.bg-surface}` — `#111827`)**: Fixed sidebar, fixed topbar, modal backdrops, and landing section wrappers.
* **Elevated (`{colors.dark.bg-elevated}` — `#1a2234`)**: Form inputs, table headers, hover states, dropdown menus, and nested interactive widgets.
* **Card Container (`{colors.dark.bg-card}` — `rgba(26, 34, 52, 0.9)`)**: Semi-translucent content containers with subtle backdrop grounding.

In light mode (`body.theme-light`):
* **Canvas Base (`{colors.light.bg-base}` — `#f1f5f9`)**: Cool light grey background.
* **Surface & Cards (`{colors.light.bg-surface}` — `#ffffff`)**: Pure white containers.
* **Elevated (`{colors.light.bg-elevated}` — `#f8fafc`)**: Off-white table headers and hover backgrounds.

### Text

Typography colors adhere to strict contrast steps:
* **Text Primary (`{colors.dark.text-primary}` — `#f1f5f9` / Light: `#0f172a`)**: Page titles, metric values, table headers, and form inputs.
* **Text Secondary (`{colors.dark.text-secondary}` — `#94a3b8` / Light: `#475569`)**: Body paragraphs, descriptions, table body content, and inactive navigation links.
* **Text Muted (`{colors.dark.text-muted}` — `#64748b` / Light: `#94a3b8`)**: Uppercase category labels, timestamps, placeholder text, and footer links.

### Borders

* **Dark Mode Border (`{colors.dark.border}` — `rgba(255, 255, 255, 0.08)`)**: 1px hair-line divider for all cards, sidebars, tables, and inputs.
* **Light Mode Border (`{colors.light.border}` — `#e2e8f0`)**: Standard crisp border.

---

## Typography

### Font Family
The system utilizes **Inter** via Google Fonts with complete fallback to native system fonts:
```css
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
```
Antialiasing (`-webkit-font-smoothing: antialiased`) is mandatory across all viewports.

### Hierarchy

| Role | Font Size | Weight | Line Height | Letter Spacing | Case | Context |
|---|---|---|---|---|---|---|
| `hero-display` | `clamp(44px, 6vw, 80px)` | 900 | 1.05 | -3px | Normal | Landing page hero headline |
| `section-display`| `clamp(32px, 4vw, 48px)` | 800 | 1.1 | -1.5px | Normal | Marketing feature sections |
| `revenue-value` | 32px | 800 | 1.1 | -1px | Normal | Financial summary cards |
| `kpi-value` | 28px | 800 | 1.1 | -1px | Normal | Admin & technician stat cards |
| `page-title` | 24px (Mobile: 18px) | 800 | 1.2 | -0.5px | Normal | H1 on admin/customer dashboard |
| `card-title` | 16px–17px | 700 | 1.3 | -0.3px | Normal | Card headers and service cards |
| `body` | 14px | 400 | 1.6 | 0 | Normal | Default reading text |
| `body-dense` | 13.5px | 500 | 1.6 | 0 | Normal | Tables, form controls, and links |
| `label-eyebrow` | 11px | 700 | 1.2 | 0.8px | UPPERCASE | Stat labels, form labels, headers |
| `fine-print` | 12px–13px | 500 | 1.4 | 0 | Normal | Timestamps, footers, helper notes |
| `badge` | 11px | 600 | 1.0 | 0.2px | Normal | Status indicators |

### Principles
1. **Tight Letter Spacing on Large Scales**: Negative letter-spacing (`-0.5px` to `-3px`) is applied to all titles and metric figures above 16px to prevent optical drift.
2. **Uppercase Micro-Labels**: Secondary operational labels, table headers, and section metadata are set in `10px`–`11px` uppercase with expanded tracking (`0.8px` to `1.2px`) and muted color.
3. **Numerals as Anchors**: Dashboard figures use 800 weight with tight tracking to act as the primary visual anchor in cards.

### Font Substitutes
When offline or in environments where Google Fonts cannot load:
- Primary: `-apple-system` (San Francisco)
- Windows: `BlinkMacSystemFont` / `Segoe UI`
- Linux: `Roboto` / `sans-serif`

---

## Layout

### Spacing System
The layout is governed by an **8px base grid** with a 4px fine-tuning step:
* `4px` (`{spacing.2xs}`): Inner badge padding, tight icon margins, divider margins.
* `8px` (`{spacing.xs}`): Button icon gaps, card label margins, sidebar link margins.
* `12px` (`{spacing.sm}`): Page title icon gap, input inner vertical padding.
* `16px` (`{spacing.md}`): Standard table cell padding, small card padding.
* `20px` (`{spacing.lg}`): Standard card body padding, stats card padding, grid gaps.
* `28px` (`{spacing.2xl}`): Main layout page gutters, page header bottom margin.
* `40px` (`{spacing.4xl}`): Marketing navigation horizontal padding, auth card padding.
* `64px`–`96px` (`{spacing.6xl}`–`{spacing.7xl}`): Section vertical padding on public landing.

### Grid & Container
* **Dashboard Shell**:
  - Sidebar Width: `260px` fixed.
  - Topbar Height: `60px` fixed (`padding: 0 24px`).
  - Page Content Area: `margin-left: 260px`, `margin-top: 60px`, `padding: 28px 28px 48px`.
* **Auth Layout Container**:
  - Fixed 2-column grid (`1fr 420px`), max-width `1100px`, `gap: 80px`.
* **Marketing Landing Container**:
  - Fluid with `padding: 0 24px` to `0 40px`, max-width bounded at sections (`~1200px`).

### Whitespace Philosophy
The interface prioritizes high information density without visual clutter. Empty space around numbers in cards is deliberately compressed (vertical margin `2px`–`6px`) to keep data sets compact and scannable.

---

## Elevation & Depth

Hierarchy is established through border luminescence and soft shadows rather than sharp layer offsets:

| Elevation Level | Treatment | Application |
|---|---|---|
| **Level 0 (Canvas)** | Flat, `#0b1120`, no shadow | Background page canvas |
| **Level 1 (Surface)** | 1px border `rgba(255,255,255,0.08)` | Fixed sidebar, fixed topbar |
| **Level 2 (Raised)** | Border + `0 1px 4px rgba(0,0,0,0.3)` | Standard cards, stat cards, tables |
| **Level 3 (Hover)** | `transform: translateY(-2px)` + `0 4px 20px rgba(0,0,0,0.4)` | Card hovers, KPI hovers, button hovers |
| **Level 4 (Floating)** | Border + `0 8px 40px rgba(0,0,0,0.5)` | Dropdown menus, modal dialogs, login cards |
| **Glow Overlay** | `box-shadow: 0 2px 10px rgba(37,99,235,0.35)` | Brand logos, primary action buttons |

---

## Shapes

### Border Radius Scale

* **`radius-sm` (6px)**:
  - Form inputs (`.form-control`, `.form-select`)
  - Standard action buttons (`.btn`, `.btn-primary`, `.btn-danger`, `.btn-sm`)
  - Dropdown items
  - Feature icons in auth
* **`radius-md` (10px)**:
  - Navigation links (`.s-link`)
  - Brand logo icon container (`.brand-logo`)
  - Icon containers in KPI cards (`.dash-kpi-icon`)
  - Alert boxes (`.alert`)
  - Date chips (`.dash-date`)
* **`radius-lg` (14px)**:
  - Content cards (`.card`, `.stats-card`, `.dash-kpi`)
  - Table wrappers (`.table-responsive`)
  - Modal window content (`.modal-content`)
  - User dropdown menu panel
* **`radius-xl` / `2xl` (20px / 24px)**:
  - Auth container card (`.login-card`)
  - Dashboard service carousel (`.slider-container`)
  - Landing CTA banner (`.cta-section`)
* **`pill` (20px / 9999px)**:
  - Status badges (`.badge`)
  - Hero notification badge (`.hero-badge`)
  - Active carousel indicators
* **`full` (50%)**:
  - User avatar (`.u-avatar`)
  - Notification indicators (`.notif-dot`)
  - Carousel round control buttons

### Image / Media Geometry
* Carousel slider banners: fixed height `320px` (mobile `360px`), radius `24px` with overflow hidden.
* Service illustrations: square icons `40px`–`52px` with `10px` or `14px` radius.
* Profile avatars: circular 1:1 aspect ratio (`28px` navbar, `36px`–`48px` profile).

---

## Components

### Navigation

#### `sidebar`
Fixed full-height vertical panel docked on the left.
- Width: `260px`.
- Background: `{colors.dark.bg-surface}`.
- Right Border: `1px solid {colors.dark.border}`.
- Header: 60px height containing the brand mark and theme toggle.

#### `sidebar-link`
Interactive navigation anchor item.
- Padding: `9px 16px 9px 18px`.
- Radius: `10px` (`{rounded.md}`).
- Inactive: Text `{colors.dark.text-secondary}`, background transparent.
- Hover: Background `{colors.dark.bg-elevated}`, text `{colors.dark.text-primary}`.
- Active: Background `linear-gradient(135deg, rgba(37,99,235,.15), rgba(96,165,250,.07))`, text `{colors.blue-400}`, border `rgba(37,99,235,.2)`.
- Active Indicator: Vertical left bar `3px x 18px` with color `{colors.blue-500}` and radius `0 3px 3px 0`.

#### `topbar`
Fixed horizontal header docked to the right of the sidebar.
- Height: `60px`.
- Padding: `0 24px`.
- Left coordinate: `260px` (desktop) / `0` (mobile).
- Contains page title, notification bell with unread dot, and user profile dropdown.

### Buttons

#### `button-primary`
Primary workflow trigger (e.g. submit order, save changes).
- Background: `linear-gradient(135deg, #2563eb, #3b82f6)`.
- Text: `#ffffff`.
- Radius: `6px` (`{rounded.sm}`).
- Box Shadow: `0 2px 8px rgba(37,99,235,0.3)`.
- Hover: `linear-gradient(135deg, #1d4ed8, #2563eb)`, `transform: translateY(-1px)`, shadow `0 4px 14px rgba(37,99,235,0.4)`.

#### `button-outline-primary`
Secondary trigger or cancel alternative.
- Background: Transparent.
- Border: `1px solid rgba(37, 99, 235, 0.4)`.
- Text: `{colors.blue-400}`.
- Hover: Background `rgba(37,99,235,0.1)`, border `{colors.blue-500}`.

#### `button-danger`
Destructive actions (cancel booking, reject payment).
- Background: `rgba(239, 68, 68, 0.9)`.
- Text: `#ffffff`.
- Hover: Background `{colors.danger}` (`#ef4444`).

### Cards & Containers

#### `card`
General purpose content grouping component.
- Background: `{colors.dark.bg-card}` (`rgba(26, 34, 52, 0.9)`).
- Border: `1px solid {colors.dark.border}`.
- Radius: `14px` (`{rounded.lg}`).
- Shadow: `{colors.dark.shadow-sm}`.
- Header: Border-bottom `1px solid {colors.dark.border}`, padding `16px 20px`, typography `{typography.card-title}`.

#### `kpi-card`
Metric display component on Admin and Technician dashboards.
- Padding: `22px 20px 18px`.
- Contains category eyebrow label, large 28px metric number, icon box (`42px x 42px`, radius 10px).
- Bottom Edge Accent: 3px height colored gradient strip pinned to bottom border (`.dash-kpi-blue`, `.dash-kpi-green`, `.dash-kpi-amber`, `.dash-kpi-red`, `.dash-kpi-indigo`).
- Hover: `translateY(-3px)`, shadow-md.

### Inputs & Forms

#### `form-control`
Form input for text, email, password, and selects.
- Background: `{colors.dark.bg-elevated}` (`#1a2234` / Light: `#f8fafc`).
- Border: `1px solid {colors.dark.border}` (`rgba(255,255,255,0.08)` / Light: `#e2e8f0`).
- Radius: `6px` (`{rounded.sm}`).
- Padding: `9px 14px`.
- Font Size: `13.5px`–`14px`.
- Text: `{colors.dark.text-primary}`.
- Placeholder: `{colors.dark.text-muted}`.
- Focus: Outline none, border `{colors.blue-500}`, shadow `0 0 0 3px rgba(37,99,235,0.15)`.

#### `form-label`
Label preceding input controls.
- Font Size: `11px`–`12px`.
- Font Weight: `600` or `700`.
- Text: `{colors.dark.text-secondary}`.
- Transform: UPPERCASE.
- Letter Spacing: `0.5px`–`0.8px`.

### Dialogs / Overlays

#### `modal-content`
Bootstrap modal dialog box.
- Background: `{colors.dark.bg-surface}` (`#111827`).
- Border: `1px solid {colors.dark.border}`.
- Radius: `14px` (`{rounded.lg}`).
- Backdrop: `rgba(0,0,0,0.5)` with `backdrop-filter: blur(2px)`.

### Other Product-Specific Components

#### `status-badge`
Pill badge indicating transaction or booking lifecycle state.
- Radius: `20px` (`{rounded.pill}`).
- Padding: `3px 9px`.
- Font Size: `11px`, weight `600`.
- Variants:
  - `pending`: background `rgba(245,158,11,.15)`, text `#f59e0b`, border `rgba(245,158,11,.3)`.
  - `confirmed`: background `rgba(6,182,212,.15)`, text `#06b6d4`, border `rgba(6,182,212,.3)`.
  - `in_progress`: background `rgba(59,130,246,.15)`, text `#60a5fa`, border `rgba(59,130,246,.3)`.
  - `completed`: background `rgba(16,185,129,.15)`, text `#10b981`, border `rgba(16,185,129,.3)`.
  - `cancelled`: background `rgba(239,68,68,.15)`, text `#ef4444`, border `rgba(239,68,68,.3)`.

#### `empty-state`
Placeholder when lists or tables contain zero items.
- Centered container with `padding: 40px 20px`.
- Circular icon placeholder: `64px x 64px`, background `{colors.dark.bg-elevated}`, radius `50%`, icon size `24px`, color `{colors.dark.text-muted}`.
- Text message: `{typography.body-dense}`, color `{colors.dark.text-secondary}`.
- CTA Button: `{components.button-primary}`.

---

## Do's and Don'ts

### Do
* Always use `var(--bg-base)` for page canvases and `var(--bg-card)` for container cards.
* Always apply `1px solid var(--border)` to cards, inputs, and dividers.
* Use `uppercase` with `0.8px` letter-spacing and `11px` font size for card labels and table column headers.
* Use `linear-gradient(135deg, var(--blue-600), var(--blue-500))` for primary action buttons.
* Maintain status badge colors strictly with 15% alpha backgrounds and 30% alpha borders.
* Use `clamp()` sizing for marketing page headlines to maintain responsiveness.

### Don't
* Don't introduce solid bright white backgrounds in dark mode.
* Don't use solid black (`#000000`) borders; always use translucent `rgba(255,255,255,0.08)`.
* Don't mix sharp corners (0px) with the established radius scale (`6px`, `10px`, `14px`, `20px`).
* Don't add arbitrary drop-shadows to flat cards; rely on `var(--shadow-sm)` and border definition.
* Don't use raw hex colors in component styles when semantic CSS variables (`var(--text-primary)`, `var(--border)`) exist.

---

## Responsive Behavior

### Breakpoints

Observed actual CSS media query breakpoints:
* **Desktop (`>= 992px`)**:
  - Sidebar fixed at `260px`.
  - Content offset `margin-left: 260px`.
  - Hamburger menu button hidden.
* **Tablet / Small Laptop (`<= 991px`)**:
  - Sidebar translated off-screen (`transform: translateX(-100%)`).
  - Topbar and page content margin-left resets to `0`.
  - Mobile hamburger toggle button (`.menu-toggle`) appears in topbar.
  - Backdrop overlay (`.s-overlay`) activates when sidebar is toggled open (`backdrop-filter: blur(2px)`).
* **Mobile Phone (`<= 767px` / `768px`)**:
  - Page wrapper padding reduces from `28px 28px 48px` to `20px 16px 40px`.
  - User name in topbar (`.u-name`) is hidden, displaying avatar only.
  - Page title font size clamps from `24px` to `18px`.
  - Marketing hero stats and footer links collapse to single column.
* **Compact Auth (`<= 600px` / `<= 900px`)**:
  - At `900px`: Auth split screen collapses to single column (`420px` max-width).
  - At `600px`: Left promotional hero side is hidden (`display: none`), displaying only `.login-card`.

### Touch Targets
- Action buttons in mobile viewports maintain a minimum height of `40px`–`44px`.
- Table action buttons use `.btn-sm` (`padding: 5px 12px`).

### Collapsing Strategy
- Multi-column metric grids (`.row.g-3` or `.why-grid`) collapse from 4 columns to 2 columns on tablet, and 1 column on mobile screens under 576px.

---

## Iteration Guide

To create new screens that visually and structurally match the existing JASAKU web application:

1. **Adopt the Master Layout**:
   Extend `layouts.app` using:
   ```html
   @extends('layouts.app')
   @section('title', 'Page Title')
   @section('content')
       <div class="page-title">
           <div class="page-title-icon"><i class="bi bi-icon-name"></i></div>
           <div>
               <h1>Page Title</h1>
               <p style="font-size:13px;color:var(--text-secondary);margin:0;">Subheading description.</p>
           </div>
       </div>
   @endsection
   ```
2. **Use Predefined CSS Variables**:
   Reference variables defined in `:root`:
   - `var(--bg-card)` for cards
   - `var(--border)` for separators
   - `var(--text-primary)`, `var(--text-secondary)`, `var(--text-muted)` for typography
   - `var(--radius-sm)`, `var(--radius-md)`, `var(--radius-lg)` for corner radius
3. **Preserve Component Grammar**:
   - Status indicators must use the `.badge.{status}` pattern.
   - Summaries must use `.dash-kpi` or `.stats-card` with an uppercase `11px` label.
   - Tables must be wrapped in `.table-responsive` with `.table.table-hover` and `.bg-elevated` headers.

---

## Known Gaps

* **Pagination Component Styling**: Custom pagination links (`.pagination`) were observed utilizing standard Bootstrap classes with partial dark-mode overrides; complete tokenization of active pagination pills is inferred.
* **Datepicker Popups**: Native browser `<input type="datetime-local">` is used across modals; no third-party dark-themed datepicker library (e.g., Flatpickr) is bundled in CSS.
* **Chart Theming**: Chart.js canvas elements are integrated dynamically via JS; color palette matches `{colors.blue-500}` and `{colors.success}`, but canvas grid-line alphas are governed by inline script rather than CSS tokens.
