<?php

namespace App\Jobs\Backup;

use App\Models\Backup;
use App\Models\BackupAnalysis;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;
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
}
