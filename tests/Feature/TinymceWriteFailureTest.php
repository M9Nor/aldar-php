<?php

namespace Tests\Feature;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use Modules\Cms\Http\Controllers\Admin\TinymceController;
use Tests\TestCase;

/** S17: a failed write is an error, never a success pointing at a file that does not exist. */
class TinymceWriteFailureTest extends TestCase
{
    public function test_a_failed_write_is_reported_as_a_failed_upload(): void
    {
        $disk = \Mockery::mock(Filesystem::class);
        $disk->shouldReceive('put')->once()->andThrow(UnableToWriteFile::atLocation('tinymce/probe.jpg', 'simulated full disk'));
        Storage::shouldReceive('disk')->with('graph')->andReturn($disk);

        $image = imagecreatetruecolor(8, 8);
        ob_start();
        imagejpeg($image);
        $jpeg = ob_get_clean();
        $request = Request::create('/en/admin/tinymce/uploader', 'POST', ['tinymce' => ['filename' => 'photo.jpg', 'base64' => base64_encode($jpeg)]]);

        $result = (new TinymceController)->uploader($request);

        $this->assertFalse($result['success']);
        $this->assertArrayNotHasKey('location', $result);
    }

    public function test_the_public_and_graph_disks_throw_on_failed_operations(): void
    {
        $this->assertTrue(config('filesystems.disks.public.throw'));
        $this->assertTrue(config('filesystems.disks.graph.throw'));
    }
}
