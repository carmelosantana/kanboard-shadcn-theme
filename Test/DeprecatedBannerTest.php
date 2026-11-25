<?php

require_once 'tests/units/Base.php';

use Kanboard\Core\Http\Request;
use Kanboard\Core\Security\Role;
use Kanboard\Plugin\ShadcnTheme\Plugin;
use KanboardTests\units\Base;

class DeprecatedBannerTest extends Base
{
    /** @dataProvider bannerRoutes */
    public function testBannerVisibility(string $role, string $path, array $params, bool $visible): void
    {
        unset($this->container['request']);
        $this->container['request'] = new Request($this->container, [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/kanboard/'.$path,
            'PHP_SELF' => '/kanboard/index.php',
        ], $params);
        $this->container['route']->enable()
            ->addRoute('config/branding/:id', 'BrandingController', 'index', 'Branding')
            ->addRoute('config/modmenu', 'ModMenuController', 'show', 'ModMenu');
        $this->container['router']->dispatch();
        $this->container['userSession']->initialize(['id' => 1, 'role' => $role]);
        (new Plugin($this->container))->initialize();

        $html = $this->container['helper']->hook->render('template:layout:top');

        if ($visible) {
            $this->assertMatchesRegularExpression(
                '~Shadcn Theme is replaced by Harbor\.\s*<a href="/kanboard/config/modmenu"[^>]*>Migrate in ModMenu</a>~',
                $html
            );
        } else {
            $this->assertSame('', trim($html));
        }
    }

    public static function bannerRoutes(): array
    {
        $config = ['controller' => 'ConfigController', 'action' => 'index'];
        $board = ['controller' => 'BoardViewController', 'action' => 'show'];

        return [
            'admin on core settings' => [Role::APP_ADMIN, '', $config, true],
            'admin on a config path' => [Role::APP_ADMIN, 'config/branding/1', [], true],
            'admin in ModMenu by query parameters' => [Role::APP_ADMIN, '', [
                'controller' => 'ModMenuController', 'action' => 'show', 'plugin' => 'ModMenu',
            ], true],
            'non-admin on settings' => [Role::APP_USER, '', $config, false],
            'admin on a board' => [Role::APP_ADMIN, '', $board, false],
            'admin on a task' => [Role::APP_ADMIN, '', ['controller' => 'TaskViewController', 'action' => 'show'], false],
            'admin on a board overriding a config path' => [Role::APP_ADMIN, 'config/branding/1', $board, false],
        ];
    }
}
