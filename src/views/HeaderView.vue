<template>
  <!--<div class="col-12">
    <a id="header">四元素部落格</a>
    <p><router-link to="/" class="btn btn-primary btn-xs" role="button">Home</router-link>
       | <router-link to="/about" class="btn btn-primary btn-xs" role="button">about</router-link></p>
  </div>-->
<nav class="navbar navbar-expand-lg bg-dark navbar-dark" role="navigation">
  <!-- Brand -->
  <router-link class="navbar-brand" to="/" @click="home()">{{HomeName}}</router-link>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
  <!-- Links -->
  <ul class="navbar-nav mr-auto">
    <li class="nav-item">
      <router-link class="nav-link" to="/" @click="home()" v-if="installflag">首頁</router-link>
    </li>
    <HeaderMAINCATEGORYSItem/>
    <li class="nav-item" v-if="!loginstatus() && installflag">
      <router-link class="nav-link" to="/login"  data-toggle="modal" data-target="#ModalView">登入</router-link>
    </li>
    <li class="nav-item" v-if="loginstatus() && installflag">
      <router-link class="nav-link"  to="/" @click="logout()">登出</router-link>
    </li>
  </ul>
  <div class="form-inline my-2 my-lg-0" v-if="installflag">
  <input class="form-control mr-sm-2" type="search" placeholder="輸入關鍵字" v-model="SearchData" >
  <button class="btn btn-outline-success " @click="HeaderSearch(true)" >查詢</button>
  </div>
  </div>
</nav>
</template>

<script>
import HeaderMAINCATEGORYSItem from '../components/HeaderMAINCATEGORYSItem.vue'
import { useStore } from 'vuex'
import Cookies from 'vue-cookie'

export default {
  inject: [
     'reload',
     'conection'
     ],
  data() {
      return {
          SearchData: '',
          HomeName:'想個好名字吧',
          installflag:false
      };
  },
  components: {
    HeaderMAINCATEGORYSItem
  },
  created(){
     this.useStore = useStore();
     this.Logined();
  },
  methods:{
  loginstatus(){
     return this.useStore.state.logined;
  },
  Logined(){
      let me = this;
      if(me.$route.name!="install"){
         let state = me.useStore.state;
         let data = new URLSearchParams();
         data.append('commandType', "gethomename");
         me.conection(data,function(response){
          let success = response.data.success;
          if (success == "1"){
              let result = response.data.result[0];
              me.HomeName = result['PAGENAME'];
              me.installflag = true;
          }});

         data = new URLSearchParams();
         data.append('commandType', "check");
         
         me.conection(data,function(response){
          let success = response.data.success;
          if (success == "1"){
              state.logined = true;
          }
          else{
              state.logined = false;
          } });
      }
  },
  logout(){
      let me = this;
      let state = me.useStore.state;
      Cookies.delete('username');
      Cookies.delete('TOKEN');
      Cookies.delete('authorname');
      state.list = [];
      state.logined = false;
      state.noDataFlag = false;
      me.Logined();
      me.HeaderSearch();
  },
  home(){
      let me = this;
      let state = me.useStore.state;
      state.list = [];
      state.logined = false;
      state.noDataFlag = false;
      if(me.$route.name!="install"){
         me.Logined();
         me.HeaderSearch();
      }
  },
  HeaderSearch(flag){
      let me = this;
      let state = me.useStore.state;
      state.homeloadflag = true;

      if(!flag){
        me.SearchData = '';
        state.SEARCHTYPE = "default";
      }
      
      if(me.SearchData.length == 0){
        state.SEARCHTYPE = "default";
      }else{
        state.SEARCHTYPE = "KEYWORD";
      }
      state.KEYWORD = me.SearchData;
      state.noDataFlag = true;
      state.list = [];

      let data = new URLSearchParams();
      data.append('commandType', "getAticle");
      data.append('SEARCHTYPE', state.SEARCHTYPE);
      data.append('KEYWORD', state.KEYWORD);
      
      me.conection(data,function(response){
       state.homeloadflag = false;
       let result = response.data;
       result.forEach(element => {
           state.list.push(element);
       });
       state.noDataFlag = false;
      });
  }
  }
}
</script>

<style>
.navbar-nav li:hover>.dropdown-menu {
  display: block;
  margin-top: 0px;
}
.navbar-nav li .dropdown-menu .dropright:hover>.dropdown-menu{
  display: block;
  margin-left: -5px;
}
</style>