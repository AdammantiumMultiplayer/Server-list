var error = false;
var connected = false;
const wsUri = "ws://127.0.0.1:13698/";
var websocket;

var gameDetails = {
    map: "",
    mode: "",
    server: "Playing Singleplayer"
}

function connectToMod() {
    $(".modstatus .message").text("Connecting...");
    $(".modstatus").removeClass("success");
    $(".modstatus").removeClass("error");
    
    error = false;
    connected = false;
    websocket = new WebSocket(wsUri);
    
    websocket.onopen = (e) => {
        connected = true;
        console.log("CONNECTED");
        $(".modstatus .message").text("MOD RUNNING");
        $(".modstatus").removeClass("error");
        $(".modstatus").addClass("success");
    };

    websocket.onclose = (e) => {
        connected = false;
        console.log("DISCONNECTED");
        if(error) {
            $(".modstatus .message").html("COULD NOT CONNECT TO MOD<h5 style='margin-block: 0px;'>Make sure the mod is running or<br>try to disable your AdBlock</h5>");
            $(".modstatus").removeClass("success");
            $(".modstatus").addClass("error");
        }else{
            $(".modstatus .message").text("DISCONNECTED");
            $(".modstatus").removeClass("success");
            $(".modstatus").addClass("error");
        }
        UpdateConnectionDetails();
    };

    websocket.onmessage = (e) => {
        console.log("RESPONSE: " + e.data);
        
        var splits = e.data.split('|');
        
        switch(splits[0]) {
            case "ERROR":
                showMessage(splits[1], "danger");
                break;
            case "INFO":
                showMessage(splits[1]);
                break;
            case "MAPINFO":
                gameDetails.map = splits[1];
                gameDetails.mode = splits[2];

                UpdateConnectionDetails();
                break;
            case "CLEARSERVER":
                gameDetails.server = "Playing Singleplayer";
                UpdateConnectionDetails();
                break;
            case "SERVER":
                gameDetails.server = splits[1];
                UpdateConnectionDetails();
                break;
        }
    };

    websocket.onerror = (e) => {
        console.log("ERROR: " + e.data);
        error = true;
    };
}

function doSend(message) {
    console.log("SENT: " + message);
    websocket.send(message);
}

function doJoin(ip, port, password) {
    if(connected) {
        var message = "join:" + ip + ":" + port + (password ? ":" + password : "");
        console.log(message);
        doSend(message);
    }else{
        alert("Couldn't find the mod running, try refreshing the page.");
    }
}

function UpdateConnectionDetails() {
    if(connected && gameDetails.map && gameDetails.map.length > 0) {
        $(".modstatus .session-details").show();
        
        $(".modstatus .session-details span").html(gameDetails.mode + " @ " + gameDetails.map + (gameDetails.server.length > 0 ? "<br>" + gameDetails.server : ""));
        $(".modstatus .session-details object").attr("data", "/img/maps/" + gameDetails.map.toLowerCase() + ".jpg");
    }else{
        $(".modstatus .session-details").hide();
    }
}

connectToMod();