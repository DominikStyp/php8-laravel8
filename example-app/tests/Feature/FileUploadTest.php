<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        Storage::fake('images');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->json('POST', '/upload_files', [
            'image' => $file,
        ]);

        // Assert the file was stored...
        Storage::disk('images')->assertExists("uploaded/".$file->hashName());

        // Assert a file does not exist...
        Storage::disk('images')->assertMissing('uploaded/missing.jpg');
    }
}
