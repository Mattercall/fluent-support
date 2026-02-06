# AGENTS.md

## Purpose
Codex must safely enhance the UI of WordPress admin plugins without breaking functionality or modifying internal plugin logic.

## Rules

### 1. Never modify:
- Plugin slugs or routes (e.g., `page=fluent-support`, `page=fluent-mail`)
- Compiled JS bundles (`app.js`, `vendor.js`, `runtime.js`)
- Vue component markup or structure
- Core spacing, flexbox, grid, or wrapper layouts
- Any plugin functionality or backend logic

### 2. UI changes must be:
- Visual-only (colors, borders, typography, shadows)
- Non-destructive
- Scoped inside the plugin root element only
  (e.g., `#fluent_support_app`, `#fluent_mail_app`)
- Specific and minimal (no global selector overrides)

### 3. Implementation guidelines:
- Load CSS/JS via admin hooks only:
  `admin_enqueue_scripts`, `admin_print_styles`, `admin_print_footer_scripts`
- Use MutationObserver only when needed and scope it tightly to the plugin container
- Avoid layout resets and structural modifications

### 4. Output requirements:
- Return full file contents, never partial diffs
- Keep all styling logic inside our own plugin
- Guarantee the plugin UI remains fully functional and stable

Codex must always produce clean, safe, production-quality UI enhancements.