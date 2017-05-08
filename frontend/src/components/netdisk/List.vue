<template>
  <div class="list">
    <!-- <h2>{{ msg }}</h2> -->
    <!-- <h2>文件分类</h2> -->
    <div class="control">
      <div class="left">
        <el-button type="primary" @click="gotoFatherFolder" :disabled="this.$store.state.netdisk.father_folder_id==0">返回上一层文件夹<i class="el-icon-upload el-icon--right"></i></el-button>
        <el-button type="primary" icon="edit" @click="createFile">新建文件</el-button>
        <el-button type="primary" icon="edit" @click="createFolder">新建文件夹</el-button>
        <el-button type="primary" icon="share">分享</el-button>
        <el-button type="primary" icon="delete">删除</el-button>
        <el-button type="primary" icon="search" @click="refresh" :loading="this.$store.state.netdisk.loading">刷新</el-button>
      </div>
      <div class="right">
        <!-- <el-button type="primary" icon="search">搜索</el-button> -->
        <span id="total-size">{{ displaySize }}</span>
      </div>
    </div>
    <div class="size">
      <el-progress :text-inside="true" :stroke-width="24" :percentage="size" :status="status"></el-progress>
    </div>
    <div class="search">
      <el-input placeholder="请输入内容" v-model="keyword">
        <el-select class="select" v-model="select" slot="prepend" placeholder="选择搜索范围">
          <el-option label="全盘搜索" value="1"></el-option>
          <el-option label="本文件夹内搜索" value="2"></el-option>
        </el-select>
        <el-button slot="append" icon="search" @click="search">搜索文件和文件夹</el-button>
      </el-input>
    </div>
    <!-- <div class="path">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/' }">首页{{ path }}</el-breadcrumb-item>
        <el-breadcrumb-item>活动管理</el-breadcrumb-item>
        <el-breadcrumb-item>活动列表</el-breadcrumb-item>
        <el-breadcrumb-item>活动详情</el-breadcrumb-item>
      </el-breadcrumb>
    </div> -->
    <!-- <span @click="test">test</span> -->
    <Explorer></Explorer>
  </div>
</template>

<script>
import Explorer from './list/Explorer'
export default {
  name: 'List',
  data () {
    return {
      msg: 'List',
      select: '',
      keyword: ''
      // size: 50
      // usedSize: 0,
      // totalSize: 0,
      // status: ''
    }
  },
  computed: {
    path () {
      return this.$store.state.netdisk.path
    },
    usedSize () {
      return this.$store.state.netdisk.usedSize
    },
    totalSize () {
      return this.$store.state.netdisk.totalSize
    },
    status () {
      return this.size > 90 ? 'exception' : ''
    },
    size () {
      return parseInt(this.usedSize / this.totalSize * 100)
    },
    displaySize () {
      return '网盘容量使用情况：' + this.getDisplaySize(this.usedSize) + ' / ' + this.getDisplaySize(this.totalSize)
    }
  },
  components: {
    Explorer
  },
  methods: {
    test () {
      console.log(this.$store.state)
    },
    getDisplaySize (size) {
      console.log(size)
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
    },
    search () {
      if (this.keyword === '') {
        this.refresh()
        return
      } else {
        let param = {keyword: this.keyword, isglobal: this.select}
        this.$store.dispatch('loadSearchList', param)
      }
    },
    createFolder () {
      let Vue = this
      this.$prompt('请输入新文件夹名称', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        // inputPattern: /[\w!#$%&'*+/=?^_`{|}~-]+(?:\.[\w!#$%&'*+/=?^_`{|}~-]+)*@(?:[\w](?:[\w-]*[\w])?\.)+[\w](?:[\w-]*[\w])?/,
        // inputPattern: /\w{1,128}/,
        inputErrorMessage: '文件夹名称格式不正确'
      }).then(({ value }) => {
        console.info(value)
        // this.$message.error('a')
        let url = 'http://127.0.0.1/netdisk/public/v1/file/createFloder'
        this.$http.post(url, {folder_name: value, father_id: Vue.$store.state.netdisk.folder_id})
        .then(function (res) {
          console.log(res)
          if (res.data.errorno === 11) {
            Vue.$message.success(res.data.errormsg)
            console.log(res.data.data)
            Vue.loadList(res.data.data.id)
          } else {
            Vue.$message.error(res.data.errormsg)
          }
        })
        .catch(function (err) {
          console.error(err)
          Vue.$message.error('创建文件夹失败，网络请求出错')
        })
        this.$message({
          type: 'success',
          message: '你的文件夹名称是: ' + value
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '取消输入'
        })
      })
    },
    gotoFatherFolder () {
      this.loadList(this.$store.state.netdisk.father_folder_id)
    },
    loadList (folderId) {
      this.$store.dispatch('loadList', folderId)
    },
    refresh () {
      this.loadList()
    }
  },
  created () {
    this.loadList()
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.list {
  position: fixed;
  left: 300px;
  top: 50px;
  height: 100vh;
  width: calc(100vw - 300px);
  /* 如果浏览器不支持CSS3 calc方法，可以使用js计算宽度 */
  /*background: red;*//* 解开此处注释来查看效果 */
  /*padding: 20px;*/
}
.control {
  margin: 20px;
  height: 30px;
}
#total-size {
  height: 36px;
  line-height: 36px;
}
.size {
  margin: 0 20px;
}
.path {
  margin: 10px;
  padding: 10px;
  border: 1px solid #bbb;
  border-radius: 10px;
  box-shadow:2px 2px 10px #ccc;
}
.search {
  margin: 10px;
  padding: 10px;
}
.el-select .el-input {
  width: 110px;
}
.select {
  width: 150px;
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
