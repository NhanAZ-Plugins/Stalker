# Stalker

Stalker records server commands and sends a copy to online staff with the `stalker` permission. It runs on [Axolotl-PM](https://github.com/axolotl-pm/PocketMine-MP) and records commands from all command senders, including the console.

## Requirements

- Axolotl-PM API 5.0.0. The [build workflow](.github/workflows/build.yml) records pinned server revisions and test results. Compatibility with every 5.x revision has not been verified.
- PHP 8.1 or newer, as required by Axolotl-PM 5.

## Installation

Download the PHAR from [GitHub Releases](https://github.com/NhanAZ-Plugins/Stalker/releases), place it in the server's `plugins` directory and restart the server. Edit `plugins/Stalker/config.yml` after the first start to change the message format, redacted commands or log file selection.

The `stalker` permission defaults to operators. Other players do not receive command messages.

## Configuration and privacy

`message` supports `{sender}` and `{command}`. The default is `<{sender}> /{command}`. `perDayFile: false` writes to `log.log`; `true` chooses a dated filename when the plugin starts. The log filename does not rotate automatically at midnight.

Stalker always replaces non-whitespace characters in `login` and `register` commands with `*`. Add authentication aliases and other commands containing private arguments to `exceptionCmds`, without a leading slash. Matching ignores letter case. For example:

```yaml
exceptionCmds: ["l", "reg", "tell"]
```

Commands outside the redaction list are stored with their arguments. Review the command names and aliases installed on your server before using Stalker, and protect access to the log files.

## Development and validation

The [build workflow](.github/workflows/build.yml) tests command redaction against pinned Axolotl-PM source revisions. It then uses [DevTools](https://github.com/NhanAZ/DevTools) to run PHPStan at maximum level, build the PHAR and inspect the artifact. Exact tool and server revisions are recorded in the workflow and its [GitHub Actions runs](https://github.com/NhanAZ-Plugins/Stalker/actions).

Console command logging and plugin startup can be checked without a game client. Delivery of command messages to a Bedrock client has not been verified.

## Support

Join [NhanAZ Discord](https://discord.gg/j2X83ujT6c) for support.

## License

Stalker is distributed under the [GNU General Public License version 3](LICENSE).
