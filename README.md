# SID

[![Build Status](https://travis-ci.org/halimonalexander/sid.svg?branch=master)](https://travis-ci.org/halimonalexander/sid)
[![Code Climate](https://codeclimate.com/github/halimonalexander/sid/badges/gpa.svg)](https://codeclimate.com/github/halimonalexander/sid)
[![Test Coverage](https://codeclimate.com/github/halimonalexander/sid/badges/coverage.svg)](https://codeclimate.com/github/halimonalexander/sid/coverage)

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
if ($this->user->status == 10) { // who knows what 10 means?
    ...
}
```

## After SID

```php
use HalimonAlexander\Sid\Sid;

class userStatus extends Sid{
  /** PHPDoc that explains this value */
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
if ($this->user->status == userStatus::UNAUTHORISED) {
    ...
}
```

## Multilanguage support

If you have vocabularies for different languages, you can do use it to get text messages. If no callback set, a default one will be called:
```php
userStatus::setVocabularyCallback('ru', 'MyRuVocabulary::translate');
...
echo userStatus::getTitle(userStatus::ACTIVE, 'ru');
echo userStatus::getTitle(userStatus::ACTIVE, 'pl');
```

Result:
```
Активный пользователь
userStatus.active
```

## Tests

Tests will become easier. Use can use `sid::NX()` method to get not defiened value.
