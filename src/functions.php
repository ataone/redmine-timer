<?php

declare(strict_types=1);

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

function getThisWeekRegisteredHours(array $config): array
{
    $toDay = new \DateTime('today');
    $toDayString = $toDay->format('Y-m-d');

    // Start on monday this week
    $fromDay = new \DateTime(sprintf('today -%d days', intval($toDay->format('w')) - 1));
    $fromDayString = $fromDay->format('Y-m-d');

    $userId = $config['redmine']['user_id'];
    $client = new Client(['verify' => false]);

    try {
        $res = $client->request('GET', $config['redmine']['api_url']."/time_entries.json?from=$fromDayString&to=$toDayString&user_id=$userId", [
            'auth' => [$config['redmine']['username'], $config['redmine']['password']],
        ]);
    } catch (ClientException $exception) {
        $res = $exception->getResponse();
        throw new \Exception('Error: '.$res->getStatusCode().' – '.$res->getBody());
    }

    $entries = json_decode($res->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

    $times = [];
    $day = clone $fromDay;

    // Initialize $times array
    do {
        $times[$day->format('Y-m-d')] = 0.0;
        $day->modify('+1 day');
    }  while ($day->format('d') !== $toDay->format('d'));

    $times[$toDay->format('Y-m-d')] = 0.0;

    foreach ($entries['time_entries'] ?? [] as $entry) {
        $times[$entry['spent_on']] += floatval($entry['hours']);
    }

    return $times;
}

function getTodayRegisteredHours(array $config): float
{
    $today = date('Y-m-d');
    $userId = $config['redmine']['user_id'];
    $client = new Client(['verify' => false]);

    try {
        $res = $client->request('GET', $config['redmine']['api_url']."/time_entries.json?spent_on=$today&user_id=$userId", [
            'auth' => [$config['redmine']['username'], $config['redmine']['password']],
        ]);
    } catch (ClientException $exception) {
        $res = $exception->getResponse();
        throw new \Exception('Error: '.$res->getStatusCode().' – '.$res->getBody());
    }

    $entries = json_decode($res->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

    $hours = 0.0;

    foreach ($entries['time_entries'] ?? [] as $entry) {
        $hours += floatval($entry['hours']);
    }

    return round($hours, 2);
}

function registerTime(array $config): ?string
{
    $error = null;

    $entry = ['time_entry' => [
        'issue_id' => (int) $_POST['issue_id'],
        'spent_on' => $_POST['spent_on'],
        'hours' => (float) str_replace(',', '.', $_POST['hours']),
        'activity_id' => (int) $_POST['activity_id'],
        'comments' => $_POST['comments'],
        'user_id' => (int) $config['redmine']['user_id'],
    ]];

    $client = new Client(['verify' => false]);

    try {
        $client->request('POST', $config['redmine']['api_url'].'/time_entries.json', [
            'auth' => [$config['redmine']['username'], $config['redmine']['password']],
            'json' => $entry,
        ]);
    } catch (ClientException $exception) {
        $res = $exception->getResponse();
        $error = 'Error: '.$res->getStatusCode().' – '.$res->getBody();
    }

    return $error;
}
