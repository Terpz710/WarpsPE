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
            "/deletewarp <WarpName>",
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
                $warpName = strtolower($args[0] ?? "");

                $warps = $this->config->getAll()["warpspe"] ?? [];

                if (isset($warps[$warpName])) {
                    unset($warps[$warpName]);

                    $this->config->setNested("warpspe", $warps);
                    $this->config->save();

                    $sender->sendMessage("§eWarp {$warpName} deleted");
                } else {
                    $sender->sendMessage("§cWarp {$warpName} not found. Use /warps to see available warps");
                }
            } else {
                $sender->sendMessage("§cYou don't have permission to use this command");
            }
        } else {
            $sender->sendMessage("This command can only be used by players.");
        }
        return true;
    }
}
