<?php

namespace FormFlow\Models;

use FormFlow\Database\MongoDB;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class User
{
    private $collection;

    public function __construct()
    {
        $this->collection = MongoDB::getInstance()->getCollection('users');
    }

    public function create(array $data): ?array
    {
        $user = [
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'name' => $data['name'] ?? '',
            'company' => $data['company'] ?? '',
            'subscription' => [
                'plan' => 'free',
                'status' => 'active',
                'stripeCustomerId' => null,
                'stripeSubscriptionId' => null,
                'currentPeriodEnd' => null
            ],
            'createdAt' => new UTCDateTime(),
            'updatedAt' => new UTCDateTime()
        ];

        $result = $this->collection->insertOne($user);

        if ($result->getInsertedCount() > 0) {
            $user['_id'] = $result->getInsertedId();
            return $user;
        }

        return null;
    }

    public function findByEmail(string $email): ?array
    {
        $user = $this->collection->findOne(['email' => $email]);
        return $user ? (array)$user : null;
    }

    public function findById(string $id): ?array
    {
        try {
            $user = $this->collection->findOne(['_id' => new ObjectId($id)]);
            return $user ? (array)$user : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function update(string $id, array $data): bool
    {
        try {
            $data['updatedAt'] = new UTCDateTime();
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateSubscription(string $id, array $subscriptionData): bool
    {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                [
                    '$set' => [
                        'subscription' => $subscriptionData,
                        'updatedAt' => new UTCDateTime()
                    ]
                ]
            );
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function emailExists(string $email): bool
    {
        return $this->collection->countDocuments(['email' => $email]) > 0;
    }
}
