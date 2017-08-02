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

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;

class PlayerListener implements Listener {
	/** @var UltimateParticles $plugin */
	private $plugin;

	/**
	 * PlayerListener constructor.
	 *
	 * @param UltimateParticles $plugin
	 */
	public function __construct(UltimateParticles $plugin) {
		$this->plugin = $plugin;
		$this->plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	/**
	 * @param PlayerJoinEvent $event
	 */
	public function onPlayerJoin(PlayerJoinEvent $event) {
		$this->plugin->initializePlayerProfile($event->getPlayer()->getName());
	}

	/**
	 * @param PlayerMoveEvent $event
	 */
	public function onPlayerMove(PlayerMoveEvent $event) {
		if ($event->getFrom()->x != $event->getPlayer()->x or $event->getFrom()->z != $event->getPlayer()->z) {
			//Has change of displacement
			$particles = new Particles($this->plugin);
			$particles->showParticleEffects($event->getPlayer(), $event->getFrom());
		}
	}
}