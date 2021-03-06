<?php

/*
 *
 *  ____  _             _         _____
 * | __ )| |_   _  __ _(_)_ __   |_   _|__  __ _ _ __ ___
 * |  _ \| | | | |/ _` | | '_ \    | |/ _ \/ _` | '_ ` _ \
 * | |_) | | |_| | (_| | | | | |   | |  __/ (_| | | | | | |
 * |____/|_|\__,_|\__, |_|_| |_|   |_|\___|\__,_|_| |_| |_|
 *                |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  Blugin team
 * @link    https://github.com/Blugin
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace blugin\chunkloader\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;

class ListSubcommand extends Subcommand{
    public const LABEL = "list";

    /**
     * @param CommandSender $sender
     * @param string[]      $args = []
     *
     * @return bool
     */
    public function execute(CommandSender $sender, array $args = []) : bool{
        //Get world name from args or player
        $worldName = null;
        if(isset($args[0])){
            $world = Server::getInstance()->getWorldManager()->getWorldByName($args[0]);
            if($world === null){
                $sender->sendMessage($this->plugin->getLanguage()->translate("commands.chunkloader.list.failure.invalidWorld", [$args[0]]));
                return true;
            }
            $worldName = $args[0];
        }elseif($sender instanceof Player){
            $worldName = $sender->getWorld()->getFolderName();
        }else{
            return false;
        }

        //Make chunkhash list for show command
        $chunkHashs = $this->plugin->getChunkDataMap($worldName)->getAll();
        $list = array_chunk($chunkHashs, $sender->getScreenLineHeight());
        $max = count($list);

        //Get page number from args
        if(isset($args[1]) && is_numeric($args[1]) && $args[1] >= 1){
            $page = min($max, (int) $args[1]);
        }else{
            $page = 1;
        }

        //Send list of registered chunk
        $sender->sendMessage($this->plugin->getLanguage()->translate("commands.chunkloader.list.head", [
            $worldName,
            (string) $page,
            (string) $max
        ]));
        if(isset($list[$page - 1])){
            foreach($list[$page - 1] as $chunkHash){
                World::getXZ($chunkHash, $chunkX, $chunkZ);
                $sender->sendMessage($this->plugin->getLanguage()->translate("commands.chunkloader.list.item", [
                    (string) $chunkX,
                    (string) $chunkZ
                ]));
            }
        }
        return true;
    }
}
