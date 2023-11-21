<?php

declare(strict_types=1);

namespace Terpz710\WarpsPE;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use Terpz710\WarpsPE\Command\WarpCommand;
use Terpz710\WarpsPE\Command\SetWarpCommand;
use Terpz710\WarpsPE\Command\DeleteWarpCommand;
use Terpz710\WarpsPE\Command\WarpsCommand;

class Main extends PluginBase {

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        $this->getServer()->getCommandMap()->register("warp", new WarpCommand($config, $this));
        $this->getServer()->getCommandMap()->register("warps", new WarpsCommand($config, $this));
        $this->getServer()->getCommandMap()->register("deletewarp", new DeleteWarpCommand($config, $this));
        $this->getServer()->getCommandMap()->register("setwarp", new SetWarpCommand($config, $this));
    }
}
