var MD5 = new Hashes.MD5;
var SHA1 = new Hashes.SHA1;
var SHA256 =  new Hashes.SHA256;
var SHA512 = new Hashes.SHA512;
var RMD160 = new Hashes.RMD160;
function random_string(len)
{
	var str="abcdefghijklmnopqrstuvwxyz0123456789";
	var random_str="";
	for(i=0;i<len;i++)
		random_str+=str.charAt(Math.floor(Math.random() * str.length));
	if(random_str.length==len)
		return random_str;
	else
		random_string(len);
}
function cnvrt(i, str)
{
	switch(parseInt(i))
	{
		case 1:
			return MD5.hex(SHA512.hex(MD5.hex(str)));
			break;
		case 2:
			return SHA1.hex(SHA512.hex(MD5.hex(str)));
			break;
		case 3:
			return SHA256.hex(SHA512.hex(MD5.hex(str)));
			break;
		case 4:
			return SHA512.hex(SHA512.hex(MD5.hex(str)));
			break;
		case 5:
			return RMD160.hex(SHA512.hex(MD5.hex(str)));
			break;
	}
}

function encryptlogin(str) {
	return SHA512.hex(SHA512.hex(MD5.hex(str)));
}

function encrypt(i, str) {
	switch (parseInt(i)) {
		case 1:
			return MD5.hex(MD5.hex(SHA512.hex(str)));
			break;
		case 2:
			return SHA1.hex(MD5.hex(SHA512.hex(str)));
			break;
		case 3:
			return SHA256.hex(MD5.hex(SHA512.hex(str)));
			break;
		case 4:
			return SHA512.hex(MD5.hex(SHA512.hex(str)));
			break;
		case 5:
			return RMD160.hex(MD5.hex(SHA512.hex(str)));
			break;
	}
}

function fun_cnvrt_uppercase(evt)
{
	evt.value=evt.value.toUpperCase();
}
function capitalize(ele) {
	var splt = ele.value.split(" ");
	var str = "";
	for (i = 0; i < splt.length; i++) {
		if (i != 0)
			str += " ";
		str += splt[i].charAt(0).toUpperCase() + splt[i].slice(1).toLowerCase();
	}
	ele.value = str;
}
// setInterval(function(){$.ajax({url: 'session_set.php',type: 'GET',success: function (response){}});}, 60000);
function change_language(lang)
{
	var path = window.location.pathname;
	var page = path.split("/").pop();
	var ref=(window.location.href).split("/");
	switch(parseInt(lang))
	{
		case 1:
			if(page=="" || page=="index")
				window.location.href="/en/";
			else
				window.location.href="en/"+ref[(ref.length-1)];
			break;
		case 2:
			if(page=="" || page=="index")
				window.location.href="../";
			else
				window.location.href="../"+ref[(ref.length-1)];
			break;
	}
}