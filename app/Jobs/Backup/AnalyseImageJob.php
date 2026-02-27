<?php

namespace App\Jobs\Backup;

use App\Models\Backup;
use App\Models\BackupAnalysis;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\NumberSchema;
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
        $backup = Backup::findOrFail($this->backupId);
        $mimeType = $this->resolveMimeType($backup);

        ray($mimeType);

        match (true) {
            $this->isImage($mimeType) => $response = $this->handleImage(),
            $this->isVideo($mimeType) => $response = $this->handleVideo(),
            $this->isDocument($mimeType) => $response = $this->handleDocument(),
            default => $response = null,
        };

        if (! $response) {
            return;
        }

        $analysis = BackupAnalysis::create([
            'description' => $response->structured['description'] ?? null,
            'tags' => $response->structured['tags'] ?? [],
            'extra_information' => Arr::except($response->structured, ['description', 'tags']), // this is the extra information which the ai gives back -> if different for every type
            'used_tokens' => $response->usage->promptTokens + $response->usage->completionTokens,
        ]);

        $backup->backup_analysis_id = $analysis->id;
        $backup->save();

        ray($backup);

        // BUG when raying response, ray will crash

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
            ->using(Provider::Anthropic, 'claude-haiku-4-5-20251001')
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
                new NumberSchema('data_classifications', 'How many secrets or confidential information does the document contain'),
                new ArraySchema(
                    'tags',
                    'The tags to describe the documents',
                    new StringSchema('tag', 'A tag to describe the document'),
                ),
            ],
            requiredFields: ['summary', 'language_percentage', 'tags', 'data_classifications']
        );

        $response = Prism::structured()
            ->using(Provider::Anthropic, 'claude-haiku-4-5-20251001')
            ->withSchema($schema)
            ->withPrompt('Summarize this document, add tags to describe it and find out the language of the document and how confident you are. Give the document a score from 1-100 how much confidential information it has in it.', [Document::fromStoragePath($this->storedPath)])
            ->asStructured();

        return $response;
    }

    private function handleVideo() {}

    private function resolveMimeType(Backup $backup): string
    {
        $backupMimeType = $backup->mime_type ? Str::lower($backup->mime_type) : null;
        if ($backupMimeType) {
            return $backupMimeType;
        }

        $storageMimeType = Storage::mimeType($this->storedPath);
        if ($storageMimeType) {
            return Str::lower($storageMimeType);
        }

        return Str::lower(Str::afterLast($this->storedPath, '.'));
    }

    private function isImage(string $mimeType): bool
    {
        return Str::startsWith($mimeType, 'image/')
            || in_array($mimeType, ['png', 'jpg', 'jpeg'], true);
    }

    private function isVideo(string $mimeType): bool
    {
        return Str::startsWith($mimeType, 'video/')
            || in_array($mimeType, ['mp4'], true);
    }

    private function isDocument(string $mimeType): bool
    {
        return $mimeType === 'application/pdf'
            || in_array($mimeType, ['pdf'], true);
    }
}
