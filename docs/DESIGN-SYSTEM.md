# 🎨 Edison Tech Design System

## Design Philosophy

*"Every pixel tells a story. Every interaction delights the user."*

Our design system ensures visual consistency, professional polish, and delightful user experience across the entire Edison Tech platform.

---

## 🎯 Core Design Principles

### 1. **Clarity First**
- Information should be immediately understandable
- No cognitive overload
- Clear visual hierarchy

### 2. **Consistent & Predictable**
- Same patterns everywhere
- Users learn once, use everywhere
- No surprises, no confusion

### 3. **Professional & Trustworthy**
- Enterprise-grade appearance
- Attention to detail
- Build confidence through design

### 4. **Efficient & Fast**
- Minimal clicks to complete tasks
- Smart defaults
- Keyboard shortcuts

### 5. **Accessible to All**
- WCAG 2.1 AA compliant
- High contrast ratios
- Screen reader friendly

---

## 🎨 Color System

### Primary Palette

```
Primary (Blue)
- Main: #3B82F6 (rgb(59, 130, 246))
- Light: #60A5FA
- Dark: #2563EB
- Usage: Primary actions, links, main CTAs
```

```
Success (Green)
- Main: #10B981 (rgb(16, 185, 129))
- Light: #34D399
- Dark: #059669
- Usage: Success states, active items, positive actions
```

```
Warning (Amber)
- Main: #F59E0B (rgb(245, 158, 11))
- Light: #FBBF24
- Dark: #D97706
- Usage: Warnings, high priority, pending states
```

```
Danger (Red)
- Main: #EF4444 (rgb(239, 68, 68))
- Light: #F87171
- Dark: #DC2626
- Usage: Errors, urgent items, destructive actions
```

```
Info (Sky)
- Main: #0EA5E9 (rgb(14, 165, 233))
- Light: #38BDF8
- Dark: #0284C7
- Usage: Informational items, medium priority
```

### Neutral Palette

```
Gray Scale
- 50: #F9FAFB - Backgrounds, subtle borders
- 100: #F3F4F6 - Hover states, disabled inputs
- 200: #E5E7EB - Borders, dividers
- 300: #D1D5DB - Icons, secondary text
- 400: #9CA3AF - Placeholder text
- 500: #6B7280 - Body text
- 600: #4B5563 - Headings
- 700: #374151 - Dark headings
- 800: #1F2937 - Very dark text
- 900: #111827 - Black text
```

### Status Colors

```
Status Mapping:
- Draft/Inactive: Gray 400
- Planning: Info (Sky)
- Active/Sent: Success (Green)
- Pending: Warning (Amber)
- On Hold: Warning (Amber)
- Overdue: Danger (Red)
- Completed/Paid: Success (Green)
- Cancelled/Failed: Danger (Red)
```

### Priority Colors

```
Priority Mapping:
- Low: Gray 400
- Medium: Info (Sky)
- High: Warning (Amber)
- Urgent: Danger (Red)
```

---

## ✍️ Typography

### Font Stack

```css
Primary: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif
Monospace: 'Fira Code', 'Cascadia Code', Consolas, Monaco, monospace
```

### Type Scale

```
Heading 1: 2.25rem (36px) - font-bold - line-height: 1.2
Heading 2: 1.875rem (30px) - font-bold - line-height: 1.3
Heading 3: 1.5rem (24px) - font-semibold - line-height: 1.4
Heading 4: 1.25rem (20px) - font-semibold - line-height: 1.5
Body Large: 1.125rem (18px) - font-normal - line-height: 1.6
Body: 1rem (16px) - font-normal - line-height: 1.6
Body Small: 0.875rem (14px) - font-normal - line-height: 1.5
Caption: 0.75rem (12px) - font-medium - line-height: 1.4
```

### Font Weights

```
Light: 300 - Rarely used
Normal: 400 - Body text
Medium: 500 - Emphasis, labels
Semibold: 600 - Subheadings, buttons
Bold: 700 - Headings, important text
```

---

## 📏 Spacing System

Using 4px base unit:

```
xs: 0.25rem (4px) - Tight spacing
sm: 0.5rem (8px) - Component internal spacing
md: 1rem (16px) - Default spacing
lg: 1.5rem (24px) - Section spacing
xl: 2rem (32px) - Large section spacing
2xl: 3rem (48px) - Page-level spacing
3xl: 4rem (64px) - Major sections
```

### Application

```
Form field spacing: md (16px)
Section spacing: lg (24px)
Card padding: lg (24px)
Button padding: sm/md (8px/16px)
Table cell padding: sm/md (8px/16px)
```

---

## 🔘 Components

### Buttons

#### Primary Button
```
Background: Primary Blue
Text: White
Padding: 0.5rem 1rem (8px 16px)
Border Radius: 0.375rem (6px)
Font Weight: 500 (Medium)
Shadow: sm

Hover: Darken 10%
Active: Darken 20%
Disabled: Opacity 50%
```

#### Secondary Button
```
Background: Transparent
Border: 1px solid Gray 300
Text: Gray 700
Padding: 0.5rem 1rem
Border Radius: 0.375rem

Hover: Background Gray 50
Active: Background Gray 100
```

#### Danger Button
```
Background: Danger Red
Text: White
Same styling as Primary
```

### Badges

```
Padding: 0.125rem 0.625rem (2px 10px)
Border Radius: 9999px (fully rounded)
Font Size: 0.75rem (12px)
Font Weight: 500
Text Transform: Capitalize

Colors match status/priority colors
```

### Forms

#### Text Input
```
Border: 1px solid Gray 300
Border Radius: 0.375rem
Padding: 0.5rem 0.75rem (8px 12px)
Font Size: 1rem

Focus: Border Primary Blue, Ring 2px Blue/10
Error: Border Danger Red, Ring 2px Red/10
Disabled: Background Gray 50, Text Gray 400
```

#### Select Dropdown
```
Same as Text Input
Icon: Chevron Down, Gray 400
Dropdown: White background, Shadow lg
Item Hover: Background Gray 50
Item Selected: Background Primary/10, Text Primary
```

#### Toggle Switch
```
Width: 2.75rem (44px)
Height: 1.5rem (24px)
Border Radius: 9999px
Background Off: Gray 200
Background On: Primary Blue
Transition: All 200ms ease
```

#### Checkbox/Radio
```
Size: 1rem (16px)
Border: 2px solid Gray 300
Border Radius: 0.25rem (checkbox), 50% (radio)
Checked: Background Primary, Border Primary
Check Icon: White
```

### Tables

```
Header:
  Background: Gray 50
  Text: Gray 700
  Font Weight: 600
  Padding: 0.75rem 1rem (12px 16px)
  Border Bottom: 1px solid Gray 200

Rows:
  Padding: 0.75rem 1rem
  Border Bottom: 1px solid Gray 200

  Hover: Background Gray 50
  Selected: Background Primary/5

Alternating: Optional Gray 50/White
```

### Cards

```
Background: White
Border: 1px solid Gray 200
Border Radius: 0.5rem (8px)
Padding: 1.5rem (24px)
Shadow: sm

Hover: Shadow md
```

### Modals

```
Overlay: Black 50% opacity
Container: White, Rounded lg, Shadow 2xl
Max Width: 32rem (512px) default
Padding: 1.5rem (24px)

Header: Font size lg, Font weight 600
Body: Gray 600
Footer: Border top Gray 200, Padding top md
```

---

## 🎭 Icons

### Icon Library
Using **Heroicons** (Outline & Solid variants)

### Icon Sizes
```
xs: 1rem (16px) - Inline with text
sm: 1.25rem (20px) - Buttons, small actions
md: 1.5rem (24px) - Standard UI icons
lg: 2rem (32px) - Feature icons
xl: 3rem (48px) - Empty states, hero sections
```

### Icon Colors
```
Default: Gray 500 (body text color)
Primary: Primary Blue
Success: Success Green
Warning: Warning Amber
Danger: Danger Red
Muted: Gray 400
```

### Common Icons
```
Add/Create: heroicon-o-plus
Edit: heroicon-o-pencil
Delete: heroicon-o-trash
View: heroicon-o-eye
Search: heroicon-o-magnifying-glass
Filter: heroicon-o-funnel
Sort: heroicon-o-arrows-up-down
Settings: heroicon-o-cog-6-tooth
User: heroicon-o-user
Company: heroicon-o-building-office-2
Project: heroicon-o-briefcase
Task: heroicon-o-clipboard-document-list
Time: heroicon-o-clock
Invoice: heroicon-o-document-text
Payment: heroicon-o-credit-card
Success: heroicon-o-check-circle
Error: heroicon-o-x-circle
Warning: heroicon-o-exclamation-triangle
Info: heroicon-o-information-circle
```

---

## 📱 Responsive Breakpoints

```
sm: 640px - Mobile landscape
md: 768px - Tablet
lg: 1024px - Desktop
xl: 1280px - Large desktop
2xl: 1536px - Extra large
```

### Mobile-First Approach
```
Default: Mobile (< 640px)
Progressive Enhancement: Tablet, Desktop, Large

Touch Targets: Minimum 44x44px
Spacing: Increase on larger screens
Columns: 1 → 2 → 3 → 4 as screen grows
```

---

## ⚡ Animations & Transitions

### Transition Timing
```
Fast: 150ms - Small UI changes
Normal: 200ms - Most transitions
Slow: 300ms - Complex animations
```

### Easing Functions
```
ease-in: Accelerating
ease-out: Decelerating (preferred for UI)
ease-in-out: Smooth start and end
```

### Common Animations
```
Fade In: Opacity 0 → 1, 200ms
Slide In: Transform translateY(10px) → 0, 200ms
Scale: Transform scale(0.95) → 1, 150ms
Hover Lift: Transform translateY(0) → translateY(-2px), 150ms
```

### Loading States
```
Skeleton: Animated gradient shimmer
Spinner: Rotating circle
Progress Bar: Indeterminate slide
Pulse: Opacity animation
```

---

## 🎯 Status & Feedback

### Toast Notifications
```
Position: Top right
Width: Max 24rem (384px)
Duration: 3-5 seconds
Animation: Slide in from right

Success: Green background, White text, Check icon
Error: Red background, White text, X icon
Warning: Amber background, White text, Warning icon
Info: Blue background, White text, Info icon
```

### Empty States
```
Icon: Large (3rem), Gray 300
Heading: Gray 700, font-semibold
Description: Gray 500
Action Button: Primary, with plus icon
Padding: 3rem vertical
```

### Error States
```
Input Error: Red border, Red text below
Form Error: Red background/10, Red border, Red text
Page Error: Centered, Large icon, Clear message, Action button
```

### Loading States
```
Table: Skeleton rows with shimmer
Form: Disabled with spinner
Button: Disabled with inline spinner
Page: Full-page spinner or progress bar
```

---

## 📊 Data Visualization

### Charts
```
Colors: Use primary palette in order
Line Thickness: 2-3px
Grid Lines: Gray 200, 1px, dotted
Axes: Gray 600, font-medium
Labels: Gray 500, font-normal
Tooltips: White background, Shadow lg
```

### Stats Cards
```
Number: 2rem, font-bold, Gray 900
Label: 0.875rem, font-medium, Gray 500
Icon: Background Primary/10, Text Primary
Trend: Up arrow Green, Down arrow Red
Chart: Mini line chart, Primary color
```

---

## ♿ Accessibility

### Color Contrast
```
Normal Text: Minimum 4.5:1
Large Text (18px+): Minimum 3:1
UI Components: Minimum 3:1
```

### Focus States
```
Ring: 2px offset, Primary color
Outline: Never remove
Keyboard Navigation: Clear visual feedback
```

### Screen Readers
```
Alt Text: All images
ARIA Labels: All interactive elements
Semantic HTML: Proper heading hierarchy
Skip Links: Skip to main content
```

### Form Accessibility
```
Labels: Associated with inputs
Required: Clearly marked
Errors: Clear, associated with field
Help Text: Available to screen readers
```

---

## 🎨 Usage Guidelines

### Do's ✅
- Use consistent spacing throughout
- Follow the color system strictly
- Maintain visual hierarchy
- Use appropriate icon sizes
- Provide clear feedback
- Design for mobile first
- Test with real content
- Ensure accessibility

### Don'ts ❌
- Mix different shades randomly
- Use colors outside the palette
- Over-animate
- Ignore loading states
- Forget error states
- Skip empty states
- Neglect mobile experience
- Sacrifice accessibility

---

## 🔄 Component States

Every component should have:

1. **Default** - Normal state
2. **Hover** - Mouse over
3. **Active** - Being clicked
4. **Focus** - Keyboard focus
5. **Disabled** - Not interactable
6. **Loading** - Processing
7. **Error** - Something wrong
8. **Success** - Action completed
9. **Empty** - No data
10. **Selected** - Currently chosen

---

## 📐 Layout System

### Grid System
```
Columns: 12-column grid
Gutter: 1rem (16px) default
Max Width: 80rem (1280px) content area
Margins: 1rem mobile, 2rem desktop
```

### Sidebar Layout
```
Sidebar: Fixed 16rem (256px) on desktop
  - Collapsible on mobile
  - Sticky on scroll
  - Dark background option

Main Content: Flex-1, Padding 2rem
  - Max width for readability
  - Responsive padding
```

### Dashboard Layout
```
Stats Row: 2-5 columns depending on screen
Charts: Full width or half width
Tables: Full width, scrollable
Widgets: Card-based, draggable (future)
```

---

## 🎭 Brand Voice

### Tone
- Professional but friendly
- Clear and concise
- Helpful and supportive
- Confident but not arrogant

### Writing Style
```
Headlines: Title Case, Bold
Body: Sentence case, Regular
Buttons: Title Case, Medium weight
Labels: Sentence case, Medium weight
Errors: Clear, actionable, empathetic
Success: Positive, confirming
```

### Microcopy Examples
```
Empty State: "No projects yet. Create your first project to get started."
Error: "Oops! Something went wrong. Please try again."
Success: "Project created successfully!"
Loading: "Loading your projects..."
Delete Confirmation: "Are you sure? This action cannot be undone."
```

---

## 🚀 Performance

### Design Performance
```
Images: WebP format, lazy load
Icons: SVG, inline where possible
Fonts: Preload critical fonts
CSS: Critical CSS inline
Animations: GPU-accelerated properties only
```

### Perceived Performance
```
Skeleton Screens: While loading
Optimistic Updates: Immediate feedback
Progressive Loading: Load critical first
Smooth Transitions: Hide delays
```

---

## 📱 Platform-Specific

### Desktop
- Hover states important
- Keyboard shortcuts
- Dense information
- Multiple columns

### Tablet
- Balance of density and touch
- Collapsible sections
- Adaptive layouts
- Touch-friendly but information-rich

### Mobile
- Touch-first design
- Single column mostly
- Collapsible everything
- Bottom navigation options
- Thumb-friendly zones

---

## 🎯 Success Metrics

A good design is measured by:

1. **Task Completion Rate** - Can users complete tasks?
2. **Time on Task** - How quickly?
3. **Error Rate** - How many mistakes?
4. **Satisfaction Score** - Do users like it?
5. **Adoption Rate** - Do they use it?
6. **Return Rate** - Do they come back?

---

## 📚 Resources

### Design Tools
- Figma - Design files
- Heroicons - Icon library
- Tailwind CSS - Utility classes
- Filament - UI framework

### Inspiration
- dribbble.com/tags/dashboard
- uxdesign.cc
- pages.xyz

### Testing
- WAVE - Accessibility testing
- Lighthouse - Performance
- axe DevTools - Accessibility

---

## 🔄 Version History

**v1.0.0** - 2025-01-15
- Initial design system
- Complete color palette
- Typography scale
- Component library
- Accessibility guidelines

---

*This design system is a living document. As we learn and grow, it evolves. Every designer, developer, and stakeholder should reference this as the single source of truth for all design decisions.*

**Design Team**: 20 Specialized Agents
**Status**: Active & Evolving
**Next Review**: Quarterly or as needed
