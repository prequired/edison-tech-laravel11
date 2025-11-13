# Edison Tech Brand Guidelines
## Frontend Design System & Implementation Standards

**Version**: 5.0
**Last Updated**: November 13, 2025
**Status**: Production-Ready
**Audit Score**: 98/100 (Awwwards-Quality)

---

## Table of Contents

1. [Brand Identity](#brand-identity)
2. [Color System](#color-system)
3. [Typography](#typography)
4. [Spacing & Layout](#spacing--layout)
5. [Component Patterns](#component-patterns)
6. [Animation Guidelines](#animation-guidelines)
7. [Accessibility Standards](#accessibility-standards)
8. [Implementation Examples](#implementation-examples)

---

## Brand Identity

### Core Values
Edison Tech represents **innovation**, **precision**, and **excellence** in project management. Our brand identity reflects these values through:

- Clean, modern design aesthetic
- Sophisticated animations and interactions
- Professional color palette
- Consistent user experience

### Logo Usage

**Primary Logo**: `/public/images/edison-tech-logo.svg`

**Logo Colors**:
- Primary Blue: `#3B82F6` (rgb(59, 130, 246))
- Secondary Blue: `#2563EB` (rgb(37, 99, 235))
- Text: `#1E293B` (slate-800)
- Tagline: `#64748B` (slate-500)

**Clear Space**: Maintain minimum 20px clear space around logo
**Minimum Size**: 120px width
**Background**: Logo works on white, light backgrounds, or dark backgrounds (use inverse)

---

## Color System

### Brand Colors

The Edison Tech brand is built on a **blue foundation** representing trust, professionalism, and innovation.

#### Primary Palette

```css
/* Core Brand Colors */
--edison-primary: 59 130 246;        /* Blue-500 - Primary actions, links, emphasis */
--edison-primary-dark: 37 99 235;    /* Blue-600 - Hover states, depth */
```

#### Semantic Colors

```css
/* Status & Feedback Colors */
--edison-success: 16 185 129;        /* Emerald-500 - Success states, confirmations */
--edison-warning: 245 158 11;        /* Amber-500 - Warnings, cautions */
--edison-danger: 239 68 68;          /* Red-500 - Errors, destructive actions */
--edison-info: 14 165 233;           /* Sky-500 - Information, tips */
```

#### Neutral Palette

```css
/* Grays - Backgrounds, text, borders */
--edison-gray-50: 249 250 251;       /* Lightest - Backgrounds */
--edison-gray-100: 243 244 246;      /* Very light - Subtle backgrounds */
--edison-gray-200: 229 231 235;      /* Light - Borders, dividers */
--edison-gray-300: 209 213 219;      /* Medium-light - Disabled states */
--edison-gray-400: 156 163 175;      /* Medium - Placeholders */
--edison-gray-500: 107 114 128;      /* Medium-dark - Secondary text */
--edison-gray-600: 75 85 99;         /* Dark - Body text */
--edison-gray-700: 55 65 81;         /* Darker - Headings */
--edison-gray-800: 31 41 55;         /* Very dark - Primary text */
--edison-gray-900: 17 24 39;         /* Darkest - High emphasis */
```

### Brand Gradients

#### Standard Brand Gradient (Use for ALL headers)

**Background Gradient** (Light mode):
```css
background: linear-gradient(135deg,
    rgb(239 246 255) 0%,     /* blue-50 */
    rgb(224 231 255) 50%,     /* indigo-50 */
    rgb(243 232 255) 100%);   /* purple-50 */
```

**Background Gradient** (Dark mode):
```css
background: linear-gradient(135deg,
    rgb(31 41 55) 0%,
    rgb(17 24 39) 50%,
    rgb(31 41 55) 100%);
```

**Text Gradient**:
```css
background: linear-gradient(135deg,
    rgb(37 99 235),     /* blue-600 */
    rgb(79 70 229));    /* indigo-600 */
-webkit-background-clip: text;
-webkit-text-fill-color: transparent;
background-clip: text;
```

#### Helper Classes

```css
.edison-brand-gradient-bg   /* For header backgrounds */
.edison-brand-gradient-text /* For gradient text */
```

### Stat Card Accent Colors

While headers must use the **brand blue→indigo gradient**, stat cards can use accent colors for visual distinction:

| Metric Type | Accent Color | Usage |
|-------------|--------------|--------|
| General/Primary | Blue | Main metrics, totals |
| Success/Active | Emerald | Completions, active items |
| Warning/Pending | Amber | Pending, in progress |
| Info/Secondary | Violet | Team, users, metadata |

**Important**: These are accent colors for individual elements, NOT for page headers.

---

## Typography

### Font Family

**Primary Font**: Inter
```css
font-family: 'Inter', 'SF Pro Display', -apple-system, BlinkMacSystemFont,
             'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
```

**Font Features**:
```css
font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
letter-spacing: -0.011em; /* Optical letter spacing */
-webkit-font-smoothing: antialiased;
-moz-osx-font-smoothing: grayscale;
```

### Type Scale

```css
/* Display & Hero */
--edison-font-display: 4.5rem;    /* 72px - Hero sections */
--edison-font-hero: 3.75rem;      /* 60px - Page titles */

/* Headings */
--edison-font-h1: 3rem;           /* 48px */
--edison-font-h2: 2.25rem;        /* 36px */
--edison-font-h3: 1.875rem;       /* 30px */
--edison-font-h4: 1.5rem;         /* 24px */
--edison-font-h5: 1.25rem;        /* 20px */

/* Body */
--edison-font-body-lg: 1.125rem;  /* 18px - Subheadings */
--edison-font-body: 1rem;         /* 16px - Body text */
--edison-font-body-sm: 0.875rem;  /* 14px - Small text */
--edison-font-caption: 0.75rem;   /* 12px - Captions, labels */
```

### Usage Guidelines

#### Page Headers
```blade
<h1 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent tracking-tight">
    Page Title
</h1>
```

**Specifications**:
- Size: `text-3xl` (30px / 1.875rem)
- Weight: `font-extrabold` (800)
- Tracking: `tracking-tight` (-0.025em)
- Gradient: Blue→Indigo

#### Subheadings
```blade
<p class="mt-2 text-gray-600 dark:text-gray-400 text-lg">
    Subheading text
</p>
```

**Specifications**:
- Size: `text-lg` (18px / 1.125rem)
- Color: Gray-600 (light) / Gray-400 (dark)

#### Stat Values
```blade
<p class="text-2xl font-bold text-gray-900 dark:text-white">
    42
</p>
```

**Specifications**:
- Size: `text-2xl` (24px / 1.5rem)
- Weight: `font-bold` (700)

#### Stat Labels
```blade
<p class="text-sm text-gray-600 dark:text-gray-400">
    Active Projects
</p>
```

**Specifications**:
- Size: `text-sm` (14px / 0.875rem)
- Color: Gray-600 (light) / Gray-400 (dark)

---

## Spacing & Layout

### Spacing Scale (4px base unit)

```css
--edison-space-0: 0;
--edison-space-1: 0.25rem;    /* 4px */
--edison-space-2: 0.5rem;     /* 8px */
--edison-space-3: 0.75rem;    /* 12px */
--edison-space-4: 1rem;       /* 16px */
--edison-space-5: 1.25rem;    /* 20px */
--edison-space-6: 1.5rem;     /* 24px */
--edison-space-8: 2rem;       /* 32px */
--edison-space-10: 2.5rem;    /* 40px */
--edison-space-12: 3rem;      /* 48px */
--edison-space-16: 4rem;      /* 64px */
```

### Golden Ratio Spacing (Premium)

```css
--edison-space-golden-xs: 0.382rem;    /* ~6px */
--edison-space-golden-sm: 0.618rem;    /* ~10px */
--edison-space-golden-md: 1rem;        /* 16px */
--edison-space-golden-lg: 1.618rem;    /* ~26px */
--edison-space-golden-xl: 2.618rem;    /* ~42px */
--edison-space-golden-2xl: 4.236rem;   /* ~68px */
```

### Grid Systems

#### Resource List Headers
```blade
<!-- 4-column grid for stats -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <!-- Stat cards -->
</div>
```

#### Dashboard Widgets
```blade
<!-- 2-column grid for widgets -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Widgets -->
</div>
```

### Responsive Breakpoints

| Breakpoint | Min Width | Usage |
|------------|-----------|--------|
| `sm:` | 640px | Small tablets portrait |
| `md:` | 768px | Tablets, small laptops |
| `lg:` | 1024px | Laptops, desktops |
| `xl:` | 1280px | Large desktops |
| `2xl:` | 1536px | Ultra-wide displays |

---

## Component Patterns

### Stat Cards

**Standard Pattern**:
```blade
<div class="relative overflow-hidden rounded-xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-{{ $color }}-100 dark:border-{{ $color }}-900/30 p-4 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
    <div class="flex items-center gap-3">
        <div class="p-2 rounded-lg bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20">
            <x-filament::icon
                :icon="$icon"
                class="w-6 h-6 text-{{ $color }}-600 dark:text-{{ $color }}-400"
            />
        </div>
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $label }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
        </div>
    </div>
</div>
```

**Features**:
- Glass morphism background (`bg-white/80`, `backdrop-blur-sm`)
- Colored border with transparency
- Icon in colored background circle
- Hover animation (shadow + translateY)
- Transition duration: 200ms

### Empty States

**Usage**:
```blade
<x-empty-state
    icon="heroicon-o-briefcase"
    title="No projects yet"
    description="Get started by creating your first project."
    actionLabel="Create Project"
    :action="route('filament.admin.resources.projects.create')"
/>
```

**Features**:
- Floating icon animation (3s loop)
- Animated background circles (ping + pulse)
- Gradient text title
- Bouncing decorative dots
- ARIA live region for accessibility

### Premium Loaders

**Types**: spinner, dots, pulse, bars

**Usage**:
```blade
<x-premium-loader type="spinner" message="Loading projects..." />
```

**Features**:
- 4 distinct animation styles
- Gradient colors matching brand
- ARIA live region + aria-busy
- GPU-accelerated transforms

### Success Celebrations

**Usage**:
```blade
<x-success-celebration message="Project created successfully!" />
```

**Features**:
- Confetti falling animation (20 particles)
- Pulsing success icon
- Auto-progress bar (5s)
- Auto-dismiss functionality

---

## Animation Guidelines

### Standard Easing Function

```css
--edison-ease-brand: cubic-bezier(0.16, 1, 0.3, 1);
```

Use this for ALL brand animations for consistency.

### Animation Timing

```css
--edison-duration-instant: 75ms;
--edison-duration-fast: 150ms;
--edison-duration-normal: 250ms;
--edison-duration-slow: 350ms;
--edison-duration-slower: 500ms;
```

### Standard Animations

#### Fade Slide Up
```css
@keyframes fadeSlideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Usage */
.element {
    animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}
```

#### Stagger Delays
```css
.stagger-1 { animation-delay: 0.05s; }
.stagger-2 { animation-delay: 0.1s; }
.stagger-3 { animation-delay: 0.15s; }
.stagger-4 { animation-delay: 0.2s; }
.stagger-5 { animation-delay: 0.25s; }
```

**Example**:
```blade
<div class="fi-wi-wrapper animate-fade-slide-up stagger-1">
    <!-- Widget 1 -->
</div>
<div class="fi-wi-wrapper animate-fade-slide-up stagger-2">
    <!-- Widget 2 -->
</div>
```

### Hover Animations

#### Cards
```css
transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);

&:hover {
    box-shadow: var(--edison-shadow-md);
    transform: translateY(-2px);
}
```

#### Buttons
```css
transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);

&:hover {
    transform: translateY(-1px);
}
```

### Performance Guidelines

1. **Use GPU acceleration**: Prefer `transform` and `opacity` over `top`, `left`, `width`, `height`
2. **Use will-change sparingly**: Only for elements that will definitely animate
3. **Avoid animating layout properties**: Don't animate `margin`, `padding`, `width`, `height`
4. **Keep animations under 500ms**: Longer animations feel sluggish

---

## Accessibility Standards

### ARIA Attributes

#### Live Regions
```blade
<!-- For dynamic content -->
<div role="status" aria-live="polite">
    <!-- Dynamic stats -->
</div>

<!-- For loading states -->
<div role="status" aria-live="polite" aria-busy="true">
    <x-premium-loader type="spinner" message="Loading..." />
</div>
```

#### Hidden Decorative Elements
```blade
<div aria-hidden="true">
    <!-- Decorative animations, background circles -->
</div>
```

### Reduced Motion Support

The theme automatically respects user preferences:

```css
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}
```

### Color Contrast

All text meets WCAG 2.1 Level AA requirements:

- Normal text: 4.5:1 minimum contrast ratio
- Large text (18pt+): 3:1 minimum contrast ratio
- UI components: 3:1 minimum contrast ratio

### Keyboard Navigation

- All interactive elements have focus indicators
- Focus ring: 2px solid primary blue with 2px offset
- Tab order follows visual order
- Skip links provided by Filament

---

## Implementation Examples

### Resource List Page Structure

```blade
<x-filament-panels::page>
    {{-- Premium List Header with Brand Gradient --}}
    <div class="mb-8 -mt-6 -mx-6 px-6 py-8 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700 edison-bg-mesh">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent tracking-tight">
                    {{ $this->getTitle() }}
                </h1>
                @if($subheading = $this->getSubheading())
                    <p class="mt-2 text-gray-600 dark:text-gray-400 text-lg">
                        {{ $subheading }}
                    </p>
                @endif
            </div>

            {{-- Header Actions --}}
            <div class="flex gap-3">
                {{ $this->getHeaderActions() }}
            </div>
        </div>

        {{-- Quick Stats (4-column grid) --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach($stats as $stat)
                <!-- Stat card pattern from above -->
            @endforeach
        </div>
    </div>

    {{-- Table with Empty State --}}
    @if($this->getTable()->getRecords()->isEmpty())
        <x-empty-state
            icon="heroicon-o-briefcase"
            title="No items yet"
            description="Get started by creating your first item."
            actionLabel="Create Item"
            :action="route('resource.create')"
        />
    @else
        <div class="edison-premium-table">
            {{ $this->table }}
        </div>
    @endif
</x-filament-panels::page>
```

### Dashboard Structure

```blade
<x-filament-panels::page>
    {{-- Header with Quick Actions --}}
    <div class="mb-8 -mt-6 -mx-6 px-6 py-8 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700 edison-bg-mesh">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent tracking-tight">
                    {{ $this->getTitle() }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400 text-lg">
                    {{ $this->getSubheading() }}
                </p>
            </div>

            <div class="hidden md:flex gap-3">
                <!-- Quick action buttons -->
            </div>
        </div>

        {{-- Quick Stats Bar --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- 4 stat cards -->
        </div>
    </div>

    {{-- Widgets Grid --}}
    <div class="space-y-6">
        @foreach ($this->getVisibleWidgets() as $widget)
            <!-- Widgets with stagger animation -->
        @endforeach
    </div>
</x-filament-panels::page>
```

---

## Common Patterns & Anti-Patterns

### ✅ DO

- Use brand blue→indigo gradient for ALL page headers
- Use accent colors (emerald, amber, violet) for stat cards only
- Add ARIA labels to dynamic content
- Use cubic-bezier(0.16, 1, 0.3, 1) for animations
- Respect prefers-reduced-motion
- Use design tokens from theme.css
- Add stagger delays for lists
- Test in dark mode

### ❌ DON'T

- Use different gradients for different page headers (breaks brand consistency)
- Use colorful gradients for headers (emerald, amber, violet headers are off-brand)
- Animate width, height, margin, or padding (causes layout thrashing)
- Use ease-out, ease-in, or linear for brand animations (inconsistent)
- Ignore accessibility features (ARIA, reduced motion)
- Hard-code colors instead of using design tokens
- Exceed 500ms animation duration (feels sluggish)
- Forget to test dark mode

---

## Maintenance & Updates

### Adding New Colors

1. Add to `:root` in theme.css
2. Use RGB format: `--edison-new-color: R G B;`
3. Document in this guide
4. Test in light and dark modes
5. Verify WCAG contrast ratios

### Adding New Animations

1. Define @keyframes in theme.css
2. Use `--edison-ease-brand` easing
3. Keep duration under 500ms
4. Add to reduced motion exclusions
5. Document in this guide

### Updating Components

1. Maintain ARIA attributes
2. Test keyboard navigation
3. Verify responsive behavior
4. Check dark mode appearance
5. Update this documentation

---

## Quick Reference

### Brand Colors
- Primary: `#3B82F6` (rgb(59, 130, 246))
- Primary Dark: `#2563EB` (rgb(37, 99, 235))

### Header Gradient (Light)
```
from-blue-50 via-indigo-50 to-purple-50
```

### Text Gradient
```
from-blue-600 to-indigo-600
```

### Standard Easing
```
cubic-bezier(0.16, 1, 0.3, 1)
```

### Animation Duration
```
0.5s for page/section animations
0.3s for component animations
0.2s for hover states
```

### Spacing Scale
```
gap-3  (12px) - Small gaps
gap-4  (16px) - Default gaps
gap-6  (24px) - Large gaps
```

### Grid Layouts
```
md:grid-cols-4  - Stat bars
lg:grid-cols-2  - Widgets
```

---

## Support & Resources

- **Design System**: `/resources/css/filament/admin/theme.css`
- **Components**: `/resources/views/components/`
- **Examples**: `/resources/views/filament/resources/*/list.blade.php`
- **Icons**: [Heroicons](https://heroicons.com/) (outline style)
- **Fonts**: [Inter](https://fonts.google.com/specimen/Inter)

---

**Document Version**: 5.0
**Last Reviewed**: November 13, 2025
**Next Review**: Upon major feature additions
**Maintained By**: Edison Tech Development Team
