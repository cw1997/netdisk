<template>
  <div class="sidebar">
    <!-- <h2>{{ msg }}</h2> -->
    <!-- <h2>· 文件分类</h2>
    <hr>
    <ul class="category">
      <li><a href="">全部文件</a></li>
      <li><a href="">全部文件</a></li>
      <li><a href="">全部文件</a></li>
      <li><a href="">全部文件</a></li>
      <li><a href="">全部文件</a></li>
      <li><a href="">全部文件</a></li>
    </ul>
    <hr>
    <div>
      统计图1
    </div>
    <div>
      统计图2
    </div> -->
    <el-upload
      class="upload-demo"
      action="http://127.0.0.1/netdisk/public/v1/file/upload"
      :with-credentials="true"
      :data="{access_token: this.$store.state.passport.access_token, folder_id: this.$store.state.netdisk.folder_id}"
      :multiple="true"
      :on-preview="handlePreview"
      :on-remove="handleRemove"
      :on-success="handleSuccess"
      :file-list="fileList">
      <el-button size="small" type="primary">点击上传</el-button>
      <div slot="tip" class="el-upload__tip">可以上传各种格式的文件（有部分office文件由于锁原因无法上传，请等待后续修复），且不超过200MB</div>
    </el-upload>
  </div>
</template>

<script>
export default {
  name: 'Sidebar',
  methods: {
    handleRemove (file, fileList) {
      console.log(file, fileList)
    },
    handlePreview (file) {
      console.log(file)
      window.open(file.url)
    },
    handleSuccess (file) {
      console.log(file)
      this.loadList()
      // console.info('handleSuccess', file)
      this.$notification.success('您所选择的文件：' + file.data.file_name + ' 已经上传完毕！')
    },
    loadList () {
      this.$store.dispatch('loadList', this.$store.state.netdisk.folder_id)
    }
  },
  data () {
    return {
      msg: 'Sidebar',
      fileList: [
      // {name: 'food.jpeg', url: 'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'}, {name: 'food2.jpeg', url: 'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'}
      ]
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
/*.sidebar {
  position: fixed;
  left: 0;
  top: 50px;
  height: 100vh;
  width: 240px;
  padding: 30px;
  background-color: gray;
  background: blue;
}*/
.sidebar {
  float: left;
  width: 240px;
  padding: 30px;
  height: 90vh;
  overflow-y: scroll;
}
/* ul, li {
  margin: 0;
  padding: 0;
}
.category li {
  list-style: none;
}
.category li a {
  display: block;
  width: 240px;
  height: 50px;
  line-height: 50px;
  text-align: center;
  text-decoration: none;
  color: white;
  font-weight: bolder;
}
.category li a:hover {
  background-color: black;
} */
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
