// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'

import './normalize.css'

import VueRouter from 'vue-router'
Vue.use(VueRouter)

import Vuex from 'vuex'
Vue.use(Vuex)

import message from './message'
// Vue.use(message)
Vue.prototype.$message = message

import notification from './notification'
// Vue.use(notification)
Vue.prototype.$notification = notification

// import messagebox from './messagebox'
// Vue.use(messagebox)
// Vue.prototype.$messagebox = messagebox

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-default/index.css'
Vue.use(ElementUI)

import common from './common'
Vue.use(common)
Vue.prototype.$common = common

Vue.config.productionTip = false

/* eslint-disable no-new */
/* new Vue({
  el: '#app',
  template: '<App/>',
  components: { App }
}) */

// 如果在模块化构建系统中，请确保在开头调用了 Vue.use(Vuex)
const netdisk = {
  state: {
    folder_id: 0,
    father_folder_id: 0,
    selected: null,
    path: 'path',
    loading: false,
    click_id: 0,
    usedSize: 0,
    totalSize: 0,
    status: '',
    explorer: [
    // {
    //   id: '1',
    //   file_name: '王小虎',
    //   file_size: '1048576 kb',
    //   path: '上海市普陀区金沙江路 1518 弄',
    //   upload_time: '2017-03-05 11:59:19',
    //   modification_time: '2017-03-05 11:59:19'
    // }, {
    //   id: '1',
    //   file_name: '王小虎',
    //   file_size: '1048576 kb',
    //   path: '上海市普陀区金沙江路 1518 弄',
    //   upload_time: '2017-03-05 11:59:19',
    //   modification_time: '2017-03-05 11:59:19'
    // }, {
    //   id: '2',
    //   file_name: '王小虎',
    //   file_size: '1048576 kb',
    //   path: '上海市普陀区金沙江路 1518 弄',
    //   upload_time: '2017-03-05 11:59:19',
    //   modification_time: '2017-03-05 11:59:19'
    // }
    ]
  },
  mutations: {
    testNetdisk (state) {
      state.count++
    },
    initList (state, data) {
      let files = data.files
      let folders = data.folders
      console.info(data)
      // console.info(files)
      // console.info(folders)
      // 文件夹对象适应表格字段
      var i
      for (i = files.length - 1; i >= 0; i--) {
        files[i].file_size = common.getDisplaySize(files[i].file_size)
      }
      for (i = 0; i < folders.length; i++) {
        folders[i].isfolder = true
        folders[i].file_name = folders[i].folder_name
        folders[i].upload_time = folders[i].create_time
        folders[i].file_mime_type = '文件夹/folders'
      }
      state.explorer = folders.concat(files)
      state.usedSize = data.used_size
      state.totalSize = data.total_size
    },
    initFolderId (state, ids) {
      state.folder_id = ids.folder_id
      state.father_folder_id = ids.father_id
    },
    setClickId (state, id) {
      state.click_id = id
    }
  },
  actions: {
    loadList (context, folderId) {
      context.state.loading = true
      // let Vue = this
      // this.$message.error('a')
      // console.info(this)
      // folderId === '' ? context.state.folder_id : folderId
      // context.state.folder_id = folderId
      let url = 'http://127.0.0.1/netdisk/public/v1/file/explorer'
      http.post(url, {folder_id: folderId})
      .then(function (res) {
        console.log(res)
        if (res.data.errorno === 9) {
          console.log(res.data.data)
          // Vue.$message.success(res.data.errormsg)
          notification.info('进入【' + res.data.data.folder_name + '】文件夹。' +
            '文件列表加载成功，本次共加载了' +
            res.data.data.folders.length + '个文件夹，' +
            res.data.data.files.length + '个文件。')
          context.commit('initList', res.data.data)
          context.commit('initFolderId', res.data.data)
        } else {
          notification.error(res.data.errormsg)
        }
        context.state.loading = false
      })
      .catch(function (err) {
        console.error(err)
        notification.error('获取文件列表失败，网络请求出错')
        context.state.loading = false
      })
    },
    loadSearchList (context, param) {
      context.state.loading = true
      let keyword = param.keyword
      let isGlobal = param.isglobal
      let fatherId = context.state.folder_id
      let url = 'http://127.0.0.1/netdisk/public/v1/file/search'
      http.post(url, {keyword, isGlobal, fatherId})
      .then(function (res) {
        console.log(res)
        if (res.data.errorno === 24) {
          console.log(res.data.data)
          // Vue.$message.success(res.data.errormsg)
          notification.info('搜索成功，本次共加载了' +
            res.data.data.folders.length + '个文件夹，' +
            res.data.data.files.length + '个文件。')
          context.commit('initList', res.data.data)
          context.commit('initFolderId', res.data.data)
        } else {
          notification.error(res.data.errormsg)
        }
        context.state.loading = false
      })
      .catch(function (err) {
        console.error(err)
        notification.error('获取文件列表失败，网络请求出错')
        context.state.loading = false
      })
    }
  },
  getters: {}
}
const passport = {
  state: {
    uid: 0,
    access_token: '',
    username: '',
    email: ''
  },
  mutations: {
    initPassport (state, user) {
      if (user) {
        state.uid = user.id
        state.username = user.username
        state.email = user.email
        state.access_token = user.access_token
        // notification.success('欢迎 ' + user.username + ' 登录\n' +
        //   '您上次登录时间为：' + user.last_login_time + '\n' +
        //   '您上次登录IP为：' + user.last_login_ip + '\n')
        notification.success('欢迎 ' + user.email + ' ，登录' +
          '您上次登录时间为：' + user.last_login_time + '，' +
          '您上次登录IP为：' + user.last_login_ip + '。')
      } else {
        state.uid = null
        state.username = null
        state.email = null
        state.access_token = null
      }
    },
    testPassport (state) {
      state.count++
    }
  },
  actions: {},
  getters: {
    getUsername: (state) => {
      if (state.uid === 0) {
        return ''
      } else {
        // return state.username + '（' + state.uid + '：' + state.email + '）'
        return state.uid + '（' + state.email + '）'
      }
    }
  }
}
const share = {
  state: {
    count: null,
    password: null,
    loading: false
  },
  mutations: {
    increment (state) {
      state.count++
    },
    initExplorer (state) {
      state.count++
    },
    changeShareLoading (state, loading) {
      state.loading = loading
    },
    initShareList (state, data) {
      let files = data.files
      let folders = data.folders
      let password = data.password
      console.info(data)
      // console.info(files)
      // console.info(folders)
      // 文件夹对象适应表格字段
      for (var i = 0; i < folders.length; i++) {
        folders[i].isfolder = true
        folders[i].file_name = folders[i].folder_name
        folders[i].upload_time = folders[i].create_time
        folders[i].file_mime_type = '文件夹/folders'
      }
      state.explorer = folders.concat(files)
      state.password = password
      state.usedSize = data.used_size
      state.totalSize = data.total_size
    },
    initFolderId (state, ids) {
      state.folder_id = ids.folder_id
      state.father_folder_id = ids.father_id
    },
    setClickId (state, id) {
      state.click_id = id
    }
  },
  actions: {},
  getters: {}
}
const myshare = {
  state: {
    count: null,
    loading: false
  },
  mutations: {
    increment (state) {
      state.count++
    },
    initExplorer (state) {
      state.count++
    }
  },
  actions: {},
  getters: {}
}
const recycle = {
  state: {
    count: null,
    explorer: null,
    loading: false
  },
  mutations: {
    increment (state) {
      state.count++
    },
    // initExplorer (state) {
    //   state.count++
    // },
    initRecycleList (state, filesAndFolders) {
      let files = filesAndFolders.files
      let folders = filesAndFolders.folders
      console.info(filesAndFolders)
      // console.info(files)
      // console.info(folders)
      // 文件夹对象适应表格字段
      for (var i = 0; i < folders.length; i++) {
        folders[i].isfolder = true
        folders[i].file_name = folders[i].folder_name
        folders[i].upload_time = folders[i].create_time
        // folders[i].delete_time = folders[i].delete_time
        folders[i].file_mime_type = '文件夹/folders'
      }
      state.explorer = folders.concat(files)
    }
  },
  actions: {
    loadRecycleList (context) {
      context.state.loading = true
      // let Vue = this
      let url = 'http://127.0.0.1/netdisk/public/v1/recycle/view'
      http.post(url, {})
      .then(function (res) {
        console.log(res)
        if (res.data.errorno === 34) {
          console.log(res.data.data)
          // Vue.$message.success(res.data.errormsg)
          notification.info('进入回收站成功。' +
            '回收站内共有' +
            res.data.data.folders.length + '个文件夹，' +
            res.data.data.files.length + '个文件。')
          context.commit('initRecycleList', res.data.data)
          // context.commit('initFolderId', res.data.data)
        } else {
          notification.error(res.data.errormsg)
        }
        context.state.loading = false
      })
      .catch(function (err) {
        console.error(err)
        notification.error('获取文件列表失败，网络请求出错')
        context.state.loading = false
      })
    }
  },
  getters: {}
}
// const store = new Vuex.Store({
//   state: {
//     count: 0,
//     explorer: null
//   },
//   mutations: {
//     increment (state) {
//       state.count++
//     },
//     initExplorer (state) {
//       state.count++
//     }
//   }
// })
const store = new Vuex.Store({
  modules: {
    netdisk,
    passport,
    share,
    myshare,
    recycle
  }
})

// import axios from 'axios'
// axios.defaults.baseURL = 'http://127.0.0.1/netdisk/public/'
/**
 * 由于为跨域请求，因此此处不必设置csrf token头部
 */
// axios.defaults.headers.common['X-CSRF-TOKEN'] = common.getCookie('XSRF-TOKEN')
// console.log(common.getCookie('XSRF-TOKEN'))
// axios.defaults.headers.common['Authorization'] = AUTH_TOKEN
// axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded'
// Vue.use(axios)
// Vue.prototype.axios = axios

// import ajax from './ajax'
// Vue.use(ajax)
// Vue.prototype.$http = ajax
import axios from 'axios'
import Qs from 'Qs'
axios.defaults.withCredentials = true
let http = axios.create({
  // baseURL: 'http://127.0.0.1/netdisk/public/',
  // axios.defaults.withCredentials = true
  // withCredentials: true,
  timeout: 10000,
  transformRequest: [function (data) {
    // Do whatever you want to transform the data
    // data.access_token = Vue.store.state.passport.access_token
    data.access_token = common.getCookie('access_token')
    // console.info(Vue)
    return Qs.stringify(data)
  }]
})
// Vue.use(http)
Vue.prototype.$http = http

// 0. 如果使用模块化机制编程，導入Vue和VueRouter，要调用 Vue.use(VueRouter)

// 1. 定义（路由）组件。
// 可以从其他文件 import 进来
import Netdisk from './components/netdisk/Netdisk'
import ShareList from './components/share/ShareList'
import Recycle from './components/recycle/Recycle'
import View from './components/share/View'
import Login from './components/passport/Login'
import Register from './components/passport/Register'
import ChangePassword from './components/passport/ChangePassword'

// 2. 定义路由
// 每个路由应该映射一个组件。 其中"component" 可以是
// 通过 Vue.extend() 创建的组件构造器，
// 或者，只是一个组件配置对象。
// 我们晚点再讨论嵌套路由。
const routes = [
  { path: '/Netdisk', component: Netdisk, name: 'Netdisk' },
  { path: '/Share', component: View, name: 'View' },
  { path: '/ShareList/:id', component: ShareList, name: 'ShareList' },
  { path: '/Recycle', component: Recycle, name: 'Recycle' },
  { path: '/Login', component: Login, name: 'Login' },
  { path: '/Register', component: Register, name: 'Register' },
  { path: '/ChangePassword', component: ChangePassword, name: 'ChangePassword' }
]

// 3. 创建 router 实例，然后传 `routes` 配置
// 你还可以传别的配置参数, 不过先这么简单着吧。
const router = new VueRouter({
  routes // （缩写）相当于 routes: routes
})

// 从Cookie中同步登录状态到Vuex
store.state.passport.access_token = common.getCookie('access_token')
store.state.passport.email = common.getCookie('email')
store.state.passport.username = common.getCookie('username')
store.state.passport.uid = common.getCookie('uid')

// 导入全屏loading服务组件
// import { Loading } from 'element-ui'
// 路由钩子，判断登录状态
router.beforeEach((to, from, next) => {
  console.log(to.path)
  // import common from './common.js'
  // console.log(common.getCookie('uid'))
  // next()
  // Loading.service(options)
  // Loading.service()
  if (to.path === '/Login' || to.path === '/Register') {
    if (store.state.passport.access_token === null) {
      next()
    } else {
      next('/Netdisk')
    }
  } else {
    if (store.state.passport.access_token === null) {
      next('/Login')
    } else {
      next()
    }
  }
})

// 4. 创建和挂载根实例。
// 记得要通过 router 配置参数注入路由，
// 从而让整个应用都有路由功能
const app = new Vue({
  el: '#app',
  template: '<App/>',
  components: { App },
  router,
  store
}).$mount('#app')

// 现在，应用已经启动了！

console.log(app)
