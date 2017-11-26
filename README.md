# SID

Sequence Identifier class.
This is used to move constants from SQL to PHP code

## Before SID:

MySQL table - UserStatus
```sql
id, status
0, inactive
1, active
9, banned
10, unauthorised
```
Somewhere in code:
```php
if ($this->user->status == 10){ // who knows what means 10?
    ...
}
```

## After SID

```php
class userStatus extends \HalimonAlexander\Sid\Sid{
  /** PHPDoc that explains this value here */
  const INACTIVE = 0;

  /** PHPDoc that explains this value here */
  const ACTIVE = 1;

  /** PHPDoc that explains this value here */
  const BANNED = 9;

  /** PHPDoc that explains this value here */
  const UNAUTHORISED = 10;
}
```
Somewhere in code:
```php
if ($this->user->status == userStatus::UNAUTHORISED){
    ...
}
```

## Multilanguage support

If you have vocabularies for different languages, you can do so:
```php
echo userStatus::getTitle(userStatus::ACITIVE, 'ru');
```
Result:
```
Активный пользователь
```