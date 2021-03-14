<?php

namespace Tests\Feature;

use GuzzleHttp\Psr7\MimeType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\MimeTypes;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_avatar_upload()
    {
        Storage::fake('images');

        $file = UploadedFile::fake()->image('avatar.jpg', 50, 50)->size(100);

        $response = $this->json('POST', '/upload_files', [
            'image' => $file,
        ]);
        $response->assertSuccessful();

        // Assert the file was stored...
        Storage::disk('images')->assertExists("uploaded/".$file->hashName());

        // Assert a file does not exist...
        Storage::disk('images')->assertMissing('uploaded/missing.jpg');
    }

    public function test_pdf_upload()
    {
        Storage::fake('images');

        $file = UploadedFile::fake()->create('document.pdf', 111, 'application/pdf');

        $response = $this->json('POST', '/upload_files', [
            'image' => $file,
        ]);
        $response->assertSuccessful();

        // Assert the file was stored...
        Storage::disk('images')->assertExists("uploaded/".$file->hashName());

    }

    public function test_put_and_get_file()
    {
        Storage::fake('logs');

        $file = UploadedFile::fake()->create('some_file.log', 5, 'text/plain');

        Storage::putFileAs('logs', $file, 'fancy_file.log', ['public']);

        Storage::assertExists('logs/fancy_file.log');

        Storage::append('logs/fancy_file.log', 'abc');

        $fetched = Storage::get('logs/fancy_file.log');

        $this->assertEquals("\r\nabc", $fetched);

    }
}
