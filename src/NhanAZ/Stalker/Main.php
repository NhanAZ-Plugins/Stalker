<?php

declare(strict_types=1);

namespace NhanAZ\Stalker;

use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\MainLogger;
use pocketmine\utils\Terminal;
use pocketmine\utils\Timezone;
use Symfony\Component\Filesystem\Path;

class Main extends PluginBase implements Listener {

	private const DEFAULT_REDACTED_COMMANDS = ["login", "register"];

	private MainLogger $logger;

	protected function onEnable(): void {
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if ($this->getConfig()->get("perDayFile") === true) {
			@mkdir($this->getDataFolder() . "logs/");
			$this->logger = new MainLogger(Path::join($this->getDataFolder(), "logs/" . date("Y-m-d") . ".log"), Terminal::hasFormattingCodes(), "Server", new \DateTimeZone(Timezone::get()));
		} else {
			$this->logger = new MainLogger(Path::join($this->getDataFolder(), "log.log"), Terminal::hasFormattingCodes(), "Server", new \DateTimeZone(Timezone::get()));
		}
	}

	protected function onDisable(): void {
		if (isset($this->logger)) {
			$this->logger->shutdownLogWriterThread();
		}
	}

	/**
	 * @handleCancelled true
	 */
	public function onCommandEvent(CommandEvent $event): void {
		$cmd = self::redactCommand($event->getCommand(), $this->getConfig()->get("exceptionCmds"));
		$name = $event->getSender()->getName();

		$replacements = ["{sender}" => $name, "{command}" => $cmd];
		$format = $this->getConfig()->get("message");
		if (!is_string($format)) {
			$this->getLogger()->warning("Invalid message format; using the default in memory");
			$format = "<{sender}> /{command}";
		}
		$message = str_replace(array_keys($replacements), array_values($replacements), $format);

		$this->logger->info($message);

		$onlinePlayers = $this->getServer()->getOnlinePlayers();
		foreach ($onlinePlayers as $stalker) {
			if ($stalker->hasPermission("stalker")) {
				$stalker->sendMessage($message);
			}
		}
	}

	private static function redactCommand(string $command, mixed $exceptionCmds): string {
		$redactedCommands = self::DEFAULT_REDACTED_COMMANDS;
		if (is_array($exceptionCmds)) {
			foreach ($exceptionCmds as $exceptionCmd) {
				if (is_string($exceptionCmd)) {
					$redactedCommands[] = strtolower($exceptionCmd);
				}
			}
		}
		$commandName = strtolower(strtok(ltrim($command), " \t\r\n") ?: "");
		if (in_array($commandName, $redactedCommands, true)) {
			return preg_replace('/[^\s]/', "*", $command) ?? str_repeat("*", strlen($command));
		}
		return $command;
	}
}
