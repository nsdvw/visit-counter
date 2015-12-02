<?php

require "../vendor/autoload.php";

$config = parse_ini_file('../config.ini');

$options = array(
    'servers' => array(
       array('host' => $config['redisHost'], 'port' => $config['redisPort']),
    )
);

$rediska = new Rediska($options);
$rediskaAdapter = new \VisitCounter\Redis\RediskaAdapter($rediska);
$vc = new \VisitCounter\VisitCounter($rediskaAdapter);
$pageID = '1';
$userIP = $_SERVER['REMOTE_ADDR'];
$vc->countVisit($pageID, $userIP);

$dbh = new PDO(
    $config['dbDsn'],
    $config['dbUser'],
    $config['dbPass'], 
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sql = "SELECT id, title, message, visits FROM posts WHERE id=:id";
$sth = $dbh->prepare($sql);
$sth->bindValue(':id', $pageID, PDO::PARAM_INT);
$sth->execute();
$sth->setFetchMode(PDO::FETCH_CLASS, 'Post');
$post = $sth->fetch();

$recentVisits = $vc->getDeltaVisits([$pageID])[$pageID];
$savedVisits = $post->visits;
$totalVisits = intval($recentVisits) + intval($savedVisits);

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdoAdapter = new \VisitCounter\Db\PdoAdapter($dbh, 'posts', 'visits');
    $vc->moveToDb($pdoAdapter);
    $message = 'Visits from redis was successfully transfered to database.';
}

require "../template/template.php";
