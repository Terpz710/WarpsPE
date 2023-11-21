<?php

declare(strict_types=1);

namespace Terpz710\WarpsPE\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\world\Position;

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
            "/warp [warp]",
            ["warps", "mywarps"]
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
            $warpPermission = "warpspe.warp." . strtolower($args[0] ?? "default");

            if ($sender->hasPermission($warpPermission)) {
                $warpName = strtolower($args[0] ?? "default");
                $playerName = $sender->getName();
                $playerWarps = $this->config->getNested("warpspe.$playerName", []);

                if (empty($playerWarps)) {
                    $sender->sendMessage("§c§lYou haven't set any warps. Use §e/setwarp [WarpName]§c to set a warp");
                    return true;
                }

                if (empty($args)) {
                    $warpList = implode("§f,§a ", array_keys($playerWarps));
                    $sender->sendMessage("§l§aYour warps§f:§a {$warpList}");
                    return true;
                }

                if (isset($playerWarps[$warpName])) {
                    $warpData = $playerWarps[$warpName];
                    $worldName = $warpData["world"];
                    $x = $warpData["x"];
                    $y = $warpData["y"];
                    $z = $warpData["z"];

                    $worldManager = $this->plugin->getServer()->getWorldManager();

                    if (!$worldManager->isWorldLoaded($worldName) && !$worldManager->loadWorld($worldName)) {
                        $sender->sendMessage("§l§cFailed to load world§f: §e{$worldName}");
                        return true;
                    }

                    $world = $worldManager->getWorldByName($worldName);
                    if ($world === null) {
                        $sender->sendMessage("§l§cWorld not found§f: §e{$worldName}");
                        return true;
                    }

                    $position = new Position($x, $y, $z, $world);
                    $sender->teleport($position);
                    $sender->sendMessage("§l§aTeleported to warp §e{$warpName}");
                } else {
                    $sender->sendMessage("§c§lWarp §e{$warpName}§c not found. Use §e/warp§c to see your available warps");
                }
            } else {
                $sender->sendMessage("§c§lYou don't have permission to use this warp");
            }
        } else {
            $sender->sendMessage("This command can only be used by players.");
        }
        return true;
    }
}
