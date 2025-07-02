import './bootstrap'; // Contains CoreUI main JS import and window.coreui setup
import 'laravel-datatables-vite';


// Import CoreUI Utilities
import * as coreui from '@coreui/coreui'; // This is already handled by bootstrap.js, but good for clarity
window.coreui = coreui; // Ensure CoreUI is globally accessible


// Import CoreUI Icons (if you use them as CIcon components or directly)
// You might not need to import all icons directly if you only use them via SVG sprites.
// If you plan to use them as web components or directly in JS, you might do:
// import { cilChartPie, cilSpeedometer } from '@coreui/icons';
// const coreuiIcons = {
//   cilChartPie,
//   cilSpeedometer,
//   // ... other icons you use
// };
// window.coreuiIcons = coreuiIcons;

import coreuiIconsSprite from '@coreui/icons/sprites/free.svg';
window.coreuiIconsSpritePath = coreuiIconsSprite; // Expose if you need it in JS

// --- Chart.js and CoreUI Chart.js Integration ---
// import { Chart, registerables } from 'chart.js';

// // Register all Chart.js components (lines, bars, etc.)
// Chart.register(...registerables);

// // Make Chart.js and CChart available globally if needed for Blade-rendered charts
// window.Chart = Chart;

// // Alternatively, for Charts, you can often render them within a Blade file using
// // a script tag that runs after the DOM is loaded, getting the canvas element.
// // Example:
// // document.addEventListener('DOMContentLoaded', function() {
// //   const ctx = document.getElementById('myChart');
// //   if (ctx) {
// //     new Chart(ctx, {
// //       type: 'bar',
// //       data: { /* ... */ }
// //     });
// //   }
// // });


// --- SimpleBar Integration ---
import SimpleBar from 'simplebar';
import 'simplebar/dist/simplebar.min.css'; // Already imported in app.scss, but good to have here too for JS side.

// Initialize SimpleBar on elements with a specific class (e.g., .simplebar-content)
// CoreUI often uses SimpleBar for its sidebar. You might need to initialize it
// after your DOM content is loaded.
document.addEventListener('DOMContentLoaded', function() {
    const simpleBarElements = document.querySelectorAll('[data-coreui="simplebar"]');
    simpleBarElements.forEach(element => {
        new SimpleBar(element);
    });
    // CoreUI's own JS typically handles SimpleBar initialization for its components.
    // You might only need this if you're using SimpleBar on custom elements.
});

// You can also expose SimpleBar globally if you need to manually initialize it later
window.SimpleBar = SimpleBar;

// CoreUI Utils don't usually require explicit global import, they are often
// functions you'd import and use directly in specific JS files if needed.
// Example: import { getStyle } from '@coreui/utils/src';

import './color-modes'
import './colors'
import './config'
import './popovers'
import './toasts'
import './tooltips'