$("button").click(function(){
  $.get("https://bns.devforce.de/bns.txt", function(data, status){
    alert("Data: " + data + "\nStatus: " + status);
  });
});