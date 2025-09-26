# Project Brief: Hostify Booking Plugin - Datepicker Enhancement

## 1. Overview

This project focuses on enhancing the datepicker component within the Hostify Booking WordPress plugin. The goal is to improve the user experience on mobile devices by implementing a full-screen view and vertical month scrolling.

## 2. Core Requirements

*   **Mobile Full-Screen:** When the datepicker is opened on a mobile device, it should occupy the full screen.
*   **Vertical Month Scrolling:** On mobile devices, users should be able to navigate through months by scrolling or swiping vertically, similar to the Airbnb datepicker experience. The current horizontal navigation with arrows should be replaced or adapted for this vertical flow on mobile.
*   **Maintain Desktop Functionality:** The existing desktop datepicker functionality should remain unchanged.
*   **Forked Library:** The changes will be made to a forked version of the `hotel-datepicker.js` library located within the plugin's `public/res/lib/datepicker/` directory.
*   **Build Process:** Any modifications to the library's source files require a rebuild using the command `npm run-all -serial --silen build`.

## 3. Goals

*   Improve mobile usability and aesthetics of the datepicker.
*   Provide a more intuitive and modern date selection experience on smaller screens.
*   Ensure the enhanced mobile view integrates seamlessly with the Hostify Booking plugin.

## 4. Scope

*   Modify the JavaScript (`hotel-datepicker.js`, potentially `datepicker.js`) and CSS (`main.css` or library-specific CSS) files related to the datepicker.
*   Implement conditional logic or styling to apply changes only on mobile viewports.
*   Execute the build command after changes are made.

## 5. Out of Scope

*   Changes to the core Hostify Booking plugin logic beyond the datepicker integration.
*   Major refactoring of the `hotel-datepicker.js` library unrelated to the mobile enhancements.
*   Implementing features not explicitly mentioned in the requirements (e.g., new date validation rules, different themes).
