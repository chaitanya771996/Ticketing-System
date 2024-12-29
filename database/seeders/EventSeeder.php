<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $events = [
            [
                'user_id' => 1, // Replace with a valid organizer ID
                'title' => 'Tech Conference 2024',
                'description' => 'A conference about the latest in technology.',
                'date' => Carbon::now()->addDays(10),
                'location' => 'San Francisco, CA',
                'ticket_availability' => 100,
            ],
            [
                'user_id' => 1, // Replace with a valid organizer ID
                'title' => 'Music Festival',
                'description' => 'An exciting music festival featuring top artists.',
                'date' => Carbon::now()->addDays(20),
                'location' => 'Los Angeles, CA',
                'ticket_availability' => 200,
            ],
            [
                'user_id' => 2, // Replace with a valid organizer ID
                'title' => 'Art Workshop',
                'description' => 'Learn to create stunning art pieces with this workshop.',
                'date' => Carbon::now()->addDays(30),
                'location' => 'New York, NY',
                'ticket_availability' => 50,
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
