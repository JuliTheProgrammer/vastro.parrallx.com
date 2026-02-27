<?php

namespace App\Jobs\Backup;

use App\Enums\AIModelEnum;
use App\Enums\UploadDataType;
use App\Helper\DataClassificationHelper;
use App\Helper\MimeHelper;
use App\Models\Backup;
use App\Models\BackupAnalysis;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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

    public function __construct(private string $storedPath, private int $backupId) {}

    public function handle(): void
    {
        $backup = Backup::findOrFail($this->backupId);
        $mimeType = MimeHelper::identifyUploadDataType($backup->mime_type);

        $currentAIModel = Auth::user()->userStatistics()->current_ai_model ?? AIModelEnum::CLAUDE_HAIKU_4;

        $response = null;

        // What we can do later is redirect to openAI when the document is other than PDF.

        if ($mimeType === UploadDataType::FILE) {
            $response = $this->handleDocument();
        }

        if ($mimeType === UploadDataType::IMAGE) {
            $response = $this->handleImage();
        }

        throw_if(empty($response), new \Exception('No response from AI')); // TODO new exception

        $backupClassification = DataClassificationHelper::identifyClassificationEnum($response->structured['data_classifications'] ?? 0);

        $analysis = BackupAnalysis::create([
            'description' => $response->structured['description'] ?? null,
            'tags' => $response->structured['tags'] ?? [],
            'extra_information' => Arr::except($response->structured, ['description', 'tags']),
            'used_tokens' => $response->usage->promptTokens + $response->usage->completionTokens,
        ]);

        if ($backupClassification) {
            $backup->update(['backup_classification' => $backupClassification]);
        }

        $backup->update(['backup_analysis_id' => $analysis->id]);
    }

    private function handleExtractedDocumentText(string $text): mixed
    {
        $schema = new ObjectSchema(
            name: 'document_summary',
            description: 'A structured document description',
            properties: [
                new StringSchema('description', 'A summary of the document'),
                new StringSchema('language_percentage', 'What is the language of the document and how confident are you in the language'),
                new NumberSchema('data_classifications', 'How many secrets or confidential information does the document contain'),
                new ArraySchema(
                    'tags',
                    'The tags to describe the documents',
                    new StringSchema('tag', 'A tag to describe the document'),
                ),
            ],
            requiredFields: ['description', 'language_percentage', 'tags', 'data_classifications']
        );

        return Prism::structured()
            ->using(Provider::Anthropic, AIModelEnum::CLAUDE_HAIKU_4)
            ->withSchema($schema)
            ->withPrompt("Summarize the following document text, add tags to describe it, identify the language and how confident you are, and give a score from 1-100 for how much confidential information it contains:\n\n{$text}")
            ->asStructured();
    }

    private function handleImage(): mixed
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
                new NumberSchema('data_classifications', 'How confidential are the contents in this image'),
            ],
            requiredFields: ['description', 'tags', 'data_classifications']
        );

        return Prism::structured()
            ->using(Provider::Anthropic, AIModelEnum::CLAUDE_HAIKU_4)
            ->withPrompt('Give this image appropriate tags and a description, identify how much confidential information this image has on it',
                [Image::fromStoragePath($this->storedPath)])
            ->withSchema($schema)
            ->asStructured();
    }

    private function handleDocument(): mixed
    {
        $schema = new ObjectSchema(
            name: 'document_summary',
            description: 'A structured document description',
            properties: [
                new StringSchema('description', 'A summary of the document'),
                new StringSchema('language_percentage', 'What is the language of the document and how confident are you in the language'),
                new NumberSchema('data_classifications', 'How many secrets or confidential information does the document contain'),
                new ArraySchema(
                    'tags',
                    'The tags to describe the documents',
                    new StringSchema('tag', 'A tag to describe the document'),
                ),
            ],
            requiredFields: ['description', 'language_percentage', 'tags', 'data_classifications']
        );

        return Prism::structured()
            ->using(Provider::Anthropic, AIModelEnum::CLAUDE_HAIKU_4)
            ->withSchema($schema)
            ->withPrompt('Summarize this document, add tags to describe it and find out the language of the document and how confident you are. Give the document a score from 1-100 how much confidential information it has in it.', [Document::fromStoragePath($this->storedPath)])
            ->asStructured();
    }
}
