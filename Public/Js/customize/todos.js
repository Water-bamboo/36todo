//��ҳ�����ʱ�򣬾�ʹtd�ڵ����click�������
$(document).ready(function(){
    $("p.content").dblclick(content_dbClick);
	$("#add").click(addClick);
	var test = $("a.set_ready");
	test.click(ajaxDoneClick);
	$("input[value=tuijian]").click(showDoneCheckBoxClick);
	$(".edit_content").click(editClick);
	//date time part
	var timepicker_remind = $(".remind_time");
	timepicker_remind.datetimepicker({
		dateFormat: "yy/mm/dd",
		onClose: function(dateText, inst) {
			var time_input_value = $(this).attr("name");
			$.post("/36todo/index.php/Todo/updateremindetime/",
				{
					'time':dateText,
					'id':time_input_value
				},
				function(data) {
					/*alert(data.result);	
					alert(data.info);*/
					
					if (data.result == 1) {
						//alert(data.info);		
					}
					else {
						alert(data.info);
					}					
				});
		}
	});
	
	/*new category*/
	$("#dialog" ).dialog({ autoOpen: false });
	$( "#dialog" ).dialog( "option", "height", 100 );
	$("#opener").click(function() {
		$("#dialog").dialog( "open" );
	});

	//add css
	$.ajax({
	type: "GET",
	url: "/36todo/index.php/Todo/get_timeup_notification/",
	dataType: "json",
	success: function(msg) {
		//alert(msg.length);
		$("div strong").html(msg.length);
		$.each(msg, function(key, value) {
			$("div#"+value.id).addClass('timeup');//
		});
	}});
});
//td�ĵ���¼�
function content_dbClick(){
    //��td���ı����ݱ���
    var content = $(this);
	var td = content.parent("div");
    var tdText = content.text();
    //��td���������
    content.remove();
    //�½�һ�������
    var textarea = $("<textarea></textarea>");
    //��������ı����ݸ�ֵ�������
    textarea.text(tdText);
    //���������ӵ�td��
    td.append(textarea);
    //�������ע���¼�����ʧȥ����ʱ�Ϳ��Խ��ı���������
    textarea.blur(function(){
        //���������ı�����
        var textarea = $(this);
		var td = textarea.parent("div");
        var id = td.attr("id");
		
		var inputText = textarea.val();
		
		var content = $("<p class='content'></p>");
		//��td�����ݣ��������ȥ��,Ȼ���td��ֵ
		content.text(inputText);
		td.append(content);
		textarea.remove();
		
        //��td����ӵ�е���¼�
        content.dblclick(content_dbClick);

		$.post("/36todo/index.php/Todo/updateajax/",
		    {
				'body':inputText,
				'id':id,
				'title':'title'
			},
			function(data) {
				if(data.result==1) {
				   //����ϴ�ʧ����ô�죬�������û��������ݡ�
				   //alert("1");
				}
				else {
				   //alert(data.result);
				}
			});
    });
    //��������е��ı�����ѡ��
    //��jquery����ת��ΪDOM����
    //var inputDom = textarea.get(0);
    //inputDom.select();
    //��td�ĵ���¼��Ƴ�
	textarea.focus();
    content.unbind("dblclick");
}
	
function addClick(){
	var add= $(this);
	var add_textarea = $("#add_area");
	if (add_textarea.length > 0) {
		if (add_textarea.val() == "") {
			add_textarea.remove();
			return;
		}
		$.post("/36todo/index.php/Todo/insertajax/",
		{
			'body':add_textarea.val(),
			'title':''
		},
		function(data) {
			if(data.id > 0) {
			   //����ϴ�ʧ����ô�죬�������û��������ݡ�
			   var add_textarea = $("#add_area");
			   var newcontent = add_textarea.val();
			   add_textarea.remove();
			   //alert(newcontent);
			   //
			   location.href = "/36todo/index.php/Todo/";
			   //$("#tbody").append("<tr><td class='width700 height20'><p class='content'>"+newcontent+"</p><input type='hidden' name='id' value='"+data.id+"'></td></tr>");
			}
			else {
			   //alert(data.result);
			}
		});
	    return;
	}
	
	var obj_newcontent = $("<textarea id='add_area' name='body'/><br />");
	obj_newcontent.insertBefore(add);
}

function ajaxDoneClick(event) {
	event.preventDefault();
	var a_done = $(this);
	var href = a_done.attr("href");
	$.get(href, function(data) {
		//alert(data.result);
		if (data.result == 1) {
			location.href="/36todo/index.php/Todo/";
			//������ֱ������������ɫ����
		}
	});
}

function showDoneCheckBoxClick(event) {
	var checkbox_done = $(this);
	var ischecked = checkbox_done.attr("checked");
	if (ischecked) {
		location.href="/36todo/index.php/Todo/index/r/1";
	}
	else {
		location.href="/36todo/index.php/Todo/index/r/0";
	}
}

function editClick(event) {
	event.preventDefault();
	var hrefvalue=$(this).attr('href');
	var p = $("div#"+hrefvalue).children("p");
	p.trigger("dblclick");
}
