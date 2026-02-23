# Design System: Cyber City Theme

## 1. Core Philosophy
The "Cyber City" theme is designed to feel premium, modern, and immersive. It combines a dark aesthetic with vibrant neon accents and glassmorphism to create a depth-filled user interface.

## 2. Typography
**Primary Font**: `Outfit` (Google Fonts) - Modern, geometric sans-serif.
- **Headings**: Weights 600/700. Used for card titles and section headers.
- **Body**: 
    - **Base Size**: `16px` (1rem) for Mobile, `~16.8px` (1.05rem) for Desktop.
    - **Line Height**: `1.6` for optimal readability.
    - **Small Text**: Min `14px` (0.875em).

## 3. Color Palette
| Token | Hex/Value | Usage |
|-------|-----------|-------|
| `bg-dark` | `#0f172a` (Slate-900) | Main Page Background |
| `primary` | `#6366f1` (Indigo-500) | Primary Buttons, Links |
| `secondary` | `#ec4899` (Pink-500) | Accents, Call-to-Actions |
| `cyan` | `#06b6d4` (Cyan-500) | Info, Verification Badges |
| `text-light`| `#f8fafc` (Slate-50) | Primary Text Color |
| `text-muted`| `#94a3b8` (Slate-400) | Secondary Text |

### Gradients & Effects
- **Neon Gradient**: `linear-gradient(135deg, #6366f1, #ec4899)` - Used for text, borders, and active states.
- **Glassmorphism**: 
    - `background: rgba(255, 255, 255, 0.05)`
    - `backdrop-filter: blur(10px)`
    - `border: 1px solid rgba(255, 255, 255, 0.1)`

## 4. Components

### Glass Cards (`.glass-card`)
The fundamental container for content. 
- **Appearance**: Translucent dark panel with a subtle white border.
- **Hover**: Glow effect or border highlight using the primary neon gradient.

### Buttons (`.btn-cyber`, `.btn-neon`)
- **Primary**: Solid gradient or filled glass with glow.
- **Outline**: Transparent with neon border.
- **Action Group**: Asymetric styling (Rounded Left/Right) for compact list actions.

### Data Tables (`.table-modern`)
- **Headings**: Uppercase, tracked (letter-spacing), slight transparency.
- **Rows**: Hover effect (`bg-white/5`).
- **Cells**: Vertically aligned middle.

## 5. Layout Patterns
- **Navbar**: Floating glass bar, detached from top, containing brand and user navigation.
- **Responsiveness**: 
    - Tables must be wrapped in `.table-responsive`.
    - Cards adjust padding from `p-5` (Desktop) to `p-3` (Mobile).
    - Grids collapse to single column on mobile.

## 6. Terminology
- **Kelas**: Refers to the Academic Grade of a student in their school (e.g., "Kelas 5A").
- **Rombel**: Refers to the Extracurricular Group (e.g., "Robotics Group 1").
