//��ҳ�����ʱ�򣬾�ʹtd�ڵ����click�������
$(document).ready(function(){
    
	/*rename category*/
		$("#dialog" ).dialog({ autoOpen: false });
	$( "#dialog" ).dialog( "option", "height", 100 );
	$("#opener").click(function() {
		$("#dialog").dialog( "open" );
	});
});
