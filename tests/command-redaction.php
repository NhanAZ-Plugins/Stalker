<?php

declare(strict_types=1);

use NhanAZ\Stalker\Main;

$source = $argv[1] ?? null;
if ($source === null || !is_file($source . '/vendor/autoload.php')) {
	fwrite(STDERR, "Usage: php tests/command-redaction.php /path/to/Axolotl-PM-source\n");
	exit(2);
}
require $source . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/NhanAZ/Stalker/Main.php';

$redact = new ReflectionMethod(Main::class, 'redactCommand');
$cases = [
	['login secret', [], '***** ******'],
	['REGISTER password', [], '******** ********'],
	["  Login\tsecret", [], "  *****\t******"],
	['tell Steve private message', ['tell'], '**** ***** ******* *******'],
	['help', [], 'help'],
	['login secret', 'invalid', '***** ******'],
	['help', [123, null], 'help'],
];
foreach ($cases as [$command, $exceptions, $expected]) {
	$actual = $redact->invoke(null, $command, $exceptions);
	if ($actual !== $expected) {
		throw new RuntimeException("Unexpected redaction result for {$command}");
	}
}

echo "PASS: command redaction policy\n";
