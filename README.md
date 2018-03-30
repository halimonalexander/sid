# SID

Sequence Identifier class.
Class is used to move constants from SQL to PHP code and for other uses.

## Before SID:

MySQL table - UserStatus
```
id, status
 0, inactive
 1, active
 9, banned
10, unauthorised
```
Somewhere in code:
```php
if ($this->user->status == 10) { // who knows what means 10?
    ...
}
```

## After SID

```php
use HalimonAlexander\Sid\Sid;

class userStatus extends Sid{
  /** PHPDoc that explains this value */
  const INACTIVE = 0;

  /** PHPDoc that explains this value */
  const ACTIVE = 1;

  /** PHPDoc that explains this value */
  const BANNED = 9;

  /** PHPDoc that explains this value */
  const UNAUTHORISED = 10;
}
```

Somewhere in code:
```php
if ($this->user->status == userStatus::UNAUTHORISED) {
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