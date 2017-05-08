<template>
  <div class="explorer">
    <el-table
      v-loading.body="this.$store.state.netdisk.loading"
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
        fixed="right"
        label="操作"
        width="200">
        <template scope="scope">
          <el-button
            v-if="scope.row.isfolder!=true"
            size="small"
            @click="handleDownload(scope.$index, scope.row)">下载</el-button>
          <el-button
            v-if="scope.row.isfolder===true"
            size="small"
            type="primary"
            @click="handleOpenFolder(scope.$index, scope.row)">打开</el-button>
          <el-button
            v-if="scope.row.isfolder!=true"
            size="small"
            @click="handleShare(scope.$index, scope.row)">分享</el-button>
          <el-button
            size="small"
            type="danger"
            @click="handleDelete(scope.$index, scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-dialog title="分享设置" v-model="dialogVisible" size="small" :modal="false" :modal-append-to-body="false">
      <el-form ref="form" :model="form" label-width="80px">
        <!-- <el-form-item label="活动名称">
          <el-input v-model="form.name"></el-input>
        </el-form-item> -->
        <!-- <el-form-item label="活动区域">
          <el-select v-model="form.region" placeholder="请选择活动区域">
            <el-option label="区域一" value="shanghai"></el-option>
            <el-option label="区域二" value="beijing"></el-option>
          </el-select>
        </el-form-item> -->
        <el-form-item label="自动过期">
          <el-switch on-text="" off-text="" v-model="form.expire"></el-switch>
        </el-form-item>
        <el-form-item label="过期时间">
          <div class="block">
            <el-date-picker
              v-model="form.time"
              type="datetime"
              placeholder="选择日期时间"
              align="right"
              :picker-options="pickerOptions1">
            </el-date-picker>
          </div>
        </el-form-item>
        <!-- <el-form-item label="活动性质">
          <el-checkbox-group v-model="form.type">
            <el-checkbox label="美食/餐厅线上活动" name="type"></el-checkbox>
            <el-checkbox label="地推活动" name="type"></el-checkbox>
            <el-checkbox label="线下主题活动" name="type"></el-checkbox>
            <el-checkbox label="单纯品牌曝光" name="type"></el-checkbox>
          </el-checkbox-group>
        </el-form-item> -->
        <el-form-item label="访问权限">
          <el-radio-group v-model="form.permission">
            <el-radio label="0">完全公开</el-radio>
            <el-radio label="1">公开但需要密码</el-radio>
            <el-radio label="2">指定好友可以访问</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="分享密码">
          <el-input v-model="form.password"></el-input>
        </el-form-item>
        <el-form-item label="分享简介">
          <el-input type="textarea" v-model="form.desc"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="share">立即分享</el-button>
          <el-button @click="dialogVisible = false">取消</el-button>
        </el-form-item>
      </el-form>
      <!-- <span>这是一段信息</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="dialogVisible = false">确 定</el-button>
      </span> -->
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'Explorer',
  data () {
    return {
      msg: 'Explorer',
      dialogVisible: false,
      form: {
        expire: false,
        time: '',
        permission: '',
        desc: '',
        pickerOptions1: {
          shortcuts: [{
            text: '今天',
            onClick (picker) {
              picker.$emit('pick', new Date())
            }
          }, {
            text: '昨天',
            onClick (picker) {
              const date = new Date()
              date.setTime(date.getTime() - 3600 * 1000 * 24)
              picker.$emit('pick', date)
            }
          }, {
            text: '一周前',
            onClick (picker) {
              const date = new Date()
              date.setTime(date.getTime() - 3600 * 1000 * 24 * 7)
              picker.$emit('pick', date)
            }
          }]
        }
      }
    }
  },
  computed: {
    tableData () {
      return this.$store.state.netdisk.explorer
    }
  },
  methods: {
    share (id) {
      console.info(id)
      let Vue = this
      // this.$message.error('a')
      let url = 'http://127.0.0.1/netdisk/public/v1/share/share'
      Vue.form.files = Vue.$store.state.netdisk.click_id
      this.$http.post(url, Vue.form)
      .then(function (res) {
        console.log(res)
        if (res.data.errorno === 29) {
          Vue.$message.success(res.data.errormsg)
          console.log(res.data.data)
        } else {
          Vue.$message.error(res.data.errormsg)
        }
      })
      .catch(function (err) {
        console.error(err)
        Vue.$message.error('下载文件失败，网络请求出错')
      })
      this.dialogVisible = true
    },
    handleSelectionChange (val) {
      console.info(val)
      this.$store.state.netdisk.selected = val
    },
    handleShare (index, row) {
      this.dialogVisible = true
      console.info(row)
      this.$store.commit('setClickId', row.id)
      // this.share(row.id)
    },
    handleDownload (index, row) {
      console.info(row)
      let Vue = this
      // this.$message.error('a')
      let url = 'http://127.0.0.1/netdisk/public/v1/file/getDownloadLink'
      this.$http.post(url, {id: row.id})
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
    },
    handleOpenFolder (index, row) {
      console.info(row)
      this.$store.dispatch('loadList', row.id)
    },
    handleDelete (index, row) {
      console.info(row)
      let Vue = this
      this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let url = row.isfolder ? 'http://127.0.0.1/netdisk/public/v1/file/deleteFolders' : 'http://127.0.0.1/netdisk/public/v1/file/deleteFiles'
        this.$http.post(url, {id: row.id})
        .then(function (res) {
          console.log(res)
          if (res.data.errorno === 7) {
            Vue.$message.success(res.data.errormsg)
            console.log(res.data.data)
            Vue.$store.dispatch('loadList')
          } else {
            Vue.$message.error(res.data.errormsg)
          }
        })
        .catch(function (err) {
          console.error(err)
          Vue.$message.error('下载文件失败，网络请求出错')
        })
        Vue.$message({
          type: 'success',
          message: '删除成功!'
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消删除'
        })
      })
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.explorer {
  height: calc(100vh - 170px);
  overflow-y: scroll;
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
