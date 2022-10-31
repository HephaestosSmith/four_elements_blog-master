<template>
      <div class="rounded text-wrap article text-white row" v-if="!useStore.state.modalloadflag">
      <div class="col" :key = uuid>
           <div class="row">
                <div class="col-6">
                  {{ article.CREATEDATE }}
                </div>
                <div class="col-6 text-right">
                   <a class="dropdown"  v-if="loginstatus()">
                     <a class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" style="height: 35px;">
                     </a>
                     <div class="dropdown-menu">
                       <router-link class="dropdown-item btn"  :to="{ name: 'edited', params: { UUID:uuid } }">編輯</router-link>
                       <button class="dropdown-item btn" @click="Delete(uuid)">刪除</button>
                     </div>
                  </a>
                </div>
           </div>
           <div style="height:8px;"/>
           <div class="row">
                <div class="col ck-content" v-html= article.CONTENT>
                </div>
           </div>
           <hr>
           <div class="row">
              <div class="col-7">
                    發文時間:{{ article.CREATETIME }}
              </div>
              <div class="col-5 text-right">
                  作者: {{article.AUTHOR}}
              </div>
           </div>
          </div>
      </div>
    <div class="row">
      <div class="col">
         <div style="height:20px"/>
      </div>
    </div>
    <div class="row" v-if="useStore.state.modalloadflag">
      <div class="col d-flex justify-content-center">
        <div class="spinner-grow text-primary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-success m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-danger m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-warning m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-info m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-light m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-dark m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <!--
         
        <div class="spinner-border text-primary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-primary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-secondary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-success m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-danger m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-warning m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-info m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-light m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-dark m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>-->
      </div>
    </div>
</template>
<script>
import { useStore } from 'vuex'

export default {
  inject: [
     'conection',
     'modalshow'
     ],
  props: {
    uuid: {
      type: String,
    },
  },
  data() {
      return {
          POWER:'1',
          UUID:'',
          article:{CONTENT:'',
                   AUTHOR:'',
                   CREATEDATE:''}
      };
  },
  watch:{
    article:function (){
         this.$nextTick(function () {
           this.useStore.commit('PrismView');
         });
    }
  },
  created(){
     let me = this;
     me.useStore = useStore();
     me.article = {CONTENT:'',
                   AUTHOR:'',
                   CREATEDATE:''}
     me.getAticle();
  },
  methods:{
      loginstatus(){
         let logined = this.useStore.state.logined;
         let load = this.useStore.state.modalloadflag;
         let flag = logined & !load;
         return flag;
      },
  getAticle(){
      let me = this;
      let UUID = me.uuid;
      let useStore = me.useStore;
      let state = me.useStore.state;
      let articledata  = state.list.filter(function(item) {
                 return item.UUID === UUID
             });

      let data = new URLSearchParams();
      data.append('commandType', "getAticle");
      data.append('SEARCHTYPE', 'CONTENT');
      data.append('KEYWORD', UUID);
      if (articledata.length == 0){
      me.conection(data,function(response){
       state.modalloadflag = false;
       if(response.data.length >0){
       let result = response.data[0];
       me.article = result;
       me.UUID = UUID;
       me.modalshow();
       }else{
         useStore.commit('ModalViewColse');
       }
      });
      }else{
       me.article = articledata [0];
       state.modalloadflag= false;
      }
  },
  Delete(UUID){
      if(!confirm("是否刪除文章?")){
        return;
      }
      let me = this;
      let useStore = me.useStore;
      let state = me.useStore.state;
      
      let data = new URLSearchParams();
      data.append('commandType', "delete");
      data.append('UUID', UUID);
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
             let list = [];
             list = state.list.filter(function(item) {
                 return item.UUID !== UUID
             });
             state.list = list;
             useStore.commit('ModalViewColse');
       }
       else{
          let msg =response.data.msg;
          alert(msg);
       }
      })
  }
  }
}
</script>