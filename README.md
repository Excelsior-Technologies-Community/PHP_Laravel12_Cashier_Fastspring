# PHP_Laravel12_Cashier_Stripe

This project is a Laravel 12 subscription-based application implemented using
Laravel Cashier with Stripe. It allows users to register, log in, view available
subscription plans, and purchase subscriptions using Stripe’s secure payment
gateway.



### Project Overview

The PHP_Laravel12_Cashier_Fastspring project demonstrates how to implement a
subscription system in Laravel 12 using Laravel Cashier with Stripe.

The application provides:
- User authentication (login & registration)
- Subscription plan management
- Secure Stripe payment integration
- Automatic subscription handling using Cashier

This project is suitable for SaaS applications, paid memberships, and
subscription-based platforms.


### Application Flow

1. User registers or logs into the application.
2. After login, the user visits the Plans page.
3. All available subscription plans are fetched from the database.
4. The user selects a plan (Basic or Premium).
5. Stripe SetupIntent is created for secure card setup.
6. User enters card details using Stripe Elements.
7. Laravel Cashier creates the subscription using the Stripe Price ID.
8. Subscription details are stored in the database.
9. A success message is shown after successful payment.


### Stripe Integration

Stripe is used as the payment provider in this project. Laravel Cashier acts as
a bridge between Laravel and Stripe, simplifying the subscription process.

Cashier handles:
- Stripe customer creation
- Payment method handling
- Subscription creation
- Subscription status tracking


---




# Step-by-Step Laravel Cashier (Stripe) Setup


---

## STEP 1: Create Laravel 12 Project

### Command:

```
composer create-project laravel/laravel PHP_Laravel12_Cashier_Fastspring "12.*"

```

### Go inside project:
```
cd PHP_Laravel12_Cashier_Fastspring

```


## STEP 2: Configure .env File

### Open .env file and set database.

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_cashier_fastspring
DB_USERNAME=root
DB_PASSWORD=

```

### Create database laravel12_cashier_fastspring in phpMyAdmin or via CLI:
```
 Create database laravel12_cashier_fastspring in phpMyAdmin.

```



## STEP 3 - Install Auth Scaffolding

### Fast and simple authentication for users:

```
composer require laravel/ui
php artisan ui bootstrap --auth
npm install
npm run build

```

 This will generate login, register, and password reset.

## STEP 4 - Install Laravel Cashier (Stripe)


### Install Cashier:

```
composer require laravel/cashier

```

### Publish migration files:

```
php artisan vendor:publish --tag="cashier-migrations"
php artisan migrate

```


## STEP 5 - Configure Stripe Credentials


### Add these to .env:

```
STRIPE_KEY=pk_test_Mxxxxxxxxxx

STRIPE_SECRET=sk_test_exxxxxxxxxxx

STRIPE_PRICE_MONTHLY=price_1Sxxxxxxx

```

These are sandbox/demo credentials. You will replace them with real ones later.




## STEP 6 - Install Sanctum

### Install sanctum:

```
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

```

Note:
Sanctum is optional in this project and is not directly used for Stripe
subscriptions. It can be used later for API authentication if required.


## STEP 7 - Update User Model

### Enable Billable in User model for subscription features:

```
<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable; // for Cashier
use Laravel\Sanctum\HasApiTokens; // for Sanctum

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime'];
}

```



## STEP 8 - Create Plans Table & Model

### Command:

```
php artisan make:model Plan -m

```

### Add database/migrations/xxxx_create_plans_table.php

```

<?php

  

use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;

  

return new class extends Migration

{

    /**

     * Run the migrations.

     *

     * @return void

     */

    public function up()

    {

        Schema::create('plans', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->string('slug');

            $table->string('stripe_plan');

            $table->integer('price');

            $table->string('description');

            $table->timestamps();

        });

    }

  

    /**

     * Reverse the migrations.

     *

     * @return void

     */

    public function down()

    {

        Schema::dropIfExists('plans');

    }

};

```
### Add app/Models/Plan.php

```
<?php

  

namespace App\Models;

  

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

  

class Plan extends Model

{

    use HasFactory;

  

    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */

    protected $fillable = [

        'name',

        'slug',

        'stripe_plan',

        'price',

        'description',

    ];

  

    /**

     * Write code on Method

     *

     * @return response()

     */

    public function getRouteKeyName()

    {

        return 'slug';

    }

}


```

### Run migration:

```
php artisan migrate

```


## STEP 9 - Create Seeder for Plans

### Command:

```
php artisan make:seeder PlanSeeder

```

### database/seeders/PlanSeeder.php:

```

<?php

  

namespace Database\Seeders;

  

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

use App\Models\Plan;

  

class PlanSeeder extends Seeder

{

    /**

     * Run the database seeds.

     *

     * @return void

     */

    public function run()

    {

        $plans = [

            [

                'name' => 'Basic', 

                'slug' => 'basic', 

                'stripe_plan' => 'price_1Lxxxxxxxxxxxxxx', 

                'price' => 10, 

                'description' => 'Basic'

            ],

            [

                'name' => 'Premium', 

                'slug' => 'premium', 

                'stripe_plan' => 'price_1xxxxxxxxxxx', 

                'price' => 100, 

                'description' => 'Premium'

            ]

        ];

  

        foreach ($plans as $plan) {

            Plan::create($plan);

        }

    }

}

```

### Run seeder:

```
php artisan db:seed --class=PlanSeeder

```


 Important:
Stripe price IDs must be created in Stripe Dashboard (Test mode).
If a price ID is deleted or recreated, it must be updated in the database.


## Step 10 - Routes

### routes/web.php:

```


<?php

  

use Illuminate\Support\Facades\Route;

 

use App\Http\Controllers\PlanController;

  

/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/

  

Route::get('/', function () {

    return view('welcome');

});

 

Auth::routes();

  

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

  

Route::middleware("auth")->group(function () {

    Route::get('plans', [PlanController::class, 'index'])
        ->name('plans.index');   //  THIS LINE WAS MISSING

    Route::get('plans/{plan}', [PlanController::class, 'show'])
        ->name('plans.show');

    Route::post('subscription', [PlanController::class, 'subscription'])
        ->name('subscription.create');
});


```






## STEP 11 - Create Controller

### Command:

```
php artisan make:controller PlanController


```


### app/Http/Controllers/PlanController.php

```

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    // Show all plans
    public function index()
    {
        $plans = Plan::all();
        return view('plans', compact('plans'));
    }

    // Show subscription page for a selected plan
    public function show(Plan $plan)
    {
        // Create Stripe SetupIntent for the logged-in user
        $intent = auth()->user()->createSetupIntent();

        return view('subscription', compact('plan', 'intent'));
    }

    // Handle subscription creation
   public function subscription(Request $request)
{
    $request->validate([
        'plan' => 'required|exists:plans,id',
        'payment_method' => 'required|string',
    ]);

    $plan = Plan::findOrFail($request->plan);

    $request->user()
        ->newSubscription('default', $plan->stripe_plan)
        ->create($request->payment_method);

    return redirect()
        ->route('plans.index')
        ->with('success', 'Subscription purchased successfully!');
}

}

```




## STEP 12 - Blade View

### resources/views/plans.blade.php

```
@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h2>Available Plans</h2>
    @foreach($plans as $plan)
        <div class="card mb-3">
            <div class="card-body">
                <h5>{{ $plan->name }}</h5>
                <p>Price: ${{ number_format($plan->price, 2) }}</p>
                <a href="{{ route('plans.show', $plan->slug) }}" class="btn btn-primary">Subscribe</a>
            </div>
        </div>
    @endforeach
</div>
@endsection

```


### resources/views/subscription.blade.php

```

@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Subscribe to {{ $plan->name }}</h2>
    <p>Price: ${{ number_format($plan->price, 2) }}</p>

    <form id="subscription-form" method="POST" action="{{ route('subscription.create') }}">
        @csrf
        <input type="hidden" name="plan" value="{{ $plan->id }}">
        <input type="hidden" name="payment_method" id="payment_method">

        <div id="card-element"><!-- Stripe Card Element --></div>

        <button type="submit" class="btn btn-primary mt-3">Purchase</button>
    </form>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe("{{ env('STRIPE_KEY') }}");
const elements = stripe.elements();
const card = elements.create('card');
card.mount('#card-element');

const form = document.getElementById('subscription-form');

form.addEventListener('submit', async (e) => {
    e.preventDefault(); // Prevent page refresh

    // Confirm the card setup
    const {setupIntent, error} = await stripe.confirmCardSetup(
        "{{ $intent->client_secret }}",
        { payment_method: { card: card } }
    );

    if(error){
        alert(error.message); // Show Stripe errors
    } else {
        // Set the payment method token in hidden input
        document.getElementById('payment_method').value = setupIntent.payment_method;
        form.submit(); // Submit the form to Laravel controller
    }
});
</script>
@endsection


```


### resources/views/subscription_success.blade.php

```

@extends('layouts.app')

  

@section('content')

<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card">

  

                <div class="card-body">

  

                    <div class="alert alert-success">

                        Subscription purchase successfully!

                    </div>

  

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

```

Note:
subscription_success.blade.php is an optional success page and can be used if
the redirect is changed after successful subscription.


STEP 13 - Test Flow (IMPORTANT)

###  Run :

```
php artisan serve

```

Now, Go to your web browser, type the given URL and view the app output:

```
http://localhost:8000/login

```

After, login you need to go on following path:

```
http://localhost:8000/plans

```

## So you will see this type Output:

### Register Page:


<img width="1919" height="966" alt="Screenshot 2026-01-13 121821" src="https://github.com/user-attachments/assets/cb64350c-2938-4876-81ea-186102036b44" />


### Login Page:


<img width="1919" height="961" alt="Screenshot 2026-01-13 132607" src="https://github.com/user-attachments/assets/5b7f0eed-8d77-49fc-bf47-97edaf5e4637" />


###  Plan Page:


<img width="1916" height="970" alt="Screenshot 2026-01-13 123855" src="https://github.com/user-attachments/assets/87776ad4-03c1-4705-b24e-78028ce88fbd" />


### Purchase Page:


<img width="1917" height="947" alt="Screenshot 2026-01-13 125525" src="https://github.com/user-attachments/assets/6e7c8016-fd3e-4804-b669-1deef3a4f370" />


### Purchase Plan Successfully:


<img width="1912" height="958" alt="Screenshot 2026-01-13 125546" src="https://github.com/user-attachments/assets/76f841d5-b068-47fc-abc7-bed9d80aceec" />



---


# Project Folder Structure:

```

PHP_Laravel12_Cashier_Fastspring
│
├── app
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── Auth
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   └── ForgotPasswordController.php
│   │   │   │
│   │   │   └── PlanController.php
│   │   │
│   │   └── Middleware
│   │
│   ├── Models
│   │   ├── User.php
│   │   └── Plan.php
│   │
│   └── Providers
│
├── database
│   ├── migrations
│   │   ├── xxxx_create_users_table.php
│   │   ├── xxxx_create_plans_table.php
│   │   ├── xxxx_create_subscriptions_table.php
│   │   └── xxxx_create_subscription_items_table.php
│   │
│   ├── seeders
│   │   ├── DatabaseSeeder.php
│   │   └── PlanSeeder.php
│
├── resources
│   ├── views
│   │   ├── auth
│   │   │   ├── login.blade.php
│   │   │   └── register.blade.php
│   │   │
│   │   ├── layouts
│   │   │   └── app.blade.php
│   │   │
│   │   ├── home.blade.php
│   │   ├── plans.blade.php
│   │   ├── subscription.blade.php
│   │   └── subscription_success.blade.php
│   │
│   └── js
│
├── routes
│   ├── web.php
│
├── .env
├── composer.json
├── package.json
├── README.md
└── artisan

```
