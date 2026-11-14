<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Tests
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\ComponentRecord;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\User\KunenaUserSocials;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The default providers: the networks which have a public profile page link to it, the others show only the icon.
 */
class KunenaUserSocialsTest extends UnitTestCase
{
    private const LINKED = [
        'weibo' => 'https://weibo.com/##VALUE##',
        'qzone' => 'https://user.qzone.qq.com/##VALUE##',
    ];

    private const UNLINKED = ['microsoft', 'qqsocial', 'wechat', 'apple', 'signal'];

    public function testWeiboAndQzoneLinkToTheProfile(): void
    {
        $providers = json_decode(KunenaUserSocials::getDefaultSocialsJson());

        $this->assertIsObject($providers, 'The default providers are not valid JSON.');

        foreach (self::LINKED as $key => $url) {
            $this->assertSame($url, $providers->$key->url);
            $this->assertStringContainsString('##VALUE##', $providers->$key->url);
            $this->assertSame(0, $providers->$key->nourl, "$key is not linked.");
        }
    }

    public function testTheNetworksWithoutAProfileAddressStayUnlinked(): void
    {
        $providers = json_decode(KunenaUserSocials::getDefaultSocialsJson());

        foreach (self::UNLINKED as $key) {
            $this->assertSame('##VALUE##', $providers->$key->url);
            $this->assertSame(1, $providers->$key->nourl, "$key is linked.");
        }
    }

    public function testSignalIsADefaultProvider(): void
    {
        $providers = json_decode(KunenaUserSocials::getDefaultSocialsJson());

        $this->assertIsObject($providers, 'The default providers are not valid JSON.');
        $this->assertSame('##VALUE##', $providers->signal->url);
        $this->assertSame('COM_KUNENA_MYPROFILE_SIGNAL', $providers->signal->title);
        $this->assertSame(1, $providers->signal->nourl);
        $this->assertSame('fa-brands fa-signal-messenger', $providers->signal->fa);
    }

    public function testADisabledProviderIsKeptWithTheValueOfTheUser(): void
    {
        $this->requireJoomla();

        $socials = $this->normalizeWithProviders(
            [
                ['key' => 'facebook', 'title' => 'Facebook', 'url' => 'https://www.facebook.com/##VALUE##', 'fa' => 'fa-brands fa-facebook', 'nourl' => 0, 'enabled' => 1],
                ['key' => 'signal', 'title' => 'Signal', 'url' => '##VALUE##', 'fa' => 'fa-brands fa-signal-messenger', 'nourl' => 1, 'enabled' => 0],
            ],
            '{
                "facebook": {"value": "fbuser", "url": "https://www.facebook.com/##VALUE##", "title": "Facebook", "nourl": 0, "fa": "fa-brands fa-facebook"},
                "signal": {"value": "signaluser.01", "url": "##VALUE##", "title": "Signal", "nourl": 1, "fa": "fa-brands fa-signal-messenger"}
            }'
        );

        $this->assertSame('signaluser.01', $socials->signal->value);
        $this->assertSame(0, $socials->signal->enabled);
        $this->assertFalse($socials->isEnabled('signal'));
        $this->assertTrue($socials->isEnabled('facebook'));
        $this->assertSame('fbuser', $socials->facebook->value);

        // What save() writes: the value of the disabled provider is still there.
        $stored = get_object_vars($socials);
        $this->assertArrayHasKey('signal', $stored);
        $this->assertSame('signaluser.01', $stored['signal']->value);
    }

    public function testACustomRowWithoutTheSwitchIsEnabled(): void
    {
        $this->requireJoomla();

        $socials = $this->normalizeWithProviders(
            [['key' => 'mastodon', 'title' => 'Mastodon', 'url' => 'https://mastodon.social/@##VALUE##', 'fa' => 'fa-brands fa-mastodon', 'nourl' => 0]],
            '{"mastodon": {"value": "tootuser", "url": "##VALUE##", "title": "Mastodon", "nourl": 0, "fa": ""}}',
            $providers
        );

        $this->assertSame(1, $providers->mastodon->enabled);
        $this->assertSame(1, $socials->mastodon->enabled);
        $this->assertTrue($socials->isEnabled('mastodon'));
        $this->assertSame('tootuser', $socials->mastodon->value);
    }

    public function testTheDefaultProvidersAreEnabled(): void
    {
        $this->requireJoomla();

        $socials = $this->normalizeWithProviders([], '{}', $providers);

        foreach ($providers as $key => $provider) {
            $this->assertSame(1, $provider->enabled, "$key is not enabled.");
            $this->assertTrue($socials->isEnabled($key), "$key is not enabled.");
        }
    }

    /**
     * Normalize the stored socials of a user with the providers of the admin, without the database.
     *
     * @param   array        $rows       The rows of the view Social websites, empty for the default list.
     * @param   string       $json       The stored socials of the user.
     * @param   object|null  $providers  The providers of these rows.
     *
     * @return  KunenaUserSocials
     */
    private function normalizeWithProviders(array $rows, string $json, ?object &$providers = null): KunenaUserSocials
    {
        $components = new ReflectionProperty(ComponentHelper::class, 'components');
        $cache      = new ReflectionProperty(KunenaUserSocials::class, 'providers');
        $saved      = $components->getValue();
        $record     = new ComponentRecord(['option' => 'com_kunena', 'enabled' => 1]);
        $record->setParams(new Registry($rows ? ['socials' => $rows] : []));
        $components->setValue(null, ['com_kunena' => $record] + $saved);
        $cache->setValue(null, null);

        try {
            $socials = new KunenaUserSocials();
            $socials->bind(json_decode($json));
            self::invoke($socials, 'normalize');
            $providers = KunenaUserSocials::getProviders();
        } finally {
            $components->setValue(null, $saved);
            $cache->setValue(null, null);
        }

        return $socials;
    }

    public function testAStoredUnlinkedEntryTakesTheNewAddress(): void
    {
        $this->requireJoomla();

        // No list of providers made by the admin, without the database: the parameters of com_kunena are empty.
        $components = new ReflectionProperty(ComponentHelper::class, 'components');
        $saved      = $components->getValue();
        $record     = new ComponentRecord(['option' => 'com_kunena', 'enabled' => 1]);
        $record->setParams(new Registry());
        $components->setValue(null, ['com_kunena' => $record] + $saved);
        $cache = new ReflectionProperty(KunenaUserSocials::class, 'providers');
        $cache->setValue(null, null);

        try {
            $socials = new KunenaUserSocials();
            $socials->bind(json_decode('{
                "weibo": {"value": "weibouser", "url": "##VALUE##", "title": "COM_KUNENA_MYPROFILE_WEIBO", "nourl": 1, "fa": "fa-brands fa-weibo"},
                "qzone": {"value": "12345678", "url": "##VALUE##", "title": "COM_KUNENA_MYPROFILE_QZONE", "nourl": 1, "fa": "fa-solid fa-square-share-nodes"}
            }'));
            self::invoke($socials, 'normalize');
        } finally {
            $components->setValue(null, $saved);
            $cache->setValue(null, null);
        }

        $this->assertSame(self::LINKED['weibo'], $socials->weibo->url);
        $this->assertSame(0, $socials->weibo->nourl);
        $this->assertSame('weibouser', $socials->weibo->value);
        $this->assertSame(self::LINKED['qzone'], $socials->qzone->url);
        $this->assertSame(0, $socials->qzone->nourl);
        $this->assertSame('12345678', $socials->qzone->value);
    }
}
