# PacketLogger
A packet logger for Pocketmine-MP\
WARNING: This plugin is experimental and may lag your server with large amount of players (If it does please make an issue, and report any bugs)\
Make sure to delete your config if you were using older versions
## What's this?
It logs any packet sent between the player and the server
## Usage
Download the plugin and drop it into your plugins folder\
`/pkclear` Clears the log file\
`/pkreload` Reloads config
## Permissions
`pklogger.clear` Gives permission to use `/pkclear`\
`pklogger.reload` Gives permission to use `/pkreload`
## Log format
`date` Self-explanatory\
`time` Self-explanatory\
`packet` Name of the packet (again, self-explanatory :D)\
`cancelled` Whenever a plugin cancels either DataPacket receive or send events it will not be handled/sent by the server and it will show as true, false otherwise\
`src` Source\
`src-ip` IP address of the source\
`src-port` Port of the source\
`dst` Destination\
`dst-ip` IP address of the destination\
`dst-port` Port of the destination
