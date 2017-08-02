<?php
/*
 * This file is a part of UltimateParticles.
 * Copyright (C) 2017 hoyinm14mc
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

namespace hoyinm14mc\ultimateparticles;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class UltimateparticlesCommand extends PluginCommand {
	/**
	 * UltimateparticlesCommand constructor.
	 *
	 * @param Plugin $owner
	 */
	public function __construct(Plugin $owner){
		parent::__construct("ultimateparticles", $owner);
		$this->setUsage("/ultimateparticles help");
		$this->setDescription("Main command for particle manipulation");
		$this->setAliases(["ultip", "up"]);
		$this->setPermission("ultimateparticles.command.ultimateparticles");
	}

	/**
	 * @return UltimateParticles
	 */
	public function getPlugin() : Plugin {
		return parent::getPlugin();
	}

	/**
	 * @param CommandSender $issuer
	 * @param string $label
	 * @param array $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $issuer, string $label, array $args) : bool {
		if (isset($args[0]) !== true) {
			return false;
		}
		if ($issuer instanceof Player !== true) {
			$issuer->sendMessage($this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["operation.inGameOnly"]));
			return true;
		}
		switch (strtolower($args[0])) {
			case "addparticle":
			case "addp":
				if ($issuer->hasPermission("ultimateparticles.command.ultimateparticles.addparticle") !== true) {
					$issuer->sendMessage($this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["operation.noPermission"]));
					return true;
				}
				if (count($args) < 3) {
					$issuer->sendMessage("Usage: /ultip addp (particle) (amplifier) (shape) (type)");
					return true;
				}
				if (is_numeric($args[2]) !== true) {
					$issuer->sendMessage(str_replace("%arg%", "amplifier", $this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["operation.parameterInvalid"])));
					$issuer->sendMessage("Usage: /ultip addp (particle) (amplifier) (shape) (type)");
					return true;
				}
				if (isset($args[3]) !== true or (isset($args[3]) !== false and $args[3] != "spiral" and $args[3] != "tail")) {
					$args[3] = $this->getPlugin()->getConfig()->get("default-shape");
				}
				if (isset($args[4]) !== true or (isset($args[4]) !== false and $args[3] != "particle")) {
					$args[4] = $this->getPlugin()->getConfig()->get("default-type");
				}
				if ($this->getPlugin()->addParticle($issuer->getName(), $args[1], $args[2], $args[3], $args[4]) !== false) {
					$issuer->sendMessage(
						str_replace("%particle%", $args[1],
									str_replace("%amplifier%", $args[2],
												str_replace("%shape%", $args[3],
															str_replace("%type%", $args[4], $this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["addparticle.success"]))
												)
									)
						)
					);
					return true;
				} else {
					$issuer->sendMessage($this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["operation.failure"]));
					return true;
				}
			break;
			case "removeparticle":
			case "removep":
				if ($issuer->hasPermission("ultimateparticles.command.ultimateparticles.removeparticle") !== true) {
					$issuer->sendMessage($this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["operation.noPermission"]));
					return true;
				}
				if (isset($args[1]) !== true) {
					$issuer->sendMessage("Usage: /ultip removep (particle)");
					return true;
				}
				$t = $this->getPlugin()->data->getAll();
				$exist = false;
				foreach (array_keys($t[$issuer->getName()]["particles"]) as $particle) {
					if ($particle == strtolower($args[1])) {
						$exist = true;
					}
				}
				if ($exist !== true) {
					$issuer->sendMessage($this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["removeparticle.particleNotContained"]));
					return true;
				}
				if ($this->getPlugin()->removeParticle($issuer->getName(), $args[1]) !== false) {
					$issuer->sendMessage(str_replace("%particle%", strtolower($args[1]), $this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["removeparticle.success"])));
					return true;
				} else {
					$issuer->sendMessage($this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["operation.failure"]));
					return true;
				}
			break;
			case "get":
			case "getparticles":
				if ($issuer->hasPermission("ultimateparticles.command.ultimateparticles.getparticles") !== true) {
					$issuer->sendMessage($this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["operation.noPermission"]));
					return true;
				}
				$issuer->sendMessage(str_replace("%list%", $this->getPlugin()->playerParticlesToString($issuer->getName()), $this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["getparticles.success"])));
				return true;
			break;
			case "help":
				$issuer->sendMessage($this->getPlugin()->colorMessage($this->getPlugin()->getLanguage()["help"]));
				return true;	
			break;
			default:
				return false;
		}
	}
}