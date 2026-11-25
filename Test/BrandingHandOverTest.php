<?php

/**
 * 1.2.0 hand-over: logo, favicon and the settings uploads belong to Steward now.
 * These tests never need Steward installed — a test double stands in for its helper.
 */

require_once 'tests/units/Base.php';

use Kanboard\Core\Base as CoreBase;
use Kanboard\Core\Security\Role;
use Kanboard\Plugin\ShadcnTheme\Plugin;
use KanboardTests\units\Base;

/** Stands in for Steward's `stewardBranding` helper. */
class FakeStewardBrandingHelper extends CoreBase
{
    public static string $logo = '';
    public static string $name = '';

    public function logoUrl(): string
    {
        return self::$logo;
    }

    public function siteName(): string
    {
        return self::$name;
    }
}

class BrandingHandOverTest extends Base
{
    protected function setUp(): void
    {
        parent::setUp();
        FakeStewardBrandingHelper::$logo = '';
        FakeStewardBrandingHelper::$name = '';
    }

    private function initializePlugin(): void
    {
        $this->container['route']->enable();
        (new Plugin($this->container))->initialize();
    }

    /** Controller the router resolves $path to ('DashboardController' when no route matches). */
    private function routedController(string $path): string
    {
        return $this->container['route']->findRoute($path)['controller'];
    }

    private function useSteward(string $logo, string $name): void
    {
        FakeStewardBrandingHelper::$logo = $logo;
        FakeStewardBrandingHelper::$name = $name;
        $this->container['helper']->register('stewardBranding', FakeStewardBrandingHelper::class);
    }

    private function loginHeader(): string
    {
        return $this->container['template']->render('ShadcnTheme:auth/login_header', []);
    }

    public function testNoServeAssetRoute()
    {
        $this->initializePlugin();

        $this->assertSame('DashboardController', $this->routedController('shadcn-theme/asset/logo'));
        $this->assertNotContains(Role::APP_PUBLIC, $this->container['applicationAccessMap']->getRoles('SettingsController', 'serveAsset'));
    }

    public function testNoSettingsRoutesOrSidebar()
    {
        $this->initializePlugin();

        $this->assertSame('DashboardController', $this->routedController('shadcn-theme/settings'));
        $this->assertSame('DashboardController', $this->routedController('shadcn-theme/upload'));
        $this->assertSame([], $this->container['hook']->getListeners('template:config:sidebar'));
        $this->assertFalse(class_exists('Kanboard\Plugin\ShadcnTheme\Controller\SettingsController'));
    }

    public function testThemeRoutesAndToggleStay()
    {
        $this->initializePlugin();

        $this->assertSame('ShadcnTheme:ThemeController', $this->routedController('theme/get'));
        $this->assertNotEmpty($this->container['hook']->getListeners('template:header:dropdown'));
    }

    public function testNoHeaderTitleOverride()
    {
        $this->initializePlugin();

        $this->assertStringNotContainsString('ShadcnTheme', $this->container['template']->getTemplateFile('header/title'));
    }

    public function testHeadEmitsNoFaviconEvenWithAnOldUpload()
    {
        $this->container['configModel']->save(['shadcn_favicon_path' => 'shadcn-theme/favicon.png']);
        $this->container['memoryCache']->flush();

        $html = $this->container['template']->render('ShadcnTheme:layout/head', []);

        $this->assertStringContainsString('theme-preload.js', $html);
        $this->assertStringNotContainsString('rel="icon"', $html);
        $this->assertStringNotContainsString('serveAsset', $html);
    }

    public function testLoginCardShowsProductNameWithoutSteward()
    {
        $this->container['configModel']->save(['shadcn_logo_path' => 'shadcn-theme/logo.png']);
        $this->container['memoryCache']->flush();

        $html = $this->loginHeader();

        $this->assertStringContainsString('<h1 class="auth-card-title">Kanboard</h1>', $html);
        $this->assertStringNotContainsString('<img', $html);
    }

    public function testLoginCardShowsStewardLogo()
    {
        $this->useSteward('?controller=BrandingController&amp;action=image&amp;slot=logo', 'Acme & Co');

        $html = $this->loginHeader();

        $this->assertStringContainsString('<img src="?controller=BrandingController&amp;action=image&amp;slot=logo"', $html);
        $this->assertStringContainsString('alt="Acme &amp; Co"', $html);
        $this->assertStringNotContainsString('auth-card-title', $html);
    }

    public function testLoginCardShowsStewardSiteNameWithoutLogo()
    {
        $this->useSteward('', 'Acme & Co');

        $this->assertStringContainsString('<h1 class="auth-card-title">Acme &amp; Co</h1>', $this->loginHeader());
    }

    public function testLoginCardFallsBackToProductNameWhenStewardBrandingIsOff()
    {
        $this->useSteward('', '');

        $html = $this->loginHeader();

        $this->assertStringContainsString('<h1 class="auth-card-title">Kanboard</h1>', $html);
        $this->assertStringNotContainsString('<img', $html);
    }

    public function testOldUploadKeysAreLeftForStewardImport()
    {
        $this->container['configModel']->save([
            'shadcn_logo_path' => 'shadcn-theme/logo.png',
            'shadcn_favicon_path' => 'shadcn-theme/favicon.png',
        ]);

        $this->initializePlugin();
        $this->container['memoryCache']->flush();

        $this->assertSame('shadcn-theme/logo.png', $this->container['configModel']->getOption('shadcn_logo_path'));
        $this->assertSame('shadcn-theme/favicon.png', $this->container['configModel']->getOption('shadcn_favicon_path'));
    }

    public function testPluginVersionMatchesManifest()
    {
        $json = json_decode(file_get_contents(__DIR__.'/../plugin.json'), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame($json['version'], (new Plugin($this->container))->getPluginVersion());
        $this->assertStringContainsString('## [1.2.0] - 2026-11-20', file_get_contents(__DIR__.'/../CHANGELOG.md'));
    }

    public function testRecommendsSteward()
    {
        $json = json_decode(file_get_contents(__DIR__.'/../plugin.json'), true);

        $this->assertContains(
            ['plugin' => 'Steward', 'min_version' => '1.0.0', 'reason' => 'site logo, favicon and name'],
            $json['recommends'] ?? []
        );
    }
}
