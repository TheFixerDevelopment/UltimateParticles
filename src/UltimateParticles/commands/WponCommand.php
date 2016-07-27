<?php

/*
 * This file is a part of UltimateParticles.
 * Copyright (C) 2016 hoyinm14mc
 *
 * UltimateParticles is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * UltimateParticles is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with UltimateParticles. If not, see <http://www.gnu.org/licenses/>.
 */
namespace UltimateParticles\commands;

use UltimateParticles\base\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;

class WponCommand extends BaseCommand{

	public function onCommand(CommandSender $issuer, Command $cmd, $label, array $args){
		switch($cmd->getName()){
			case "wpon":
				if($issuer->hasPermission("walkingparticles.command.wpon")){
					if($issuer instanceof Player !== false){
						if($this->getPlugin()->enableEffects($issuer) !== true){
							return true;
						}
						$issuer->sendMessage($this->getPlugin()->colorMessage("&aYour UltimateParticles has been turned on!"));
						return true;
					} else{
						$issuer->sendMessage("Command only works in-game!");
						return true;
					}
				} else{
					$issuer->sendMessage($this->getPlugin()->colorMessage("&cYou don't have permission for this!"));
					return true;
				}
			break;
		}
	}

}
?>