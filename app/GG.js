var version=201704192055;//版本号
var electron = require('electron');
var fs = require('fs');
var http = require('http') ;
var shell = electron.shell;

//----------------------------------------------
var date = new Date(),h=date.getHours(), m=date.getMinutes(),s=date.getSeconds(),hms=h+':'+m+':'+s;
//----------------------------------------
  window.addEventListener('online', function(){});//监听上线
  window.addEventListener('offline', function(){});//监听离线
//----------------------------------------自定义function
function openurl(url){shell.openExternal(url);}
//--------------------------------------------JQ start
$(document).ready(function(){
//--------------------------------------------
	$("#loadfile").click(function(){//最新hosts
		$.ajax({
			type: "GET",
			url:"http://42.51.158.129/hosts/hosts.php",
			success:function(data){ $("#content").val(data);},
		    error:function(xhr){alert("错误提示： " + xhr.status + " " + xhr.statusText);}
		});
});
//--------------------------------------------
	$("#now_hosts").click(function(){//当前hosts
	
	fs.readFile('C:\\Windows\\System32\\drivers\\etc\\hosts','utf8',function(err,data){
	 if(err){
      $("#content").val(err);
 }else{
  $("#content").val(data);
    }
	});
});
//--------------------------------------------
	$("#app").click(function(){//替换hosts
	fs.writeFile('C:\\Windows\\System32\\drivers\\etc\\hosts', $("#content").val(), function (err){
  if (err) {alert("应用hosts失败:"+err);}else{
  alert('应用hosts成功');
  }
});
	
});
//--------------------------------------------
$('#debug').append("<hr/><b style='color:#00CC00;'>["+hms+"]→程序加载完毕!</b>")

});