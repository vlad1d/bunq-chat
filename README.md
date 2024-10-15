# Bunq Final Coding Assignment
#### by Vlad Ichim

## Description
This project is a simple chat application backend written in PHP. It allows users to create chat groups, join these groups, and send messages within them. The chat groups are public, 
meaning any user can join any group, where they can chat for a while or list all the messages. The data is stored in a SQLite 
database.

## Running the Application
```bash
cd bunq-chat
composer run start
```
After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

```bash
composer run test
```

## Functionality

### Requirements
I used curl to make requests to the API. The following commands satisfy the requirements:
- Creating chat groups:
```
curl -X POST 'http://localhost:8080/chats/{id}'
```
- Join chat groups:
    
```
curl -X POST 'http://localhost:8080/chats/{chatId}/users/{userId}'
```
- List all messages in a chat group:

```
curl -X GET 'http://localhost:8080/messages/{chatId}/users/{userId}'
```

- Send messages to a chat group:

``` 
  curl -X POST 'curl -X POST http://localhost:8080/messages/{chatId}/users/{userId} \
     -H "Content-Type: application/json" \
     -d '{"content": "Hello, Bunq!"}'
```

### Additional Features
All routes are defined in the `routes.php` file. Additional implemented routes are:
- Users: Get a list of users, get a user by id, create a new user.
- Chats: Get a list of chats, get a chat by id, delete a chat, leave a chat.

### Further Improvements
The application could be improved by adding more features such as:
- User authentication
- User roles
- Message editing/deletion
- etc.