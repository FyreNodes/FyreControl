<?php

namespace Pterodactyl\Services\Helpers;

use Exception;
use GuzzleHttp\Client;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Http;
use Pterodactyl\Exceptions\Service\Helper\CdnVersionFetchingException;

class SoftwareVersionService
{
    private static array $results;

    protected CacheRepository $cache;
    protected Client $client;

    public function __construct(CacheRepository $cache, Client $client)
    {
        $this->cache = $cache;
        $this->client = $client;
        self::$results = $this->cacheVersionData();
    }

    /**
     * @param string $type
     * @return string
     */
    public function getVersion(string $type): string
    {
        return $this->cacheVersionData()[$type];
    }

    /**
     * Get the latest version of the daemon from the CDN servers.
     *
     * @return string
     */
    public function getDaemon(): string
    {
        return Arr::get(self::$results, 'wings') ?? 'error';
    }

    /**
     * Get the URL to the discord server.
     *
     * @return string
     */
    public function getDiscord()
    {
        return Arr::get(self::$results, 'discord') ?? 'https://pterodactyl.io/discord';
    }

    /**
     * Get the URL for donations.
     *
     * @return string
     */
    public function getDonations()
    {
        return Arr::get(self::$results, 'donations') ?? 'https://paypal.me/PterodactylSoftware';
    }

    /**
     * Determine if the current version of the panel is the latest.
     *
     * @return bool
     */
    public function isLatest(): bool
    {
        $versions = [0 => false, 1 => false];
        if (version_compare(config('app.version'), $this->getVersion('fyrecontrol')) >= 0) $versions[0] = true;
        if (version_compare(config('pterodactyl.version'), $this->getVersion('pterodactyl')) >= 0) $versions[1] = true;
        return !in_array(false, $versions);
    }

    /**
     * Determine if a passed daemon version string is the latest.
     *
     * @param string $version
     *
     * @return bool
     */
    public function isLatestDaemon($version)
    {
        return version_compare($version, $this->getDaemon()) >= 0;
    }

    protected function cacheVersionData(): array
    {
        //$res1 = Http::get('https://thefallenspirit.github.io/fyre.json');
        $res2 = Http::get('https://cdn.pterodactyl.io/releases/latest.json');
        return ['fyrecontrol' => '0.8.1', 'pterodactyl' => json_decode($res2->body())->panel];
    }
}
