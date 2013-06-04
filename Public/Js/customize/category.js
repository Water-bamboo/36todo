//在页面加载时候，就使td节点具有click点击能力
$(document).ready(function(){
    
	/*rename category*/
		$("#dialog" ).dialog({ autoOpen: false });
	$( "#dialog" ).dialog( "option", "height", 100 );
	$("#opener").click(function() {
		$("#dialog").dialog( "open" );
	});
});
