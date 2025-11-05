<?php

namespace FormFlow\Models;

use FormFlow\Database\MongoDB;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Response
{
    private $collection;

    public function __construct()
    {
        $this->collection = MongoDB::getInstance()->getCollection('responses');
    }

    public function create(string $formId, array $data): ?array
    {
        $response = [
            'formId' => new ObjectId($formId),
            'userId' => isset($data['userId']) ? new ObjectId($data['userId']) : null,
            'answers' => $data['answers'] ?? [],
            'metadata' => [
                'ipAddress' => $data['metadata']['ipAddress'] ?? '',
                'userAgent' => $data['metadata']['userAgent'] ?? '',
                'referrer' => $data['metadata']['referrer'] ?? '',
                'completionTime' => $data['metadata']['completionTime'] ?? 0,
                'paymentStatus' => $data['metadata']['paymentStatus'] ?? null,
                'stripePaymentId' => $data['metadata']['stripePaymentId'] ?? null
            ],
            'submittedAt' => new UTCDateTime()
        ];

        $result = $this->collection->insertOne($response);

        if ($result->getInsertedCount() > 0) {
            $response['_id'] = $result->getInsertedId();
            return $response;
        }

        return null;
    }

    public function findById(string $id): ?array
    {
        try {
            $response = $this->collection->findOne(['_id' => new ObjectId($id)]);
            return $response ? (array)$response : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findByFormId(string $formId, array $options = []): array
    {
        $query = ['formId' => new ObjectId($formId)];
        $limit = $options['limit'] ?? 100;
        $skip = $options['skip'] ?? 0;

        $responses = $this->collection->find(
            $query,
            [
                'sort' => ['submittedAt' => -1],
                'limit' => $limit,
                'skip' => $skip
            ]
        );

        return iterator_to_array($responses);
    }

    public function delete(string $id, string $formId): bool
    {
        try {
            $result = $this->collection->deleteOne([
                '_id' => new ObjectId($id),
                'formId' => new ObjectId($formId)
            ]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function countByFormId(string $formId): int
    {
        return $this->collection->countDocuments(['formId' => new ObjectId($formId)]);
    }

    public function getAnalytics(string $formId): array
    {
        try {
            $pipeline = [
                ['$match' => ['formId' => new ObjectId($formId)]],
                [
                    '$group' => [
                        '_id' => null,
                        'totalResponses' => ['$sum' => 1],
                        'avgCompletionTime' => ['$avg' => '$metadata.completionTime']
                    ]
                ]
            ];

            $result = $this->collection->aggregate($pipeline)->toArray();

            if (empty($result)) {
                return [
                    'totalResponses' => 0,
                    'avgCompletionTime' => 0
                ];
            }

            return [
                'totalResponses' => $result[0]['totalResponses'] ?? 0,
                'avgCompletionTime' => round($result[0]['avgCompletionTime'] ?? 0, 2)
            ];
        } catch (\Exception $e) {
            return [
                'totalResponses' => 0,
                'avgCompletionTime' => 0
            ];
        }
    }

    public function countByUserIdInPeriod(string $userId, int $startTimestamp, int $endTimestamp): int
    {
        try {
            return $this->collection->countDocuments([
                'userId' => new ObjectId($userId),
                'submittedAt' => [
                    '$gte' => new UTCDateTime($startTimestamp * 1000),
                    '$lte' => new UTCDateTime($endTimestamp * 1000)
                ]
            ]);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
