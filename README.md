# EloquentRelations

Adds a new type of relationship to Eloquent (the ORM that Laravel uses) that matches either of two foreign keys.


```
composer require boukeversteegh/eloquent-relations dev-master
```

## HasManySymmetric
Eloquent Relationship that defines symmetric relations. Implemented as a HasMany relation with two candidate foreign keys.

The relation is equivalent to the following join:

```sql
SELECT * FROM parent
JOIN related ON (parent.id = related.foreign_key_1 OR parent.id = related.foreign_key_2)
```

Use cases:

- Friendships (id, inviting_user_id, receiving_user_id): $user->friendships
- Messages (id, receiver_user_id, sending_user_id): $user->messages
- Games (id, home_team_id, away_team_id): $team->games

### Usage

Use the provided trait to support symmetric relations, and define a relationship.

```php
class User extends \Illuminate\Database\Eloquent\Model
{
    use \EloquentRelations\HasManySymmetricTrait;

    public function friendships()
    {
        return $this->hasManySymmetric(Friendship::class, ['inviting_user_id', 'receiving_user_id']);
    }
}
```

```
# Lazy load the relationship
$user = User::find(1);
$user->friendships;

# Eager loading
$user = User::with('friendships')->find(1);
$user->friendships;
```