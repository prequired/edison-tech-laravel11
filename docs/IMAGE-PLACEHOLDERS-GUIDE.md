# Image & Logo Placeholders Guide

## Overview

The Edison Tech Filament admin panel includes comprehensive image and logo support with proper sizing, placeholders, and visual consistency throughout the application.

---

## Brand Assets

### 1. Brand Logo
**Location**: `/public/images/edison-tech-logo.svg`

**Specifications**:
- **Dimensions**: 200×60px
- **Format**: SVG (vector, infinitely scalable)
- **Colors**:
  - Primary Blue: `#3B82F6`
  - Text Dark: `#1E293B`
  - Text Gray: `#64748B`
- **Usage**: Admin panel navigation, headers, login page
- **Features**:
  - Lightning bolt icon (Edison's innovation symbol)
  - "Edison Tech" text
  - "Project Management" subtitle

### 2. Favicon
**Location**: `/public/images/favicon.svg`

**Specifications**:
- **Dimensions**: 32×32px
- **Format**: SVG
- **Colors**: Primary Blue background with white lightning bolt
- **Usage**: Browser tabs, bookmarks

---

## Company Logos

### Upload Specifications
**Field**: Company → `logo`
**Location**: `CompanyResource` form

#### Size Requirements
- **Maximum File Size**: 2MB
- **Recommended Dimensions**: 512×512px
- **Aspect Ratio**: 1:1 (square)
- **Accepted Formats**:
  - PNG (recommended for transparency)
  - JPG/JPEG
  - SVG (vector)
  - WebP (modern format)

#### Upload Features
- ✅ **Image Editor**: Built-in cropping and editing
- ✅ **Aspect Ratio Options**: 1:1, 16:9, 4:3
- ✅ **Auto-resize**: Resizes to 512×512px on upload
- ✅ **Preview**: 120px height preview during upload
- ✅ **Validation**: File type and size validation

#### Display Sizes
1. **Table View**: 48px circular
2. **InfoList View**: 120px circular
3. **Form View**: 120px preview height

#### Default Placeholder
When no logo is uploaded, a dynamic placeholder is generated using UI Avatars:

```
https://ui-avatars.com/api/?name={Company Name}&color=3B82F6&background=EFF6FF&size=512
```

**Features**:
- Company name initials
- Primary blue color (`#3B82F6`)
- Light blue background (`#EFF6FF`)
- 512px size for crisp display

**Example**:
- Company: "Acme Corporation" → Shows "AC" in circular badge

---

## User Avatars

### Upload Specifications
**Field**: User → `avatar`
**Location**: `UserResource` form

#### Size Requirements
- **Maximum File Size**: 1MB
- **Recommended Dimensions**: 256×256px
- **Aspect Ratio**: 1:1 (square, mandatory)
- **Accepted Formats**:
  - PNG (recommended)
  - JPG/JPEG
  - WebP

#### Upload Features
- ✅ **Image Editor**: Built-in cropping and editing
- ✅ **Aspect Ratio**: Fixed 1:1 for circular display
- ✅ **Auto-resize**: Resizes to 256×256px on upload
- ✅ **Preview**: 120px height preview during upload
- ✅ **Validation**: File type and size validation

#### Display Sizes
1. **Table View**: 40px circular
2. **InfoList View**: 120px circular
3. **Form View**: 120px preview height
4. **Navigation**: 32px circular (user menu)

#### Default Placeholder
When no avatar is uploaded, a dynamic placeholder is generated using UI Avatars:

```
https://ui-avatars.com/api/?name={User Name}&color=3B82F6&background=EFF6FF&size=256
```

**Features**:
- User name initials
- Primary blue color (`#3B82F6`)
- Light blue background (`#EFF6FF`)
- 256px size for crisp display

**Example**:
- User: "John Smith" → Shows "JS" in circular badge

---

## Document Thumbnails

### Upload Specifications
**Field**: Document → `file_path`
**Location**: `DocumentResource` form

#### Size Requirements
- **Maximum File Size**: 10MB (for documents)
- **Image Preview Sizes**:
  - PDF: First page rendered at 200×280px
  - Images: Thumbnail at 200px width
- **Accepted Formats**: All common document and image formats

#### Display Features
- **File Type Icons**: Heroicons for different file types
- **Image Previews**: For image files (PNG, JPG, etc.)
- **Document Icons**: For non-image files (PDF, DOC, etc.)

#### Icon Sizes
1. **Table View**: 24px icon
2. **InfoList View**: 48px icon
3. **Preview Modal**: Full-size image

---

## Image Best Practices

### For Company Logos
1. **Use Vector Formats** (SVG) when possible for infinite scalability
2. **Transparent Backgrounds** work best (PNG or SVG)
3. **Square Aspect Ratio** ensures proper circular cropping
4. **High Resolution**: Minimum 512×512px for crisp display on retina screens
5. **Color Considerations**: Ensure logo works on both light and dark backgrounds

### For User Avatars
1. **Headshot Photos** work best (face centered)
2. **Good Lighting**: Clear, well-lit photos
3. **Neutral Background**: Solid color or blurred background
4. **Proper Framing**: Face should occupy 60-80% of the frame
5. **High Quality**: Minimum 256×256px, preferably 512×512px

### For Document Thumbnails
1. **Clear Text**: Ensure text is readable in thumbnail
2. **High Contrast**: Good contrast between text and background
3. **Proper Format**: PDF for multi-page documents
4. **Optimize Size**: Compress images before upload to stay under limits

---

## Placeholder API Details

### UI Avatars API
**Service**: https://ui-avatars.com/

#### Parameters Used
- `name`: Text to generate initials from (URL encoded)
- `color`: Foreground color (hex without #)
- `background`: Background color (hex without #)
- `size`: Image size in pixels (512 or 256)

#### Example Request
```
GET https://ui-avatars.com/api/?name=Edison+Tech&color=3B82F6&background=EFF6FF&size=512
```

#### Response
- Returns PNG image
- Caches for 7 days
- Always available (no authentication required)

#### Customization Options (Available but not used)
- `font-size`: Text size (0.1 to 1, default 0.5)
- `length`: Number of initials (default 2)
- `rounded`: Whether to round corners (boolean)
- `bold`: Whether text is bold (boolean)

---

## Image Storage

### Directory Structure
```
public/
├── images/
│   ├── edison-tech-logo.svg     # Brand logo
│   └── favicon.svg               # Favicon
│
storage/
└── app/
    └── public/
        ├── companies/
        │   └── logos/             # Company logos (512×512px)
        │       ├── logo-uuid.png
        │       └── logo-uuid.svg
        │
        └── users/
            └── avatars/           # User avatars (256×256px)
                ├── avatar-uuid.jpg
                └── avatar-uuid.png
```

### Symbolic Link
Ensure storage link is created:
```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

---

## Image Processing

### Upload Flow
1. **User selects file** → Client-side preview
2. **File uploaded** → Server receives file
3. **Validation** → Check size, type, dimensions
4. **Image Editor** → User crops/adjusts (optional)
5. **Processing** → Resize to target dimensions
6. **Storage** → Save to appropriate directory
7. **Database** → Store file path in model

### Auto-resize Settings

#### Company Logos
```php
->imageResizeTargetWidth('512')
->imageResizeTargetHeight('512')
->imageCropAspectRatio('1:1')
```
- Resizes to exactly 512×512px
- Maintains 1:1 aspect ratio
- Centers content if needed

#### User Avatars
```php
->imageResizeTargetWidth('256')
->imageResizeTargetHeight('256')
->imageCropAspectRatio('1:1')
```
- Resizes to exactly 256×256px
- Maintains 1:1 aspect ratio
- Centers face if possible

---

## Responsive Behavior

### Desktop (1920×1080)
- Company logos: 48px table, 120px detail
- User avatars: 40px table, 120px detail
- Brand logo: 60px height in navigation

### Tablet (768×1024)
- Company logos: 40px table, 100px detail
- User avatars: 36px table, 100px detail
- Brand logo: 50px height in navigation

### Mobile (375×667)
- Company logos: 36px table, 80px detail
- User avatars: 32px table, 80px detail
- Brand logo: 40px height in collapsed navigation

---

## Accessibility

### Image Alt Text
All images include proper alt text:
- **Company logos**: "Logo of {Company Name}"
- **User avatars**: "Avatar of {User Name}"
- **Brand logo**: "Edison Tech Project Management"

### Color Contrast
- **Placeholders**: 4.5:1 contrast ratio (WCAG AA)
- **Icons**: Visible on all backgrounds
- **Focus states**: Clear 2px outline

### Screen Readers
- Images have descriptive aria-labels
- Decorative images are properly marked
- Focus order follows logical flow

---

## Performance Optimization

### Image Optimization
1. **Compression**: Auto-compress on upload
2. **Format**: Use WebP when supported
3. **Lazy Loading**: Load images as needed
4. **Caching**: Browser cache for 30 days
5. **CDN**: Consider CDN for production

### Loading Strategy
1. **Placeholders first**: Show placeholder immediately
2. **Progressive loading**: Load actual image in background
3. **Blur up**: Blur placeholder while loading
4. **Error handling**: Fallback to placeholder on error

---

## Troubleshooting

### Common Issues

#### 1. Image Not Displaying
**Possible Causes**:
- Storage link not created
- Incorrect permissions
- Wrong file path

**Solution**:
```bash
php artisan storage:link
chmod -R 775 storage
chmod -R 775 public/storage
```

#### 2. Upload Fails
**Possible Causes**:
- File too large
- Invalid file type
- Insufficient storage

**Solution**:
- Check `php.ini` settings:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  ```
- Verify disk space: `df -h`

#### 3. Image Quality Poor
**Possible Causes**:
- Source image too small
- Over-compression
- Wrong format

**Solution**:
- Use higher resolution source (2x target size)
- Reduce compression level
- Use PNG for logos, JPG for photos

#### 4. Circular Crop Off-Center
**Possible Causes**:
- Non-square aspect ratio
- Face detection failed

**Solution**:
- Manually crop to square before upload
- Use image editor in Filament to adjust
- Center subject in photo

---

## Future Enhancements

### Planned Features
- [ ] **Image Optimization**: Automatic WebP conversion
- [ ] **Multiple Sizes**: Generate thumbnails automatically
- [ ] **CDN Integration**: Serve images from CDN
- [ ] **AI Cropping**: Intelligent face detection and cropping
- [ ] **Bulk Upload**: Upload multiple images at once
- [ ] **Gallery View**: Browse uploaded images
- [ ] **Image Editor**: Advanced editing tools (filters, adjustments)
- [ ] **Dark Mode Variants**: Separate images for dark mode

---

## Code Examples

### Company Logo Upload
```php
Forms\Components\FileUpload::make('logo')
    ->label('Company Logo')
    ->image()
    ->imageEditor()
    ->imageEditorAspectRatios(['1:1', '16:9', '4:3'])
    ->directory('companies/logos')
    ->visibility('public')
    ->maxSize(2048)
    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'])
    ->helperText('Upload a company logo (PNG, JPG, SVG, or WebP, max 2MB). Recommended size: 512×512px')
    ->imagePreviewHeight('120')
    ->imageCropAspectRatio('1:1')
    ->imageResizeTargetWidth('512')
    ->imageResizeTargetHeight('512')
    ->columnSpanFull();
```

### Company Logo Display (Table)
```php
Tables\Columns\ImageColumn::make('logo')
    ->label('Logo')
    ->circular()
    ->size(48)
    ->defaultImageUrl(fn() => 'https://ui-avatars.com/api/?name=' . urlencode('Company') . '&color=3B82F6&background=EFF6FF&size=512')
    ->toggleable();
```

### User Avatar Upload
```php
Forms\Components\FileUpload::make('avatar')
    ->label('Profile Picture')
    ->image()
    ->imageEditor()
    ->imageEditorAspectRatios(['1:1'])
    ->directory('users/avatars')
    ->visibility('public')
    ->maxSize(1024)
    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])
    ->helperText('Upload a profile picture (PNG, JPG, or WebP, max 1MB). Recommended size: 256×256px')
    ->imagePreviewHeight('120')
    ->imageCropAspectRatio('1:1')
    ->imageResizeTargetWidth('256')
    ->imageResizeTargetHeight('256')
    ->columnSpanFull();
```

### User Avatar Display (Table)
```php
Tables\Columns\ImageColumn::make('avatar')
    ->label('Avatar')
    ->circular()
    ->size(40)
    ->defaultImageUrl(fn(User $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=3B82F6&background=EFF6FF&size=256')
    ->toggleable();
```

---

## Summary

✅ **Brand Assets**: Custom logo and favicon in SVG format
✅ **Company Logos**: 512×512px with upload, crop, and resize
✅ **User Avatars**: 256×256px with upload, crop, and resize
✅ **Placeholders**: Dynamic UI Avatars with brand colors
✅ **Circular Display**: All profile images displayed circular
✅ **Proper Sizing**: Responsive sizes for table, detail, and form views
✅ **Validation**: File type, size, and dimension validation
✅ **Image Editor**: Built-in cropping and editing tools
✅ **Accessibility**: Proper alt text and ARIA labels
✅ **Performance**: Optimized loading and caching

**Result**: Professional, consistent, and brand-aligned image handling throughout the Edison Tech admin panel.

---

**Version**: 1.0.0
**Last Updated**: 2025-11-13
**Maintained By**: Edison Tech Development Team
