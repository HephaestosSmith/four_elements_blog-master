<template>
    <div class="rounded text-wrap article" @click="clearAlert()">
        <form class="col-12">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Username" v-model="username">
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" v-model="password">
        </div>
        <a class="btn btn-info" @click="login()"  style="width:100px; height:35px;">登入</a>
        </form>
    </div>
    <div class="alert alert-danger d-flex align-items-center" role="alert" v-if= Alertflag>
      <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
      <div>
        {{ msg }}
      </div>
    </div>
</template>

<script>
//import router from '@/router';
import { useStore } from 'vuex'

export default {
  inject: [
     'reload',
     'conection'
     ],
  data() {
    return {
      username: '',
      password: '',
      ModalView:null,
      Alertflag:false,
      msg:''
    }
  },
  created() {
      this.useStore = useStore();
  },
  methods:{
  login(){
      let me = this;
      let useStore = me.useStore;
      let state = me.useStore.state;
      let Cookies = useStore.state.Cookies;
      let data = new URLSearchParams();
      data.append('commandType', "login");
      data.append('username', me.username);
      data.append('password', me.password);

      me.conection(data,function(response){
       let success = response.data.success;
       let result = response.data.result;
       if (success == "1"){
           Cookies.set('TOKEN',result.TOKEN, { expires: 1 });
           Cookies.set('username',result.username, { expires: 1 });
           Cookies.set('authorname',result.authorname, { expires: 1 });
           state.list = [];
           state.noDataFlag = false;
           useStore.commit('ModalViewColse');
           me.reload();
       }
       else{
          me.msg =response.data.msg;
          me.Alertflag = true;
       }
      });
  },
  clearAlert(){
      let me = this;
      me.msg ='';
      me.Alertflag = false;
  }
  }
}
</script>