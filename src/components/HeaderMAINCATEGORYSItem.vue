<template>
    <li class="nav-item dropdown">
      <div class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
        分類
      </div>
      <div class="dropdown-menu bg-dark">
        <div class="dropright" v-for="(item,index) in MAINCATEGORYS" :key="index">
        <div class="dropdown-item">
          {{item.CATEGORYNAME}}
        </div>
        <div class="dropdown-menu bg-dark">
          <a class="dropdown-item" v-for="(subitem,subindex) in item.SUBCATEGORYS" :key="subindex" @click="SearchCATEGORY(subitem.CATEGORYNAME)">{{subitem.CATEGORYNAME}}</a>
        </div>
        </div>
      </div>
    </li>
</template>
<script>
import { useStore } from 'vuex'

export default {
  inject: [
     'conection',
     'modalshow'
     ],
  data() {
      return {
          MAINCATEGORYS:[],
          useStore:[]
      };
  },
  created(){
     let me = this;
     me.useStore = useStore();
     me.getALLCATEGORYS();
  },
  methods:{
  getALLCATEGORYS(){
      let me = this;
      let data = new URLSearchParams();
      data.append('commandType', "getALLCATEGORYS");
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           let result = response.data.result
           me.MAINCATEGORYS = result.filter(function(item) {
               return item.MAINCATEGORYID === 0
           });
           me.MAINCATEGORYS.forEach(element =>{
               let SUBCATEGORYS = {"SUBCATEGORYS":result.filter(function(item) {
                                                   return item.MAINCATEGORYID === element.CATEGORYINDEX
                                                }),"ISSHOW":false};
               element = Object.assign(element,SUBCATEGORYS)
             });
          me.useStore.state.MAINCATEGORYS = me.MAINCATEGORYS;
       }
       else{
          let msg =response.data.msg;
          me.loading = false;
          alert(msg);
       }
      })
  },
  SearchCATEGORY(CATEGORY){
    let me = this;
    let state = me.useStore.state;
    state.homeloadflag = true;
    state.list = [];
 
    state.SEARCHTYPE = "CATEGORY";
    state.KEYWORD = CATEGORY;
    let data = new URLSearchParams();
    data.append('commandType', "getAticle");
    data.append('SEARCHTYPE', state.SEARCHTYPE);
    data.append('KEYWORD',state.KEYWORD);
    
    me.conection(data,function(response){
     state.homeloadflag = false;
     let success = response.data.success;
     if (success == "1"){
         let result = response.data.result;
            result.forEach(element => {
                state.list.push(element);
            });
            state.noDataFlag = false;
         }
     })
   }
  }
}
</script>

<style>
.nav-item .dropdown-item:hover, .dropdown-item:focus {
    cursor: pointer;
    color: rgba(255, 255, 255, 1);
    text-decoration: none;
    background-color: rgba(0, 0, 0, 0);
}

.nav-item .dropdown-item {
    color: rgba(255, 255, 255, 0.5);
}
</style>