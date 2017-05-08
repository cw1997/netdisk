<template>
  <div class="hello">
    Share
  </div>
</template>

<script>
import Explorer from '../netdisk/list/Explorer'
export default {
  name: 'Footer',
  components: {
    Explorer
  },
  data () {
    return {
      msg: 'Footer'
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
footer p {
  text-align: center;
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
