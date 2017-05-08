<template>
  <div class="recycle">
    <!-- <p>{{ msg }}</p> -->
    <div class="tools">
      <el-button @click="loadList" :loading="this.$store.state.recycle.loading">刷新</el-button>
      <el-button type="primary" @click="recoverAll">全部还原</el-button>
      <el-button type="danger" @click="emptyAll">清空回收站</el-button>
    </div>
    <hr>
    <div class="explorer">
      <el-table
        v-loading.body="this.$store.state.recycle.loading"
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
              @click="handleRecover(scope.$index, scope.row)">还原</el-button>
              <!-- v-if="scope.row.isfolder!=true" -->
            <el-button
              size="small"
              type="danger"
              @click="handleDelete(scope.$index, scope.row)">永久删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>
  </div>
</template>

<script>
// import Sidebar from './Sidebar'
// import List from './List'

export default {
  name: 'Recycle',
  data () {
    return {
      msg: 'Recycle'
    }
  },
  computed: {
    tableData () {
      return this.$store.state.recycle.explorer
    }
  },
  components: {
    // Sidebar,
    // List
  },
  methods: {
    loadList () {
      this.$store.dispatch('loadRecycleList')
    },
    recoverAll () {
      let Vue = this
      this.$confirm('此操作将永久删除清空回收站内所有文件, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let url = 'http://127.0.0.1/netdisk/public/v1/recycle/recover'
        this.$http.post(url, {})
        .then(function (res) {
          console.log(res)
          if (res.data.errorno === 45) {
            Vue.$message.success(res.data.errormsg)
            console.log(res.data.data)
            Vue.loadList()
          } else {
            Vue.$message.error(res.data.errormsg)
          }
        })
        .catch(function (err) {
          console.error(err)
          Vue.$message.error('下载文件失败，网络请求出错')
        })
        // Vue.$message({
        //   type: 'success',
        //   message: '还原所有文件成功!'
        // })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消还原所有文件操作'
        })
      })
    },
    emptyAll () {
      let Vue = this
      this.$confirm('此操作将永久删除清空回收站内所有文件, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let url = 'http://127.0.0.1/netdisk/public/v1/recycle/clear'
        this.$http.post(url, {})
        .then(function (res) {
          console.log(res)
          if (res.data.errorno === 13) {
            Vue.$message.success(res.data.errormsg)
            console.log(res.data.data)
            Vue.loadList()
          } else {
            Vue.$message.error(res.data.errormsg)
          }
        })
        .catch(function (err) {
          console.error(err)
          Vue.$message.error('下载文件失败，网络请求出错')
        })
        // Vue.$message({
        //   type: 'success',
        //   message: '清空回收站成功!'
        // })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消清空回收站操作'
        })
      })
    },
    handleDelete (index, row) {
      console.info(row)
      let Vue = this
      this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let url = row.isfolder ? 'http://127.0.0.1/netdisk/public/v1/recycle/deleteFolders' : 'http://127.0.0.1/netdisk/public/v1/recycle/deleteFiles'
        this.$http.post(url, {id: row.id})
        .then(function (res) {
          console.log(res)
          if (res.data.errorno === 39 || res.data.errorno === 41) {
            Vue.$message.success(res.data.errormsg)
            console.log(res.data.data)
            Vue.loadList()
          } else {
            Vue.$message.error(res.data.errormsg)
          }
        })
        .catch(function (err) {
          console.error(err)
          Vue.$message.error('删除文件失败，网络请求出错')
        })
        // Vue.$message({
        //   type: 'success',
        //   message: '删除成功!'
        // })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消删除'
        })
      })
    },
    handleRecover (index, row) {
      console.info(row)
      let Vue = this
      this.$confirm('此操作将还原该文件到原始文件夹内, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let url = row.isfolder ? 'http://127.0.0.1/netdisk/public/v1/recycle/recoverFolders' : 'http://127.0.0.1/netdisk/public/v1/recycle/recoverFiles'
        this.$http.post(url, {id: row.id})
        .then(function (res) {
          console.log(res)
          if (res.data.errorno === 47 || res.data.errorno === 49) {
            Vue.$message.success(res.data.errormsg)
            console.log(res.data.data)
            Vue.loadList()
          } else {
            Vue.$message.error(res.data.errormsg)
          }
        })
        .catch(function (err) {
          console.error(err)
          Vue.$message.error('还原文件失败，网络请求出错')
        })
        // Vue.$message({
        //   type: 'success',
        //   message: '删除成功!'
        // })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消还原'
        })
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
.tools {
  margin: 20px;
}
.explorer {
  height: calc(100vh - 140px);
  overflow-y: scroll;
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
