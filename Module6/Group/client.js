let socketio = io.connect();

// Log in request and response
const loginBtn = document.getElementById("loginBtn");
loginBtn.addEventListener("click", loginReq, false);
let curRoom = " ";
const roomsDiv = document.getElementsByClassName("rooms")[0];
roomsDiv.style.display = 'none';
let chatroom = " ";
const chatroomDiv = document.getElementsByClassName("chatroom")[0];
chatroomDiv.style.display = 'none';

const uploadForm = document.getElementById('upload-form');
const uploadInput = document.getElementById('upload-input');
uploadForm.addEventListener('submit', uploadImage, false);


// Check the input valid or not
// const Regex = /^[a-zA-Z][a-zA-Z0-9_-]{2,19}$/;
const Regex = /^[\w_\.\-]+$/;
function isInputValid(input) {
    return Regex.test(input);
}

// Login function
function loginReq() {
    let username = document.getElementById("username").value;
    // check username valid or not
    if (isInputValid(username)) {
        console.log("Username is valid.");
        socketio.emit("loginReq", {message: username});
    } else {
        alert("Username is invalid.");
    }
}

socketio.on("login",function(data) {
    document.getElementsByClassName("login")[0].style.display = 'none';
    document.getElementsByClassName("rooms")[0].style.display = 'inline-block';
    document.getElementById("curUser").innerHTML = data['message'];
 });

socketio.on("loginError",function(data) {
    alert(data['message']);
});

// Create a chatroom
newroomBtn = document.getElementById("newroomBtn");
newroomBtn.addEventListener("click", newroomReq, false);
function newroomReq() {
    let newroom = document.getElementById("newroom").value;
    // check new room name valid or not
    if(isInputValid(newroom)) {
        let username = document.getElementById("curUser").innerHTML;
        let newpwd = document.getElementById("newpwd").value;
        document.getElementById("newroom").value = '';
        document.getElementById("newpwd").value = '';
        socketio.emit("newroomReq", {message: [newroom, newpwd, username]});
    }
    else {
        alert("Room name is invalid.");
    }
}

socketio.on("newroomError", function(data) {
    alert(data['message']);
})

// Show room list
socketio.on("showRooms", function(data) {
    const rooms = data['message'];
    const roomlist = document.getElementById("roomlist");
    roomlist.innerHTML = '';

    for(let roomname in rooms) {
        const roomListItem = createRoomListItem(roomname);
        roomlist.append(roomListItem);
    }
});

function createRoomListItem(roomname) {
    let li = document.createElement("li");
    li.setAttribute('id', 'room_' + roomname);
    li.innerHTML = roomname;
    li.style.cursor = 'pointer';
    li.addEventListener("click", joinRoomReq, false);
    return li;
}


// Show user list
function showUsers(curUsers, creator) {
    const userlist = document.getElementsByClassName("userlist")[0];
    userlist.innerHTML = '';

    const currentUser = document.getElementById("curUser").innerHTML;
    const isCreator = creator === currentUser;

    curUsers.forEach((curUser) => {
        const userListItem = createUserListItem(curUser, currentUser, isCreator);
        userlist.append(userListItem);
    });

    updateWhoToSendDropdown(curUsers, currentUser);
}

function createUserListItem(curUser, currentUser, isCreator) {
    let div = document.createElement("div");
    div.setAttribute("id", "user_" + curUser);

    let chatBtn = createChatButton(curUser, currentUser);
    div.appendChild(chatBtn);

    if (isCreator && curUser !== currentUser) {
        let kickBtn = createKickButton();
        let banBtn = createBanButton();
        div.appendChild(kickBtn);
        div.appendChild(banBtn);
    }

    return div;
}

function createChatButton(curUser, currentUser) {
    // need check
    
    let chatBtn = document.createElement("span");
    chatBtn.innerHTML = curUser;

    if (curUser !== currentUser) {
        chatBtn.style.cursor = 'pointer';
        chatBtn.addEventListener("click", chatUser, false);
    }

    return chatBtn;
}

function createKickButton() {
    let kickBtn = document.createElement("button");
    kickBtn.innerHTML = "Kick";
    kickBtn.addEventListener("click", kickUser, false);
    return kickBtn;
}

function createBanButton() {
    let banBtn = document.createElement("button");
    banBtn.innerHTML = "Ban";
    banBtn.addEventListener("click", banUser, false);
    return banBtn;
}

function updateWhoToSendDropdown(curUsers, currentUser) {
    const whoToSend = document.getElementById("select");
    whoToSend.innerHTML = '';

    let allOption = document.createElement("option");
    allOption.value = "0";
    allOption.text = "All";
    whoToSend.options.add(allOption);

    curUsers.forEach((curUser) => {
        if (curUser !== currentUser) {
            let newOption = document.createElement("option");
            newOption.value = 'sendTo_' + curUser;
            newOption.text = curUser;
            whoToSend.options.add(newOption);
        }
    });
}

// join a room
function joinRoomReq(e) {
    const roomname = e.currentTarget.id.substring(5);
    if(curRoom != roomname){
        let username = document.getElementById("curUser").innerHTML;
        socketio.emit("joinRoomReq", {message: [roomname, username]});
    }

}

// User is banned from joining the room
socketio.on("hasBeenBanned", function(data) {
    alert("You have been banned from joining that room!");
});

// User is prompted to enter a password to join a room
socketio.on("showPwd", function(data) {
    let roomname = data['message'];
    let inputPwd = prompt("Please enter password to join this room", "");
    let username = document.getElementById("curUser").innerHTML;
    socketio.emit("inputPwd", {message: [roomname, inputPwd, username]});
});

// User entered the wrong password
socketio.on("reject", function(data) {
    alert("Wrong Password!");
});

// User is allowed to join the room
socketio.on("roomIn", function(data) {
    const chatroom = document.getElementsByClassName("chatroom")[0];
    chatroom.style.display = 'inline-block';

    const [roomname, curUsers, chatlog, creator] = data['message'];

    showLogs(chatlog);
    showUsers(curUsers, creator);

    curRoom = roomname;
    document.getElementById("roomname").innerHTML = roomname;
});

// Someone leaves the room, update user list and logs
socketio.on("userLeave", function(data) {
    const [leaveUser, curUsers, logs, creator] = data['message'];

    showUsers(curUsers, creator);
    showLogs(logs);
});

// Someone joins the room, update user list and logs
socketio.on("userJoin", function(data) {
    const [joinUser, curUsers, logs, creator] = data['message'];

    showUsers(curUsers, creator);
    showLogs(logs);
});

// mention all
const mentionBtn = document.getElementById("mentionAllBtn");
mentionBtn.addEventListener("click", mentionAll, false);
function mentionAll(){
    let username = document.getElementById("curUser").innerHTML;
    let curRoom = document.getElementById("roomname").innerHTML;
    socketio.emit("mentionAll", {message: [username, curRoom]});
}


// Send message
const inputBtn = document.getElementById("inputBtn");
inputBtn.addEventListener("click", sendMsg, false);
function sendMsg(){
    let inputText = document.getElementById("inputText").value;
    let username = document.getElementById("curUser").innerHTML;
    let curRoom = document.getElementById("roomname").innerHTML;
    document.getElementById("inputText").value = '';
    if(inputText == ''){
        alert("Message cannot be empty!");
        return;
    }
    let whoToSend = document.getElementById("select").value;
    // Send public message
    if(whoToSend == 0) {
        socketio.emit("sendMsgReq", {message: [inputText, username, curRoom]});
    }
    // Send private message
    else {
        whoToSend = whoToSend.substring(7);
        socketio.emit("privateMsgReq", {message: [inputText, username, curRoom, whoToSend]});
    }
}

// Upload image
function uploadImage(e) {
    e.preventDefault();
    const file = uploadInput.files[0];
    // if file exists
    if (file) {
        const fileType = file.type;
        // if file is an image
        if (fileType.startsWith('image/')) {
            const maxFileSize = 520 * 1024; // 520k
            if (file.size > maxFileSize) {
                alert('File is too large. Maximum size is 520k.');
                return;
            }
            let reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                const url = reader.result
                let username = document.getElementById("curUser").innerHTML;
                let curRoom = document.getElementById("roomname").innerHTML;
                let whoToSend = document.getElementById("select").value;
                // Send public message
                if(whoToSend == 0) {
                    socketio.emit("sendImage", {message: [url, username, curRoom]});
                }
                // Send private message
                else {
                    whoToSend = whoToSend.substring(7);
                    socketio.emit("sendPrivateImage", {message: [url, username, curRoom, whoToSend]});
                }
            } 
        } else {
            alert("Only accept images!")
        }
    } else {
        alert("Please select an image before upload!")
    }
}


// Update chat log when someone sends a message
socketio.on("updateLog", function(data) {
    const logs = data['message'];
    showLogs(logs);
});

// Display chat logs
function showLogs(logs){
    const chatlog = document.getElementById("chatlog");
    chatlog.innerHTML = '';
    for(let i=0; i<logs.length; i++) {
        let msg = logs[i];
        if(msg.length == 3){
            const username = msg[1];
            const sendTime = msg[2];
            let line1 = username + '  ' + sendTime;
            let line1li = document.createElement("li");
            line1li.setAttribute('id', 'userInfo');
            line1li.textContent = line1;
            let line2li = document.createElement("li");
            // if it is with an image tag
            if(Array.isArray(msg[0]) && msg[0].length === 2 && msg[0][0] === "imageTag"){
                const img = msg[0][1];
                let imgTag = document.createElement("img");
                imgTag.src = img;
                imgTag.width = 200;
                imgTag.height = 200;
                line2li.appendChild(imgTag);
            }else{
                const inputText = msg[0];
                let line2 = inputText;
                line2li.setAttribute('id', 'userChat');
                line2li.textContent = line2;
            }
            if(username == document.getElementById("curUser").innerHTML) {
                line1li.style.textAlign = 'right';
                line2li.style.textAlign = 'right';
            }
            chatlog.appendChild(line1li);
            chatlog.appendChild(line2li);

        }
        else if(msg.length == 4) {
            const inputText = msg[0];
            const username = msg[1];
            const sendTime = msg[2];
            const whoToSend = msg[3];
            let line1 = username + '  ' + sendTime + ' (Private)';
            let line1li = document.createElement("li");
            line1li.setAttribute('id', 'userInfo');
            line1li.textContent = line1;
            let line2li = document.createElement("li");
            if(whoToSend == document.getElementById("curUser").innerHTML || username == document.getElementById("curUser").innerHTML){
                // if it is with an image tag
                if(msg[0].length==2){
                    const img = inputText[1];
                    let imgTag = document.createElement("img");
                    imgTag.src = img;
                    imgTag.width = 200;
                    imgTag.height = 200;
                    line2li.appendChild(imgTag);
                }else{
                    let line2 = inputText;
                    let line2li = document.createElement("li");
                    line2li.setAttribute('id', 'userChat');
                    line2li.textContent = line2;
                }
                if(username == document.getElementById("curUser").innerHTML) {
                    line1li.style.textAlign = 'right';
                    line2li.style.textAlign = 'right';
                }
                chatlog.appendChild(line1li);
                chatlog.appendChild(line2li);
            }
        }
        else{
            const inputText = msg[0];
            let line1 = inputText;
            let line1li = document.createElement("li");
            line1li.setAttribute('id', 'notice');
            line1li.textContent = line1;
            chatlog.appendChild(line1li);
        }
    }
    chatlogDiv = document.getElementsByClassName("chatlog")[0];
    chatlogDiv.scrollTop = chatlogDiv.scrollHeight;
}


// Chat with a user
function chatUser(e) {
    const user = e.currentTarget.parentElement.id.substring(5);
    console.log("chat with " + user);
}

// Kick a user
function kickUser(e) {
    const username = e.currentTarget.parentElement.id.substring(5);
    const curRoom = document.getElementById("roomname").innerHTML;
    socketio.emit("kickUserReq", {message: [username, curRoom]});
}

socketio.on("kicked", function(data) {
    const roomname = data['message'];
    const chatroom = document.getElementsByClassName("chatroom")[0];
    chatroom.style.display = 'none';
    document.getElementById("roomname").innerHTML = '';
    curRoom = '';
    alert("You have been kicked from room " + roomname);
});

socketio.on("userKicked", updateUserAndLogs);

// Ban a user
function banUser(e) {
    const username = e.currentTarget.parentElement.id.substring(5);
    const curRoom = document.getElementById("roomname").innerHTML;
    socketio.emit("banUserReq", {message: [username, curRoom]});
}

// User being banned
socketio.on("banned", function(data) {
    const roomname = data['message'];
    const chatroom = document.getElementsByClassName("chatroom")[0];
    chatroom.style.display = 'none';
    document.getElementById("roomname").innerHTML = '';
    curRoom = '';
    alert("You have been banned from room " + roomname);
});

socketio.on("userBanned", updateUserAndLogs);

// Update users and chat logs
function updateUserAndLogs(data) {
    const [, curUsers, logs, creator] = data['message'];
    showUsers(curUsers, creator);
    showLogs(logs);
}

// Users being tagged
function handleBeTagged(data) {
    let from = data['message'][0];
    alert("You have been tagged by " + from);
    updateUserAndLogs(data);
}

socketio.on("userMentioned", updateUserAndLogs);
socketio.on("beTagged",handleBeTagged);
