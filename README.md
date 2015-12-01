VisitCounter
============
VisitCounter is a small library which provides a way to get unique visits statistics of your website using redis.

Summary
-------
Assume we need an information about the count of unique visits for every page of your website. So we have several issues: where to store that data persistant, how to keep it up-to-date and how to get it from store quickly enough.

To store altering visits statistics in relational database like MySQL is not suitable because of highload. Memcache isn't good too because it uses LRU mechanism and not provides persistance. It also has no complex data structures, but we need ones.

So redis will be a good choice for our purposes. This library provides you an api to save information about visit to redis, to transfer data from redis to main database and finally to get recently statistics for a single page.

How to use and implementation details
-------------------------------------
At first, we need to create an instance of main class `\VisitCounter\VisitCounter`.
The library is driver independent, so it has dependency on redis adapter. At the moment only adapter for rediska http://rediska.geometria-lab.net is included in package, but you can implement any other client adapter by your own, see section How to extend below.
   ``` php
   $rediska = new \Rediska($options);
   $rediskaAdapter = new \VisitCounter\Redis\RediskaAdapter($rediska);
   $vc = new \VisitCounter\VisitCounter($rediskaAdapter);
   ```
Also you can set several config options:
   ```php
   $vc->setKeyPrefix('myprefix'); // set a key prefix to avoid name conflict
   $vc->setKeyExpiration(3600 * 24 * 7); /* set key ttl if you want to store information about visits
   for last week only; default is 2.592.000, i.e. one month */
   $vc->setPerTransaction(10000); /* defines how many visits will be moved to db by one transaction;
   default is 1000, optional */
   ```

The library has three main public methods.

Method `\VisitCounter\VisitCounter::countVisit()` allows you to save information about current visit to redis.
In your page controller call
   ``` php
   $vc->countVisit($pageID, $userIP);
   ```
- first it will save information about visit in a separate redis string key "keyPrefix:pageID:userIP" if it is not already exists
- if the key already exists, method return false
- next it will `RPUSH keyPrefixQueue pageID`, where "keyPrefixQueue" is a list key, which we use as a queue
- and finally it will `HINCRBY VisitCounter pageID`, where "VisitCounter" is a hash key, which stores count of recently visits that not saved to main db yet

Method `\VisitCounter\VisitCounter::moveToDb()` is aimed to transfer recently visits to main db.
It has dependency on database adapter. At the moment only PDO adapter is included, but you can implement any other driver, see section How to extend below.
   ```php
   $pdoAdapter = new \VisitCounter\Db\PdoAdapter($pdo);
   $vc->moveToDb($pdoAdapter);
   ```
- first it takes current queue length `LLEN keyPrefixQueue`
- then it will begin to take `\VisitCounter\VisitCounter::perTransaction` records from the queue's head `LRANGE keyPrefixQueue 0 N`
- save them to database `\VisitCounter\Db\DbAdapter::save()`
- delete them from queue `LTRIM keyPrefixQueue N -1`
- foreach page in that bunch `HINCRBY VisitCounter pageID -visitCount`

Method `\VisitCounter\VisitCounter::getDeltaVisits()` returns count of recent visits for given pages. The list of pages must be passed as array, e.g.:
   ``` php
   $savedVisits = $model->visits;
   $recentVisits = $vc->getDeltaVisits( [$model->id] );
   $visits = $savedVisits + $recentVisits;
   ```
Internally it just executes `HMGET`.

How to extend
-------------
You can use library with any redis client and any database. You need only to implement interfaces `\VisitCounter\Redis\RedisAdapterInterface` and `\VisitCounter\Db\DbAdapterInterface`. 
For example `RedisAdapter::ltrim` method requires a wrapper to your client's realization of this redis command.
Every method must return value or true/false.
For universal exceptions class use `\VisitCounter\Exception\DbException` and `\VisitCounter\Exception\RedisException`.

How to install
--------------
You can install the package by composer or download it manually.
To install it from github by composer you simply need to add its repository to "repositories" section of your composer.json, and then package name to "require" section, like this:
   ``` json
   {
      "repositories": [
         {
            "type": "vcs",
            "url": "https://github.com/nsdvw/visit-counter"
         }
      ],
      "require": {
         "geometria-lab/rediska": "^0.5.10",
         "nsdvw/visit-counter": "dev-master"
      }
   }
   ```
   Then run `composer update` and that's all.