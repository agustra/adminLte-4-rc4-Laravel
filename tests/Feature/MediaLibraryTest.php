<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Storage::fake('media');
    }

    /** @test */
    public function user_can_upload_file_to_root()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($this->user)
            ->post('/api/media/upload/file', [
                'file' => $file,
                'collection' => '',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('media', [
            'collection_name' => '',
            'name' => 'test',
        ]);
    }

    /** @test */
    public function user_can_upload_file_to_folder()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($this->user)
            ->post('/api/media/upload/file', [
                'file' => $file,
                'collection' => 'images',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('media', [
            'collection_name' => 'images',
            'name' => 'test',
        ]);
    }

    /** @test */
    public function user_can_copy_media_file()
    {
        $media = $this->user->addMediaFromUrl('https://via.placeholder.com/150')
            ->toMediaCollection('images');

        $response = $this->actingAs($this->user)
            ->post('/api/media/copy', [
                'media_id' => $media->id,
                'target_folder' => 'documents',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(2, Media::count());
        $this->assertDatabaseHas('media', [
            'collection_name' => 'documents',
            'name' => $media->name.' (Copy)',
        ]);
    }

    /** @test */
    public function user_can_move_media_file()
    {
        $media = $this->user->addMediaFromUrl('https://via.placeholder.com/150')
            ->toMediaCollection('images');

        $response = $this->actingAs($this->user)
            ->post('/api/media/move', [
                'media_id' => $media->id,
                'target_folder' => 'documents',
            ]);

        $response->assertStatus(200);
        $media->refresh();
        $this->assertEquals('documents', $media->collection_name);
    }

    /** @test */
    public function user_can_delete_media_file()
    {
        $media = $this->user->addMediaFromUrl('https://via.placeholder.com/150')
            ->toMediaCollection('images');

        $response = $this->actingAs($this->user)
            ->delete("/api/media-management/{$media->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    /** @test */
    public function user_can_create_folder()
    {
        $response = $this->actingAs($this->user)
            ->post('/api/media/folders', [
                'name' => 'test-folder',
                'path' => 'test-folder',
                'parent' => '',
            ]);

        $response->assertStatus(200);
        $this->assertTrue(Storage::disk('media')->exists('test-folder'));
    }

    /** @test */
    public function user_can_rename_folder()
    {
        Storage::disk('media')->makeDirectory('old-folder');

        $response = $this->actingAs($this->user)
            ->post('/api/media/folders/rename', [
                'oldPath' => 'old-folder',
                'newName' => 'new-folder',
            ]);

        $response->assertStatus(200);
        $this->assertFalse(Storage::disk('media')->exists('old-folder'));
        $this->assertTrue(Storage::disk('media')->exists('new-folder'));
    }

    /** @test */
    public function user_can_get_media_list()
    {
        $this->user->addMediaFromUrl('https://via.placeholder.com/150')
            ->toMediaCollection('images');

        $response = $this->actingAs($this->user)
            ->get('/api/media-management/json');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'file_name',
                    'collection',
                    'mime_type',
                    'size',
                    'url',
                ],
            ],
            'folders',
        ]);
    }

    /** @test */
    public function media_files_are_served_correctly()
    {
        // Create a test file
        Storage::disk('media')->put('test.jpg', 'fake-image-content');

        $response = $this->get('/media/test.jpg');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    /** @test */
    public function context_menu_returns_folder_list()
    {
        Storage::disk('media')->makeDirectory('folder1');
        Storage::disk('media')->makeDirectory('folder2');

        $response = $this->actingAs($this->user)
            ->get('/api/media/folders');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'path',
                ],
            ],
        ]);
    }
}
