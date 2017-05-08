<template>
  <div class="hello">
    <nav>
      <ul class="menu left">
        <li class="logo"><a href="" title="">百毒网盘</a></li>
        <!-- <li><a href="" title="">我的网盘</a></li> -->
        <!-- <li><a href="" title="">分享大区</a></li> -->
        <!-- <li><a href="" title="">统计信息</a></li> -->
        <li><router-link to="/Netdisk">我的网盘</router-link></li>
        <li><router-link to="/ShareList">分享大区</router-link></li>
        <li><router-link to="/Recycle">回收站</router-link></li>
        <li><router-link to="/foo">统计信息</router-link></li>
      </ul>
      <ul class="menu right">
        <li><router-link to="/Login" v-if="this.$store.state.passport.uid===null">登录</router-link></li>
        <li><router-link to="/Register" v-if="this.$store.state.passport.uid===null">注册</router-link></li>
        <li><a href="" title="" v-if="this.$store.state.passport.uid!==null">{{ username }}</a></li>
        <li><router-link to="/ChangePassword" v-if="this.$store.state.passport.uid!==null">个人资料</router-link></li>
        <li><span @click="dialogVisible = true;return false;" v-if="this.$store.state.passport.uid!==null">退出登录</span></li>
      </ul>
    </nav>
    <el-dialog title="提示" v-model="dialogVisible" size="tiny">
      <span>您是否要退出登录？（如果您想保持登录状态，可以直接关闭浏览器，无需退出登录）</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" :loading="logouting" @click="logout">确 定</el-button>
      </span>
    </el-dialog>
    <!-- <h2>{{ msg }}</h2> -->
  </div>
</template>

<script>
export default {
  name: 'Navbar',
  data () {
    return {
      msg: 'Navbar',
      // username: '昌维',
      dialogVisible: false,
      logouting: false
    }
  },
  computed: {
    username () {
      return this.$store.getters.getUsername
    }
  },
  methods: {
    logout () {
      this.logouting = true
      let Vue = this
      // console.log(this.$store.getter.netdisk.getUsername())
      console.log('退出登录')
      let url = 'http://127.0.0.1/netdisk/public/v1/passport/logout'
      this.$http.post(url, {timestamp: Date.parse(new Date())})
      .then(function (res) {
        console.log(res)
        Vue.$common.delCookie('access_token')
        Vue.$common.delCookie('uid')
        Vue.$common.delCookie('email')
        Vue.$common.delCookie('username')
        Vue.$store.commit('initPassport')
        Vue.$message.success(res.data.errormsg)
        Vue.dialogVisible = false
        Vue.logouting = false
        Vue.$router.push('/Login')
        // if (res.data.errorno === 3) {
        //   Vue.$common.delCookie('access_token')
        //   Vue.$store.commit('initPassport')
        //   Vue.$message.success(res.data.errormsg)
        //   Vue.dialogVisible = false
        //   Vue.logouting = false
        //   Vue.$router.push('/Login')
        // } else {
        //   Vue.dialogVisible = false
        //   Vue.logouting = false
        //   Vue.$router.push('/Login')
        //   Vue.$message.error(res.data.errormsg)
        // }
      })
      .catch(function (error) {
        Vue.dialogVisible = false
        // Vue.logouting = false
        Vue.$router.push('/Login')
        Vue.$message.error('注销失败，网络连接错误，请检查你的网络连接')
        console.error(error)
      })
    },
    test () {
      console.log(this.$store.getters.getUsername)
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.left {
  float: left;
}
.right {
  float: right;
}
nav {
  background-color: #3b8cff;
  width: 100%;
  height: 50px;
  /*line-height: 50px;*/
  /*padding: 0 40px;*/
}
nav ul, nav li {
  list-style: none;
  margin: 0;
  padding: 0;
}
nav li {
  float: left;
}
nav a, nav span {
  display: block;
  height: 50px;
  line-height: 50px;
  text-decoration: none;
  color: white;
  /*font-family: 微软雅黑;*/
  padding: 0 10px;
}
.logo {
  font-weight: bolder;
  font-size: 18px;
  margin-right: 30px;
}
.menu {
  /*background-color: #3b8cff;*/
}
/* h1, h2 {
  font-weight: normal;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  display: inline-block;
  margin: 0 10px;
}

a {
  color: #42b983;
} */
</style>
