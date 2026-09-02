- make dialog to adapt its width **done**
- start using translations **done**
- fill user's products with default products
- adjustable menu **done**
- move sex input to inputs **done**
- factors react page **done**

---
- layout for factors **done**
- calc OUV fatal **done**
- ---
- test all three on mobile, tablet, desktop
  - [x] factors ok
  - [x] settings ok
  - [x] calculations ok
- [x] langvars for all three from back **done**
- [x] close dialog on ESC, click outside **done**
- [x] the hamburger menu is not working properly **done**
- [x] set BE to the settings from factors on migration from old diacalc **done**
- [x] Archive page

### pwa
* [ ] think over the ability to start an app without the network
* [ ] then we should cache the remote data in the local storage
* [ ] once we get fresh data, put them to local storage to have the last actual copy of data
* [ ] do not forget the refresh the browser cache once the app code is updated
  <br>read more https://share.google/aimode/WocWEZTAiEWYePC98



---
### info
- Mobile (Base): 320px – 480px (Standard smartphones).
- Large Phones / Small Tablets: 481px – 767px.
- Tablets / Small Laptops: 768px – 1024px.
- Desktops: 1025px – 1280px.

---
To achieve the best balance of maintainability, performance, and simplicity, do not create two separate Laravel applications. Managing two apps connected to the same database introduces a high risk of code duplication, broken migrations, and synchronization bugs whenever your database schema changes.
Since both domains are hosted on the same physical server, the cleanest architectural solution is to run a single, unified Laravel 12 application that handles both domains simultaneously using Laravel Multi-Domain Routing.
Here is exactly how to set this up so your code, models, and migrations stay in one place.

 * **Step 1:** Configure Dual Domains in routes/api.php<br>
Instead of creating separate projects, you can use Laravel's native domain routing within your single routes/api.php file to split traffic based on the incoming hostname.

```php
// routes/api.php
use App\Http\Controllers\LegacyApiController;
use App\Http\Controllers\ModernApiController;
use App\Http\Middleware\StatelessLegacyAuth;

// 1. OLD TRAFFIC: Responds only to example.com/old
Route::domain('example.com')->group(function () {
Route::prefix('old')
->middleware([StatelessLegacyAuth::class])
->group(function () {
    Route::post('/submit-data', [LegacyApiController::class, 'handleData']);
    // Add all other legacy endpoints here...
    });
});

// 2. NEW TRAFFIC: Responds only to react.com/api
Route::domain('react.com')->group(function () {
    Route::prefix('api')->group(function () {
        Route::post('/v1/auth', [ModernApiController::class, 'login']);
        // Add all modern React-facing endpoints here...
    });
});
```

_(Note: If your React app is a single-page app hosted on react.com and hitting the API on the same domain, this layout prevents cross-origin CORS headaches entirely)._

  * **Step 2:** Handle URL Re-writes on Your Server (Nginx / Apache)<br>
  Because you are using a single Laravel application, both example.com and react.com must point to the exact same public directory on your server (e.g., /var/www/my-laravel-app/public).

#### If you use Nginx:

Configure both server blocks to point to the same directory. Nginx passes the host headers automatically, and Laravel will instantly know which route group to match.

```# Server block for Old Clients
server {
    listen 80;
    server_name example.com;
    root /var/www/my-laravel-app/public; # 👈 Points to the single app
    index index.php;
    # ... standard laravel fastcgi settings ...
}

# Server block for React App / Modern API
server {
    listen 80;
    server_name react.com;
    root /var/www/my-laravel-app/public; # 👈 Points to the exact same app
    index index.php;
    # ... standard laravel fastcgi settings ...
}
```

  * **Step 3:** Implement Data Transformation (The "Adapter" Pattern)To solve your concern about changing the database structure and breaking the old clients, isolate your legacy responses using Laravel Eloquent Resources.Never return raw database models directly from the LegacyApiController. Instead, pass them through an explicit data transformer layer. If your new database table layout shifts, you only have to adjust this single transformer class to re-map the fields back into the layout the old client expects.

    * Generate a resource class:
    ```
    php artisan make:resource LegacyUserResource
    ```
    * Hardcode the old schema map inside the resource:
    ```
    // app/Http/Resources/LegacyUserResource.php
    namespace App\Http\Resources;
    
    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;
    
    class LegacyUserResource extends JsonResource
    {
        public function toArray(Request $request): array
        {
            return [
                // Old key format expected by legacy clients => New database column
                'user_id'    => $this->id,
                'login'      => $this->name,
                'user_email' => $this->email,
                'is_active'  => $this->status === 'active' ? 1 : 0, // Handles logic shifts
            ];
        }
    }
    ```
    * Return it cleanly inside your legacy controller
    ```php
    use App\Http\Resources\LegacyUserResource;
    
    public function handleData()
    {
        $user = Auth::user();
    
        // Returns the data perfectly matching the structural layout of the old API
        return new LegacyUserResource($user); 
    }
    ```
  ####Summary of Benefits📦
  * **Single Migration History:** Running php artisan migrate updates the system globally. It is physically impossible to forget to run or update a migration on a secondary app.
  * **💸 Low Maintenance:** You only have one codebase to secure, optimize, back up, and monitor.
  * **🛠️ Seamless Decoupling:** The legacy client thinks it is talking to an old system, your React frontend interacts with modern routing profiles, and the database engine remains beautifully unified underneath.

