<?php

namespace App\Services;

use App\Exceptions\AdvertiserNotFoundException;
use App\Exceptions\InvalidBlacklistsFormatException;
use App\Exceptions\PublisherNotFoundException;
use App\Exceptions\SiteNotFoundException;
use App\Models\Advertisers;
use App\Models\Blacklist;
use App\Models\Publisher;
use App\Models\Site;

class Blacklists
{
    const SITE_TYPE = 's';
    const PUBLISHER_TYPE = 'p';
    const TYPES = [self::SITE_TYPE, self::PUBLISHER_TYPE];

    /**
     * @throws InvalidBlacklistsFormatException
     * @throws SiteNotFoundException
     * @throws PublisherNotFoundException
     * @throws AdvertiserNotFoundException
     */
    public static function save(string $blacklists, int $adviserId):void
    {
        $blacklistsByType = self::parse($blacklists);

        if (!Advertisers::where('id', $adviserId)->exists()) {
            throw new AdvertiserNotFoundException();
        }

        if (isset($blacklistsByType[self::SITE_TYPE]) &&
            !Site::isModelsExists($blacklistsByType[self::SITE_TYPE])) {
            throw new SiteNotFoundException();
        }

        if (isset($blacklistsByType[self::PUBLISHER_TYPE]) &&
            !Publisher::isModelsExists($blacklistsByType[self::PUBLISHER_TYPE])) {
            throw new PublisherNotFoundException();
        }

        $blacklists = [];

        foreach (self::TYPES as $type) {
            foreach ($blacklistsByType[$type] ?? [] as $value) {
                $blacklists[] = [
                    'advertiser_id' => $adviserId,
                    'value' => $value,
                    'type' => $type
                ];
            }
        }

        Blacklist::insertOrIgnore($blacklists);
    }

    public static function get(int $advertiserId): string
    {
        $blacklists = Blacklist::select(['value', 'type'])->where('advertiser_id', $advertiserId)->get()->toArray();

        return self::format($blacklists);
    }

    /**
     * @throws InvalidBlacklistsFormatException
     */
    private static function parse(string $blacklists): array
    {
        $result = [];
        $blacklists = explode(', ', $blacklists);

        foreach ($blacklists as $blacklist) {
            $type = substr($blacklist, 0, 1);
            $id = substr($blacklist, 1);

            if (self::isBlacklistValid($type, $id)) {
                throw new InvalidBlacklistsFormatException();
            }

            $result[$type][] = intval($id);
        }

        return $result;
    }

    private static function isBlacklistValid(string $type, string $id): bool
    {
        return !in_array($type, self::TYPES) || !is_numeric($id) || intval($id) <= 0;
    }

    private static function format(array $blacklists): string
    {
        $result = '';

        foreach ($blacklists as $blacklist) {
            $result .= $blacklist['type'] . $blacklist['value'] . ', ';
        }

        if ($result) {
            $result = substr($result, 0, strlen($result) - 2);
        }

        return $result;
    }
}
