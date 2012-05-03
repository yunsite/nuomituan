$(document).ready(function(){
	$(document).pngFix();
	//绑定城市下拉
	$("#guides-city-change").click(function(){
		if($("#guides-city-list").css("display") == "none")
		{
			$("#guides-city-list").show();
			$("body").one("click", function(){
				$("#guides-city-list").hide();
			}); 
		}
		else
			$("#guides-city-list").hide();
		return false;
	});
	
	/* 会员菜单的事件 */
	$("#myaccount").hover(function(){
		$(this).addClass("hover");
		$("#myaccount-menu").show();		   
	},function(){
		var menuHide = function(){
			$("#myaccount").removeClass("hover");
			$("#myaccount-menu").hide();
		};
		userMenuTimeOut = setTimeout(menuHide,100);
	});

	$("#myaccount-menu").hover(function(){
		clearTimeout(userMenuTimeOut);
		$("#myaccount").addClass("hover");   
	},function(){
		$("#myaccount").removeClass("hover");
		$(this).hide();
	});


	//关于订单购物车提交按钮的事件
	$("#order_done").click(function(){
		submit_buy();
	});
	
	$('#share-copy-button').click(function(){
			$.copyText('#share-copy-text');
	});		
	
	$('#submit-mail-image,#tip-submit-deal-mail').click(function(){	
		submit_mail($(this));
	});
	
	$('#verify_ecv').bind("click",function(){
		var ecvsn = $(this).parent().find("input[name='ecvsn']").val();
		var ecvpassword = $(this).parent().find("input[name='ecvpassword']").val();
		var ajaxurl = APP_ROOT+"/ajax.php?act=verify_ecv&ecvsn="+ecvsn+"&ecvpassword="+ecvpassword;
		$.ajax({ 
			url: ajaxurl,
			success: function(text){
				alert(text);
			},
			error:function(ajaxobj)
			{
				if(ajaxobj.responseText!='')
				alert(ajaxobj.responseText);
			}
		});
	});

});



		


/*定义错误的输出提示*/
var errHideTimeOut;
$(window).scroll(function(){
	if($("#sysmsg-error") != "none" || $("#sysmsg-success") != "none")
	{
		var top = $.getBodyScrollTop();
		if(top < 157)
			top = 157;
		$("#sysmsg-error-box").stop();
		$("#sysmsg-error-box").animate({"top":top},{duration:300}); 
	}
});

$.showErr = function(str)
{
	var top = $.getBodyScrollTop();
	if(top < 157)
		top = 157;
	$("#sysmsg-error-box").css({"top":top});
	$("#sysmsg-error span:first").html(str);
	$("#sysmsg-error").show();
	$("#sysmsg-success").hide();
	$("#sysmsg-error-box").show();
	
	clearTimeout(errHideTimeOut);
	
	var hideErr = function(){
		$("#sysmsg-error-box").slideUp(300);
		$(".deal_cart_row").removeClass("cart_warn");
	};
	
	errHideTimeOut = setTimeout(hideErr,5000);
	
	$("#sysmsg-error-box .close").one("click", function(){
		$("#sysmsg-error-box").hide();
		$(".deal_cart_row").removeClass("cart_warn");
	});
}

var successHideTimeOut;
$.showSuccess = function(str)
{
	var top = $.getBodyScrollTop();
	if(top < 157)
		top = 157;
	$("#sysmsg-error-box").css({"top":top});
	$("#sysmsg-success span:first").html(str);
	$("#sysmsg-success").show();
	$("#sysmsg-error").hide();
	$("#sysmsg-error-box").show();
	
	clearTimeout(successHideTimeOut);
	
	var hideSuccess = function(){
		$("#sysmsg-error-box").slideUp(300);
	};
	
	successHideTimeOut = setTimeout(hideSuccess,5000);
	
	$("#sysmsg-error-box .close").one("click", function(){
		$("#sysmsg-error-box").hide();
	});
}

$.getBodyScrollTop=function(){
    var scrollPos; 
    if (typeof window.pageYOffset != 'undefined') { 
        scrollPos = window.pageYOffset; 
    } 
    else if (typeof document.compatMode != 'undefined' && 
        document.compatMode != 'BackCompat') { 
        scrollPos = document.documentElement.scrollTop; 
    } 
    else if (typeof document.body != 'undefined') { 
        scrollPos = document.body.scrollTop; 
    } 
    return scrollPos;
}

/*end 错误输出提示*/

/*验证*/
$.minLength = function(value, length , isByte) {
	var strLength = $.trim(value).length;
	if(isByte)
		strLength = $.getStringLength(value);
		
	return strLength >= length;
};

$.maxLength = function(value, length , isByte) {
	var strLength = $.trim(value).length;
	if(isByte)
		strLength = $.getStringLength(value);
		
	return strLength <= length;
};
$.getStringLength=function(str)
{
	str = $.trim(str);
	
	if(str=="")
		return 0; 
		
	var length=0; 
	for(var i=0;i <str.length;i++) 
	{ 
		if(str.charCodeAt(i)>255)
			length+=2; 
		else
			length++; 
	}
	
	return length;
}

$.checkMobilePhone = function(value){
	if($.trim(value)!='')
		return /^(13\d{9}|14\d{9}|18\d{9}|15\d{9})|(0\d{9}|9\d{8})$/i.test($.trim(value));
	else
		return true;
}
$.checkEmail = function(val){
	var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/; 
	return reg.test(val);
};

/* 加入购物车 */
function add_cart(id,attr)
{
	var ajaxurl = APP_ROOT+"/cart.php?act=addcart&id="+id;
	if(attr != '')
		ajaxurl += attr;
	$.ajax({ 
		url: ajaxurl,
		dataType: "json",
		success: function(obj){
			if(obj.open_win == 1)
			{
				$.weeboxs.open(obj.html, {contentType:'text',showButton:false,title:LANG['SELECT_AND_ADDCART'],width:550});
			}
			else if(obj.open_win == 2)
			{
				$.showErr(obj.info);
			}
			else
			{
				location.href = CART_URL;
			}
		},
		error:function(ajaxobj)
		{
			if(ajaxobj.responseText!='')
			alert(ajaxobj.responseText);
		}
	});
	
}
//删除购物车
function del_cart(id)
{
	var del_url = APP_ROOT + '/Cart/delete/id/'+id;
	$.ajax({ 
		url: del_url,
		dataType: "json",
		success: function(obj){
			if(obj.status == 1)
			{
				$(".deal_"+id).remove();
				$.showSuccess("删除成功");
				computerCart();
			}
		},
		error:function(ajaxobj)
		{
			if(ajaxobj.responseText!='')
			alert(ajaxobj.responseText);
		}
	});
}
//修改购物车
function editCart(id,htmlobj)
{
	var count = $(htmlobj).val();
	var ajaxurl = APP_ROOT+"/Cart/edit/id/" + id + '/count/'+count;
	$.ajax({ 
		url: ajaxurl,
		dataType: "json",
		success: function(obj){
			if(obj.status == 1)
			{
				$.showSuccess(obj.info);
				computerCart();
			}
			else
			{
				$.showErr(obj.info);
				$(".deal_cart_row").removeClass("cart_warn");
				$(".deal_"+id).addClass("cart_warn");
			}
		},
		error:function(ajaxobj)
		{
			if(ajaxobj.responseText!='')
			alert(ajaxobj.responseText);
		}
	});	
}
//计算购物车金额
function computerCart(){
	var total = 0;
    $(".deal_cart_row").each(function(){
        var price = $(this).find(".deal-buy-price").html();
        price = parseFloat(price.substring(2));
        var count = $(this).find("#deal-buy-quantity-input").val();
        deal_total = price*count;
        total += deal_total;
        $(this).find(".deal-buy-total").html("￥" + deal_total);
    });
    $("#deal-buy-total-t").html("￥" + total);
    $("#total_pay").html("￥" + total);
    $("#should_pay").html("￥" + total);
}
//提交购物车到结算页
function submit_cart()
{
	var ajaxurl = APP_ROOT+"/cart.php?act=check&ajax=1";
	$.ajax({ 
		url: ajaxurl,
		dataType: "json",
		success: function(obj){
			if(obj.status == 1)
			{
				location.href = CART_CHECK_URL;
			}
			else
			{
				if(obj.open_win == 1)
				{
					$.weeboxs.open(obj.html, {contentType:'text',showButton:false,title:LANG['PLEASE_LOGIN_FIRST'],width:550});
				}
				else
				{
					var str = obj.info.split("|");
					var msg = str[0];
					$.showErr(msg);
					$(".deal_cart_row").removeClass("cart_warn");
					$(".deal_"+str[1]).addClass("cart_warn");
				}
			}
		},
		error:function(ajaxobj)
		{
			if(ajaxobj.responseText!='')
			alert(ajaxobj.responseText);
		}
	});		
}

//定义复制文本
$.copyText = function(id)
{
	var txt = $(id).val();
	if(window.clipboardData)
	{
		window.clipboardData.clearData();
		var judge = window.clipboardData.setData("Text", txt);
		if(judge === true)
			alert(LANG.JS_COPY_SUCCESS);
		else
			alert(LANG.JS_COPY_NOT_SUCCESS);
	}
	else if(navigator.userAgent.indexOf("Opera") != -1)
	{
		window.location = txt;
	} 
	else if (window.netscape) 
	{
		try
		{
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
		}
		catch(e)
		{
			alert(LANG.JS_NO_ALLOW);
		}
		var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
		if (!clip)
			return;
		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
		if (!trans)
			return;
		trans.addDataFlavor('text/unicode');
		var str = new Object();
		var len = new Object();
		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
		var copytext = txt;
		str.data = copytext;
		trans.setTransferData("text/unicode",str,copytext.length*2);
		var clipid = Components.interfaces.nsIClipboard;
		if (!clip)
			return false;
		clip.setData(trans,null,clipid.kGlobalClipboard);
		alert(LANG.JS_COPY_SUCCESS);
	}
};

//定义邮件订阅的js
function submit_mail(o)
{	
	var email = $(o).parent("td").parent("tr").find("input[name='email']").val();
	if(email == '')
	{
		$.showErr(LANG.EMAIL_EMPTY_TIP);
		return;
	}
	if(!$.checkEmail(email))
	{
		$.showErr(LANG.EMAIL_FORMAT_ERROR_TIP);
		return;
	}
	var ajaxurl = APP_ROOT+"/subscribe.php?act=addmail&email="+email+"&ajax=1";
	$.ajax({ 
		url: ajaxurl,
		dataType: "json",
		success: function(obj){
			if(obj.status == 1)
			{
				$.showSuccess(LANG.SUBSCRIBE_SUCCESS);
				return;
			}
			else
			{
				$.showErr(obj.info);
				return;
			}
		},
		error:function(ajaxobj)
		{
			if(ajaxobj.responseText!='')
			alert(ajaxobj.responseText);
		}
	});	
}

//关于购物结算页的相关脚本
//装载配送地区
function load_consignee(consignee_id)
{
	var ajaxurl = APP_ROOT+"/ajax.php?act=load_consignee&id="+consignee_id;
	$.ajax({ 
		url: ajaxurl,
		success: function(html){
			$("#cart_consignee").html(html);
			load_delivery();
		},
		error:function(ajaxobj)
		{
			if(ajaxobj.responseText!='')
			alert(LANG['REFRESH_TOO_FAST']);
		}
	});	
}
//载入配送方式
function load_delivery()
{
	var select_last_node = $("#cart_consignee").find("select[value!='0']");
	if(select_last_node.length>0)
	{		
		var region_id = $(select_last_node[select_last_node.length - 1]).val();
	}
	else
	{
		var region_id = 0;
	}
	
	var ajaxurl = APP_ROOT+"/ajax.php?act=load_delivery&id="+region_id;
	$.ajax({ 
		url: ajaxurl,
		success: function(html){
			$("#cart_delivery").html(html);
			count_buy_total();  //加载完配送方式重新计算总价
		},
		error:function(ajaxobj)
		{
			if(ajaxobj.responseText!='')
			alert(LANG['REFRESH_TOO_FAST']);
		}
	});	
}

//计算购物总价
function count_buy_total()
{
	$("#order_done").attr("disabled",true);
	var query = new Object();
	
	//获取配送方式
	var delivery_id = $("input[name='delivery']:checked").val();

	if(!delivery_id)
	{
		delivery_id = 0;
	}
	query.delivery_id = delivery_id;

	//配送地区
	var select_last_node = $("#cart_consignee").find("select[value!='0']");
	if(select_last_node.length>0)
	{		
		var region_id = $(select_last_node[select_last_node.length - 1]).val();
	}
	else
	{
		var region_id = 0;
	}
	query.region_id = region_id;
	
	//余额支付
	var account_money = $("input[name='account_money']").val();
	if(!account_money||$.trim(account_money)=='')
	{
		account_money = 0;
	}
	query.account_money = account_money;
	
	//全额支付
	if($("#check-all-money").attr("checked"))
	{
		query.all_account_money = 1;
	}
	else
	{
		query.all_account_money = 0;
	}
	
	//代金券
	var ecvsn = $("input[name='ecvsn']").val();
	if(!ecvsn)
	{
		ecvsn = '';
	}
	var ecvpassword = $("input[name='ecvpassword']").val();
	if(!ecvpassword)
	{
		ecvpassword = '';
	}
	query.ecvsn = ecvsn;
	query.ecvpassword = ecvpassword;
	
	//支付方式
	var payment = $("input[name='payment']:checked").val();
	if(!payment)
	{
		payment = 0;
	}
	query.payment = payment;
	
	if(!isNaN(order_id)&&order_id>0)
	var ajaxurl = APP_ROOT+"/ajax.php?act=count_order_total&id="+order_id;
	else
	var ajaxurl = APP_ROOT+"/ajax.php?act=count_buy_total";
	$.ajax({ 
		url: ajaxurl,
		data:query,
		type: "POST",
		dataType: "json",
		success: function(data){
			$("#cart_total").html(data.html);
			$("input[name='account_money']").val(data.account_money);
			if(data.pay_price == 0)
			{
				$("input[name='payment']").attr("checked",false);
			}
			$("#order_done").attr("disabled",false);
		},
		error:function(ajaxobj)
		{
			if(ajaxobj.responseText!='')
			alert(LANG['REFRESH_TOO_FAST']);
		}
	});	
}


//购物提交
function submit_buy()
{
	$("#order_done").attr("disabled",true);
	var query = new Object();
	
	//获取配送方式
	var delivery_id = $("input[name='delivery']:checked").val();

	if(!delivery_id)
	{
		delivery_id = 0;
	}
	query.delivery_id = delivery_id;

	//配送地区
	var select_last_node = $("#cart_consignee").find("select[value!='0']");
	if(select_last_node.length>0)
	{		
		var region_id = $(select_last_node[select_last_node.length - 1]).val();
	}
	else
	{
		var region_id = 0;
	}
	query.region_id = region_id;
	
	//余额支付
	var account_money = $("input[name='account_money']").val();
	if(!account_money||$.trim(account_money)=='')
	{
		account_money = 0;
	}
	query.account_money = account_money;
	
	//全额支付
	if($("#check-all-money").attr("checked"))
	{
		query.all_account_money = 1;
	}
	else
	{
		query.all_account_money = 0;
	}
	
	//代金券
	var ecvsn = $("input[name='ecvsn']").val();
	if(!ecvsn)
	{
		ecvsn = '';
	}
	var ecvpassword = $("input[name='ecvpassword']").val();
	if(!ecvpassword)
	{
		ecvpassword = '';
	}
	query.ecvsn = ecvsn;
	query.ecvpassword = ecvpassword;
	
	//支付方式
	var payment = $("input[name='payment']:checked").val();
	if(!payment)
	{
		payment = 0;
	}
	query.payment = payment;
	
	if(!isNaN(order_id)&&order_id>0)
	var ajaxurl = APP_ROOT+"/ajax.php?act=count_order_total&id="+order_id;
	else
	var ajaxurl = APP_ROOT+"/ajax.php?act=count_buy_total";
	$.ajax({ 
		url: ajaxurl,
		data:query,
		type: "POST",
		dataType: "json",
		success: function(data){
			if(data.is_delivery == 1)
			{
				//配送验证
				if(!data.region_info||data.region_info.region_level != 4)
				{
					$.showErr(LANG['FILL_CORRECT_CONSIGNEE_ADDRESS']);
					$("#order_done").attr("disabled",false);
					return;
				}
				if($.trim($("input[name='consignee']").val())=='')
				{
					$.showErr(LANG['FILL_CORRECT_CONSIGNEE']);
					$("#order_done").attr("disabled",false);
					return;
				}
				if($.trim($("input[name='address']").val())=='')
				{
					$.showErr(LANG['FILL_CORRECT_ADDRESS']);
					$("#order_done").attr("disabled",false);
					return;
				}
				if($.trim($("input[name='zip']").val())=='')
				{
					$.showErr(LANG['FILL_CORRECT_ZIP']);
					$("#order_done").attr("disabled",false);
					return;
				}
				if($.trim($("input[name='mobile']").val())=='')
				{
					$.showErr(LANG['FILL_MOBILE_PHONE']);
					$("#order_done").attr("disabled",false);
					return;
				}
				if(!$.checkMobilePhone($("input[name='mobile']").val()))
				{
					$.showErr(LANG['FILL_CORRECT_MOBILE_PHONE']);
					$("#order_done").attr("disabled",false);
					return;
				}
				if(!data.delivery_info)
				{
					$.showErr(LANG['PLEASE_SELECT_DELIVERY']);
					$("#order_done").attr("disabled",false);
					return;
				}			
			}
			
			if(data.pay_price!=0&&!data.payment_info)
			{
				$.showErr(LANG['PLEASE_SELECT_PAYMENT']);
				$("#order_done").attr("disabled",false);
				return;
			}	
			
			$("#cart_form").submit();
		},
		error:function(ajaxobj)
		{			
			alert("error: "+ajaxobj.responseText);
			return false;
		}
	});	
}

function submit_sms()
{
	$.weeboxs.open(APP_ROOT+"/sms.php?act=subscribe", {contentType:'ajax',showButton:false,title:LANG['SMS_SUBSCRIBE'],width:400,height:200});	
}
function unsubmit_sms()
{
	$.weeboxs.open(APP_ROOT+"/sms.php?act=unsubscribe", {contentType:'ajax',showButton:false,title:LANG['SMS_UNSUBSCRIBE'],width:400,height:200});	
}


//验证消费券
function check_coupon()
{
	var coupon_sn = $.trim($("#coupon_sn").val());
	var ajaxurl = APP_ROOT+"/coupon.php?act=check_coupon&coupon_sn="+coupon_sn;
	$.ajax({ 
		url: ajaxurl,
		success: function(msg){
			alert(msg);
		},
		error:function(ajaxobj)
		{
			if(ajaxobj.responseText!='')
			alert(ajaxobj.responseText);
		}
	});	
}

function use_coupon()
{
	var coupon_sn = $.trim($("#coupon_sn").val());
	var coupon_pwd = $.trim($("#coupon_pwd").val());
	var ajaxurl = APP_ROOT+"/coupon.php?act=use_coupon&coupon_sn="+coupon_sn+"&coupon_pwd="+coupon_pwd;
	$.ajax({ 
		url: ajaxurl,
		dataType: "json",
		success: function(obj){
			if(obj.status==2)//未登录
			{
				$.weeboxs.open(APP_ROOT+"/coupon.php?act=ajax_supplier_login", {contentType:'ajax',showButton:false,title:LANG['SUPPLIER_LOGIN'],width:500,height:150});	
			}
			if(obj.status == 0)
			{
				//确认失败
				alert(obj.msg);
			}
			if(obj.status == 1)
			{
				//确认成功
				alert(obj.msg);
			}
		},
		error:function(ajaxobj)
		{
			if(ajaxobj.responseText!='')
			alert(ajaxobj.responseText);
		}
	});
}

function formSuccess(obj,msg)
{
	if(msg!='')
	$(obj).parent().find(".f-input-tip").html("<span class='form_success'>"+msg+"</span>");
	else
	$(obj).parent().find(".f-input-tip").html("");
}
function formError(obj,msg)
{
	$(obj).parent().find(".f-input-tip").html("<span class='form_err'>"+msg+"</span>");
}