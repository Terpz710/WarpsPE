<?php

declare(strict_types=1);

namespace Terpz710\WarpsPE\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;
use pocketmine\player\Player;
use pocketmine\utils\Config;

use Terpz710\WarpsPE\Main;

class SetWarpCommand extends Command implements PluginOwned {

    /** @var Config */
    private $config;

    /** @var Plugin */
    private $plugin;

    public function __construct(Config $config, Plugin $plugin) {
        parent::__construct("setwarp", "Set a warp location", null, ["setlobby", "setspawn"]);
        $this->config = $config;
        $this->plugin = $plugin;
        $this->setPermission("warpspe.setwarp");
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if ($sender instanceof Player) {
            if ($sender->hasPermission("warpspe.setwarp")) {
                $warpName = strtolower($args[0] ?? "default");
                $position = $sender->getPosition();
                $world = $position->getWorld()->getFolderName();

                $warpData = [
                    "x" => $position->getX(),
                    "y" => $position->getY(),
                    "z" => $position->getZ(),
                    "world" => $world,
                    "permission" => "warpspe.warp.$warpName",
                ];

                $warps = $this->config->getAll()["warpspe"] ?? [];
                $warps[$warpName] = $warpData;

                $this->config->setNested("warpspe", $warps);
                $this->config->save();

                $sender->sendMessage("§aWarp location {$warpName} set");
            } else {
                $sender->sendMessage("§cYou don't have permission to use this command");
            }
        } else {
            $sender->sendMessage("This command can only be used by players.");
        }
        return true;
    }
}
