import { Notification } from 'element-ui'
// import 'element-ui/lib/theme-default/index.css'
export default {
	error: function (message) {
		// alert(message)
		Notification.error({
      title: '成功',
      message: message,
      type: 'error'
    })
	},
	success: function (message) {
		// alert(message)
		Notification.success({
      title: '成功',
      message: message,
      type: 'success'
    })
	},
	info: function (message) {
		// alert(message)
		Notification.info({
      title: '成功',
      message: message,
      type: 'info'
    })
	},
	warning: function (message) {
		// alert(message)
		Notification.warning({
      title: '成功',
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