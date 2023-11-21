<?php

declare(strict_types=1);

namespace Terpz710\WarpsPE\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginOwned;
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

                $playerName = $sender->getName();
                $playerWarps = $this->config->getNested("warpspe.$playerName", []);

                if (isset($playerWarps[$warpName])) {
                    $playerWarps[$warpName] = [
                        "x" => $position->getX(),
                        "y" => $position->getY(),
                        "z" => $position->getZ(),
                        "world" => $world,
                        "permission" => "warpspe.warp.$warpName",
                    ];
                    $message = "§l§aWarp location §e{$warpName}§a updated";
                } else {
                    $playerWarps[$warpName] = [
                        "x" => $position->getX(),
                        "y" => $position->getY(),
                        "z" => $position->getZ(),
                        "world" => $world,
                        "permission" => "warpspe.warp.$warpName",
                    ];
                    $message = "§l§aWarp location §e{$warpName}§a set";
                }

                $this->config->setNested("warpspe.$playerName", $playerWarps);
                $this->config->save();

                $sender->sendMessage($message);
            } else {
                $sender->sendMessage("§l§cYou don't have permission to use this command");
            }
        } else {
            $sender->sendMessage("This command can only be used by players.");
        }
        return true;
    }
}
