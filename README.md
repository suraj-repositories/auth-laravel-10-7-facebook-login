        
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# auth-laravel-10-7-facebook-login

- Facebook Auth system
- Role based authentication system where we can give the user permission according to his role.
- Also see : [Laravel Socialite Documentation](https://laravel.com/docs/10.x/socialite)

### If downloading this
-------------------------------------------- START -----------------------------------------------<br> 
never forgot to run 
```sh
composer install
php artisan migrate     # never forgot to create database
composer require laravel/socialite
php artian serve
```
application is running on `http://localhost:8000`<br>

---------------------------------------------- END ------------------------------------------------<br> 

## Step 0
 - first you need to create a simple working login system 
 - you can take reference of : [auth-laravel-10-1](https://github.com/suraj-repositories/auth-laravel-10-1)

## Steps
1. require the 'socialite' package
```sh
composer require laravel/socialite
```

2. Now you need to create credentials on facebook app
    - go to [developers.facebook.com](https://developers.facebook.com/) -> my apps
    - create new app -> fill details and create an app
        - on `use cases` choose `Authenticate and request data from users with Facebook Login` 
        - on `app detail` fill the app name and your email from which you manage this login system
        - under `review` create app
        - after creating app you will be redirected to dashboard page
    - Under `use cases` on `Authentication and account creation` click on customize
        - Under the permissions add the `email` to allow emails permission from `actions` column
        - Under settings on `Valid OAuth Redirect URIs` fill the redirect path 
            eg. `https://www.example.com/auth/facebook/callback`, but if you are using localhost for practice leave this field empty
        - Under the `quickstart` click on `web` 
            - on `Tell Us about Your Website` fill your website url example - `http://localhost:8000` or `https://www.example.com`. (this field is important and required telling the facebook this website is allowed from my side to use this facebook app)
            - click on save
            - leave others empty
    - On the left sidebar go to `App Settings` -> `basic` 
        - here you can find the `App ID` which is `FACEBOOK_CLIENT_ID`
        - and `App secret` which is your `FACEBOOK_CLIENT_SECRET`
    - fill those details in your .env file. like this - 
```sh
FACEBOOK_CLIENT_ID=********************
FACEBOOK_CLIENT_SECRET=**************************************

FACEBOOK_REDIRECT=http://localhost:8000/auth/facebook/callback    # using local enviourment (localhost)
# or
FACEBOOK_REDIRECT=https://www.example.com/auth/facebook/callback  # using on deployed website 

```
3. After the credential setup you need to open `config\services.php` in which you need to setup the following
```sh
   'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => 'http://localhost:8000/auth/facebook/callback',
    ],
```
4. on your login file create a link for login 
```php
<a href="{{ url('/auth/facebook') }}">Login with Facebook</a>
```
5. setup the route for the URL and success-redirect-url
```php
   Route::get('/auth/facebook', [AuthController::class, 'facebookPage'])->name('facebook.page');
   Route::get('/auth/facebook/callback', [AuthController::class, 'facebookRedirect'])->name('facebook.redirect');
```
6. setup a controller method to handle facebook login
```php
    public function facebookPage(){
        return Socialite::driver('facebook')->redirect();
    }
```
```php
   public function facebookRedirect(){
       try{
            $user = Socialite::driver('facebook')->user();
            $findUser = User::where('email', $user->email)->first();

            if(!$findUser){
                $findUser = new User();
                $findUser->email = $user->email;
                $findUser->password = Hash::make('123');
                $findUser->name = $user->name;
                $findUser->role = "USER";
                
                $findUser->save();
            }
           
            Auth::login($findUser);
            return redirect()->route('home');
            
       }catch(Exception $e){
            dd('Something went wrong!! : ' . $e->getMessage() );
       } 
    }

```
Now your app is ready to login with facebook.

### Further steps
- Also when you make changes on env - do this command before test your application
```sh
php artisan cache:clear
php artisan optimize
php artisan serve
```

<br />
<p align="center">⭐️ Star my repositories if you find it helpful.</p>