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

class WarpsCommand extends Command implements PluginOwned {

    /** @var Config */
    private $config;

    /** @var Plugin */
    private $plugin;

    public function __construct(Config $config, Plugin $plugin) {
        parent::__construct(
            "warps",
            "List available warps",
            "/warps",
            ["listwarps"]
        );
        $this->config = $config;
        $this->plugin = $plugin;
        $this->setPermission("warpspe.warps");
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if ($sender instanceof Player) {
            if ($sender->hasPermission("warpspe.warps")) {
                $playerName = $sender->getName();
                $playerWarps = $this->config->getNested("warpspe.$playerName", []);

                if (empty($playerWarps)) {
                    $sender->sendMessage("§c§lYou haven't set any warps. Use §e/setwarp [WarpName]§c to set a warp");
                } else {
                    $warpList = implode("§f,§a ", array_keys($playerWarps));
                    $sender->sendMessage("§l§aAvailable warps§f:§a {$warpList}");
                }
            } else {
                $sender->sendMessage("§c§lYou don't have permission to use this command");
            }
        } else {
            $sender->sendMessage("This command can only be used by players.");
        }
        return true;
    }
}
