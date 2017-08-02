<?php
/*
 * This file is the main class of UltimateParticles.
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

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class UltimateParticles extends PluginBase {
	/** @var array $lang */
	private $lang = [];
	/** @var Config $data */
	public $data;

	public function onLoad() {
		$this->saveDefaultConfig();
		$this->data = new Config($this->getDataFolder() . "players.yml", Config::YAML);
		$lang = $this->getConfig()->get("default-lang", "en");
		$this->lang = (new Config($this->getResource("lang_{$lang}.json"), Config::JSON))->getAll();
		$this->getServer()->getCommandMap()->register(self::class, new UltimateparticlesCommand($this));
		new PlayerListener($this);
	}

	/**
	 * @return array
	 */
	public function getLanguage() : array {
		return $this->lang;
	}

	/**
	 * @param string $message
	 *
	 * @return string
	 */
	public function colorMessage(string $message) : string {
		$colors = [
			"0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f", "k", "l", "m", "n", "o", "r",
		];
		$search = [];
		$replace = [];
		foreach ($colors as $code) {
			$search[] = "&" . $code;
			$replace[] = TextFormat::ESCAPE . $code;
		}
		return str_replace($search, $replace, $message);
	}

	/**
	 * @param string $player_name
	 *
	 * @return bool
	 */
	public function initializePlayerProfile(string $player_name) : bool {
		$t = $this->data->getAll();
		if ($this->playerProfileExists($player_name) !== false) {
			return false;
		}
		$t[$player_name]["particles"]["item_0"]["amplifier"] = 0;
		$t[$player_name]["particles"]["item_0"]["shape"] = "tail";
		$t[$player_name]["particles"]["item_0"]["type"] = "particle";
		$this->data->setAll($t);
		$this->data->save();
		return true;
	}

	/**
	 * @param string $player_name
	 *
	 * @return bool
	 */
	public function playerProfileExists(string $player_name) : bool {
		$t = $this->data->getAll();
		return array_key_exists($player_name, $t);
	}

	/**
	 * @param string $player_name
	 * @param string $particle
	 * @param int $amplifier
	 * @param string $shape
	 * @param string $type
	 *
	 * @return bool
	 */
	public function addParticle(string $player_name, string $particle, int $amplifier = 1, string $shape = "tail", string $type) : bool {
		$t = $this->data->getAll();
		if ($this->playerProfileExists($player_name) !== true) {
			return false;
		}
		if ($type != "particle" or $type != "block") {
			return false;
		}
		$t[$player_name]["particles"][$particle]["amplifier"] = $amplifier;
		$t[$player_name]["particles"][$particle]["shape"] = (($shape == "tail" or $shape == "spiral") ? $shape : "tail");
		$t[$player_name]["particles"][$particle]["type"] = $type;
		$this->data->setAll($t);
		$this->data->save();
		return true;
	}

	/**
	 * @param string $player_name
	 * @param string $particle
	 *
	 * @return bool
	 */
	public function removeParticle(string $player_name, string $particle) : bool {
		$t = $this->data->getAll();
		if ($this->playerProfileExists($player_name) !== true) {
			return false;
		}
		unset($t[$player_name]["particles"][$particle]);
		$this->data->setAll($t);
		$this->data->save();
		return true;
	}

	/**
	 * @param string $player_name
	 *
	 * @return string
	 */
	public function playerParticlesToString(string $player_name) : string {
		$t = $this->data->getAll();
		$message = "Particle:Amplifier:Shape:Type\n";
		foreach ($t[$player_name]["particles"] as $p => $array) {
			$message = $message . $p . ":" . (string)$t[$player_name]["particles"][$p]["amplifier"] . ":" . $t[$player_name]["particles"][$p]["shape"] . ":" . $t[$player_name]["particles"][$p]["type"] . "\n";
		}
		return $message;
	}
}