# EcoPower Tracker Assets

## Minification Process

### CSS Minification
To create minified CSS files:
1. Use a CSS minifier tool or build process
2. Remove comments, whitespace, and optimize selectors
3. Save as `.min.css` files

### JavaScript Minification  
To create minified JS files:
1. Use a JS minifier like UglifyJS, Terser, or online tools
2. Remove comments, whitespace, and optimize code
3. Save as `.min.js` files

### Required Minified Files
- `ecopower-tracker-frontend.min.css`
- `ecopower-tracker-frontend.min.js` 
- `ecopower-tracker-admin.min.css`
- `ecopower-tracker-admin.min.js`

### Fallback Behavior
If minified files don't exist, the system automatically falls back to the regular versions.

### Debug Mode
When `SCRIPT_DEBUG` is true, non-minified versions are always used for easier debugging.
