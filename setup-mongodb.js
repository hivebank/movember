// MongoDB setup script
// Run with: mongosh < setup-mongodb.js

use formflow;

// Create collections with validation
db.createCollection("users", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["email", "password", "name", "subscription", "createdAt"],
      properties: {
        email: {
          bsonType: "string",
          description: "must be a string and is required"
        },
        password: {
          bsonType: "string",
          description: "must be a string and is required"
        },
        name: {
          bsonType: "string",
          description: "must be a string and is required"
        }
      }
    }
  }
});

db.createCollection("forms");
db.createCollection("responses");
db.createCollection("subscriptions");

// Create indexes
db.users.createIndex({ "email": 1 }, { unique: true });
db.users.createIndex({ "subscription.stripeCustomerId": 1 });

db.forms.createIndex({ "userId": 1 });
db.forms.createIndex({ "slug": 1 }, { unique: true });
db.forms.createIndex({ "status": 1 });

db.responses.createIndex({ "formId": 1 });
db.responses.createIndex({ "submittedAt": -1 });

db.subscriptions.createIndex({ "userId": 1 });
db.subscriptions.createIndex({ "stripeSubscriptionId": 1 }, { unique: true });

print("MongoDB setup completed successfully!");
print("Collections created: users, forms, responses, subscriptions");
print("Indexes created for optimal performance");
