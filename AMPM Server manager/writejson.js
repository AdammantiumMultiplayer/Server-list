var obj = {
    serverconf: []
 };
 
 
 function writejson() {
 var portconf = document.getElementById("portconf").value; 
 var nameconf = document.getElementById("nameconf").value;
 var maxplayersconf = document.getElementById("maxplayersconf").value;
 var pvpmpconf = document.getElementById("pvpconf").value;
 var Epvp = document.getElementById("Epvp").value;



 obj.serverconf.push({portconf, nameconf, maxplayersconf, pvpmpconf, Epvp});
 var json = JSON.stringify(obj);
 var fs = require('fs');
 fs.writeFile('server.config.json', json, 'utf8', callback);
 print("wrote json")
}