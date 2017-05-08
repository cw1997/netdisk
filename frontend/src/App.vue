<template>
  <div id="app">
    <!-- <img src="./assets/logo.png">
    <hello></hello> -->
    <Navbar></Navbar>
    <router-view></router-view>
    <!-- <Footer></Footer> -->
  </div>
</template>

<script>
// import Hello from './components/Hello'
import Navbar from './components/common/Navbar'
import Footer from './components/common/Footer'

export default {
  name: 'app',
  components: {
    Navbar,
    Footer
  }
}
</script>

<style>
.router-link-active{
  /*text-decoration-line: underline;*/
  /*text-decoration-style: solid;*/
  /*text-decoration-color: white;*/
  background-color: rgba(255,255,255,0.3);
}
.left {
  float: left;
}
.right {
  float: right;
}
/* #app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
} */
</style>
