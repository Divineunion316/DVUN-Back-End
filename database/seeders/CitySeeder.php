<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Tamil Nadu' => [
                'Chennai', 'Coimbatore', 'Madurai', 'Trichy', 'Salem', 'Erode', 'Tirunelveli'
            ],
            'Karnataka' => [
                'Bangalore', 'Mysore', 'Mangalore', 'Hubli', 'Belgaum'
            ],
            'Kerala' => [
                'Kochi', 'Trivandrum', 'Calicut', 'Thrissur'
            ],
            'Maharashtra' => [
                'Mumbai', 'Pune', 'Nagpur', 'Nashik', 'Aurangabad'
            ],
            'Telangana' => [
                'Hyderabad', 'Warangal', 'Nizamabad'
            ],
            'Andhra Pradesh' => [
                'Visakhapatnam', 'Vijayawada', 'Guntur', 'Nellore'
            ],
            'Delhi' => [
                'New Delhi', 'Dwarka', 'Rohini', 'Saket'
            ],
            'West Bengal' => [
                'Kolkata', 'Howrah', 'Durgapur'
            ],
            'Gujarat' => [
                'Ahmedabad', 'Surat', 'Vadodara', 'Rajkot'
            ],
            'Rajasthan' => [
                'Jaipur', 'Udaipur', 'Jodhpur', 'Kota'
            ],
            'Uttar Pradesh' => [
                'Lucknow', 'Noida', 'Ghaziabad', 'Kanpur', 'Varanasi'
            ],
        ];

        foreach ($cities as $stateName => $cityList) {

            $state = DB::table('states')->where('name', $stateName)->first();

            if (!$state) {
                continue;
            }

            foreach ($cityList as $city) {
                DB::table('cities')->insert([
                    'state_id' => $state->id,
                    'name' => $city,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
