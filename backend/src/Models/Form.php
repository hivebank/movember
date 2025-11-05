<?php

namespace FormFlow\Models;

use FormFlow\Database\MongoDB;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Form
{
    private $collection;

    public function __construct()
    {
        $this->collection = MongoDB::getInstance()->getCollection('forms');
    }

    public function create(string $userId, array $data): ?array
    {
        $form = [
            'userId' => new ObjectId($userId),
            'title' => $data['title'] ?? 'Untitled Form',
            'description' => $data['description'] ?? '',
            'slug' => $this->generateSlug($data['title'] ?? 'untitled-form'),
            'fields' => $data['fields'] ?? [],
            'settings' => $data['settings'] ?? [
                'theme' => 'default',
                'submitText' => 'Submit',
                'redirectUrl' => '',
                'requirePayment' => false,
                'paymentAmount' => 0,
                'notifications' => [
                    'email' => '',
                    'sendCopy' => false
                ]
            ],
            'status' => 'draft',
            'views' => 0,
            'submissions' => 0,
            'createdAt' => new UTCDateTime(),
            'updatedAt' => new UTCDateTime()
        ];

        $result = $this->collection->insertOne($form);

        if ($result->getInsertedCount() > 0) {
            $form['_id'] = $result->getInsertedId();
            return $form;
        }

        return null;
    }

    public function findById(string $id): ?array
    {
        try {
            $form = $this->collection->findOne(['_id' => new ObjectId($id)]);
            return $form ? (array)$form : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findBySlug(string $slug): ?array
    {
        $form = $this->collection->findOne(['slug' => $slug, 'status' => 'published']);
        return $form ? (array)$form : null;
    }

    public function findByUserId(string $userId, array $filters = []): array
    {
        $query = ['userId' => new ObjectId($userId)];

        if (!empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }

        $forms = $this->collection->find(
            $query,
            ['sort' => ['updatedAt' => -1]]
        );

        return iterator_to_array($forms);
    }

    public function update(string $id, array $data): bool
    {
        try {
            $data['updatedAt'] = new UTCDateTime();
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(string $id, string $userId): bool
    {
        try {
            $result = $this->collection->deleteOne([
                '_id' => new ObjectId($id),
                'userId' => new ObjectId($userId)
            ]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function incrementViews(string $id): bool
    {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$inc' => ['views' => 1]]
            );
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function incrementSubmissions(string $id): bool
    {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$inc' => ['submissions' => 1]]
            );
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function countByUserId(string $userId): int
    {
        return $this->collection->countDocuments(['userId' => new ObjectId($userId)]);
    }

    private function generateSlug(string $title): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = $slug . '-' . bin2hex(random_bytes(4));
        return $slug;
    }

    public function belongsToUser(string $formId, string $userId): bool
    {
        try {
            $count = $this->collection->countDocuments([
                '_id' => new ObjectId($formId),
                'userId' => new ObjectId($userId)
            ]);
            return $count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
