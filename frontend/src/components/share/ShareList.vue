<template>
  <div class="recycle">
    <div class="sidebar">
      <p>分享人：昌维</p>
      <p>分享时间：2017年3月23日</p>
    </div>
    <div class="list">
      <div class="tools">
        <div class="left">
          <el-button @click="loadList" :loading="this.$store.state.share.loading">刷新</el-button>
        </div>
        <div class="right">
          <el-button @click="loadList" :loading="this.$store.state.share.loading">刷新</el-button>
        </div>
        <!-- <el-button type="primary" @click="recoverAll">全部还原</el-button> -->
        <!-- <el-button type="danger" @click="emptyAll">清空回收站</el-button> -->
      </div>
      <hr>
      <div class="explorer" v-if="showList">
        <el-table
          v-loading.body="this.$store.state.share.loading"
          :data="tableData"
          stripe
          border
          style="width: 100%"
          @selection-change="handleSelectionChange"
          :default-sort = "{prop: 'date', order: 'descending'}">
          <el-table-column
            fixed
            type="selection"
            width="55">
          </el-table-column>
          <el-table-column
            fixed
            prop="id"
            label="ID"
            width="75">
          </el-table-column>
          <el-table-column
            fixed
            prop="file_name"
            label="名称">
          </el-table-column>
          <el-table-column
            prop="file_size"
            label="文件大小"
            width="150">
          </el-table-column>
          <el-table-column
            prop="file_mime_type"
            label="文件类型"
            width="150">
          </el-table-column>
          <!-- <el-table-column
            prop="path"
            label="路径">
          </el-table-column> -->
          <el-table-column
            prop="upload_time"
            label="上传日期"
            width="180"
            sortable>
          </el-table-column>
          <el-table-column
            prop="modification_time"
            label="修改日期"
            width="180"
            sortable>
          </el-table-column>
          <el-table-column
            prop="delete_time"
            label="删除日期"
            width="180"
            sortable>
          </el-table-column>
          <el-table-column
            fixed="right"
            label="操作"
            width="170">
            <template scope="scope">
              <el-button
                size="small"
                @click="handleDownload(scope.$index, scope.row)">下载</el-button>
                <!-- v-if="scope.row.isfolder!=true" -->
              <!-- <el-button
                size="small"
                type="danger"
                @click="handleDelete(scope.$index, scope.row)">永久删除</el-button> -->
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div class="permission" v-if="!showList">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span style="line-height: 36px;">有人给你分享了私密文件，请输入分享密码</span>
            <el-button style="float: right;" type="primary" @click="submitPassword">提交</el-button>
          </div>
          <el-input
            placeholder="请输入分享密码"
            icon="search"
            v-model="password"
            :on-icon-click="handleIconClick">
          </el-input>
        </el-card>
      </div>
    </div>
    <!-- <p>{{ msg }}</p> -->
  </div>
</template>

<script>
// import Sidebar from './Sidebar'
// import List from './List'

export default {
  name: 'ShareList',
  data () {
    return {
      msg: 'ShareList',
      shareId: 0,
      showList: false,
      password: ''
    }
  },
  computed: {
    tableData () {
      return this.$store.state.share.explorer
    },
    getShareId () {
      // let a = this.$route
      // console.info(a)
      return this.$route.params.id
    }
  },
  components: {
    // Sidebar,
    // List
  },
  methods: {
    submitPassword () {
      this.loadList(this.password)
    },
    loadList (password) {
      console.log('start ajax')
      // this.$store.dispatch('loadShareList')
      // this.$store.state.share.loading = true
      let Vue = this
      Vue.$store.commit('changeShareLoading', true)
      let url = 'http://127.0.0.1/netdisk/public/share'
      this.$http.post(url, {share_id: Vue.getShareId, password})
      .then(function (res) {
        console.log(res)
        if (res.data.errorno === 31) {
          Vue.showList = false
        }
        if (res.data.errorno === 55) {
          Vue.showList = true
          console.log(res.data.data)
          // Vue.$message.success(res.data.errormsg)
          Vue.$notification.info('分享文件列表加载成功，本次共加载了' +
            res.data.data.folders.length + '个文件夹，' +
            res.data.data.files.length + '个文件。')
          Vue.$store.commit('initShareList', res.data.data)
          // Vue.$store.share.commit('initFolderId', res.data.data)
        } else {
          Vue.$notification.error(res.data.errormsg)
        }
        Vue.$store.commit('changeShareLoading', false)
        // Vue.$store.share.state.loading = false
      })
      .catch(function (err) {
        console.error(err)
        Vue.$notification.error('获取我的分享文件列表失败，网络请求出错')
        Vue.$store.commit('changeShareLoading', false)
        // Vue.$store.share.state.loading = false
      })
    },
    handleDownload (index, row) {
      console.info(row)
      let Vue = this
      // this.$message.error('a')
      let url = 'http://127.0.0.1/netdisk/public/getDownloadLinkFromShare'
      this.$http.post(url, {id: row.id, share_id: Vue.getShareId, password: Vue.$store.state.share.password})
      .then(function (res) {
        console.log(res)
        if (res.data.errorno === 21) {
          Vue.$message.success(res.data.errormsg)
          console.log(res.data.data)
          // Vue.$store.dispatch('loadList')
          let downloadLink = 'http://127.0.0.1/netdisk/public/download?download_token=' + res.data.data.download_token
          Vue.$alert(res.data.errormsg + '，' + downloadLink, '下载地址', {
            confirmButtonText: '开始下载',
            // size: 'large',
            type: 'info',
            callback: action => {
              window.open(downloadLink)
            }
          })
        } else {
          Vue.$message.error(res.data.errormsg)
        }
      })
      .catch(function (err) {
        console.error(err)
        Vue.$message.error('下载文件失败，网络请求出错')
      })
    }
  },
  created () {
    this.loadList()
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

.sidebar {
  float: left;
  width: 240px;
  padding: 30px;
  height: 90vh;
  overflow-y: scroll;
}
.list {
  height: calc(100vh - 170px);
  overflow-y: scroll;
}

.tools {
  margin: 20px;
  height: 36px;
}


.permission {
  margin: 100px auto;
  width: 480px;
}
.text {
  font-size: 14px;
}

.item {
  padding: 18px 0;
}

.clearfix:before,
.clearfix:after {
    display: table;
    content: "";
}
.clearfix:after {
    clear: both
}

.box-card {
  /*width: 480px;*/
}
/*
.netdisk {
background-color: #eee;
position:fixed;
display:block;
left:0;
top:50px;
bottom:0;
width:100%;
color:black;
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
}
*/
</style>
