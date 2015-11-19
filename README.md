# EloquentRelations

Adds a new type of relationship to Eloquent that matches either of two foreign keys.


```
composer require boukeversteegh/eloquent-relations dev-master
```

## HasManySymmetric
Eloquent Relationship that defines symmetric relations. Implemented as HasMany relation with two candidate foreign keys.

The relation is equivalent to the following join:

```mysql
SELECT * FROM parent JOIN related ON (parent.id = related.foreign_key_1 OR parent.id = related.foreign_key_2)
```

### Use cases

- Friendships on a social network involve two users (inviting_user_id, accepting_user_id). You want to retrieve a user's friends regardless of the direction of the relationship.
- Message has a sender and a receiver, and you want to get user->messages through one relationship (match sender AND receiver)
- Games are played by two teams, but belong to both teams equally (home_team_id OR guest_team_id).

### Usage

Use the provided trait to support symmetric relations, and define a relationship.

```php
<?php

class User extends \Illuminate\Database\Eloquent\Model
{
    use \EloquentRelations\HasManySymmetricTrait;

    public function friendships()
    {
        return $this->hasManySymmetric(Friendship::class, ['first_user_id', 'second_user_id']);
    }
}

# Lazy load the relationship
$user = User::find(1);
$user->friendships;

# Eager loading
$user = User::with('friendships')->find(1);
$user->friendships;
```