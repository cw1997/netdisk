import { Message } from 'element-ui'
// import 'element-ui/lib/theme-default/index.css'
export default {
	error: function (message) {
		// alert(message)
		Message({
	      showClose: true,
	      message: message,
	      type: 'error'
	    })
	},
	success: function (message) {
		// alert(message)
		Message({
	      showClose: true,
	      message: message,
	      type: 'success'
	    })
	},
	info: function (message) {
		// alert(message)
		Message({
	      showClose: true,
	      message: message,
	      type: 'info'
	    })
	},
	warning: function (message) {
		// alert(message)
		Message({
	      showClose: true,
	      message: message,
	      type: 'warning'
	    })
	}
	/*success: function (message) {
		MessageBox.alert(message, '错误', {
			confirmButtonText: '确定',
			callback: action => {
				this.message({
					type: 'success',
					message: message
				})
			}
		})
	}*/
}