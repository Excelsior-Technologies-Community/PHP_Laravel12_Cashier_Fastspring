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

                'name' => 'Basic Plan', 

                'slug' => 'basic', 

                'stripe_plan' => env('STRIPE_BASIC_PRICE'), 

                'price' => 10, 

                'description' => 'Basic'

            ],

            [

                'name' => 'Premium Plan', 

                'slug' => 'premium', 

                'stripe_plan' => env('STRIPE_PREMIUM_PRICE'),

                'price' => 100, 

                'description' => 'Premium'

            ]

        ];

  

        foreach ($plans as $plan) {

            Plan::create($plan);

        }

    }

}