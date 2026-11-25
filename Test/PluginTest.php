<?php

require_once 'tests/units/Base.php';

use Kanboard\Plugin\ShadcnTheme\Plugin;
use KanboardTests\units\Base;

class PluginTest extends Base
{
    public function testPluginNameIsNotEmpty()
    {
        $plugin = new Plugin($this->container);
        $this->assertNotEmpty($plugin->getPluginName());
    }

    public function testPluginVersionIsNotEmpty()
    {
        $plugin = new Plugin($this->container);
        $this->assertNotEmpty($plugin->getPluginVersion());
    }

    public function testPluginVersionValue()
    {
        $plugin = new Plugin($this->container);
        $this->assertSame('1.3.0', $plugin->getPluginVersion());
    }

    public function testManifestDeclaresFinalRelease()
    {
        $manifest = json_decode(file_get_contents(__DIR__.'/../plugin.json'), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('1.3.0', $manifest['version']);
        $this->assertSame([
            'superseded_by' => 'Harbor',
            'since' => '1.3.0',
            'reason' => "Harbor is ShadcnTheme's successor: the same look, a no-flash theme and the suite UI kit.",
        ], $manifest['deprecated'] ?? null);
    }

    public function testPluginNameValue()
    {
        $plugin = new Plugin($this->container);
        $this->assertSame('ShadcnTheme', $plugin->getPluginName());
    }

    public function testPluginAuthor()
    {
        $plugin = new Plugin($this->container);
        $this->assertNotEmpty($plugin->getPluginAuthor());
    }
}
