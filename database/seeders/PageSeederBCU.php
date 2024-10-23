<?php

namespace Database\Seeders;

use App\Models\Page;
use A17\Twill\Models\Media;
use A17\Twill\Models\User;
use App\Repositories\PageRepository;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class PageSeederBCU extends Seeder
{
    public function run(): void
    {
        // Create sample image in storage if it doesn't exist
        $sampleImagePath = storage_path('app/public/sample-image.jpg');
        if (!File::exists($sampleImagePath)) {
            if (!File::isDirectory(storage_path('app/public'))) {
                File::makeDirectory(storage_path('app/public'), 0755, true);
            }

            // Create a blank image
            $image = imagecreatetruecolor(1600, 900);
            $bgColor = imagecolorallocate($image, 200, 200, 200);
            imagefill($image, 0, 0, $bgColor);

            // Add some text
            $textColor = imagecolorallocate($image, 50, 50, 50);
            imagestring($image, 5, 700, 400, 'Sample Image', $textColor);

            // Save the image
            imagejpeg($image, $sampleImagePath);
            imagedestroy($image);
        }

        // Get image dimensions
        [$width, $height] = getimagesize($sampleImagePath);

        // Create unique folder name for the file
        $folderName = str_replace('-', '', \Illuminate\Support\Str::uuid());
        $filename = 'sample-image.jpg';
        $uuid = $folderName . '/' . $filename;

        // Store the file
        Storage::disk(config('twill.media_library.disk'))
            ->putFileAs($folderName, $sampleImagePath, $filename);

        // Create media entry
        $media = Media::create([
            'uuid' => $uuid,
            'filename' => $filename,
            'width' => $width,
            'height' => $height,
            'alt_text' => 'Sample page cover image',
            'caption' => 'A sample image for demonstration'
        ]);


        // Get last created user
        Auth::guard('twill_users')->login(User::first());


        // Create 10 pages
        $faker = Faker::create();
        $repository = app(PageRepository::class);


        // Create 10 pages with revisions
        for ($i = 1; $i <= 10; $i++) {
            // Create initial published version
            $initialData = [
                'title' => [
                    'en' => $faker->sentence(3),
                ],
                'description' => [
                    'en' => $faker->sentence(5),
                ],
                'published' => true,

            ];

            // Create the page with initial data
            $page = $repository->create($initialData);


            $page->blocks()->create([
                'editor_name' => 'default',
                'type' => 'text',
                'position' => 1,
                'content' => [
                    'title' => [
                        'en' => $faker->sentence(3),
                    ],
                    'text' => [
                        'en' => $faker->sentence(5),
                    ],
                ],
            ]);

            $page->medias()->attach($media->id, [
                'role' => 'cover',
                'crop' => 'default',
                'metadatas' => json_encode([
                    'default' => [
                        'crop_x' => 0,
                        'crop_y' => 0,
                        'crop_w' => $width,
                        'crop_h' => $height,
                    ],
                ])
            ]);


            $page->update();

        }
    }
}
