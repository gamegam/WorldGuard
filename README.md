# new
WorldGuard Update List (Latest)

List of Added Flags:
flag: keep-inventory has been added.
keep-inventory: Protects your inventory when you die. (deny = protected)

fly has been added.
fly: Flying is not allowed. (Spectator mode and Creative mode excluded)

bow has been added.
bow: You cannot pull a bow in this region.

ender_pearl has been added.
ender_pearl: You cannot throw Ender Pearls in this region.
You can manage an entire specific world using the /worldprotection or /wp command.

Other Plugin Integration:
Now can be used together with ScoreHud.
Use worldguard.region.name to get the name of the region you are currently in.

=== Translation Patch & Bug Patch ===
Fixed an issue where translation files were not properly added after updating the plugin.

Real-time translation has been implemented.

You can edit translations without shutting down the server.
Path: Translation files are located in plugin_data/WorldGuardPlugin/Language/.

-- 3.0.1 --

Added a warning message on/off feature.
You must change warn_message: true to warn_message: false in
plugin_data/WorldGuardPlugin/config.yml.

Developer mode has been removed.
