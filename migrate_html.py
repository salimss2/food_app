import os
import re
import shutil

src_dir = r"f:/admin_dashboard/نسخ الاحتياطية/3/admin_dashboard"
dest_app_dir = r"f:/admin_dashboard/نسخ الاحتياطية/3/food_app"
admin_module_dir = os.path.join(dest_app_dir, "Modules", "Admin")

views_dir = os.path.join(admin_module_dir, "resources", "views")
layouts_dir = os.path.join(views_dir, "layouts")
partials_dir = os.path.join(layouts_dir, "partials")
public_css_dir = os.path.join(dest_app_dir, "public", "modules", "admin", "css")
public_js_dir = os.path.join(dest_app_dir, "public", "modules", "admin", "js")
routes_file = os.path.join(admin_module_dir, "routes", "web.php")

# Ensure directories exist
os.makedirs(layouts_dir, exist_ok=True)
os.makedirs(partials_dir, exist_ok=True)
os.makedirs(public_css_dir, exist_ok=True)
os.makedirs(public_js_dir, exist_ok=True)

# 1. Move Static Assets
css_src = os.path.join(src_dir, "css")
if os.path.exists(css_src):
    for f in os.listdir(css_src):
        shutil.copy2(os.path.join(css_src, f), os.path.join(public_css_dir, f))

js_src = os.path.join(src_dir, "js")
if os.path.exists(js_src):
    for f in os.listdir(js_src):
        shutil.copy2(os.path.join(js_src, f), os.path.join(public_js_dir, f))

# Helper to process file content
def process_content(content, filename):
    # Fix Asset Links
    content = re.sub(r'href="css/([^"]+)"', lambda m: f'href="{{{{ asset(\'modules/admin/css/{m.group(1)}\') }}}}"', content)
    content = re.sub(r'src="js/([^"]+)"', lambda m: f'src="{{{{ asset(\'modules/admin/js/{m.group(1)}\') }}}}"', content)
    
    # Specific active logic for sidebar
    if filename == 'sidebar.html':
        def fix_sidebar_link_class(match):
            full_match = match.group(0)
            href = match.group(1)
            classes = match.group(2)
            
            target = href.replace('.html', '')
            if target == 'index':
                target = 'dashboard'
                
            # Replace static active/inactive classes
            for c in ["text-primary", "bg-indigo-50", "border-l-4", "border-primary", "rounded-r-lg", "text-gray-600", "hover:text-gray-800", "border-transparent", "px-6"]:
                classes = classes.replace(c, "").replace("  ", " ").strip()
                
            dynamic_class = f'px-6 {{{{ request()->routeIs(\'admin.{target}\') ? \'text-primary bg-indigo-50 border-l-4 border-primary rounded-r-lg\' : \'text-gray-600 hover:text-gray-800 border-l-4 border-transparent\' }}}}'
            new_classes = f'{classes} {dynamic_class}'.strip()
            
            return f'<a href="{{{{ route(\'admin.{target}\') }}}}" class="{new_classes}"'

        content = re.sub(r'<a href="([^"]+\.html)".*?class="([^"]+)"', fix_sidebar_link_class, content, flags=re.DOTALL)

    # Fix Navigation Wiring (Internal Links)
    def href_replacer(match):
        target = match.group(1)
        if target.startswith('http') or target.startswith('#') or target.startswith('mailto:') or target.startswith('javascript:'):
            return match.group(0)
        
        # Determine route name
        route_name = target.replace('.html', '')
        if route_name == 'index':
            route_name = 'dashboard'
        
        # Override specific undefined routes
        if route_name == 'forgot-password':
            return 'href="#"'
            
        return f'href="{{{{ route(\'admin.{route_name}\') }}}}"'
        
    content = re.sub(r'href="([^"]+\.html)"', href_replacer, content)

    return content

# Read files and compile routes
gathered_routes = []

# 2. Process layout components
components_dir = os.path.join(src_dir, "components")
if os.path.exists(components_dir):
    for f in os.listdir(components_dir):
        if f.endswith('.html'):
            with open(os.path.join(components_dir, f), "r", encoding="utf-8") as file_in:
                content = file_in.read()
            
            content = process_content(content, f)
            blade_name = f.replace('.html', '.blade.php')
            with open(os.path.join(partials_dir, blade_name), "w", encoding="utf-8") as file_out:
                file_out.write(content)

# 3. Process main pages
for f in os.listdir(src_dir):
    if f.endswith('.html') and f != 'sidebar_new.html':
        with open(os.path.join(src_dir, f), "r", encoding="utf-8") as file_in:
            content = file_in.read()
        
        content = process_content(content, f)
        blade_name = f.replace('.html', '.blade.php')
        
        with open(os.path.join(views_dir, blade_name), "w", encoding="utf-8") as file_out:
            file_out.write(content)
            
        route_name = f.replace('.html', '')
        if route_name == 'index':
            route_name = 'dashboard'
        gathered_routes.append(route_name)


# 4. Append routes to web.php
# we need to append standard Route::get('/page', ...) closures to the existing prefix('admin') group or add a new admin group.
with open(routes_file, "r", encoding="utf-8") as file_in:
    routes_content = file_in.read()

new_routes = "\n\n// Integrated Routes\nRoute::prefix('admin')->group(function () {\n"
for route in set(gathered_routes):
    # Some routes might already be defined in AdminController (like dashboard), but wait! dashboard is already mapped in web.php.
    if route not in ['dashboard', 'users', 'restaurants', 'drivers', 'login']:
        # users is grouped under admin prefix but with controller. 
        # Actually it's safer to just define closure routes for the ones that don't have endpoints yet. Or just define all non-colliding.
        new_routes += f"    Route::get('/{route}', function () {{ return view('admin::{route}'); }})->name('admin.{route}');\n"

new_routes += "});\n"

if "// Integrated Routes" not in routes_content:
    with open(routes_file, "a", encoding="utf-8") as file_out:
        file_out.write(new_routes)

print("Migration completed successfully.")
