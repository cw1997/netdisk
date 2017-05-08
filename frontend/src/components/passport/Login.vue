<template>
  <div class="login">
    <div class="top-img"></div>
    <div class="main-form">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="ruleForm.email"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input type="password" v-model="ruleForm.password"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm('ruleForm')" :loading="logining">登录</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data () {
    return {
      logining: false,
      ruleForm: {
        email: '635491570@qq.com',
        password: '321321'
      },
      rules: {
        email: [
          { required: true, message: '请输入邮箱', trigger: 'blur' }
        ],
        password: [
          { required: true, message: '请输入密码' },
          { min: 6, max: 15, message: '长度在 6 到 15 个字符', trigger: 'blur' }
        ]
      }
    }
  },
  methods: {
    submitForm () {
      let form = this.ruleForm
      let Vue = this
      // this.$message.error('a')
      let url = 'http://127.0.0.1/netdisk/public/v1/passport/login'
      if (form.email !== '' && form.password !== '') {
        this.logining = true
        this.$http.post(url, form)
        .then(function (res) {
          console.log(res)
          if (res.data.errorno === 1) {
            Vue.$message.success(res.data.errormsg)
            console.log(res.data.data.user.username, res.data.data.user.email)
            Vue.$common.setCookie('access_token', res.data.data.user.access_token)
            Vue.$common.setCookie('uid', res.data.data.user.id)
            Vue.$common.setCookie('username', res.data.data.user.username)
            Vue.$common.setCookie('email', res.data.data.user.email)
            Vue.$store.commit('initPassport', res.data.data.user)
            Vue.logining = false
            Vue.$router.push('/Netdisk')
          } else {
            Vue.$message.error(res.data.errormsg)
            Vue.logining = false
          }
        })
        .catch(function (err) {
          console.error(err)
          Vue.$message.error('登录失败，网络请求出错')
          Vue.logining = false
        })
      } else {
        this.$alert('请在文本框内正确输入各项内容', '错误', {
          confirmButtonText: '确定',
          callback: action => {
            this.$message({
              type: 'error',
              message: '请在文本框内正确输入各项内容'
            })
          }
        })
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.login {
  width: 800px;
  height: 550px;
  margin: 50px auto;
  border: 1px solid gray;
  border-radius: 10px;
  overflow: hidden;
  box-shadow:10px 10px 15px #aaaaaa;
}
.top-img {
  width: 800px;
  height: 300px;
  background-position: left;
  background-image: url(../../assets/login.jpg);
  margin-bottom: 30px;
}
.main-form {
  margin: auto 100px;
}
/*h1, h2 {
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
}*/
</style>
