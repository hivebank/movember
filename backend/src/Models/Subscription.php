<?php

namespace FormFlow\Models;

use FormFlow\Database\MongoDB;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Subscription
{
    private $collection;

    public function __construct()
    {
        $this->collection = MongoDB::getInstance()->getCollection('subscriptions');
    }

    public function create(string $userId, array $data): ?array
    {
        $subscription = [
            'userId' => new ObjectId($userId),
            'stripeSubscriptionId' => $data['stripeSubscriptionId'],
            'stripePriceId' => $data['stripePriceId'],
            'status' => $data['status'] ?? 'active',
            'currentPeriodStart' => new UTCDateTime($data['currentPeriodStart'] * 1000),
            'currentPeriodEnd' => new UTCDateTime($data['currentPeriodEnd'] * 1000),
            'cancelAtPeriodEnd' => $data['cancelAtPeriodEnd'] ?? false,
            'createdAt' => new UTCDateTime(),
            'updatedAt' => new UTCDateTime()
        ];

        $result = $this->collection->insertOne($subscription);

        if ($result->getInsertedCount() > 0) {
            $subscription['_id'] = $result->getInsertedId();
            return $subscription;
        }

        return null;
    }

    public function findByUserId(string $userId): ?array
    {
        try {
            $subscription = $this->collection->findOne(
                ['userId' => new ObjectId($userId)],
                ['sort' => ['createdAt' => -1]]
            );
            return $subscription ? (array)$subscription : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findByStripeSubscriptionId(string $stripeSubscriptionId): ?array
    {
        $subscription = $this->collection->findOne(['stripeSubscriptionId' => $stripeSubscriptionId]);
        return $subscription ? (array)$subscription : null;
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

    public function updateByStripeId(string $stripeSubscriptionId, array $data): bool
    {
        try {
            $data['updatedAt'] = new UTCDateTime();
            $result = $this->collection->updateOne(
                ['stripeSubscriptionId' => $stripeSubscriptionId],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
