initCheck();

var month_olympic = [31,29,31,30,31,30,31,31,30,31,30,31];
var month_normal = [31,28,31,30,31,30,31,31,30,31,30,31];
var month_name = ["January","Febrary","March","April","May","June","July","Auguest","September","October","November","December"];

var holder = document.getElementById("days");
var prev = document.getElementById("prev");
var next = document.getElementById("next");
var ctitle = document.getElementById("calendar-title");
var cyear = document.getElementById("calendar-year");
var addEventButton = document.getElementById("add-event-btn");
const modal = document.getElementById("add-event-modal");
const closeButton = modal.querySelector(".close");
const form = modal.querySelector("#add-event-form");
const titleInput = document.getElementById("event-title-input");
const contentInput = document.getElementById("event-content");
const yearInput = document.getElementById("year-input");
const monthInput = document.getElementById("month-input");
const dayInput = document.getElementById("day-input");
const timeInput = document.getElementById("event-time");
const eventidInput = document.getElementById("eventid");
const shareFormSubmit = document.getElementById("share-event-form");
const shareUserInput = document.getElementById("sharename");
const categoryInput = document.getElementById("event-category");

var my_date = new Date();
var my_year = my_date.getFullYear();
var my_month = my_date.getMonth();
var my_day = my_date.getDate();
refreshDate();

// adding event handlers
document.getElementById("loginBtn").addEventListener("click", login, false);
document.getElementById("registerBtn").addEventListener("click", register, false);
document.getElementById("logoutBtn").addEventListener("click", logout, false);
prev.addEventListener("click", prevClicked);
next.addEventListener("click", nextClicked);
addEventButton.addEventListener("click", showAddEventModal);
shareFormSubmit.addEventListener("submit", submitShareEvent);


// User Log In
async function login(){
    let inputUsername = document.getElementById("username").value;
    let inputPassword = document.getElementById("password").value;

    // Make a URL-encoded string for passing POST data:
    const data = { 'username': inputUsername, 'password': inputPassword };

    let res = await fetch("login.php", {
            method: 'POST',
            body: JSON.stringify(data),
            headers: { 'content-type': 'application/json' }
        })
        .then(response => response.json())
        .catch(err => console.error(err));

    if(res.success){
            sessionStorage.setItem("token",res.token);
            alert("Hello, " + inputUsername + "!");
            // hide login frame
            initCheck();
    }
}

// User Register
async function register(){
    let newUser = document.getElementById("username").value;
    let newPassword = document.getElementById("password").value;

    const data = { 'username': newUser, 'password': newPassword };

    let res = await fetch("register.php", {
            method: 'POST',
            body: JSON.stringify(data),
            headers: { 'content-type': 'application/json' }
        })
        .then(response => response.json())
        .catch(err => console.error(err));

    if(res.success)
    {
        document.getElementById("username").value = "";
        document.getElementById("password").value = "";
        alert("Hi " + newUser + ", your account has been successfully created!");
    } else 
    {
        alert("Duplicate Username!");
    }
}

async function logout(){
    let res = await fetch("logout.php", {
        method: 'GET',
        headers: { 'content-type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if(data['success']==true){
            initCheck();
            var todoTbody = document.getElementById("todo-list-items");
            todoTbody.innerHTML = ""; 
            var doneTbody = document.getElementById("done-list-items");
            doneTbody.innerHTML = ""; 
        }
        })
    .catch(err => console.error(err));
}

function initCheck(){
    // hide add event modal
    const modal = document.getElementById("add-event-modal");
    modal.style.display = "none";
    let res = fetch("loginStatus.php", {
        method: 'GET',
        headers: { 'content-type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
            if(data['status']==true){
                var usernameDiv = document.getElementById("usernameDisplay");
                updateLoginFrame("hide");
                usernameDiv.textContent = data['username'];
                getEvents(my_day,my_year,my_month);
            }else{
                updateLoginFrame("display");
            }
        })
    .catch(err => console.error(err));
}

function updateLoginFrame(e){
    var loginDiv = document.getElementById("login");
    var logoutDiv = document.getElementById("logout");
    var usernameDiv = document.getElementById("usernameDisplay");
    if(e=="hide"){	
        logoutDiv.style.display = "block";
        loginDiv.style.display = "none";
        usernameDiv.style.display = "block";
    }
    if(e=="display"){
        logoutDiv.style.display = "none";
        loginDiv.style.display = "block";
        usernameDiv.style.display = "none";

    }			
}

function dayStart(month, year) {
    var tmpDate = new Date(year, month, 1);
    return (tmpDate.getDay());
}

function daysMonth(month, year) {
    var tmp = year % 4;
    if (tmp == 0) {
        return (month_olympic[month]);
    } else {
        return (month_normal[month]);
    }
}

function refreshDate(){
    var str = "";
    var totalDay = daysMonth(my_month, my_year); 
    var firstDay = dayStart(my_month, my_year); 
    var myclass;
    for(var i=1; i<firstDay; i++){ 
        str += "<li></li>"; 
    }
    for(var i=1; i<=totalDay; i++){
        if (i==my_day && my_year==my_date.getFullYear() && my_month==my_date.getMonth()){
            myclass = " class='green greenbox listener'"; 
        }else{
            myclass = " class='darkgrey listener'"; 
        }
        str += "<li"+myclass+"><a href='' id='"+i+"'>"+i+"</a></li>";
    }
    holder.innerHTML = str; 
    ctitle.innerHTML = month_name[my_month]; 
    cyear.innerHTML = my_year; 
    // eventlisteners for all days
    var days = document.querySelectorAll(".listener");
    for (var i = 0; i < days.length; i++) {
        days[i].addEventListener("click", function(e) {
            e.preventDefault();
            getEvents(this.querySelector("a").getAttribute("id"), my_year, my_month);
        });
    }
}

function prevClicked(e){
    e.preventDefault();
    my_month--;
    if(my_month<0){
        my_year--;
        my_month = 11;
    }
    refreshDate();
} 
function nextClicked(e){
    e.preventDefault();
    my_month++;
    if(my_month>11){
        my_year++;
        my_month = 0;
    }
    refreshDate();
}

// get events from database
async function getEvents(day, year, month){
    // store date to modal input
    titleInput.value = "";
    contentInput.value = "";
    categoryInput.value = "";
    yearInput.value = year;
    monthInput.value = month + 1;
    dayInput.value = day;

    const myMonth = month + 1;
    var getUsername = usernameDisplay.textContent;


    var usernameDiv = document.getElementById("usernameDisplay");
    // get events from database
    await fetch("getEvent.php", {
        method: 'POST',
        body: JSON.stringify({
            date: year+"-"+myMonth+"-"+day,
            // userid
            username: getUsername
        }),
        headers: {
            'Content-Type': 'application/json'
        }
        })
        .then(response => response.json())
           .then(data => {
            const todoCode = data.todoHtmlCode;
            const todoTbody = document.getElementById("todo-list-items");
            todoTbody.innerHTML = todoCode;

            const doneCode = data.doneHtmlCode;
            const doneTbody = document.getElementById("done-list-items");
            doneTbody.innerHTML = doneCode;
            // eventlisteners for all delete butons
            var deletes = document.querySelectorAll(".delete-button");
            for (var i = 0; i < deletes.length; i++) {
                deletes[i].addEventListener("click", function(e) {
                    e.preventDefault();
                    deleteEvents(this.getAttribute("data-id"));
                });
            }
            // eventlisteners for all edit buttons
            var edits = document.querySelectorAll(".edit-button");
            for (var i = 0; i < deletes.length; i++) {
                edits[i].addEventListener("click", function(e) {
                    e.preventDefault();
                    editEvents(this.getAttribute("data-id"));
                });
            }
            // eventlisteners for all radio 
            var radios = document.querySelectorAll(".radio");
            for (var i = 0; i < deletes.length; i++) {
                radios[i].addEventListener("click", function(e) {
                    e.preventDefault();
                    doneEvents(this.getAttribute("value"));
                });
            }
        })
        .catch(err => console.error(err));
}


function showAddEventModal() {
    closeButton.addEventListener("click", function() {
        modal.style.display = "none";
    });
    let res = fetch("loginStatus.php", {
        method: 'GET',
        headers: { 'content-type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
            if(data['status']==true){
                form.removeEventListener("submit", submitEditEvent);
                form.addEventListener("submit", submitAddEvent);
                modal.style.display = "block";
            }else{
                alert("Please log in before adding events!")
            }
        })
    .catch(err => console.error(err));
}

function showEditEventModal(id) {
    closeButton.addEventListener("click", function() {
        modal.style.display = "none";
    });
    let res = fetch("loginStatus.php", {
        method: 'GET',
        headers: { 'content-type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
            if(data['status']==true){
                form.removeEventListener("submit", submitAddEvent);
                form.addEventListener("submit", submitEditEvent);
                modal.style.display = "block";
                eventidInput.value = id;
            }else{
                alert("Please log in before adding events!")
            }
        })
    .catch(err => console.error(err));
}

function submitAddEvent(e){
    e.preventDefault();
    var getUsername = usernameDisplay.textContent;
    var selectedTime = timeInput.value;
    const postData = {
        title: titleInput.value,
        content: contentInput.value,
        year: yearInput.value,
        month: monthInput.value,
        day: dayInput.value,
        username: getUsername,
        time: selectedTime,
        category: categoryInput.value,
        token: sessionStorage.getItem("token")
    };
    // console.log(postData);
    fetch("addEvent.php", {
        method: "POST",
        body: JSON.stringify(postData),
        headers: { "Content-Type": "application/json" }
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
            if(data['result']=="success"){
                alert("Add event successfully!");
                modal.style.display = "none";
                // refresh eventlist
                getEvents(postData.day,postData.year,postData.month-1);
            }else{
                alert("Fail to add event!");
            }
        })
        .catch(err => console.error(err));
}

function submitEditEvent(e){
    e.preventDefault();
    var getUsername = usernameDisplay.textContent;
    var selectedTime = timeInput.value;
    const postData = {
        title: titleInput.value,
        content: contentInput.value,
        year: yearInput.value,
        month: monthInput.value,
        day: dayInput.value,
        username: getUsername,
        time: selectedTime,
        eventId: eventidInput.value,
        category: categoryInput.value,
        token: sessionStorage.getItem("token")
    };
    fetch("editEvent.php", {
        method: "POST",
        body: JSON.stringify(postData),
        headers: { "Content-Type": "application/json" }
    })
    .then(response => response.json())
    .then(data => {
            if(data['result']=="success"){
                console.log(data);
                alert("Edit event successfully!");
                modal.style.display = "none";
                // refresh eventlist
                getEvents(postData.day,postData.year,postData.month-1);
            }else{
                alert("Fail to add event!");
            }
        })
        .catch(err => console.error(err));
}

// delete events
async function deleteEvents(id){
    // delete event from database
    var getUsername = usernameDisplay.textContent;
    await fetch("deleteEvent.php", {
        method: 'POST',
        body: JSON.stringify({
            eventId: id,
            username: getUsername
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data['result']=="success"){
            alert("Delete event successfully!");
            // refresh eventlist
            getEvents(dayInput.value,yearInput.value,monthInput.value-1);
        }else{
            alert("Fail to add event!");
        }
    })
    .catch(err => console.error(err));
}
// edit events
async function editEvents(id){
    // show edit modal
    showEditEventModal(id);
}

function submitShareEvent(e){
    e.preventDefault();
    var sharename = shareUserInput.value;
    var getUsername = usernameDisplay.textContent;
    fetch("shareEvent.php", {
        method: 'POST',
        body: JSON.stringify({
            name: sharename,
            username: getUsername
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data['result']=="success"){
            alert("Share events successfully!");
            // refresh eventlist
            getEvents(dayInput.value,yearInput.value,monthInput.value-1);
        }else if(data['result']=="fail"){
            alert("Invaild Username!");
        }else{
            alert("Username not in Database!");
        }
    })
    .catch(err => console.error(err));
}

function doneEvents(id){
    // delete event from database
    var getUsername = usernameDisplay.textContent;
    fetch("changeStatus.php", {
        method: 'POST',
        body: JSON.stringify({
            eventId: id,
            username: getUsername
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data['result']=="success"){
            alert("Change event status successfully!");
            // refresh eventlist
            getEvents(dayInput.value,yearInput.value,monthInput.value-1);
        }else{
            alert("Fail to add event!");
        }
    })
    .catch(err => console.error(err));
}