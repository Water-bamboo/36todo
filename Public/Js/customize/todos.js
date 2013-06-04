//在页面加载时候，就使td节点具有click点击能力
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
//td的点击事件
function content_dbClick(){
    //将td的文本内容保存
    var content = $(this);
	var td = content.parent("div");
    var tdText = content.text();
    //将td的内容清空
    content.remove();
    //新建一个输入框
    var textarea = $("<textarea></textarea>");
    //将保存的文本内容赋值给输入框
    textarea.text(tdText);
    //将输入框添加到td中
    td.append(textarea);
    //给输入框注册事件，当失去焦点时就可以将文本保存起来
    textarea.blur(function(){
        //将输入框的文本保存
        var textarea = $(this);
		var td = textarea.parent("div");
        var id = td.attr("id");
		
		var inputText = textarea.val();
		
		var content = $("<p class='content'></p>");
		//将td的内容，即输入框去掉,然后给td赋值
		content.text(inputText);
		td.append(content);
		textarea.remove();
		
        //让td重新拥有点击事件
        content.dblclick(content_dbClick);

		$.post("/36todo/index.php/Todo/updateajax/",
		    {
				'body':inputText,
				'id':id,
				'title':'title'
			},
			function(data) {
				if(data.result==1) {
				   //如果上传失败怎么办，不能让用户丢了数据。
				   //alert("1");
				}
				else {
				   //alert(data.result);
				}
			});
    });
    //将输入框中的文本高亮选中
    //将jquery对象转化为DOM对象
    //var inputDom = textarea.get(0);
    //inputDom.select();
    //将td的点击事件移除
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
			   //如果上传失败怎么办，不能让用户丢了数据。
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
			//理论上直接设置属性颜色即可
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
