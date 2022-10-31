<template>
    <div class="rounded text-wrap article" @click="clearAlert()">
        <form class="col-sm-11">
        <div class="input-group mb-3">
          <span class="input-group-text">網站名稱</span>
          <input type="text" class="form-control" placeholder="網站名稱" v-model="HOME">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">資料庫網址</span>
          <input type="text" class="form-control" placeholder="資料庫網址" v-model="hostname_gb">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">資料庫網址</span>
          <input type="text" class="form-control" placeholder="資料庫名稱" v-model="database_gb">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">資料庫使用者</span>
          <input type="text" class="form-control" placeholder="資料庫使用者" v-model="username_gb">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">資料庫密碼</span>
          <input type="password" class="form-control" placeholder="資料庫密碼" v-model="password_gb">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">登入帳號</span>
          <input type="text" class="form-control" placeholder="登入帳號" v-model="USERNAME">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">登入密碼</span>
          <input type="password" class="form-control" placeholder="登入密碼" v-model="PASSWORD">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">發文名稱</span>
          <input type="text" class="form-control" placeholder="發文名稱" v-model="AUTHORNAME">
        </div>
        <a class="btn btn-info" @click="install()"  style="width:100px; height:35px;">安裝</a>
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

export default {
  inject: [
     'conection'
     ],
  data() {
    return {
      HOME:'',
      hostname_gb: '',
      database_gb: '',
      username_gb:'',
      password_gb:'',
      USERNAME:'',
      PASSWORD:'',
      AUTHORNAME:'',
      msg:'',
      Alertflag:false
    }
  },
  methods:{
  install(){
      let me = this;
      
      let data = new URLSearchParams();
      data.append('HOME', me.HOME);
      data.append('hostname_gb', me.hostname_gb);
      data.append('database_gb', me.database_gb);
      data.append('username_gb', me.username_gb);
      data.append('password_gb', me.password_gb);
      data.append('USERNAME', me.USERNAME);
      data.append('PASSWORD', me.PASSWORD);
      data.append('AUTHORNAME', me.AUTHORNAME);

      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           me.$router.push("/");
           me.$router.go(0);
       }
       else{
          me.msg =response.data.msg;
          me.Alertflag = true;
       }
      },"install");
  },
  clearAlert(){
      let me = this;
      me.msg ='';
      me.Alertflag = false;
  }
  }
}
</script>