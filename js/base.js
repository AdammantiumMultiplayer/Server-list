$("#search_name").keyup(search);
$("#search_map").keyup(search);
$("#search_ip").keyup(search);

function search() {
    filter_name = $("#search_name").val().toLowerCase();
    filter_map  = $("#search_map").val().toLowerCase();
    filter_ip	= $("#search_ip").val().toLowerCase();
    
    $("#serverlist tr").filter(function() {
        var columns = $(this).find("td");
        if(columns.length == 0) return;
        
        $(this).toggle(	(filter_name.length > 0 && columns[1].innerText.toLowerCase().indexOf(filter_name) > -1)
                      ||(filter_map.length  > 0 && columns[3].innerText.toLowerCase().indexOf(filter_map)  > -1)
                      ||(filter_ip.length   > 0 && columns[5].innerText.toLowerCase().indexOf(filter_ip)   > -1)
                      ||(filter_name.length == 0 && filter_ip.length == 0 && filter_map.length == 0)
                      );
    });
}

function joinRow(row) {
   var address = $(row).closest("tr").find("td")[5].innerText;
   
   var ip = address.split(':')[0];
   var port = parseInt(address.split(':')[1]);
   
   doJoin(ip, port);
}

function infoRow(row) {
    var address = $(row).closest("tr").find("td")[5].innerText;

    var ip = address.split(':')[0];
    var port = parseInt(address.split(':')[1]);

    var servername = $($(row).closest("tr").find("td")[1]).find("span")[0].innerText;
	
	var requirePassword = servername.endsWith(" 🔒");
	
    var map = $(row).closest("tr").find("td")[3].innerText;
    var mode;
    if(map.length > 0) {
        var splits = map.split(" @ ");
        map = splits[1];
        mode = splits[0].replace("\n", "");
    }

    showDetails(servername, ip, port, requirePassword, map, mode);
}

if(findGetParameter("key")) {
    var key = atob(findGetParameter("key"));
    
    var ip = key.split(":")[0];
    var port = key.split(":")[1];
    
    var requirePassword = findGetParameter("require_password");

	var name = findGetParameter("name");
	
	if(requirePassword == "1") {
		name += " 🔒";
	}
    
	$("#serverlist tbody").prepend(`
	<tr class="server">
		<td><img src="/img/discord.jpg"></td>
		<td><span>` + name + `</span><span class="description">Shared server hosted by the discord bot.<br>This is only visible to you.</span></td>
		<td>
			PvP: <span style="color: green; font-weight: bold;">&#9745;</span><br>
			Map changable: <span style="color: green; font-weight: bold;">&#9745;</span>
		</td>
		<td>
		<object class="map-preview" data="/img/maps/discord.jpg" type="image/png">
			<img src="/img/AMP.jpg" alt="Arena">
		</object>
		<br>
		
		</td>
		<td></td>
		<td>` + ip + `:` + port + `</td>
		<td>
		<button onclick="joinRow(this)" class="btn green">Join</button>
		</td>
	</tr>
	`);
	
	setTimeout(function() {
		$("#serverlist tr:nth-child(3) td:first").click();
	}, 100);
	
	//showDetails(findGetParameter("name"), ip, port, requirePassword && requirePassword == 1, "", "");
}

function showDetails(servername, ip, port, require_password, map, mode) {
    $("#details").show();

    if(require_password) {
        $("#join_password").parent().show();
    }else{
        $("#join_password").parent().hide();
    }
    
    $("#join_address").text(ip);
    $("#join_port").text(port);
    $("#join_name").text(servername);
    
    $('#join-invite').unbind('click');
    $("#join-invite").on("click", function() {
        var password = $("#join_password input").val();
        
        doJoin(ip, port, password);
    });

    if(map && mode && map.length > 0 && mode.length > 0) {
        $("#map_info").show();
        $("#map_info span").text(mode + " @ " + map);
        $("#map_info object").attr("data", "/img/maps/" + map.toLowerCase() + ".jpg");
    }else{
        $("#map_info").hide();
    }
}

function closeDetails() {
    $("#details").hide();
}

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
          tmp = item.split("=");
          if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

function showMessage(message, type = "primary") {
    $("body .alert").remove();

    $("body").append(`
            <div class="alert alert-` + type + `">
                ` + message + `
                <button type="button" class="close" aria-label="Close" onclick="dismissMessage(this);">
                    <span>&times;</span>
                </button>
            </div>`);
}

function dismissMessage(element) {
    $(element).parent().remove();
}



$("tr.server").find("td:not(:last)").click(function() {
    infoRow(this);
});