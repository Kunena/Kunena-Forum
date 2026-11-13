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

    private const UNLINKED = ['microsoft', 'qqsocial', 'wechat', 'apple'];

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

    public function testAStoredUnlinkedEntryTakesTheNewAddress(): void
    {
        $this->requireJoomla();

        // No list of providers made by the admin, without the database: the parameters of com_kunena are empty.
        $components = new ReflectionProperty(ComponentHelper::class, 'components');
        $saved      = $components->getValue();
        $record     = new ComponentRecord(['option' => 'com_kunena', 'enabled' => 1]);
        $record->setParams(new Registry());
        $components->setValue(null, ['com_kunena' => $record] + $saved);

        try {
            $socials = new KunenaUserSocials();
            $socials->bind(json_decode('{
                "weibo": {"value": "weibouser", "url": "##VALUE##", "title": "COM_KUNENA_MYPROFILE_WEIBO", "nourl": 1, "fa": "fa-brands fa-weibo"},
                "qzone": {"value": "12345678", "url": "##VALUE##", "title": "COM_KUNENA_MYPROFILE_QZONE", "nourl": 1, "fa": "fa-solid fa-square-share-nodes"}
            }'));
            self::invoke($socials, 'normalize');
        } finally {
            $components->setValue(null, $saved);
        }

        $this->assertSame(self::LINKED['weibo'], $socials->weibo->url);
        $this->assertSame(0, $socials->weibo->nourl);
        $this->assertSame('weibouser', $socials->weibo->value);
        $this->assertSame(self::LINKED['qzone'], $socials->qzone->url);
        $this->assertSame(0, $socials->qzone->nourl);
        $this->assertSame('12345678', $socials->qzone->value);
    }
}
