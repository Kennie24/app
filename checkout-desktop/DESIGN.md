---
name: Vivid Pulse Dark
colors:
  surface: '#131313'
  surface-dim: '#131313'
  surface-bright: '#393939'
  surface-container-lowest: '#0e0e0e'
  surface-container-low: '#1c1b1b'
  surface-container: '#201f1f'
  surface-container-high: '#2a2a2a'
  surface-container-highest: '#353534'
  on-surface: '#e5e2e1'
  on-surface-variant: '#e2bfb0'
  inverse-surface: '#e5e2e1'
  inverse-on-surface: '#313030'
  outline: '#a98a7d'
  outline-variant: '#5a4136'
  surface-tint: '#ffb693'
  primary: '#ffb693'
  on-primary: '#561f00'
  primary-container: '#ff6b00'
  on-primary-container: '#572000'
  inverse-primary: '#a04100'
  secondary: '#c8c6c5'
  on-secondary: '#303030'
  secondary-container: '#474746'
  on-secondary-container: '#b7b5b4'
  tertiary: '#c8c6c5'
  on-tertiary: '#303030'
  tertiary-container: '#9a9999'
  on-tertiary-container: '#313131'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#ffdbcc'
  primary-fixed-dim: '#ffb693'
  on-primary-fixed: '#351000'
  on-primary-fixed-variant: '#7a3000'
  secondary-fixed: '#e5e2e1'
  secondary-fixed-dim: '#c8c6c5'
  on-secondary-fixed: '#1b1b1c'
  on-secondary-fixed-variant: '#474746'
  tertiary-fixed: '#e4e2e1'
  tertiary-fixed-dim: '#c8c6c5'
  on-tertiary-fixed: '#1b1c1c'
  on-tertiary-fixed-variant: '#474747'
  background: '#131313'
  on-background: '#e5e2e1'
  surface-variant: '#353534'
typography:
  headline-xl:
    fontFamily: Manrope
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Manrope
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Manrope
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
  headline-md:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Manrope
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Manrope
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-md:
    fontFamily: Manrope
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Manrope
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.02em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  xxl: 48px
  container-max: 1280px
  gutter: 24px
---

## Brand & Style

This design system is a high-fidelity, high-contrast dark mode environment designed for performance and precision. It targets a modern tech-savvy audience, balancing the energy of a vibrant primary accent with the sophisticated calm of a deep charcoal interface.

The design style is **Corporate / Modern** with a lean toward **Minimalism**. It utilizes deep backgrounds to create a sense of infinite space, where the primary brand orange acts as a high-visibility beacon for interaction. The aesthetic response is one of focus, clarity, and premium quality, avoiding unnecessary flourishes in favor of geometric rigor and intentional hierarchy.

## Colors

The palette is built on a "Night Vision" philosophy, prioritizing legibility and eye comfort in low-light environments.

- **Primary (#FF6B00):** Used exclusively for high-intent actions, active states, and critical feedback.
- **Surface Tiers:** 
    - `Base`: #121212 for the lowest layer (background).
    - `Elevated`: #1E1E1E for primary content containers.
    - `Overlay`: #2C2C2C for borders, dividers, and hover states.
- **Typography:** Headlines utilize pure white for maximum "pop" against the dark canvas, while secondary text is muted to #A0A0A0 to manage visual noise and establish clear information architecture.

## Typography

The design system utilizes **Manrope** across all roles to maintain a cohesive, modern geometric feel. The type scale is optimized for high-contrast environments.

- **Headlines:** Use Bold (700) or SemiBold (600) weights. Tighten letter-spacing on larger sizes to maintain visual density.
- **Body:** Standard body text uses a Medium weight for improved legibility against dark backgrounds, preventing the "thinning" effect often seen with light text on dark.
- **Labels:** Use uppercase sparingly for metadata, relying on SemiBold weights to ensure small-scale clarity.

## Layout & Spacing

This design system adheres to a strict **8px grid system**. All margins, paddings, and component heights must be multiples of 8 (or 4 for micro-adjustments).

- **Grid Model:** A 12-column fluid grid for desktop with 24px gutters.
- **Mobile:** Transitions to a 4-column grid with 16px margins.
- **Rhythm:** Vertical spacing between sections should typically follow the `xxl` (48px) or `xl` (32px) units to allow the dark interface "room to breathe," preventing a cramped or cluttered aesthetic.

## Elevation & Depth

In this dark mode system, depth is conveyed through **Tonal Layers** rather than heavy shadows.

- **Level 0 (Base):** #121212. Used for the main application background.
- **Level 1 (Surface):** #1E1E1E. Used for cards, sidebars, and navigation bars.
- **Level 2 (Interaction):** #2C2C2C. Used for borders, input fields, and hover states.
- **Borders:** Use a subtle 1px solid stroke of #2C2C2C for all containers to define boundaries where tonal shifts are subtle.
- **Shadows:** Use very soft, large-radius black shadows (0px 8px 24px rgba(0,0,0,0.5)) only for floating elements like modals or dropdown menus to lift them off the Level 1 surface.

## Shapes

The shape language is consistently **Rounded**, providing a friendly counterpoint to the sharp high-contrast color scheme.

- **Components:** Standard buttons and cards use a 0.5rem (8px) radius to align with the base grid unit.
- **Large Containers:** Modals or featured sections may scale up to `rounded-xl` (24px) for a more pronounced, modern look.
- **Icons:** Should follow a similar rounded geometry, avoiding sharp corners or extreme "needle" points.

## Components

### Buttons
- **Primary:** Background #FF6B00, Text #FFFFFF. No border. On hover, apply a subtle white overlay (10% opacity).
- **Secondary:** Background transparent, Border 1px #2C2C2C, Text #FFFFFF. On hover, background becomes #2C2C2C.
- **Ghost:** Text #A0A0A0. On hover, Text #FFFFFF and Background #1E1E1E.

### Input Fields
- **Default:** Background #121212, Border 1px #2C2C2C, Text #FFFFFF, Placeholder #A0A0A0.
- **Focused:** Border 1px #FF6B00.

### Cards
- **Container:** Background #1E1E1E, Border 1px #2C2C2C. 
- **Padding:** 24px (lg) inner padding for standard layout.

### Chips & Tags
- **Informational:** Background #2C2C2C, Text #A0A0A0, 12px font size.
- **Active:** Background #FF6B00 (at 15% opacity), Text #FF6B00, 1px Border #FF6B00.

### Lists & Dividers
- **Dividers:** 1px solid #2C2C2C.
- **List Items:** On hover, change background to #1E1E1E to indicate interactivity.