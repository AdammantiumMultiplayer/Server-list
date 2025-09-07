$("#search_name").keyup(search);
$("#search_map").keyup(search);
$("#search_ip").keyup(search);

function search() {
    filter_name = $("#search_name").val().toLowerCase();
    filter_map  = $("#search_map").val().toLowerCase();
    //filter_ip	= $("#search_ip").val().toLowerCase();
    
    $("#serverlist tr").filter(function() {
        var columns = $(this).find("td");
        if(columns.length == 0) return;
        
        $(this).toggle(	(filter_name.length > 0 && columns[1].innerText.toLowerCase().indexOf(filter_name) > -1)
                      ||(filter_map.length  > 0 && columns[3].innerText.toLowerCase().indexOf(filter_map)  > -1)
                      //||(filter_ip.length   > 0 && columns[5].innerText.toLowerCase().indexOf(filter_ip)   > -1)
                      ||(filter_name.length == 0 && /*filter_ip.length == 0 &&*/ filter_map.length == 0)
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
    $("#serverlist tr.active").removeClass("active");

    $(row).closest("tr").addClass("active");

    var address = $(row).closest("tr").find("td")[5].innerText;

    var ip = address.split(':')[0];
    var port = parseInt(address.split(':')[1]);

    var servername = $($(row).closest("tr").find("td")[1]).find("span")[0].innerText;
	
    var description = $($(row).closest("tr").find("td")[1]).find("span.description")[0].innerText;

	var requirePassword = servername.endsWith(" 🔒");
	var official = $($(row).closest("tr").find("td")[1]).find("span.tick").length == 1;

    var players = $($(row).closest("tr").find("td")[4])[0].innerText;

    var map = $(row).closest("tr").find("td")[3].innerText;

    if(requirePassword) {
        $('#join-btn').text("🔒 Join 🔒");
    }else{
        $('#join-btn').text("Join");
    }
    
    $('#join-btn').unbind('click');
    $("#join-btn").on("click", function() {
        //var password = $("#join_password input").val();
        var password = undefined;
        if(requirePassword) {
            password = prompt("Password:");
            if(password == null) return;
        }

        doJoin(ip, port, password);
    });

    $(".serverinfo .map-image").empty();
    $(".serverinfo .map-image").append("data", $($(row).closest("tr")).find(".map-preview").clone());
    $(".serverinfo .map-image").append("data", $($(row).closest("tr")).find(".gamemode-preview").clone());

    $(".serverinfo .address").text(ip);
    $(".serverinfo .port").text(port);
    $(".serverinfo .name").text(servername);
    $(".serverinfo .description").text(description);
    $(".serverinfo .info").html($($(row).closest("tr").find("td")[2]).html());
    $(".serverinfo .gamemode").text(map);

    if(official) {
        $(".serverinfo .name").css("color", "#0c8fb9");
    } else {
        $(".serverinfo .name").css("color", "");
    }

    /*
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
    */
    //showDetails(servername, ip, port, requirePassword, map, mode);
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
		<td><span>` + name + `</span><span class="description">Shared server hosted by the discord bot.</span><span class='note'>This is only visible to you.</span></td>
		<td style="display: none;">
            PvP: <span style="color: green; font-weight: bold;">☑</span>
            <br>
            Map changable: <span style="color: green; font-weight: bold;">☑</span>
        </td>
		<td>
            <div class='map-image'>
                <object class="map-preview" data="/img/maps/discord.jpg" type="image/png">
                    <img src="/img/AMP.jpg" alt="Arena">
                </object>
            </div>
		</td>
		<td>?/?</td>
		<td style="display: none;">` + ip + `:` + port + `</td>
	</tr>
	`);
	
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

function decode_boolean(str) {
    if(str == "true") {
        return "<span style='color: green; font-weight: bold;'>&#9745;</span>";
    }else{
        return "<span style='color: red; font-weight: bold;'>&#9746;</span>";
    }
}

// Assign stagger values to each server row for CSS animation
function applyRowStagger() {
    $("#serverlist tr.server").each(function(i){
        this.style.setProperty('--stagger', i.toString());
        // Add animation class after a microtask so initial paint is visible
        const row = this;
        setTimeout(function(){ row.classList.add('animate-in'); }, 0);
    });
}

$("tr.server").find("td:not(:last)").on('click', function() {
    // add a small refresh animation to side panel
    var panel = document.querySelector('.serverinfo');
    if(panel) {
        panel.classList.remove('refreshing');
        // force reflow
        void panel.offsetWidth;
        panel.classList.add('refreshing');
    }
    infoRow(this);
});

document.addEventListener('DOMContentLoaded', function(){
    applyRowStagger();
    // Mark body loaded to trigger CSS entrance animations
    document.body.classList.add('loaded'); // still used for title underline but no longer hides rows
    // Auto-select first row after slight delay so animation can begin
    setTimeout(function() {
        $("#serverlist tr.server:first td:first").trigger('click');
    }, 180);
});