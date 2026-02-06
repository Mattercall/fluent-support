# UI-STYLE-GUIDE.md

## Purpose
This design guide defines the visual language for all UI enhancements applied to WordPress admin plugins. The goal is a clean, modern, industry-level interface while preserving the plugin's layout and functionality.

---

## Core Design Principles
- Clean, minimal, modern
- High contrast and accessible colors
- Consistent spacing and alignment
- Subtle use of shadows and borders
- Professional SaaS-like visual language (Stripe, Linear, Notion, Zendesk)

---

## Color System

### Primary Colors
- Primary Button Background: **#000**
- Primary Button Text: **#FFF**
- Primary Button Border: **#000**

### Secondary Colors
- Secondary Button Background: **#FFF**
- Secondary Button Text: **#000**
- Secondary Button Border: **#000**

### Surface Colors
- Card Background: **#FFF**
- Card Border: **#E5E5E5**
- Table Header Background: **#F8F8F8**
- Input Border: **#D8D8D8**

---

## Components

### Buttons
- Border radius: **4px**
- Font weight: **500**
- Hover: slight opacity change (0.9)
- No heavy shadows
- Clear contrast between primary and secondary buttons

### Cards / Panels
- Border: **1px solid #E5E5E5**
- Border Radius: **6px**
- Shadow: very subtle, soft
- Internal spacing: 18–24px consistent

### Tables
- Header: light gray background
- Borders: thin, subtle
- Row height: consistent and breathable

### Inputs
- Border: **1px solid #D8D8D8**
- Border radius: **4px**
- Focus state: slightly darker border

### Typography
- Clean, legible
- Slightly larger headings
- Consistent line-height (1.4–1.6)

---

## Restrictions
Do **not** modify:
- HTML structure
- Flex/grid layouts
- Component wrappers
- Vue-rendered internal logic

Only apply **visual improvements**, never functional changes.

---

## Scope
All styling must target only the plugin's container: