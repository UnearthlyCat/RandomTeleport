<?php

namespace unearthly\randomtp\command;

use pocketmine\command\Command;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\world\format\Chunk;
use pocketmine\math\Vector3;
use unearthly\randomtp;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\sound\EndermanTeleportSound;

class RandomTeleportCommand extends Command
{

    function __construct()
    {
        parent::__construct("rtp", "Телепортация в случайное место");
        $this->setPermission(DefaultPermissions::ROOT_USER);
    }

    function execute(CommandSender $sender, string $commandLabel, array $args) : void
    {
        if ($sender instanceof Player)
        {
            $world = $sender->getWorld();
            $x = mt_rand(-5000, 5000);
            $z = mt_rand(-5000, 5000);
            $sender->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 80));
            $world->orderChunkPopulation(($x >> Chunk::COORD_BIT_SIZE), ($z >> Chunk::COORD_BIT_SIZE), null)->onCompletion(
                function () use ($world, $x, $z, $sender) : void
                {
                    $y = ($world->getHighestBlockAt($x, $z) + 1);
                    $position = new Vector3($x, $y, $z);
                    $sender->teleport($position);
                    $sender->sendMessage("Вы телепортировались в случайное место:§e$x $y $z §f");
                    RandomTeleport::getInstance()->getScheduler()->scheduleDelayedTask(
                        new ClosureTask(
                            function () use ($world, $position) : void
                            {
                                $world->addSound($position, new EndermanTeleportSound());
                            }
                        ),
                        5
                    );
                },
                fn () => null
            );
        } else {
            $sender->sendMessage("§cИспользуйте только в игре");
        }
    }
}
