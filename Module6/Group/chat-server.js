// Require the packages we will use:
var http = require('http'),
	url = require('url'),
	path = require('path'),
	mime = require('mime'),
	path = require('path'),
	fs = require('fs');

const { off } = require('process');

// Make a simple fileserver for all of our static content.
// Everything underneath <STATIC DIRECTORY NAME> will be served.
var app = http.createServer(function(req, resp){
	var filename = path.join(__dirname, "./", url.parse(req.url).pathname);
	(fs.exists || path.exists)(filename, function(exists){
		if (exists) {
			fs.readFile(filename, function(err, data){
				if (err) {
					// File exists but is not readable (permissions issue?)
					resp.writeHead(500, {
						"Content-Type": "text/plain"
					});
					resp.write("Internal server error: could not read file");
					resp.end();
					return;
				}
				
				// File exists and is readable
				var mimetype = mime.getType(filename);
				resp.writeHead(200, {
					"Content-Type": mimetype
				});
				resp.write(data);
				resp.end();
				return;
			});
		}else{
			// File does not exist
			resp.writeHead(404, {
				"Content-Type": "text/plain"
			});
			resp.write("Requested file not found: "+filename);
			resp.end();
			return;
		}
	});
});
app.listen(3456);

// Import Socket.IO and pass our HTTP server object to it.
const socketio = require("socket.io")(http, {
    wsEngine: 'ws'
});

// Attach our Socket.IO server to our HTTP server to listen
const io = socketio.listen(app);

let users = {}; // username: socket.id
let rooms = {}; // roomname: password, creator, []curUsers, []banUsers
let logs = {} // roomname: [(user, text, time)]

function showRooms() {
	io.emit("showRooms",{message: rooms}); 
}

io.sockets.on("connection", function (socket) {

    // Log in request from the client
    socket.on("loginReq", function (data) {
        let username = data['message'];
        if(!(username in users)) {
            users[username] = [socket.id]; 
            socket.emit("login", {message: username});
			showRooms();
        }
        else {
            socket.emit("loginError",{message: "Username already exists!"})
        }
    })

	// disconnect
	socket.on("disconnect", function() {
		let curUser = "";
        for(let user in users){
            if(users[user][0] == socket.id)
            {
                curUser = user;
                break;
            }
        }
		delete users[curUser]; 

		// if the user is in a room, quit the room before disconnection
		for(let room in rooms){
			if(rooms[room][2].indexOf(curUser) > -1){
				rooms[room][2].splice(rooms[room][2].indexOf(curUser), 1);
				// broadcast to all other users that this user has left
				logs[room].push([curUser + " has left the room."])
				const otherUsers = rooms[room][2];
				for(let i = 0; i < otherUsers.length; i++)
				{
					socket.broadcast.to(users[otherUsers[i]][0]).emit("userLeave",{message: [curUser, rooms[room][2], logs[room], rooms[room][1]]});
				}
				break;
			}
		}
	})

	// New room request from the client
	socket.on("newroomReq", function (data) {
		// message: [newroom, newpwd, username]
		let newroom = data['message'][0];
		if(!(newroom in rooms)) {
			let newpwd = data['message'][1];
			let creator = data['message'][2];
			rooms[newroom] = [newpwd, creator, [], []]; // password, creator, []curUsers, []bannedUsers
			logs[newroom] = []; // []logs
			showRooms();
		}
		else {
			socket.emit("newroomError", {message: "Roomname already exists!"});
		}
	})

	// Show Chatroom request from the client
	socket.on("joinRoomReq", function(data){
		// message: [roomname, username]
		let roomname = data['message'][0]; 
		let username = data['message'][1];
		if(rooms[roomname][3].indexOf(username) > -1) {
			socket.emit("hasBeenBanned", {message: roomname})
		}
		else{
			if(rooms[roomname][0] != '') {	// if the room has password...
				socket.emit("showPwd", {message: roomname});
			}
			else{
				for(let room in rooms){
					if(rooms[room][2].indexOf(username) > -1){
						rooms[room][2].splice(rooms[room][2].indexOf(username), 1);
						// broadcast to all other users that this user has left
						logs[room].push([username + " has left the room."])
						const otherUsers = rooms[room][2];
						for(let i = 0; i < otherUsers.length; i++)
						{
							socket.broadcast.to(users[otherUsers[i]][0]).emit("userLeave",{message: [username, rooms[room][2], logs[room], rooms[room][1]]});
						}
						break;
					}
				}
				rooms[roomname][2].push(username);
				socket.emit("roomIn", {message: [roomname, rooms[roomname][2], logs[roomname], rooms[roomname][1]]}); // roomname, userlist, chatlog
				// broadcast to all users that this user has joined
				logs[roomname].push([username + ' has joined the room.']);
				const curUsers = rooms[roomname][2];
				for(let curUser in curUsers) {
					socket.broadcast.to(users[curUsers[curUser]][0]).emit("userJoin",{message: [username, curUsers, logs[roomname], rooms[roomname][1]]});
					
				}
				socket.emit("userJoin",{message: [username, curUsers, logs[roomname], rooms[roomname][1]]});
			}
		}
	})

	// Receive password
	socket.on("inputPwd", function(data) {
		// message: [roomname, password, username]
		let roomname = data['message'][0]; 
		let inputPwd = data['message'][1];
		let username = data['message'][2];
		if(rooms[roomname][0] == inputPwd) {
			for(let room in rooms){
				if(rooms[room][2].indexOf(username) > -1){
					rooms[room][2].splice(rooms[room][2].indexOf(username), 1);
					// broadcast to all other users that this user has left
					logs[room].push([username + " has left the room."])
					const otherUsers = rooms[room][2];
					for(let i = 0; i < otherUsers.length; i++)
					{
						socket.broadcast.to(users[otherUsers[i]][0]).emit("userLeave",{message: [username, rooms[room][2], logs[room], rooms[room][1]]});
					}
					break;
				}
			}
			rooms[roomname][2].push(username);
			socket.emit("roomIn", {message: [roomname, rooms[roomname][2], rooms[roomname][1]]});
			// broadcast to all users that this user has joined
			logs[roomname].push([username + ' has joined the room.']);
			const curUsers = rooms[roomname][2];
			for(let curUser in curUsers) {
				socket.broadcast.to(users[curUsers[curUser]][0]).emit("userJoin",{message: [username, curUsers, logs[roomname], rooms[roomname][1]]});
				
			}
			socket.emit("userJoin",{message: [username, curUsers, logs[roomname], rooms[roomname][1]]});
		}
		else {
			socket.emit("reject",{message: roomname});
		}
	})

	// Filter inappropriate words
	function filter(text){
		text = text.replace(/bitch|fuck|nigg|shit|negro|chink|spic/gi, '****');
		return text;
	}

	// Send message request from client
	socket.on("sendMsgReq", function(data){
		// message: [text, username, curRoom, sentTime]
		let inputText = data['message'][0];
		inputText = filter(inputText);
		let username = data['message'][1];
		let curRoom = data['message'][2];
		let curDate = new Date();
		let sendTime = curDate.toLocaleTimeString();
		logs[curRoom].push([inputText, username, sendTime]);
		// broadcast to all users in the room to update chat log
		const curUsers = rooms[curRoom][2];
		for(let curUser in curUsers) {
			socket.broadcast.to(users[curUsers[curUser]][0]).emit("updateLog",{message: logs[curRoom]});
		}
		socket.emit("updateLog",{message: logs[curRoom]});
	})

	// Send private message
	socket.on("privateMsgReq", function(data){
		// message: [text, username, curRoom, sentTime]
		let inputText = data['message'][0];
		inputText = filter(inputText);
		let username = data['message'][1];
		let curRoom = data['message'][2];
		let whoToSend = data['message'][3];
		let curDate = new Date();
		let sendTime = curDate.toLocaleTimeString();
		logs[curRoom].push([inputText, username, sendTime, whoToSend]);
		// broadcast to all users in the room to update chat log
		const curUsers = rooms[curRoom][2];
		for(let curUser in curUsers) {
			socket.broadcast.to(users[curUsers[curUser]][0]).emit("updateLog",{message: logs[curRoom]});
			
		}
		socket.emit("updateLog",{message: logs[curRoom]});
	})


	// Kick a user request from a room's creator
	socket.on("kickUserReq", function(data){
		let kickedUser = data['message'][0];
		let roomname = data['message'][1];
		let curUsers = rooms[roomname][2];
		// Remove the user from the room's user list
		rooms[roomname][2].splice(rooms[roomname][2].indexOf(kickedUser), 1);
		logs[roomname].push([kickedUser + ' has been kicked from the room.']);
		// Inform to the kicked user
		socket.broadcast.to(users[kickedUser][0]).emit("kicked",{message: [roomname]});
		// Broadcast to all other users in the room
		for(let curUser in curUsers) {
			socket.broadcast.to(users[curUsers[curUser]][0]).emit("userKicked",{message: [kickedUser, curUsers, logs[roomname], rooms[roomname][1]]});
		}
		socket.emit("userKicked",{message: [kickedUser, curUsers, logs[roomname], rooms[roomname][1]]});
	})

	// Ban a user request from a room's creator
	socket.on("banUserReq", function(data){
		let bannedUser = data['message'][0];
		let roomname = data['message'][1];
		let curUsers = rooms[roomname][2];
		// Remove the user from the room's user list
		rooms[roomname][2].splice(rooms[roomname][2].indexOf(bannedUser), 1);
		logs[roomname].push([bannedUser + ' has been banned from the room.']);
		// Add the user to the room's ban list
		rooms[roomname][3].push(bannedUser);
		// Inform to the banned user
		socket.broadcast.to(users[bannedUser][0]).emit("banned",{message: [roomname]});
		// Broadcast to all other users in the room
		for(let curUser in curUsers) {
			socket.broadcast.to(users[curUsers[curUser]][0]).emit("userBanned",{message: [bannedUser, curUsers, logs[roomname], rooms[roomname][1]]});
		}
		socket.emit("userBanned",{message: [bannedUser, curUsers, logs[roomname], rooms[roomname][1]]});
	})

	// get image and send to the rooms
	socket.on("sendImage", function(data){
		// message: [text, username, curRoom, sentTime]
		let imageData = data['message'][0];
		// console.log(imageData);
		let username = data['message'][1];
		let curRoom = data['message'][2];
		let curDate = new Date();
		let sendTime = curDate.toLocaleTimeString();
		
		logs[curRoom].push([["imageTag",imageData], username, sendTime]);
		// broadcast to all users in the room to update chat log
		const curUsers = rooms[curRoom][2];
		for(let curUser in curUsers) {
			socket.broadcast.to(users[curUsers[curUser]][0]).emit("updateLog",{message: logs[curRoom]});
		}
		socket.emit("updateLog",{message: logs[curRoom]});
	})

	// get image and send to the room with private user
	socket.on("sendPrivateImage", function(data){
		// message: [text, username, curRoom, sentTime]

		let imageData = data['message'][0];
		// console.log(imageData);
		let username = data['message'][1];
		let curRoom = data['message'][2];
		let whoToSend = data['message'][3];
		let curDate = new Date();
		let sendTime = curDate.toLocaleTimeString();
		
		logs[curRoom].push([["imageTag",imageData], username, sendTime, whoToSend]);
		// broadcast to all users in the room to update chat log
		const curUsers = rooms[curRoom][2];
		for(let curUser in curUsers) {
			socket.broadcast.to(users[curUsers[curUser]][0]).emit("updateLog",{message: logs[curRoom]});
		}
		socket.emit("updateLog",{message: logs[curRoom]});
	})

	// Get mentionAll request
	socket.on("mentionAll", function(data){
		let fromUser = data['message'][0];
		let roomname = data['message'][1];
		let curUsers = rooms[roomname][2];
		// update the log
		logs[roomname].push([fromUser + ' has tagged everyone in the room.']);
		// Broadcast to all other users in the room
		for(let curUser in curUsers) {
			socket.broadcast.to(users[curUsers[curUser]][0]).emit("beTagged",{message: [fromUser, curUsers, logs[roomname], rooms[roomname][1]]});
		}
		socket.emit("userMentioned",{message: [fromUser, curUsers, logs[roomname], rooms[roomname][1]]});
	})

});