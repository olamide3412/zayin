<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class UpdateNewsMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gallery = [
            [
                'type' => 'image',
                'url' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=800&q=80'
            ],
            [
                'type' => 'video',
                'url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'
            ],
            [
                'type' => 'image',
                'url' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=800&q=80'
            ]
        ];

        // Update all existing news items to have this gallery
        $newsItems = News::all();
        foreach ($newsItems as $news) {
            $news->media_gallery = $gallery;
            $news->save();
        }
    }
}
