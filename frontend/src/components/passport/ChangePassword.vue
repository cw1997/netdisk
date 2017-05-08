<template>
  <div class="login">
    <div class="top-img"></div>
    <div class="main-form">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
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
        <!-- <el-form-item label="旧密码" prop="oldpassword">
          <el-input type="password" v-model="ruleForm.oldpassword"></el-input>
        </el-form-item> -->
        <el-form-item label="新密码" prop="newpassword">
          <el-input type="password" v-model="ruleForm.newpassword"></el-input>
        </el-form-item>
        <el-form-item label="重复新密码" prop="repassword">
          <el-input type="password" v-model="ruleForm.repassword"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm">修改密码</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Register',
  data () {
    return {
      loading: false,
      sendLoading: false,
      ruleForm: {
        vcode: '',
        // oldpassword: '',
        newpassword: '',
        repassword: ''
      },
      select: '',
      // vcode: '',
      rules: {
        vcode: [
          { required: true, message: '请输入验证码', trigger: 'blur' },
          { min: 4, max: 6, message: '长度在 4 到 6 个字符', trigger: 'blur' }
        ],
        // oldpassword: [
        //   { required: true, message: '请输入旧密码（用于防止非法修改他人密码）' },
        //   { min: 6, max: 15, message: '长度在 6 到 15 个字符', trigger: 'blur' }
        // ],
        newpassword: [
          { required: true, message: '请输入新密码' },
          { min: 6, max: 15, message: '长度在 6 到 15 个字符', trigger: 'blur' }
        ],
        repassword: [
          { required: true, message: '请重复输入密码（防止用户输入错误）' },
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
      this.$http.post(url, {})
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
      let Vue = this
      // let vcode = this.vcode
      // let oldpassword = this.oldpassword
      let newpassword = this.newpassword
      let repassword = this.repassword
      if (newpassword === repassword) {
        let url = 'http://127.0.0.1/netdisk/public/v1/passport/changePassword'
        this.$http.post(url, this.ruleForm)
        .then(function (res) {
          console.log(res)
          if (res.data.errorno === 26) {
            Vue.$message.success(res.data.errormsg)
            console.log(res.data.data)
            // Vue.$store.dispatch('loadList')
          } else {
            Vue.$message.error(res.data.errormsg)
          }
        })
        .catch(function (err) {
          console.error(err)
          Vue.$message.error('修改密码失败，网络请求出错')
        })
      } else {
        Vue.$message.error('新密码与重复输入的密码不一致')
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.login {
  width: 800px;
  height: 600px;
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
  background-image: url(../../assets/changepassword.jpg);
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
