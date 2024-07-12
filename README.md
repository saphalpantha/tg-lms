### Project Setup
- Initialize a new WordPress plugin named "TG LMS" with the text-domain 'tg-lms'.
- Set up the GitHub repository named ‘tg-lms’ for version control.

### Admin Side Development
1. **Menu Creation:**
   - Use `add_menu_page` and `add_submenu_page` to create an admin menu named 'Courses' with sub-menus 'All Courses' and 'New Course'.

2. **Custom Post Type:**
   - Register the custom post type 'tg-course' with fields (Title, Description, Image, Price, Category, Status) using `register_post_type`.

3. **All Courses Page:**
   - Develop a React-based admin interface to list all courses.
   - Implement actions for editing, deleting, and viewing courses.
   - Add a button to add new courses.
   - Implement search by Title, filter by Status, and sort by Title using React Query.
   - Add pagination using Chakra UI components.

### Frontend Development
1. **Course Listing:**
   - Create a shortcode to list all courses.
   - Implement pagination and Ajax search for courses.
   - Ensure clicking on a course redirects to a single course page.

2. **Custom Query:**
   - Develop a custom query to retrieve free or paid courses.

### Plugin Specifications Implementation
1. **Translation Ready:**
   - Make the plugin translation-ready using WordPress internationalization functions.

2. **Security:**
   - Implement sanitization and escaping for all inputs.
   - Use nonces for form and URL security.

3. **Extensibility:**
   - Add hooks and filters to allow easy extension of the plugin.

4. **Code Quality and Documentation:**
   - Follow PSR-4 coding standards.
   - Write clear doc comments and maintain consistent code formatting.
