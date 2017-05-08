<template>
  <div class="register">
    <div class="top-img"></div>
    <div class="main-form">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="ruleForm.email"></el-input>
        </el-form-item>
        <el-form-item label="验证码" prop="vcode">
          <el-input placeholder="请输入内容" v-model="ruleForm.vcode">
            <!-- <el-select v-model="select" slot="prepend" placeholder="请选择">
              <el-option label="邮箱验证码" value="1"></el-option>
              <el-option label="短信验证码" value="2"></el-option>
              <el-option label="用户电话" value="3"></el-option>
            </el-select> -->
            <el-button slot="append" icon="message" @click="sendVcode" :sendLoading="false">发送验证码</el-button>
          </el-input>
          <!-- <el-input v-model="ruleForm.email"></el-input> -->
          <!-- <el-button @click="resetForm('ruleForm')">重置</el-button> -->
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input type="password" v-model="ruleForm.password"></el-input>
        </el-form-item>
        <el-form-item label="重复密码" prop="repassword">
          <el-input type="repassword" v-model="ruleForm.repassword"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm" :loading="loading">注册</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
// import register_ajax from './axios.config.js'
export default {
  name: 'Register',
  data () {
    return {
      loading: false,
      sendLoading: false,
      ruleForm: {
        email: '867597730@qq.com',
        vcode: '',
        password: '',
        repassword: ''
      },
      rules: {
        email: [
          { required: true, message: '请输入邮箱', trigger: 'blur' }
        ],
        vcode: [
          { required: true, message: '请输入验证码', trigger: 'blur' },
          { min: 4, max: 6, message: '长度在 4 到 6 个字符', trigger: 'blur' }
        ],
        password: [
          { required: true, message: '请输入新密码' },
          { min: 6, max: 15, message: '长度在 6 到 15 个字符', trigger: 'blur' }
        ],
        repassword: [
          { required: true, message: '请重复输入密码' },
          { min: 6, max: 15, message: '长度在 6 到 15 个字符', trigger: 'blur' }
        ]
      }
    }
  },
  methods: {
    sendVcode () {
      this.sendLoading = true
      let Vue = this
      let url = 'http://127.0.0.1/netdisk/public/v1/passport/sendEmail'
      this.$http.post(url, {email: Vue.ruleForm.email})
      .then(function (res) {
        console.log(res)
        if (res.data.errorno === 51) {
          Vue.$message.success(res.data.errormsg)
          console.log(res.data.data)
          // Vue.$store.dispatch('loadList')
        } else {
          Vue.$message.error(res.data.errormsg)
        }
        this.sendLoading = false
      })
      .catch(function (err) {
        this.sendLoading = false
        console.error(err)
        Vue.$message.error('发送验证码失败，网络请求出错')
      })
    },
    submitForm () {
      let form = this.ruleForm
      let Vue = this
      let url = 'http://127.0.0.1/netdisk/public/v1/passport/register'
      if (form.password === form.repassword) {
        Vue.loading = true
        this.$http.post(url, form)
        .then(function (res) {
          console.log(res)
          if (res.data.errorno === 5) {
            console.log(res.data.data.user.username, res.data.data.user.email)
            Vue.$common.setCookie('access_token', res.data.data.user.access_token)
            Vue.$store.commit('initPassport', res.data.data.user)
            Vue.loading = false
            Vue.$router.push('/Netdisk')
            Vue.$message.success(res.data.errormsg)
          } else {
            Vue.loading = false
            Vue.$message.error(res.data.errormsg)
          }
        })
        .catch(function (err) {
          Vue.loading = false
          console.error(err)
          Vue.$alert('注册失败，网络请求出错', '注册失败', {
            type: 'error',
            confirmButtonText: '确定',
            callback: action => {
              Vue.$message({
                type: 'error',
                message: '注册失败，网络请求出错，请检查你的网络连接是否正常。'
              })
            }
          })
        })
      } else {
        this.$alert('两次输入的密码不一致，请仔细检查后输入', '错误', {
          confirmButtonText: '确定',
          callback: action => {
            this.$message({
              type: 'error',
              message: '两次输入的密码不一致，请仔细检查后输入'
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
.register {
  width: 800px;
  height: 650px;
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
  background-image: url(../../assets/register.jpg);
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
