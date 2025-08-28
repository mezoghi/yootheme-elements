# YOOtheme Products Element - Advanced Filtering & Dynamic Pagination

A powerful YOOtheme Builder element that provides advanced multi-select filtering with dynamic pagination for product displays.

## 🚀 Features

### **Advanced Multi-Select Filtering**
- **Category-based Logic**: Different logic for different tag categories (Colors AND Sizes vs Colors OR Sizes)
- **Flexible AND/OR Logic**: Configurable logic between and within categories
- **Real-time Filtering**: Instant results without page refresh
- **Smooth Animations**: Fade, Slide, and Delayed Fade animations
- **No Results Message**: Customizable message when no products match filters

### **Dynamic Pagination**
- **Smart Style Switching**: Automatically switches to "Load More" when filters are applied
- **Multiple Pagination Styles**: Default (numbered), Previous/Next, Load More Button
- **Configurable Items Per Page**: 1-50 items per page
- **Responsive Design**: Mobile-optimized with dark mode support
- **Smooth Animations**: UIkit-compatible transitions

### **Integration Features**
- **Seamless YOOtheme Integration**: Works with existing YOOtheme grid systems
- **Filter Animation Sync**: Matches YOOtheme's native filter animations
- **UIkit Compatible**: Uses UIkit classes and components
- **Event-Driven Architecture**: Custom events for extensibility

## 📁 File Structure

```
products/
├── element.json              # Element configuration & settings
├── element.php              # PHP element registration
├── templates/
│   └── template.php         # Main template with filtering UI
├── app/
│   ├── multiselect-filter.js    # Core filtering logic
│   ├── multiselect-filter.css   # Filtering styles & animations
│   └── pagination.js           # Dynamic pagination system
└── README.md               # This documentation
```



## 🎯 Filtering Logic Examples

### **AND Between Categories + OR Within Categories** (Default)
- User selects: `Red + Blue` (Colors) + `Large` (Sizes)
- Result: Items that have `(Red OR Blue) AND Large`
- Logic: Must match at least one color AND must have the size

### **OR Between Categories + OR Within Categories**
- User selects: `Red + Blue` (Colors) + `Large` (Sizes)  
- Result: Items that have `(Red OR Blue) OR Large`
- Logic: Must match at least one color OR must have the size

### **AND Between Categories + AND Within Categories**
- User selects: `Red + Blue` (Colors) + `Large + Medium` (Sizes)
- Result: Items that have `(Red AND Blue) AND (Large AND Medium)`
- Logic: Must have both colors AND both sizes

## 🔄 Dynamic Pagination Behavior

### **Initial State**
- Shows configured pagination style (Default, Previous/Next, etc.)
- Displays items according to "Items Per Page" setting

### **When Filters Applied**
- **Automatically switches** to "Load More" style
- Shows only first page of filtered results
- "Load More" button appears if more filtered items exist

### **When Filters Cleared**
- **Restores original** pagination style
- Returns to normal pagination behavior
- Shows all items with original pagination

## 🛠️ Installation & Setup

### **1. Copy Files**
Copy the entire `products/` folder to your YOOtheme theme's builder directory:
```
wp-content/themes/your-theme/builder/products/
```

### **2. Element Registration**
The element will automatically register when YOOtheme scans the builder directory.

### **3. Add to Page**
1. Open YOOtheme Builder
2. Add "Products" element to your page
3. Configure filtering and pagination options
4. Add your product content with appropriate tags


## 🎨 Styling & Customization

### **CSS Classes**
- `.multiselect-filter-container` - Main container
- `.filter-tag-checkbox` - Individual filter checkboxes
- `.filter-all-checkbox` - "All" checkbox
- `.no-results-message` - No results message
- `.pagination-container` - Pagination wrapper
- `.load-more-btn` - Load More button

### **Animation Classes**
- `.filter-fade-in` / `.filter-fade-out` - Fade animations
- `.filter-slide-in` / `.filter-slide-out` - Slide animations
- `.uk-animation-*` - UIkit animation classes




## 📝 Changelog

### **v1.3.0** - Dynamic Pagination
- ✅ Added dynamic pagination style switching
- ✅ Load More integration with filtering
- ✅ Enhanced event system
- ✅ Improved memory management

### **v1.2.0** - Enhanced Filtering
- ✅ Category-based AND/OR logic
- ✅ Animation control settings
- ✅ No results message
- ✅ Pagination integration

### **v1.0.0** - Initial Release
- ✅ Multi-select filtering
- ✅ Basic pagination
- ✅ UIkit integration



**Developed for YOOtheme Builder** | **Compatible with UIkit 3.x** | **Responsive & Mobile-Friendly**
