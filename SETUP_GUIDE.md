# FoodieDash - Food Delivery App Setup Guide

## 🎨 Recent Updates

### 1. **Modern Food-Related Design**
Your food ordering application now features:
- **Food-themed Login/Register Background**: Beautiful SVG pattern with food emojis (🍕 🍔 🍜)
- **Professional Color Scheme**: Orange and yellow gradients throughout
- **Modern Card Styling**: Sleek white cards with shadows and hover effects

### 2. **Food Images & Product Cards**
All dishes now display with:
- **High-quality images** from Unsplash
- **Dish names** prominently displayed
- **Descriptions** of each item
- **Prices** clearly shown
- **Beautiful hover animations**

### 3. **Database Setup with 18 Food Items**

## 🚀 Quick Start Guide

### Step 1: Populate the Menu Database
Visit one of these URLs to add food items to your database:

**Option A: Using the Setup Page (Recommended)**
```
http://localhost/task4/setup.html
```
Click the "Populate Menu Now" button to add all items at once.

**Option B: Direct Database Seeding**
```
http://localhost/task4/seed_menu.php
```

### What Gets Added:
✅ **6 Food Categories:**
- 🍕 Pizza
- 🍔 Burgers
- 🍜 Noodles
- 🌯 Wraps
- 🍰 Desserts
- 🥤 Beverages

✅ **18 Delicious Menu Items** including:
- Margherita Pizza
- Pepperoni Pizza
- Veggie Deluxe Pizza
- Classic Burger
- Cheese Burger
- Bacon Burger
- Spicy Ramen
- Chicken Ramen
- Veggie Ramen
- Falafel Wrap
- Grilled Chicken Wrap
- Veggie Wrap
- Chocolate Cake
- Cheesecake
- Ice Cream Sundae
- Fresh Lemonade
- Iced Coffee
- Mango Smoothie

✅ **Professional Images** from Unsplash for each dish
✅ **Realistic Pricing** ($3.99 - $14.99)
✅ **Detailed Descriptions** for each item

---

## 📁 Key Files Modified/Created

### CSS Updates
- **`/assets/css/style.css`**
  - Food-themed gradient background for auth pages
  - Enhanced product card styling
  - Better food image display
  - Improved responsive design

### New Setup Files
- **`setup.html`** - Beautiful setup wizard interface
- **`seed_menu.php`** - Database seeding script with 18 food items

### Updated Files
- **`/customer/index.php`** - Enhanced to display food images properly
  - Supports both CDN URLs and local images
  - Fallback for missing images
  - Better product card layout

---

## 🎯 Features

### Login/Register Pages
- 🎨 Food-themed SVG background pattern
- 🏢 Professional dark overlay for readability
- 📱 Fully responsive design
- ✨ Smooth animations

### Menu Display
- 🖼️ High-resolution food images
- 📝 Clear dish names and descriptions
- 💰 Price display
- ⚡ Hover animations with zoom effect
- 📱 Mobile-friendly grid layout

---

## ⚙️ Technical Details

### Image Handling
The system supports:
- **CDN Images**: Direct URLs from Unsplash
- **Local Images**: Files in `/assets/uploads/`
- **Fallback**: Graceful SVG placeholder if image fails to load

### Browser Compatibility
- ✅ Chrome
- ✅ Firefox
- ✅ Safari
- ✅ Edge
- ✅ Mobile browsers

---

## 🔧 Customization

### Change Colors
Edit the CSS variables in `/assets/css/style.css`:
```css
:root {
    --primary: #ff6b35;        /* Main orange */
    --primary-light: #ff8a5a;  /* Light orange */
    --primary-dark: #e55a2b;   /* Dark orange */
    --secondary: #f7931e;      /* Yellow-orange */
}
```

### Add More Food Items
1. Edit `seed_menu.php`
2. Add new items to the `$menuItems` array
3. Include image URL from Unsplash or your server
4. Run the seeding script again

### Update Images
You can replace Unsplash URLs with:
- Your own hosted images in `/assets/uploads/`
- Other image services
- Local file paths

---

## 📱 Responsive Breakpoints

- **Desktop**: 1024px+ (3-column grid)
- **Tablet**: 768px-1023px (2-column grid)
- **Mobile**: 480px-767px (2-column grid)
- **Small Mobile**: <480px (optimized layout)

---

## ✨ Design Highlights

### Food Background
The login/register pages feature:
- Embedded SVG with food emoji patterns (🍕 🍔 🍜)
- Dark overlay (opacity 50-60%) for text readability
- Fixed background attachment for depth effect
- Smooth gradient overlay

### Product Cards
Each dish displays:
- Beautiful image with hover zoom effect
- Dish name in bold
- Short description (60 characters)
- Price in orange color
- Add to cart button with hover animation

---

## 🐛 Troubleshooting

### Images Not Loading
1. Check internet connection (for Unsplash CDN)
2. Verify image URLs are accessible
3. Check browser console for errors
4. Try accessing Unsplash directly

### Database Errors
1. Ensure database connection is working
2. Check if categories table exists
3. Verify menu_items table has 'image' column
4. Run the seed script from setup.html

### Styling Issues
1. Clear browser cache (Ctrl+Shift+Delete)
2. Hard refresh page (Ctrl+Shift+R)
3. Check CSS file is loading (F12 > Network)

---

## 🎓 Learning Resources

### CSS Features Used
- CSS Grid and Flexbox
- CSS Variables
- Gradients and Overlays
- CSS Animations
- Media Queries
- Box Shadows
- Backdrop Filters

### Best Practices
- Mobile-first responsive design
- Semantic HTML
- Accessibility considerations
- Performance optimizations
- Clean, maintainable code

---

## 📞 Support

For issues or questions:
1. Check this guide
2. Review browser console (F12)
3. Verify database connection
4. Check file permissions
5. Ensure all dependencies are installed

---

**Last Updated**: May 12, 2026
**Version**: 2.0
**Status**: Ready for Production ✅
