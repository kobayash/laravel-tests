<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UploadImageTest extends TestCase
{
    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        Storage::fake('s3');
    }

    private function postFile($file)
    {
        return $this->json('POST', '/upload', [
            'image' => $file,
        ]);
    }

    /**
     * @group normal
     * @testdox アップロードされたjpgファイルがストレージに保存されること
     */
    public function testStoreUploadedJpegImageToStorage()
    {
        $file = UploadedFile::fake()->image('dummy.jpg', 80, 80);

        $this->postFile($file);

        Storage::disk('s3')->assertExists($file->hashName('uploads'));
    }

    /**
     * @group normal
     * @testdox アップロードされたpngファイルがストレージに保存されること
     */
    public function testStoreUploadedPngImageToStorage()
    {
        $file = UploadedFile::fake()->image('dummy.png', 80, 80);

        $this->postFile($file);

        Storage::disk('s3')->assertExists($file->hashName('uploads'));
    }

    /**
     * @group illegal
     * @testdox アップロードされたファイルがイメージでなければストレージに保存されないこと
     */
    public function testStoreUploadedImageToStorageOnlyImage()
    {
        $file = UploadedFile::fake()->create('not_image.txt', 8);

        $this->postFile($file);

        Storage::disk('s3')->assertMissing($file->hashName('uploads'));
    }

    /**
     * @group illegal
     * @testdox アップロードされたファイルが2MB以下でなければストレージに保存されないこと
     */
    public function testStoreUploadedImageToStorageLessThan2Mb()
    {
        $file = UploadedFile::fake()->create('2001kb.txt', 2001);

        $this->postFile($file);

        Storage::disk('s3')->assertMissing($file->hashName('uploads'));
    }

    /**
     * @group illegal
     * @testdox アップロードされたイメージの横幅が80px以上でなければストレージに保存されないこと
     */
    public function testStoreUploadedImageToStorageWidthGreaterThan80px()
    {
        $file = UploadedFile::fake()->image('image.jpg', 79, 80);

        $this->postFile($file);

        Storage::disk('s3')->assertMissing($file->hashName('uploads'));
    }

    /**
     * @group illegal
     * @testdox アップロードされたイメージの高さが80px以上でなければストレージに保存されないこと
     */
    public function testStoreUploadedImageToStorageHeightGreaterThan80px()
    {
        $file = UploadedFile::fake()->image('image.jpg', 80, 79);

        $this->postFile($file);

        Storage::disk('s3')->assertMissing($file->hashName('uploads'));
    }

    /**
     * @group illegal
     * @testdox アップロードされたイメージの横幅が2000px以下でなければストレージに保存されないこと
     */
    public function testStoreUploadedImageToStorageWidthLessThan2000px()
    {
        $file = UploadedFile::fake()->image('image.jpg', 2001, 80);

        $this->postFile($file);

        Storage::disk('s3')->assertMissing($file->hashName('uploads'));
    }

    /**
     * @group illegal
     * @testdox アップロードされたイメージの高さが2000px以下でなければストレージに保存されないこと
     */
    public function testStoreUploadedImageToStorageHeightLessThan2000px()
    {
        $file = UploadedFile::fake()->image('image.jpg', 80, 2001);

        $this->postFile($file);

        Storage::disk('s3')->assertMissing($file->hashName('uploads'));
    }

}