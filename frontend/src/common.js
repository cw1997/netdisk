// jshint unused:false
export default {
	getCookie (name) {
	  var arr = null
	  var reg = new RegExp('(^| )' + name + '=([^;]*)(;|$)')
	  arr = document.cookie.match(reg)
	  if (arr) {
	    return unescape(arr[2])
	  } else {
	    return null
	  }
	},
	delCookie (name) {
	    var exp = new Date();
	    exp.setTime(exp.getTime() - 1);
	    var cval = this.getCookie(name);
	    if (cval != null)
	        document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
	},
	setCookie (name, value, time) {
	    // var strsec = this.getsec(time);
	    var exp = new Date();
	    exp.setTime(exp.getTime() + time * 1);
	    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
	},
	getsec (str) {
	    console.log('cookie有效期：' + str);
	    var str1 = str.substring(1, str.length) * 1;
	    var str2 = str.substring(0, 1);
	    if (str2 == "s") {
	        return str1 * 1000;
	    } else if (str2 == "h") {
	        return str1 * 60 * 60 * 1000;
	    } else if (str2 == "d") {
	        return str1 * 24 * 60 * 60 * 1000;
	    }
	},

	getDisplaySize (size) {
	  // console.log(size)
	  // while (size > 1024) {
	  //   size /= 1024
	  // }
	  // return size + 'M'
	  if (size < 1024) {
	    return parseInt(size) + ' Byte'
	  }
	  if (size < 1024 * 1024) {
	    return parseInt(size / 1024) + ' KByte'
	  }
	  if (size < 1024 * 1024 * 1024) {
	    return parseInt(size / 1024 / 1024) + ' MByte'
	  }
	  if (size < 1024 * 1024 * 1024 * 1024) {
	    return parseInt(size / 1024 / 1024 / 1024) + ' GByte'
	  }
	  return size
	}
}

