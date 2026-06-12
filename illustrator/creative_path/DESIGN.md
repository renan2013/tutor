---
name: Creative Path
colors:
  surface: '#131313'
  surface-dim: '#131313'
  surface-bright: '#393939'
  surface-container-lowest: '#0e0e0e'
  surface-container-low: '#1c1b1b'
  surface-container: '#20201f'
  surface-container-high: '#2a2a2a'
  surface-container-highest: '#353535'
  on-surface: '#e5e2e1'
  on-surface-variant: '#dac2ad'
  inverse-surface: '#e5e2e1'
  inverse-on-surface: '#313030'
  outline: '#a28d7a'
  outline-variant: '#544434'
  surface-tint: '#ffb86d'
  primary: '#ffc183'
  on-primary: '#492900'
  primary-container: '#ff9a00'
  on-primary-container: '#653a00'
  inverse-primary: '#8a5100'
  secondary: '#c8c6c6'
  on-secondary: '#303030'
  secondary-container: '#474747'
  on-secondary-container: '#b6b5b4'
  tertiary: '#89d8ff'
  on-tertiary: '#003547'
  tertiary-container: '#00c0fa'
  on-tertiary-container: '#004a63'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#ffdcbd'
  primary-fixed-dim: '#ffb86d'
  on-primary-fixed: '#2c1600'
  on-primary-fixed-variant: '#693c00'
  secondary-fixed: '#e4e2e1'
  secondary-fixed-dim: '#c8c6c6'
  on-secondary-fixed: '#1b1c1c'
  on-secondary-fixed-variant: '#474747'
  tertiary-fixed: '#c0e8ff'
  tertiary-fixed-dim: '#71d2ff'
  on-tertiary-fixed: '#001e2b'
  on-tertiary-fixed-variant: '#004d66'
  background: '#131313'
  on-background: '#e5e2e1'
  surface-variant: '#353535'
typography:
  headline-xl:
    fontFamily: Hanken Grotesk
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Hanken Grotesk
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Hanken Grotesk
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-lg:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.02em
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.04em
  label-caps:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '700'
    lineHeight: 16px
    letterSpacing: 0.06em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 12px
  md: 16px
  lg: 24px
  xl: 32px
  container-margin: 20px
  gutter: 16px
---

## Brand & Style
The design system is engineered to feel like an immersive extension of a professional creative suite. It targets aspiring designers and digital artists who require a focused, high-fidelity environment for learning complex software. 

The aesthetic is **Modern Corporate** with a heavy influence from **Tool-centric UI**. It prioritizes "Instructional Clarity" through a dark-themed interface that reduces eye strain during long study sessions and makes the vibrant orange brand color pop as a functional signifier. The style leans into high-fidelity precision—using subtle borders, organized card layouts, and refined typography to evoke a sense of professional mastery and technical reliability.

## Colors
The palette is anchored in a professional "Creative Suite" dark mode. 

- **Primary Orange (#FF9A00):** Reserved for high-priority actions, active tool states, progress indicators, and "Pro" tips. It serves as the primary focal point against the dark background.
- **Deep Charcoal/Black (#121212 - #1E1E1E):** Used for the background and surface layers to create depth. This mimics the Illustrator workspace, providing a familiar environment for the user.
- **Contrast White:** Used sparingly for primary typography and iconography to ensure AAA accessibility against dark surfaces.
- **Tertiary Blue (#00C4FF):** A secondary accent used for links, selection states, or secondary tool categories to distinguish them from primary actions.

## Typography
The typography system utilizes **Hanken Grotesk** for headlines to provide a sharp, contemporary architectural feel, while **Inter** handles all functional and body text for its legendary legibility in technical contexts.

- **Headlines:** Should be tight and impactful. Use `-0.02em` tracking for larger titles to maintain a premium, high-fidelity look.
- **Body Text:** Optimized for instructional reading. Use a generous 1.5x line height for `body-md` to ensure complex explanations are easy to digest.
- **Labels:** Small caps or bold weights are used for tool names and keyboard shortcuts (e.g., "V" for Selection Tool) to make them instantly recognizable as UI elements.

## Layout & Spacing
This design system employs a **Fluid Grid** model with a strict 8px spacing rhythm. 

- **Mobile Grid:** A 4-column layout with 20px side margins and 16px gutters.
- **Consistency:** All vertical margins between sections should follow a scale of 24px (lg) or 32px (xl) to create a sense of breathability in an otherwise information-dense app.
- **Safe Areas:** Ensure interactive elements (buttons/inputs) maintain a minimum height of 48px for touch ergonomics, regardless of the visual scale.

## Elevation & Depth
Depth in this design system is created through **Tonal Layering** supplemented by **Subtle Ambient Shadows**.

- **Base Layer:** #121212 (Background).
- **Mid Layer:** #1E1E1E (Cards/Containers). Used for the primary content blocks.
- **Top Layer:** #2D2D2D (Navigation bars/Modals).
- **Shadows:** Use low-opacity, wide-spread shadows (`y: 4, blur: 12, color: rgba(0,0,0,0.3)`) to lift cards off the background without creating visual clutter. 
- **Interactivity:** On press, elements should shift depth using a 1px inner stroke in the primary orange color to simulate a "selected" or "active" tool state.

## Shapes
The shape language is **Rounded**, using a 0.5rem (8px) base radius.

- **Cards:** Use `rounded-lg` (16px) to create a soft, modern container for lessons and tool guides.
- **Buttons & Inputs:** Use the base `rounded` (8px) for a professional, slightly sharper appearance.
- **Icon Enclosures:** Small decorative chips or tool icons should use a pill-shape (32px+) to distinguish them from structural UI components.

## Components
Consistent styling of these core components ensures the app feels like a unified tool.

- **Tool Cards:** The centerpiece of the UI. Feature a subtle 1px border (#2D2D2D), an icon of the Illustrator tool, a title in `headline-md`, and a short description.
- **Primary Buttons:** Solid Vibrant Orange (#FF9A00) with Black text. High-contrast, no shadow, using `label-lg` typography.
- **Secondary Buttons:** Outlined with a 1.5px border in Charcoal (#2D2D2D) and White text.
- **Progress Bars:** Thin 4px tracks in Charcoal with a solid Orange fill. Use rounded ends for the progress indicator.
- **Keyboard Shortcut Chips:** Small, high-contrast boxes (Background: #2D2D2D, Text: White) that display keys like "Cmd + P". These should have a slight 2px bottom border to mimic physical keys.
- **Lesson Lists:** Use dividers with 10% opacity white. Each list item should have a chevron-right icon in Neutral Gray to indicate interactivity.
- **Input Fields:** Darker than the surface color (#161616) with a 1px border that turns Orange on focus.