# Mask

Mask is a PocketMine plugin originally created by Ghast Noob. When enabled, it rotates a decorative mob head in the player's helmet slot.

## Requirements

- Targets Axolotl-PM API 5.0.0. CI checks API 5.0.0 and 5.49.1; this does not verify every intervening or future server revision.
- PHP requirements are inherited from the selected Axolotl-PM server.

## Commands

| Command | Description | Permission |
| --- | --- | --- |
| `/mask on` | Enable the rotating mask. | `mask.command` (operator by default) |
| `/mask off` | Disable the mask and restore the saved helmet. | `mask.command` (operator by default) |

Use the commands in game. The plugin does not grant permissions or run console commands.

## Helmet preservation

The existing `NguoiChoi.yml` player-mode file keeps its username keys and `on`/`off` values. A separate `MaskData.yml` stores each player's original helmet by UUID as an item snapshot, including its NBT metadata.

When a player turns the mask off, the plugin restores the saved helmet. If the player changed their helmet while the mask was active, Mask returns the saved helmet to their inventory when there is room. If the inventory is full, the mode stays enabled and the snapshot remains intact. A helmet that the player chose while the mask was active is not replaced by the repeating task.

On shutdown and player quit, Mask restores the saved helmet only when the helmet slot still contains a plugin-managed mask or is empty. If the player has equipped a different helmet, Mask leaves it in place and retains the snapshot for `/mask off`.

Older versions replaced helmet slots without saving the previous item. On the first run of this port, a legacy `on` entry can only snapshot the helmet that exists at that time; an item already overwritten by an older version cannot be recovered by this port.

## Build and verification

The GitHub Actions workflow uses [DevTools](https://github.com/NhanAZ/DevTools) to build and inspect the plugin PHAR. It checks PHP syntax, helmet snapshot safety fixtures, PHPStan at maximum level against pinned API 5 revisions, and plugin startup on an isolated Axolotl-PM server. The workflow records exact tool and server revisions for repeatable builds.

## Provenance

The original repository credits Ghast Noob. No license file or redistribution grant is present in this repository, so this port does not publish a stable release or claim a license that has not been verified. The earlier `setuperm Ghast_Noob *` startup command was removed; do not deploy historical revisions that contain it.
