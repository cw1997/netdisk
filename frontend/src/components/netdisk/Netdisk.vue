<template>
  <div class="netdisk">
    <Sidebar></Sidebar>
    <List></List>
  </div>
</template>

<script>
import Sidebar from './Sidebar'
import List from './List'

export default {
  name: 'Netdisk',
  data () {
    return {
      msg: 'Netdisk'
    }
  },
  components: {
    Sidebar,
    List
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
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
