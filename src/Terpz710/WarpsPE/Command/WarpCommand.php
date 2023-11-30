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

class WarpCommand extends Command implements PluginOwned {

    /** @var Config */
    private $config;

    /** @var Plugin */
    private $plugin;

    public function __construct(Config $config, Plugin $plugin) {
        parent::__construct(
            "warp",
            "Teleport to a warp location",
            "/warp <WarpName>",
            ["teleport"]
        );
        $this->config = $config;
        $this->plugin = $plugin;
        $this->setPermission("warpspe.warp");
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if ($sender instanceof Player) {
            if ($sender->hasPermission("warpspe.warp")) {
                $warpName = strtolower($args[0] ?? "");

                $warps = $this->config->getAll()["warpspe"] ?? [];

                if (isset($warps[$warpName])) {
                    $warpData = $warps[$warpName];
                    $world = $warpData["world"];
                    $x = $warpData["x"];
                    $y = $warpData["y"];
                    $z = $warpData["z"];

                    $sender->teleport($this->plugin->getServer()->getWorldManager()->getWorldByName($world)->getSpawnLocation()->setComponents($x, $y, $z));
                    $sender->sendMessage("§l§aTeleported to warp §e{$warpName}§a");
                } else {
                    $sender->sendMessage("§c§lWarp §e{$warpName}§c not found. Use §e/warps§c to see available warps");
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
