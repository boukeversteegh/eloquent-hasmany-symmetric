# eloquent-relations

## HasManySymmetric
Eloquent Relationship that defines symmetric relations

### Installation

```
composer require boukeversteegh/eloquent-relations dev-master
```

### Usage

Use the provided trait to support symmetric relations, and define a relationship.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use \EloquentRelations\HasManySymmetricTrait;

    /**
     * A message belongs to a user when either the sending_user_id or receiving_user_id matches user.id
     */
    public function messages()
    {
        return $this->hasManySymmetric(Message::class, ['sending_user_id', 'receiving_user_id']);
    }
}

class Message extends Model
{
    public function sendingUser()
    {
        return $this->belongsTo(User::class, 'sending_user_id', 'id');
    }

    public function receivingUser()
    {
        return $this->belongsTo(User::class, 'receiving_user_id', 'id');
    }
}
```

Retrieve the relations.

```php
<?php

$user = \App\User::find(1);
$user->messages();
```