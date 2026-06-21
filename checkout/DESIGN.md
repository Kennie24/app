---
name: Vivid Pulse
colors:
  surface: '#fcf9f8'
  surface-dim: '#dcd9d9'
  surface-bright: '#fcf9f8'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f6f3f2'
  surface-container: '#f0eded'
  surface-container-high: '#eae7e7'
  surface-container-highest: '#e5e2e1'
  on-surface: '#1c1b1b'
  on-surface-variant: '#5a4136'
  inverse-surface: '#313030'
  inverse-on-surface: '#f3f0ef'
  outline: '#8e7164'
  outline-variant: '#e2bfb0'
  surface-tint: '#a04100'
  primary: '#a04100'
  on-primary: '#ffffff'
  primary-container: '#ff6b00'
  on-primary-container: '#572000'
  inverse-primary: '#ffb693'
  secondary: '#a33f00'
  on-secondary: '#ffffff'
  secondary-container: '#fe752e'
  on-secondary-container: '#5f2100'
  tertiary: '#665d55'
  on-tertiary: '#ffffff'
  tertiary-container: '#a2978e'
  on-tertiary-container: '#373029'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdbcc'
  primary-fixed-dim: '#ffb693'
  on-primary-fixed: '#351000'
  on-primary-fixed-variant: '#7a3000'
  secondary-fixed: '#ffdbcd'
  secondary-fixed-dim: '#ffb596'
  on-secondary-fixed: '#360f00'
  on-secondary-fixed-variant: '#7c2e00'
  tertiary-fixed: '#eee0d6'
  tertiary-fixed-dim: '#d1c4bb'
  on-tertiary-fixed: '#211a15'
  on-tertiary-fixed-variant: '#4e453e'
  background: '#fcf9f8'
  on-background: '#1c1b1b'
  surface-variant: '#e5e2e1'
typography:
  display-lg:
    fontFamily: Manrope
    fontSize: 48px
    fontWeight: '800'
    lineHeight: 56px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Manrope
    fontSize: 32px
    fontWeight: '800'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Manrope
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  title-lg:
    fontFamily: Manrope
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
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
  label-lg:
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
  md: 24px
  lg: 40px
  xl: 64px
  gutter: 16px
  margin-mobile: 20px
  margin-desktop: 80px
---

## Brand & Style

The design system is engineered for a high-energy, premium music commerce environment. It balances the urgency of retail with the emotional resonance of music through a "Warm Modernist" aesthetic. This style combines the structural clarity of **Minimalism** with a high-saturation **Bold** color strategy.

The UI should evoke a sense of professional reliability and sonic energy. It prioritizes clarity and speed, ensuring the "buying" action is always frictionless and joyful. The use of large tap targets, high-contrast typography, and generous whitespace ensures that product imagery (album art) remains the hero while the commerce framework provides a premium, "boutique" feel.

## Colors

The palette is anchored by a high-chroma orange, symbolizing energy and action. 

- **Primary:** Use #FF6B00 for the "hero" actions, key metrics, and brand touchpoints. It must always pass WCAG AA contrast against white for text-based icons.
- **Support:** Use #C94F00 for interactive depth (hover/pressed) and #FFF1E7 for large surface areas like item backgrounds or subtle section containers.
- **Neutrals:** The "Near-black" (#171717) provides a sophisticated anchor for typography, preventing the optical vibrating effect often caused by pure #000000 against high-vibrancy colors.
- **Surface:** A dual-tone background strategy uses pure white for cards and off-white for the main canvas to create subtle structural depth without relying on heavy borders.

## Typography

This design system uses **Manrope** exclusively to maintain a modern, technical, yet friendly geometric appearance.

- **Headlines:** Use Bold (700) or ExtraBold (800) for all commerce triggers and product titles. Tighten letter spacing slightly on larger displays to maintain a "compact" editorial look.
- **Body:** Standardized at a 16px minimum for accessibility. Use Medium (500) for emphasized metadata like artist names or price points.
- **Labels:** Use for secondary information, timestamps, and micro-copy. Always ensure labels remain above 12px for readability.

## Layout & Spacing

The system follows a strict **8px Grid** to ensure mathematical harmony across all components.

- **Mobile (Fan Experience):** Uses a fluid-width single column. All primary containers should have a side margin of 20px. 16px gutters between grid items (e.g., album tiles).
- **Desktop (Dashboard):** Implements a 12-column fixed grid with a maximum content width of 1280px. 24px gutters provide breathing room for data-heavy views.
- **Touch Targets:** Any interactive element must maintain a minimum height/width of 44px, regardless of the visual size of the icon or text within it.

## Elevation & Depth

To maintain a "Premium Modern" feel, depth is created through **Tonal Layers** and **Ambient Shadows** rather than harsh borders.

- **Level 0 (Canvas):** Warm Off-white (#FAFAF8).
- **Level 1 (Cards/Sections):** Pure White (#FFFFFF) with a very soft, diffused shadow (0px 4px 20px rgba(0,0,0,0.04)).
- **Level 2 (Interactive/Floating):** Pure White (#FFFFFF) with a more defined shadow (0px 8px 30px rgba(0,0,0,0.08)) to indicate hover states or modals.
- **Inlays:** Use the Pale Orange (#FFF1E7) for background "wells" where items are grouped, creating a recessed look without shadows.

## Shapes

The shape language is generous and friendly. 

- **Containers & Cards:** Use a base radius of 16px to 20px to soften the layout and reflect the "joyful" brand principle.
- **Media:** Album artwork and artist avatars should follow a consistent 12px radius, ensuring they look "held" within their containers.
- **Buttons:** Use 14px for standard buttons and fully pill-shaped (100px) for small chips or tags.

## Components

- **Buttons:** Primary buttons use Vivid Orange with White text. Labels must be action-oriented (e.g., "Buy Album"). Height is 48px or 56px for mobile "Sticky" actions.
- **Chips:** Used for genres or status. Use Pale Orange background with Deep Orange text for active states; Neutral Gray for inactive.
- **Cards:** White background, 16px radius. Content is stacked: Image (1:1 ratio) -> Title (Bold) -> Price/Metadata (Secondary Text).
- **Input Fields:** 16px radius, Warm Off-white background, 2px border on focus (Vivid Orange). Labels sit above the field in Label-LG style.
- **Lists:** Transaction history and tracklists use 16px vertical padding with a subtle #F0F0F0 bottom border.
- **Placeholders:** All loading states and empty album slots should use a geometric gradient utilizing the Orange palette to maintain brand warmth even when content is missing.