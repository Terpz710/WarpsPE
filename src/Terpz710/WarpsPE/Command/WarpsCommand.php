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
                $warps = $this->config->getAll()["warpspe"] ?? [];

                if (empty($warps)) {
                    $sender->sendMessage("§cNo warps are set. Ask an admin to set some warps with /setwarp [WarpName].");
                } else {
                    $warpList = implode(", ", array_keys($warps));
                    $sender->sendMessage("§aAvailable warps: {$warpList}");
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
