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

class DeleteWarpCommand extends Command implements PluginOwned {

    /** @var Config */
    private $config;

    /** @var Plugin */
    private $plugin;

    public function __construct(Config $config, Plugin $plugin) {
        parent::__construct(
            "deletewarp",
            "Delete a warp",
            null,
            ["delwarp"]
        );
        $this->config = $config;
        $this->plugin = $plugin;
        $this->setPermission("warpspe.deletewarp");
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if ($sender instanceof Player) {
            if ($sender->hasPermission("warpspe.deletewarp")) {
                $warpName = strtolower($args[0] ?? "default");
                $playerName = $sender->getName();
                $playerWarps = $this->config->getNested("warpspe.$playerName", []);

                if (isset($playerWarps[$warpName])) {
                    unset($playerWarps[$warpName]);

                    $this->config->setNested("warpspe.$playerName", $playerWarps);
                    $this->config->save();

                    $sender->sendMessage("§l§eWarp §c{$warpName}§e deleted");
                } else {
                    $sender->sendMessage("§c§lWarp §e{$warpName}§c not found. Use §e/warps§c to see your available warps");
                }
            } else {
                $sender->sendMessage("§l§cYou don't have permission to use this command");
            }
        } else {
            $sender->sendMessage("This command can only be used by players.");
        }
        return true;
    }
}
