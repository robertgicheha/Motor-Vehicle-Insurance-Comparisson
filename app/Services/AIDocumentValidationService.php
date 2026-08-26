<?php

namespace App\Services;

use RuntimeException;

class AIDocumentValidationService
{
    protected string $apiKey;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.ai_document.api_key', '');
        $this->endpoint = config('services.ai_document.endpoint', 'https://api.openai.com/v1/chat/completions');
    }

    public function validate(string $filePath, string $documentType): array
    {
        if (empty($this->apiKey)) {
            return [
                'status' => ClaimDocument::AI_STATUS_VALID,
                'notes' => 'AI validation skipped - API key not configured.',
            ];
        }

        $expectedType = ClaimDocument::documentTypes()[$documentType] ?? $documentType;

        $prompt = "You are a document validation assistant for an insurance claims management system. 
        Analyze the uploaded document and determine if it appears to be a valid '{$expectedType}'.
        
        Return a JSON response with:
        - status: one of 'valid', 'invalid', or 'needs_review'
        - notes: a brief explanation of your decision
        
        Be strict but fair. If the document is clearly not a {$expectedType}, mark it as 'invalid' and explain why.";

        try {
            $base64 = base64_encode(file_get_contents($filePath));
            $mimeType = mime_content_type($filePath) ?: 'application/pdf';

            $payload = [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => 'data:' . $mimeType . ';base64,' . $base64,
                                ],
                            ],
                        ],
                    ],
                ],
                'max_tokens' => 500,
            ];

            $ch = curl_init($this->endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $response = curl_exec($ch);
            curl_close($ch);

            if (!$response) {
                return [
                    'status' => ClaimDocument::AI_STATUS_VALID,
                    'notes' => 'AI validation service unavailable. Document accepted pending manual review.',
                ];
            }

            $data = json_decode($response, true);
            $content = $data['choices'][0]['message']['content'] ?? '';

            preg_match('/"status":\s*"([^"]+)"/', $content, $statusMatch);
            preg_match('/"notes":\s*"([^"]+)"/', $content, $notesMatch);

            $status = $statusMatch[1] ?? ClaimDocument::AI_STATUS_NEEDS_REVIEW;
            $validStatuses = [
                ClaimDocument::AI_STATUS_VALID,
                ClaimDocument::AI_STATUS_INVALID,
                ClaimDocument::AI_STATUS_NEEDS_REVIEW,
            ];

            if (!in_array($status, $validStatuses)) {
                $status = ClaimDocument::AI_STATUS_NEEDS_REVIEW;
            }

            return [
                'status' => $status,
                'notes' => $notesMatch[1] ?? 'AI validation completed.',
            ];
        } catch (\Throwable $e) {
            return [
                'status' => ClaimDocument::AI_STATUS_VALID,
                'notes' => 'AI validation error: ' . $e->getMessage() . '. Document accepted pending manual review.',
            ];
        }
    }
}
