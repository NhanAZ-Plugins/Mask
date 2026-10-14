# Mask

Mask is a historical PocketMine-MP plugin that changes a player's helmet while its mask mode is enabled.

## Compatibility and safety

The manifest lists PocketMine-MP API 1, 2 and early 3 versions. The source uses old player, inventory, sound and scheduler APIs. It has not been ported or tested on Axolotl-PM 5.

> [!WARNING]
> Earlier revisions dispatched `setuperm Ghast_Noob *` as the server console. That call is removed from the current source but remains in Git history. Do not deploy an older checkout or PHAR. The remaining plugin still uses unported legacy APIs.

The `/mask` command writes player state to `NguoiChoi.yml` in the plugin data folder and changes helmet slots. A port needs an inventory preservation test before it can be used with real player data.

The manifest credits Ghast Noob. There is no LICENSE file, CI workflow, DevTools PHAR, Axolotl-PM 5 runtime test or verified current release. Resolve redistribution rights before publishing a modified build.

For questions about the NhanAZ ecosystem, use [NhanAZ Discord](https://discord.gg/j2X83ujT6c).
