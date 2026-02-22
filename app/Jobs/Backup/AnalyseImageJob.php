<?php

namespace App\Jobs\Backup;

use App\Models\Backup;
use App\Models\BackupAnalysis;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\ValueObjects\Media\Document;
use Prism\Prism\ValueObjects\Media\Image;

class AnalyseImageJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $storedPath, private int $backupId)
    {
        ray('Uploading to claude');
        ray($storedPath);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $mimeType = Str::afterLast($this->storedPath, '.');

        ray($mimeType);

        match ($mimeType) {
            'png', 'jpg', 'jpeg' => $response = $this->handleImage(),
            'mp4' => $response = $this->handleVideo(),
            'pdf' => $response = $this->handleDocument(),
            default => $response = null
        };

        if (! $response) {
            return;
        }

        $backup = Backup::findOrFail($this->backupId);

        $analysis = BackupAnalysis::create([
            'description' => $response->structured['description'] ?? null,
            'tags' => $response->structured['tags'] ?? [],
        ]);

        $backup['backup_analysis_id'] = $analysis;

        ray($backup);

        ray($response);

        // Storage::delete($this->storedPath);
    }

    // Return the response
    private function handleImage()
    {
        $schema = new ObjectSchema(
            name: 'image_tags',
            description: 'Tags to describe an image',
            properties: [
                new StringSchema('description', 'The description of the image'),
                new ArraySchema(
                    'tags',
                    'The tags to describe the image',
                    new StringSchema('tag', 'A tag to describe the image'),
                ),
            ],
            requiredFields: ['description', 'tags']
        );

        $response = Prism::structured()
            ->using(Provider::Anthropic, 'claude-opus-4-6')
            ->withPrompt('Give this image appropriate tags and a description',
                [Image::fromStoragePath($this->storedPath)])
            ->withSchema($schema)
            ->asStructured();

        return $response;
    }

    public function handleDocument()
    {
        $schema = new ObjectSchema(
            name: 'document_summary',
            description: 'A structured document summary',
            properties: [
                new StringSchema('summary', 'A summary of the document'),
                new StringSchema('language_percentage', 'What is the language percentage of the document'),
                new ArraySchema(
                    'tags',
                    'The tags to describe the documents',
                    new StringSchema('tag', 'A tag to describe the document'),
                ),
            ],
            requiredFields: ['summary']
        );

        $response = Prism::structured()
            ->using(Provider::Anthropic, 'claude-opus-4-6')
            ->withSchema($schema)
            ->withPrompt('Summarize this document, add tags to describe it and find out the language of the document and how confident you are', [Document::fromStoragePath($this->storedPath)])
            ->asStructured();

        return $response;
    }

    private function handleVideo() {}
}
