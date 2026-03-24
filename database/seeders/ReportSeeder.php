<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Schema::disableForeignKeyConstraints();
        DB::table('reports')->truncate();
        DB::table('predictions')->truncate();
        Schema::enableForeignKeyConstraints();
        

        $userIds = User::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->error('No users found. Please seed users first!');
            return;
        }

        $csvPath = database_path('data/test_data.csv');

        if (!File::exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            return;
        }

        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file); // Skip: type,remarks,latitude,longitude,created_at

        $this->command->info('Importing reports and triggering AI validation...');

        while (($row = fgetcsv($file)) !== false) {
            /**
             * Mapping based on the new test_reports.csv:
             * 0: disaster_type (e.g., Flood)
             * 1: remarks/description (e.g., "Baha na kaayo...")
             * 2: latitude
             * 3: longitude
             * 4: created_at (Timestamp)
             */
            
            $timestamp = Carbon::parse($row[4]);

            Report::create([
                'user_id'       => $userIds[array_rand($userIds)],
                'disaster_type' => $row[0],
                'description'   => $row[1], // This is the "remarks" for your AI
                'latitude'      => $row[2],
                'longitude'     => $row[3],
                'location'      => 'Davao City, Philippines', // Optional default
                'status'        => 'pending',
                'created_at'    => $timestamp,
                'updated_at'    => $timestamp,
            ]);
        }

        fclose($file);
        $this->command->info('Reports seeded successfully!');
    }
}